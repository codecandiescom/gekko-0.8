<?php

	/*
	*	Gekko - Open Source Web Development Framework
	*	------------------------------------------------------------------------
	*	Copyright (C) 2004-2006, J. Carlos Nieto <xiam@users.sourceforge.net>
	*	This program is Free Software.
	*
	*	@package	Gekko
	*	@license	http://www.gnu.org/copyleft/gpl.html GNU/GPL License 2.0
	*	@author		J. Carlos Nieto <xiam@users.sourceforge.net>
	*	@link		http://www.gekkoware.org
	*/


	if (!defined("IN-GEKKO")) die("Get a life!");
	

	function getmicrotime() {
		$mtime = explode(" ",microtime());
		return ((float)$mtime[0] + (float)$mtime[1]);
	}

	// registering Gekko's initiation time
	define ("GEKKO_START_TIME", getmicrotime());

	// we don't need such a 'feature'
	ini_set("register_globals", false);

	// Gekko's root (path to the source directory)
	define("GEKKO_SOURCE_DIR", dirname(__FILE__)."/");

	// If you're planning to use Gekko in a vhost.example.org scheme, then
	// you may find useful this feature.
	define("GEKKO_SUBDOMAIN_MODE", false);

	if (GEKKO_SUBDOMAIN_MODE) {

		preg_match("/([^.]*)\..*/", $_SERVER["HTTP_HOST"], $subdomain);
		if (isset($subdomain[1])) {
			define ("GEKKO_SUBDOMAIN", $subdomain[1]);

			define ("GEKKO_USER_DIR", GEKKO_SOURCE_DIR."virtual/".GEKKO_SUBDOMAIN."/");

			$requires = array("dbconf.php", "cgdb.php", "data", "temp");

			foreach ($requires as $require)
				if (!file_exists(GEKKO_USER_DIR."/{$require}"))
					trigger_error("File '/virtual/".GEKKO_SUBDOMAIN."/{$require}' for '".GEKKO_SUBDOMAIN."' does not exist.", E_USER_ERROR);

		} else {
			trigger_error("Please make sure that you're inside a subdomain.example.org environment or that GEKKO_SUBDOMAIN_MODE is set to 'false' in conf.php file.");
		}
	}

	if (!defined("GEKKO_USER_DIR"))
		define("GEKKO_USER_DIR", dirname(__FILE__)."/");

	define("GEKKO_MODULE_DIR", GEKKO_SOURCE_DIR."modules/");
	define("GEKKO_TEMP_DIR", GEKKO_USER_DIR."temp/");
	define("GEKKO_DATA_DIR", GEKKO_USER_DIR."data/");
	define("GEKKO_LIB_DIR", GEKKO_SOURCE_DIR."lib/");
	define("GEKKO_PLUGIN_DIR", GEKKO_LIB_DIR."plugins/");
	define("GEKKO_LANG_DIR", GEKKO_SOURCE_DIR."lang/");
	define("GEKKO_TEMPLATE_DIR", GEKKO_SOURCE_DIR."templates/");
	define("GEKKO_COOKIE_PREFIX", "gekko_");
	define("GEKKO_COOKIE_LIFE", 3600*24*3);
	define("GEKKO_FULL_URL_ENCODING", false);

	if (!filesize(GEKKO_USER_DIR."dbconf.php")) {
		if (basename($_SERVER["SCRIPT_NAME"]) != "install.php") {
			header("Location: install.php");
			exit(0);
		}
	}

	// enable this for showing Gekko detailed errors (useful when you're
	// developing modules or the core)
	define("GEKKO_ENABLE_DEBUG", false);

	// current version of gekko (as distribution)
	define("GEKKO_VERSION", "0.8");

	// let Gekko save a cache of everithing possible?
	define("GEKKO_VORAZ_CACHE", true);

	// default language files to load
	define("GEKKO_DEFAULT_LANG", "es");

	// seconds that a nasty user must be banned when trying to perform
	// common attacks such as d.o.s. against Gekko engine
	define("GEKKO_BAN_TIME", 600);

	// use 'real ip' (not 'real' since it can be faked) instead of the
	// proxy ip
	define("GEKKO_REAL_IP", false);

	// enables gekko demo mode (all module "action" requests are denied for
	// privileged users)
	define("GEKKO_DEMO_MODE", false);

	// set this as 'true' when you're having 'connection timeout' problems
	// or if you know your host is not allowing secure http (shttp)
	// connections.
	define("GEKKO_NO_SSL", true);

	// Do you want to use pretty urls of the classical ones?
	// pretty: index.php/module/action
	// classic: index.php?module=foo&action=bar
	define("GEKKO_USE_PRETTY_URLS", true);
	
	// Works with pretty URLs (only under Apache)
	define("GEKKO_MOD_REWRITE", isset($_GET["path_info"]));

	if (GEKKO_ENABLE_DEBUG) {
		error_reporting(E_ALL);
		ini_set("display_errors", true);
	}

	function getIP() {
		$ip = false;
		$ips = array();
		if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}
		if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$ips = explode(", ", $_SERVER["HTTP_X_FORWARDED_FOR"]);
			if ($ip) { array_unshift($ips, $ip); $ip = false; }
		}
		for ($i = 0; $i < count($ips); $i++) {
			if (!eregi("^(10|172\.16|192\.168)\.", $ips[$i])) {
				$ip = $ips[$i];
				break;
			}
		}
		return (($ip && GEKKO_REAL_IP == true) ? $ip : $_SERVER["REMOTE_ADDR"]);
	}

	define ("IP_ADDR", getIP());

	// trying to solve some problems between different PHP versions
	if (isset($_SERVER["ORIG_PATH_INFO"]) && $_SERVER["ORIG_PATH_INFO"])
		$_SERVER["PATH_INFO"] = $_SERVER["ORIG_PATH_INFO"];

	if (isset($_ENV["ORIG_PATH_INFO"]) && $_ENV["ORIG_PATH_INFO"])
		$_SERVER["PATH_INFO"] = $_ENV["ORIG_PATH_INFO"];

	if (isset($_ENV["PATH_INFO"]) && $_ENV["PATH_INFO"])
		$_SERVER["PATH_INFO"] = $_ENV["PATH_INFO"];

	if (!isset($_SERVER["SCRIPT_FILENAME"]) && isset($_ENV["PATH_TRANSLATED"]))
		$_SERVER["SCRIPT_FILENAME"] = $_ENV["PATH_TRANSLATED"];

	if (isset($_SERVER["HTTP_REFERER"])) {
		$_SERVER["HTTP_REFERER"] = explode(", ", $_SERVER["HTTP_REFERER"]);
		$_SERVER["HTTP_REFERER"] = array_pop($_SERVER["HTTP_REFERER"]);
	}

	$_SERVER["SCRIPT_NAME"] = urldecode(preg_replace("/\?.*$/", "", $_SERVER["REQUEST_URI"]));

	if (isset($_SERVER["SCRIPT_URL"])) // bugfix for php4u
		$_SERVER["SCRIPT_NAME"] = $_SERVER["SCRIPT_URL"];

	if (isset($_SERVER["PATH_INFO"]) && $_SERVER["PATH_INFO"]) {
		if ($_SERVER["PATH_INFO"] == $_SERVER["SCRIPT_NAME"] && isset($_SERVER["PHP_SELF"])) {
			$_SERVER["SCRIPT_NAME"] = $_SERVER["PHP_SELF"];
			$_SERVER["PATH_INFO"] = substr($_SERVER["PATH_INFO"], strlen($_SERVER["SCRIPT_NAME"]));
		} else {
			$_SERVER["SCRIPT_NAME"] = substr($_SERVER["SCRIPT_NAME"], 0, -1*strlen($_SERVER["PATH_INFO"]));
		}
	}

	if (!isset($_SERVER["HTTP_USER_AGENT"]))
		$_SERVER["HTTP_USER_AGENT"] = "Unknown/0.0";

	if (isset($_GET["path_info"]))
		$_SERVER["PATH_INFO"] = '/'.ltrim($_GET["path_info"], '/');
		
	// Redirecting ./index.php to ./
	preg_match("/^(.*?)index.php(.*?)$/", isset($_SERVER["SCRIPT_URL"]) ? $_SERVER["SCRIPT_URL"] : $_SERVER["REQUEST_URI"], $match);
	if (isset($match[1])) {
		$path = rtrim($match[1], '/');	
		$trailing = ltrim($match[2], '/');
		if (GEKKO_MOD_REWRITE || (!$trailing || substr($trailing, 0, 1) == '?'))
			exit(header("Location: {$path}/{$trailing}"));
	}

	// --------------- begin blacklist ---------------
	define("GEKKO_BLACKLIST", GEKKO_TEMP_DIR."ip.blacklist");

	$is_banned = false;

	$ban_reason = "You've been a bad, bad girl! I though I told you to not bother me!";

	// this array will be used in lib/auth.inc.php, please don't mess with it!
	$banlist = array();

	if (file_exists(GEKKO_BLACKLIST) && (($fh = fopen(GEKKO_BLACKLIST, "rw")) !== false)) {

		$banlist = unserialize(fread($fh, filesize(GEKKO_BLACKLIST)));

		for ($i = 0; isset($banlist[$i]); $i++) {

			$entry = &$banlist[$i];

			$ban_time = ($entry['ban_time'] ? $entry['ban_time'] : GEKKO_BAN_TIME);
			if ($entry['ban_start']+$ban_time < time()) {
				unset($entry);
			} else {
				if ($entry['ip_addr'] == IP_ADDR) {
					$is_banned = true;
					if ($entry['ban_reason'])
						$ban_reason = $entry['ban_reason'];
					$ban_seconds_left = ($entry['ban_start']+$ban_time)-time();
				}
			}
		}

		fwrite($fh, serialize($banlist));

		fclose($fh);
	}

	if ($is_banned) {
		header("HTTP/1.1 403 Forbidden");
		echo "
		<h1>Forbidden</h1>
		{$ban_reason} ({$ban_seconds_left} seconds left)
		<hr /><address>This site is powered by <a href=\"http://www.gekkoware.org\">Gekko</a></address>
		";
		die;
	}
	// --------------- end blacklist ---------------

	require(GEKKO_LIB_DIR."core.lib.php");

	appSessionStart();

	_L("C_SITE.REQUEST_PATH", $_SERVER["PHP_SELF"]);

	_L("C_SITE_ROOT", dirname($_SERVER["PHP_SELF"]));


	// this variable should be EVER pointing to this directorie's URL.
	$script_path = $L["C_SITE_ROOT"];

	if (isset($_SERVER["SCRIPT_URI"])) {
		preg_match("/[a-z]*:\/\/[^\/]*/", $_SERVER["SCRIPT_URI"], $match);
		$gekko_uri = "{$match[0]}$script_path";
	} else {
		$gekko_uri = "http://{$_SERVER["HTTP_HOST"]}{$script_path}";
	}

	$module = null;

	// Guessing requested module
	if (ereg("/modules/", $gekko_uri)) {
		// The user requested an action
		$match = Array();
		preg_match("/(.*)\/modules\/([^\/]*)/", $gekko_uri, $match);
		$gekko_uri = $match[1];
		$module = $match[2];
		define("GEKKO_ACTION_MODULE", $module);
	} else {
		// The user is viewing the website
		Extract (
			urlImportMap("module=alpha")
		);
		if ($module && file_exists(GEKKO_MODULE_DIR.$module))
			define("GEKKO_REQUEST_MODULE", $module);
	}

	$gekko_uri = rtrim($gekko_uri, '/').'/';

	_L("C_SITE.URL", $gekko_uri);
	
	$path_level = isset($_SERVER["PATH_INFO"]) ? substr_count($_SERVER["PATH_INFO"], '/') : 0;

	if (GEKKO_MOD_REWRITE) {
		if ($path_level)
			$path_level--;	
	}

	_L("C_SITE.REL_URL", $path_level ? rtrim(str_repeat("../", $path_level), '/') : '');

	if ($L["C_SITE.REL_URL"])
		$L["C_SITE.REL_URL"] .= '/';

	$website_credits = Array();
	$website_credits[] = 'Powered by <a href="http://www.gekkoware.org">Gekko</a>';

	if (!defined("IN-INSTALL")) {
		
		dbInit();
		

		$lang = preg_replace("/[^a-zA-Z\-]/", '', getUserConf("lang"));

		if (!$lang && isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
			$languages = preg_split('/[;,]/', $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
			foreach ($languages as $language) {
				$language = preg_replace("/[^a-zA-Z\-]/", '', $language);
				if (file_exists(GEKKO_LANG_DIR."{$language}")) {
					$lang = $language;
					break;
				}
			}
		}

		$Lang = new Lang($lang);

		if (!defined("GEKKO_REQUEST_MODULE")) {
			define("GEKKO_DEFAULT_VIEW", true);
			define("GEKKO_REQUEST_MODULE", conf::getkey("modules", "default"));
		}

		cacheInit();

		$Lang->loadCoreFiles();
		if (defined('GEKKO_ACTION_MODULE'))
			$Lang->LoadFromModule(GEKKO_ACTION_MODULE);

		define("GEKKO_LANG", $Lang->langCode);

		// creating an access table
		$AccessTable = new AccessControl();

		// loading core configuration variables
		$Conf = new Conf();
		$Conf->Load("core");

		// note that we're requesting both 'core' and 'lang' modules, when 'lang' module
		// is specified, it's key and value will overwrite the default definition.
		$q = $db->query("SELECT module, keyname, keyvalue
		FROM ".dbTable("conf")." WHERE module = 'core' OR module = 'lang'", __FILE__, __LINE__);

		// you can access core configuration variables by using _L('C_KEYNAME')
		while ($row = $db->fetchRow($q))
			_L((($row["module"] == 'core') ? 'C' : 'L').'_'.strtoupper($row["keyname"]), $row["keyvalue"]);

		// style
		if (isset($_GET["style"])) {
			$style = $_GET["style"];
		} else {
			$style = (($user_style = getUserConf('style')) != false) ? $user_style : $L["C_SITE.STYLESHEET"];
		}

		$style = preg_replace("/[^a-zA-Z0-9\-_\/]/", '', $style);

		preg_match("/^([^\/]+)\/(.*)$/", $style, $match);

		if (isset($match[2])) {
			
			$theme = GEKKO_TEMPLATE_DIR."$match[1]/_themes/$match[2]/theme.css";
		
			if (!file_exists($theme)) 
				$match[0] = $match[1] = "default";
			
			$style_info = null;
			if (($fh = @fopen($style_filename, "r")) !== false) {
				preg_match("/<author>(.*?)<\/author>/s", fread($fh, filesize($theme)), $style_info);
				if (isset($style_info[0]))
					$website_credits[] = $style_info[0];
				fclose($fh);
			}

			_L("C_SITE.TEMPLATE", $match[1]);
			_L("C_SITE.STYLESHEET", $match[2]);
			
			appSetMessage("info", sprintf(_L("L_STYLESHEET_CHANGE"), $match[1], $match[2]));
		}

		require(GEKKO_LIB_DIR."plugins.lib.php");
	} else {

		if (isset($_GET["lang"]))
			$_SESSION["sess_lang"] = $_GET["lang"];

		if (isset($_SESSION["sess_lang"]))
			$_SESSION["sess_lang"] = preg_replace("/[^a-zA-Z\-]/", '', $_SESSION["sess_lang"]);

		define("GEKKO_LANG", (isset($_SESSION["sess_lang"]) && (file_exists("install/lang/{$_SESSION["sess_lang"]}/main.php"))) ? $_SESSION["sess_lang"] : GEKKO_DEFAULT_LANG);
	}
	
	_L("C_SITE.LANG", GEKKO_LANG);
	
	if (!isset($L["C_SITE.ICONTHEME"]))
		_L("C_SITE.ICONTHEME", "default");

	$icontheme_path = GEKKO_SOURCE_DIR."media/icons/{$L["C_SITE.ICONTHEME"]}";
	
	$icontheme_credits = null;
	if (($fh = @fopen("$icontheme_path/CREDITS", "r")) !== false) {
		$website_credits[] = fread($fh, filesize("$icontheme_path/CREDITS"));
		fclose($fh);
	}

	if (!isset($L["C_SITE.TEMPLATE"]))
		$L["C_SITE.TEMPLATE"] = "default";
	
	_L("V_ENABLE_DEBUG", true);
	_L("V_WEBSITE_CREDITS", implode(" | ", $website_credits));

	_L("C_TEMPLATE_DIR", "{$L["C_SITE.URL"]}templates/{$L["C_SITE.TEMPLATE"]}/");
	_L("C_ICON_DIR", "{$L["C_SITE.URL"]}media/icons/{$L["C_SITE.ICONTHEME"]}/");

	_L("C_GEKKO_VERSION", GEKKO_VERSION);

	_L("C_IP_ADDR", IP_ADDR);

	register_shutdown_function("appShutdown");
?>
