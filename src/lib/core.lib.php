<?php

	/*
	*	Gekko - Open Source Web Development Framework
	*	------------------------------------------------------------------------
	*	Copyright (C) 2004-2006, J. Carlos Nieto <xiam@users.sourceforge.net>
	*	This program is Free Software.
	*
	*	@package	Gekko
	*	@subpackage	/lib
	*	@license	http://www.gnu.org/copyleft/gpl.html GNU/GPL License 2.0
	*	@author		J. Carlos Nieto <xiam@users.sourceforge.net>
	*	@link		http://www.gekkoware.org
	*/

	if (!defined("IN-GEKKO")) die("Get a life!");

	// magic quotes are evil
	set_magic_quotes_runtime(0);

	require_once(GEKKO_USER_DIR."dbconf.php");
	require_once(GEKKO_USER_DIR."cgdb.php");

	require_once(GEKKO_SOURCE_DIR."lib/database.lib.php");
	require_once(GEKKO_SOURCE_DIR."modules/conf/functions.php");
	require_once(GEKKO_SOURCE_DIR."lib/lang.lib.php");
	require_once(GEKKO_SOURCE_DIR."lib/cache.lib.php");
	require_once(GEKKO_SOURCE_DIR."lib/accesscontrol.lib.php");

	// dont' mess with this yet, unless you're planning to make it work ;)
	define('GEKKO_URL_VAR_SEPARATOR', '&');
	define('GEKKO_URL_ARG_SEPARATOR', '?');
	define('GEKKO_URL_PATH_SEPARATOR', '/');

	/**
	* ------------------------------------------------
	* Gekko Module Handling Class
	* ------------------------------------------------
	**/
	
	Class gekkoModule {
		var $buff;
		var $main;
		function srcPath($module) {
			return GEKKO_SOURCE_DIR."modules/$module/";
		}
		function exists($module) {
			if ($module) {
				$module = preg_replace("/[^a-z0-9\.\-\_]/i", '', $module);
				return ereg("\.\.", $module) ? false : file_exists(gekkoModule::srcPath($module));
			}
			return false;
		}
		function isInstalled($module) {
			$db =& $GLOBALS["db"];
			$q = $db->query("
				SELECT
					id
				FROM
					".dbTable("module")."
				WHERE
					module = '{$module}'",
				__FILE__, __LINE__, true
			);
			return $db->numRows($q);
		}
		function getVersion($module) {
			return conf::getKey($module, "_version");
		}
		function auth($module, $type = "exec") {
			$db =& $GLOBALS["db"];
			$q = $db->query("SELECT auth_{$type} FROM ".dbTable("module")."
			WHERE module = '{$module}'", __FILE__, __LINE__, true);
			if ($db->numRows($q)) {
				$row = $db->fetchRow($q);
				return appAuthorize($row["auth_{$type}"]);
			}
			return false;
		}
		function getCoreClass($module) {
			return "gekkoModule_".preg_replace("/[^a-z0-9]/i", '_', $module);
		}
		/*
			Types:
				U = Uninstall
				R = Reinstall
				I = Install
				P = Upgrade
		*/
		function install($module, $type = "I") {

			$db =& $GLOBALS["db"];

			// required libraries
			appLoadLibrary ("menu-editor", "packages");

			require_once GEKKO_SOURCE_DIR."modules/{$module}/model.php";

			// initializing module's core class (was included just above this line)
			$modclass = gekkoModule::getCoreClass($module);
			$modclass = new $modclass;

			// parsing module information
			$modxml = packages::parseModuleXML($module);

			if ($type == 'P') {
				// U[P]GRADE
				if (($inform = Packages::checkInstall($modxml)) == false) {
					// getting installed version
					$installed_version = conf::getKey($module, "_version");

					// upgrading
					$modclass->upgrade($installed_version);

					// new configuration variables, keeping user settings
					$modclass->configure();

					// updating version
					conf::setKey($module, "_version", $modxml["release"]["version"], "s");
				}
			}

			if ($type == 'U' || $type == 'R') {
				// [U]NINSTALL, [R]EINSTALL

				// dropping from modules table
				$db->query("DELETE FROM ".dbTable("module")." WHERE module = '{$module}'",
				__FILE__, __LINE__, true);

				// dropping configuration variables
				conf::delKey($module, "*");

				// invoking an uninstall routine for module's core class
				$modclass->uninstall();

				Menu_Editor::deleteLink("index.php/module=$module");

				// involing externalModuleUninstall routine for each module required by this one
				if (isset($modxml["requires"]["module"]))
					foreach ($modxml["requires"]["module"] as $required) {
						require_once GEKKO_SOURCE_DIR."modules/$required/model.php";

						$reqclass = gekkoModule::getCoreClass($required);
						$reqclass = new $reqclass;

						$reqclass->externalModuleUninstall($module);

						unset($reqclass);
					}
			}

			if ($type == 'I' || $type == 'R') {
				// [I]NSTALL, [R]EINSTALL
				if (($inform = Packages::checkInstall($modxml)) == false) {

					// the module must register by itself and create its
					// own tables using the install routine invoked below.
					$modclass->install();
					
					if (!defined("IN-INSTALL") || defined("IN-UPGRADE")) {
						if (!$modclass->createMenu())
							Menu_Editor::insertLink(1, "{L_MODULE_".strtoupper($module)."}", "index.php/module=$module", "$module.png", "*");
					}

					// invoking self configuration
					$modclass->configure();

					// setting module version entry
					conf::setKey($module, "_version", $modxml["release"]["version"], "s");

				} else {
					// there is a missing dependency
					header("content-type: text/plain");
					echo $inform;
					appAbort();
				}

			}

			unset ($modclass);

			return false;
		}
		function save($data) {
			$this->buff .= $data;
		}
		function data() {
			return $this->buff;
		}

		/**
		* getPackages()
		* @returns: The list of all available packages
		*/
		function getPackages() {

			$cache = "gekkoModule::getPackages";
			$packages = cacheVar($cache);

			if (!$packages) {
				$dh = opendir(GEKKO_MODULE_DIR);
				
				while (($module = readdir($dh)) !== false) {
					if ($module[0] != '.' && is_dir(GEKKO_SOURCE_DIR."modules/$module")) {
						$packages[] = $module;
					}
				}

				closedir($dh);
				
				cacheVar($cache, $packages);
			}

			return $packages;
		}
		
		/**
		* getList()
		* @returns: The list of all _installed_ modules
		*/
		function getList() {

			$cache = "gekkoModule::getList";
			$modules = cacheVar($cache);

			if (!$modules) {

				$packages = gekkoModule::getPackages();

				if (!defined("IN-INSTALL")) {
					$db =& $GLOBALS["db"];

					$enabled = array();
					$q = $db->query("SELECT module FROM ".dbTable("module")." WHERE status = '1'", __FILE__, __LINE__, true);
					while ($row = $db->fetchRow($q))
						$enabled[$row["module"]] = true;
					
					foreach ($packages as $module) {
						if (isset($enabled[$module]))
							$modules[] = $module;
					}
				} else {
					$modules = $packages;
				}

				cacheVar($cache, $modules);

			}

			return $modules;
		}

		function register($id, $module, $access, $admin) {
			/*
				Assigned IDs for new modules are reserved from 1 up to 64
				for Gekko official modules. Custom modules for your
				own application must be registered with an ID major than
				64
				* read HACKING file
			*/
			$db =& $GLOBALS["db"];

			$module = strtolower($module);

			// checking if module or id is already registered
			$q = $db->query("SELECT id FROM ".dbTable("module")."
			WHERE id = '{$id}' OR module = '{$module}'",
			__FILE__, __LINE__, true);

			if ($db->numRows($q)) {
				// refusing to duplicate a module name or id
				trigger_error("Couldn't register module '$module' with id '$id': Duplicated ID or NAME. Please read HACKING file.", E_USER_ERROR);
			}

			// registering module
			$q = $db->query("
			INSERT INTO ".dbTable("module")."
				(id, module, auth_exec, auth_manage, modified)
			VALUES
				('$id', '".strtolower($module)."', '$access', '$admin', '".time()."')
			", __FILE__, __LINE__, true);

			// creating a group for this module (if doesn't exists)
			$q = $db->query(
				"SELECT id FROM ".dbTable("group")." WHERE id = '$id'",
				__FILE__, __LINE__, true
			);
			if (!$db->numRows($q)) {
				$db->query("INSERT INTO ".dbTable("group")." (id, groupname, description)
				VALUES ('$id', '$module', '".ucfirst($module)."')");
			}
		}
	}


	/**
	* ------------------------------------------------
	* Gekko application functions
	* ------------------------------------------------
	**/

	function appEventURL($module) {
		return _L("C_SITE.URL")."modules/{$module}/actions.php";
	}

	/**
	* appLoadLibrary(libname1, [libname2], [...]);
	* @returns: error when a library couldn't be found
	*
	*/
	function appLoadLibrary() {
		$args = func_get_args();
		foreach ($args as $lib) {
			unset($f);
			if (file_exists($f = GEKKO_SOURCE_DIR."lib/$lib")) {
				require_once $f;
			} elseif (file_exists($f = GEKKO_SOURCE_DIR."modules/$lib/functions.php")) {
				require_once $f;
			} else {
				trigger_error("Could not load library '$lib'", E_USER_ERROR);
			}
		}
	}

	/*
		appAuthorize(String auth, String levels);

		Returns true when $level is contained within $auth string.
		When $level is not set, this function takes users groups
		as $level and checks if one of the $levels (separated by
		commas) matchs with one of the auth levels (separated by
		commas too).

		For example:
			appAuthorize("1,2,3", "4,5");
		Returns false, since both 4 and 5 are out of $auth
		variable
			appAuthorize("4,2", "2");
		Returns true, "2" is contained into "4,2"

		Remember that both $auth and $levels must be strings
		containing comma-separated numeric values. But you can
		pass (at least for $auth) a module name, this will be
		automatically replaced by its corresponding id.
	*/

	function appAuthorize($auth, $level = "") {

		// using user's groups when no level is specified
		if (!$level)
			$level = $GLOBALS["USER"]["groups"];

		// both $auth & $level are separated by commas
		$auth = explode(",", $auth);
		$level = explode(",", $level);

		// when $auth argument is not an integer it should
		// be a module name
		while (list($key) = each($auth)) {
			if ((strlen($auth[$key]) > 1) && (!is_integer($auth[$key]))) {
				$auth[$key] = isset($GLOBALS["cgdb"][$auth[$key]]) ? $GLOBALS["cgdb"][$auth[$key]] : $auth[$key];
			}
		}

		// checking level codes
		/*
			Level ranges
			code -  returns true for
			  1  -  super user (el mero mero)
			  *  -  any level
			  @  -  reserved gekko core modules (1 - 200)
			  #  -  reserved module levels (1 - 400)
			  $  -  user level (1 - 500)
			  ?  -  unprivileged level (501 or no groups)
		*/

		if (in_array("*", $auth)) return true;

		// special fix for visitors
		if (isset($auth[0]) && $auth[0] == '?')
			return !(defined("AUTH") && AUTH);

		foreach ($level as $check) {
			// note that I'm using (x > 0)
			if (($check = intval(trim($check))) > 0) {
				// access granted using level codes
				if ($check <= 200 && in_array("@", $auth)) return true;
				if ($check <= 400 && in_array("#", $auth)) return true;
				if ($check <= 500 && in_array("$", $auth)) return true;
				if ($check == 501 && in_array("?", $auth)) return true;
				// superadmin has total access to any level because we love him/her :)
				if ($check === $GLOBALS["cgdb"]["admin"]) return true;
				// access granted using specific levels
				if (in_array($check, $auth)) return true;
			} else {
				// no groups means no access
				if (in_array("?", $auth)) return true;
			}
		}
		return false;
	}
	
	// authentication hash
	function createAuthCookie($hash, $key) {
		return $key.":".md5($hash.IP_ADDR);
	}
	// random string
	function createRandomKey($len, $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789") {
		$charlen = strlen($charset);
		$return = '';
		for ($i = 0; $i < $len; $i++)
			$return .= $charset[rand(0, $charlen-1)];
		return $return;
	}

	/**
	* ------------------------------------------------
	* Gekko URL Handling functions
	* ------------------------------------------------
	**/
	
	// converts text strings into a url-friendly form
	function urlTitleEncode($title, $full_encoding = false) {
		if ($full_encoding || GEKKO_FULL_URL_ENCODING) {
			$title = rawurlencode(str_replace("/", "-", $title));
			$title = str_replace("%20", "+", $title);
		} else {
			$title = htmlentities(utf8_decode($title));
			$title = preg_replace('/&amp;[^;]*;/', ' ', $title);
			$title = preg_replace('/&(.)[^;]*;/', '\\1', $title);
			$title = trim(preg_replace('/[^0-9a-zA-Z]/', ' ', $title));
			$title = str_replace(' ', '-', $title);
			// avoiding multiple occurences of - in the same line
			$title = preg_replace('/-+/', '-', $title);
		}
		return $title;
	}

	/**
	* urlEvalPrototype(URL Prototype);
	* @returns: a ready-to-use version of the provided prototype
	*
	*/
	function urlEvalPrototype($href, $relative = true, $html = true) {

		// if the url begins with proto:// leave as is.
		if (strpos($href, "://") == false) {

			if (GEKKO_MOD_REWRITE) {
				if (preg_match("/^index.php.+/", $href))
					$href = preg_replace("/^index\.php/", '', $href);
			}

			// url formatting
			$parts = urlSplitVars($href);

			// new href prototype
			$href = Array("path" => Array(), "vars" => Array());

			if (GEKKO_LANGUAGE_ID) {
				if (!in_array("lang", $href["vars"]))
					$href["vars"][] = "lang=".GEKKO_LANGUAGE_ID;
			}

			// checking parts
			foreach ($parts['path'] as $part) {
				if (($d = strpos($part, '=')) !== false) {
					if (GEKKO_USE_PRETTY_URLS) {
						// if allowed to use pretty URLs
						$href['path'][] = substr($part, $d+1);
					} else {
						// when we are not allowed
						$href['vars'][] = $part;
					}
				} else {
					// leaving part as is (it is not a variable)
					$href['path'][] = $part;
				}
			}

			// merging 'vars'
			$href['vars'] = array_merge($href['vars'], $parts['vars']);

			// joining 'path' and 'vars'
			$href['path'] = implode(GEKKO_URL_PATH_SEPARATOR, $href['path']);
			$href['vars'] = implode(GEKKO_URL_VAR_SEPARATOR, $href['vars']);

			// joining again into a single href with a relative path if needed
			$href = implode($href['vars'] ? GEKKO_URL_ARG_SEPARATOR : '', $href);
			if ($html) $href = HTMLSpecialChars($href);

			if ((defined("GEKKO_USE_ABSOLUTE_URIS") && GEKKO_USE_ABSOLUTE_URIS) || (!$relative)) {
				return _L("C_SITE.URL").$href;
			} else {
				$href = (substr($href, 0, 1) == '/') ? dirname($_SERVER["SCRIPT_NAME"]).$href : _L('C_SITE.URL').$href;
			}
		}
		return $href;
	}

	/**
	* urlCheckVarType(Type, &Variable);
	* @returns: modified variable according it's type
	*
	*/
	function urlCheckVarType($type, &$value) {
		switch ($type) {
			case "int":
				$value = 0+intval(preg_replace("/[^\d\-]/", "", $value));
			break;
			case "alpha":
				$value = preg_replace("/[^\w\-\_]/", "", $value);
			break;
			case "date":
				$value = preg_replace("/[^0-9.\-]/", "", $value);
				$value = preg_replace("/[^0-9]/", "-", $value);
			break;
			case "page":
				if ($value != "last") {
					$value = preg_replace("/[^0-9]/", "", $value);
				}
				if (!$value)
					$value = 1;
			break;
			case "string":
				$value = preg_replace("/[^\w\s\.\-\_\/,]/", "", $value);
			break;
			case "data":
				// as is
			break;
			default:
				if (preg_match("/\[(.*?)\]/", $type, $match)) {
					$value = preg_replace("/[^$match[1]]/", "", $value);
				} else {
					trigger_error("Missing datatype for variable '$value'", E_USER_WARNING);
				}
			break;
		}
		return $value;
	}

	/**
	* urlSplitVars(URL Prototype);
	* @returns: an array containing both 'path' and 'vars' (normal variables)
	*
	*/
	function urlSplitVars($url) {

		$parts = Array('path' => array(), 'vars' => array());

		// splitting by path/info?var=val
		$buff = explode(GEKKO_URL_ARG_SEPARATOR, $url);

		// simple variables
		if (isset($buff[1]))
			$parts['vars'] = explode(GEKKO_URL_VAR_SEPARATOR, $buff[1]);

		// path variables
		if ($buff[0])
			$parts['path'] = explode(GEKKO_URL_PATH_SEPARATOR, trim($buff[0], GEKKO_URL_PATH_SEPARATOR));

		return $parts;
	}

	/**
	* urlImportMap(URL Prototype);
	* @returns: an array of checked URL variables according it's prototype
	* @example: URLGetVars('/module=alpha/title=string?var1=int');
	*
	*/
	function urlImportMap($proto) {
		$return = Array();
		$varList = Array();

		$parts = urlSplitVars($proto);

		// initializing variables to null
		foreach ($parts as $part) {
			if (is_array($part))
				foreach ($part as $var) {
					if (preg_match("/([^=]*)=.*/", $var, $buff)) {
						$GLOBALS[$buff[1]] = '';
						$return[$buff[1]] = null;
					}
				}
		}

		// PATH_INFO
		if (GEKKO_USE_PRETTY_URLS && isset($_SERVER['PATH_INFO'])) {
			$buff = explode('/', trim($_SERVER['PATH_INFO'], '/'));
			foreach ($buff as $i => $value) {
				if (isset($parts['path'][$i])) {
					// variable type & name (default type is string)
					$tmp = explode('=', trim($parts['path'][$i]));
					list($name, $type) = (count($tmp) > 1) ? $tmp : array($tmp[0], 'string');

					// PATH_INFO variables are overwritten by those setted in _GET
					if (isset($_GET[$name]))
						$value = $_GET[$name];
					// checking variable type
					if (($tmp = urlCheckVarType($type, $value)) != false)
						$return[$name] = $tmp;
				}
			}
		} elseif ($parts['path']) {
			// no path info
			$parts['vars'] = array_merge($parts['vars'], $parts['path']);
		}

		// _GET
		if ($parts['vars']) {
			foreach ($parts['vars'] as $part) {
				$tmp = explode('=', trim($part));
				list($name, $type) = (count($tmp) > 1) ? $tmp : array($tmp[0], 'string');
				if (isset($_GET[$name])) {
					$value = $_GET[$name];
					if (urlCheckVarType($type, $value))
						$return[$name] = $value;
				} else {
					$return[$name] = "";
				}
			}
		}
		return $return;
	}


	/**
	* urlSetArgs(URL Prototype, Base URL);
	* @returns: modified url arguments (and path info)
	* @example: urlSetArgs('v1=anakin&v2=skywalker', '/path/info?v1=foo&v2=var');
	*
	*/
	function urlSetArgs($proto, $base_url = null) {

		// getting current url arguments
		if ($base_url === null)
			$base_url = substr($_SERVER["REQUEST_URI"], strlen($_SERVER["SCRIPT_NAME"]));

		$varList = Array();

		// splitting variable list
		if (($path = strpos($proto, '/')) !== false) {
			// when url contains path_info
			$proto = explode('/', trim($proto, '/'));
		} else {
			$proto = explode('&', $proto);
		}

		// variable names
		foreach ($proto as $part) {
			$part = explode("=", $part);
			if (count($part) == 1)
				$part[1] = '';
			$varList[] = Array('name'=>$part[0],'value'=>$part[1]);
		}

		if (GEKKO_USE_PRETTY_URLS && $path) {

			// setting variable range
			if ($base_url && preg_match("/(.*)\?(.*)/", $base_url, $buff))
				list($tmp, $base_url, $base_args) = $buff;

			$return = ($base_url) ? explode('/', trim('/', $base_url)) : array();

			// overwriting or creating variables (checking range)
			for ($i = 0; $i < count($varList); $i++)
				$return[$i] = $varList[$i]['value'];

			return '/'.implode("/", $return).(isset($base_args) ? "?$base_args" : '');
		} else {
			foreach ($varList as $var) {
				// is this variable set in base_url?
				if (preg_match("/(\?|&){$var['name']}=.*(&|$)/", $base_url)) {
					$base_url = preg_replace("/(\?|&)({$var['name']}\=.*?)(&|$)/", "\\1{$var['name']}={$var['value']}\\3", $base_url);
				} else {
					// not found, appending it
					$base_url .= (preg_match("/\?.*\=.*/", $base_url) ? "&" : "?")."{$var['name']}={$var['value']}";
				}
			}
			return $base_url;
		}
	}

	/**
	* createLink(URL Prototype, Text, [icon], [require confirmation?], [link style]);
	* @returns: A Formatted HTML <a> tag
	*
	*/
	function createLink($href, $text = false, $icon = false, $confirm = false, $style = null) {

		if (!$text)
			$text = htmlspecialchars($href);

		// evaluating prototype
		if (!preg_match("/^[a-z]*:.*/", $href))
			$href = urlEvalPrototype($href);

		// if you need a confirmation before following this link
		if ($confirm)
			$href = "javascript:confirmAction('{$href}', '{$text}')";

		// creating html according to style

		if (substr($href, 0, 11) == "javascript:")
			$attr = "href=\"javascript:void(0)\" onclick=\"".substr($href, 11)."\"";
		else
			$attr = "href=\"$href\"";

		switch ($style) {
			case "icon":
				return '<a '.$attr.' title="'.$text.'">'.createIcon($icon, 16).'</a>';
			break;
			default:
				return '<a '.$attr.'>'.($icon ? createIcon($icon, 16)." " : "").$text.'</a>';
			break;
		}
	}
	

	// set cookie
	function setUserConf($name, $value = null, $life = null) {
		$path = dirname($_SERVER["SCRIPT_NAME"]);

		if (preg_match("/(.*?)\/modules\/.*/", $_SERVER["SCRIPT_NAME"], $match)) {
			if (isset($match[1]))
				$path = rtrim($match[1], '/').'/';
		}

		setCookie (
			GEKKO_COOKIE_PREFIX.$name,
			$value,
			time()+GEKKO_COOKIE_LIFE,
			$path ? $path : '/'
		);
	}

	// read cookie
	function getUserConf($name, $type = null) {
	
		if (isset($_GET[$name])) {
			setUserConf($name, $_GET[$name]);		
			$value = $_GET[$name];
		} else if (isset($_COOKIE[GEKKO_COOKIE_PREFIX.$name]))
			$value = $_COOKIE[GEKKO_COOKIE_PREFIX.$name];
		else
			$value = null;

		if ($type)
			urlCheckVarType($type, $value);

		return $value;		
	}

	function appSetMessage($type, $message) {
		if (!isset($_SESSION[$type]))
			$_SESSION[$type] = '';
		else
			$_SESSION[$type] .= '<br />';
		$_SESSION[$type] .= "&raquo; {$message}";
	}

	/*
	*	appWriteLog(System message, file to save, importance level);
	*	Logs
	*/
	function appWriteLog($string, $file = "messages", $level = 1) {

		$logdir = GEKKO_TEMP_DIR."logs/";

		if (!file_exists($logdir)) {
			mkdir($logdir, 0775);
			chmod($logdir, 0775);
		}

		if (($h = @fopen($logdir.$file.".log", "a")) !== false) {
			if (!defined("SLOG_LOGGED_".strtoupper($file))) {
				$log = "\n[".date("d/m/Y H:i:s")."] ".((isset($GLOBALS["USER"]) ? "(uid={$GLOBALS["USER"]["id"]}) {$GLOBALS["USER"]["username"]}@": ""))."".getIP()." [{$_SERVER["HTTP_USER_AGENT"]}]\n{$_SERVER["REQUEST_URI"]}";
				$log = $string ? "$log\n-- $string\n" : "$log\n";
				define("SLOG_LOGGED_".strtoupper($file), true);
			}
			fwrite($h, isset($log) ? $log : "-- $string\n");
			fclose($h);
		}
	}

	/*
	*	appAbort();
	*	Aborts Gekko execution, closes SQL connections.
	*/
	function appAbort($msg = null, $output_buffer = true) {

		appShutdown(false);

		$msg = Lang::Parse($msg);

		appWriteLog("core: Script aborted ".($msg ? $msg : "No error message")."");

		if ($msg)
			echo $msg;


		if ($output_buffer && function_exists("gzoutput"))
			gzoutput();

		exit();
	}

	/**
		Reads and entire file
	*/
	function loadFile($fname) {
		if (($h = @fopen($fname, "r")) !== false) {
			$c = fread($h, filesize($fname));
			fclose($h);
			return $c;
		} else {
			trigger_error("Couldn't read \"$fname\".", E_USER_ERROR);
		}
	}

	function listDirectory($dir, $recursive = false, $only_files = false) {

		if (substr($dir, -1) != "/") $dir .= "/";

		$dp = @opendir($dir);
		$dc = Array();
		while (($file = @readdir($dp)) !== false) {
			if ($file != "." && $file != "..") {
				if ($recursive && is_dir($dir.$file)) {
					$tmp = listDirectory($dir.$file, true);
					$dfs = Array();
					foreach ($tmp as $f) {
						$dfs[] = $file."/".$f;
					}
					$dc = array_merge($dc, $dfs);
					if ($only_files) continue;

				}
				$dc[] = $file;
			}
		}
		@closedir($dp);
		return $dc;
	}

	function appRedirect($where = null) {

		$where = trim(preg_replace("/[\r|\n](.*?)/", "", $where));

		if (substr($where, 0, 1) != '/') {

			if ($where)
				$where = urlEvalPrototype($where, true, false);

			if (preg_match("/^[a-z0-9A-Z]:\/\/.*$/", $where)) {
				$location = $where;
			} else {
				$location = $where ? $where : $_SERVER['HTTP_REFERER'];
			}

		} else {
			$location = $where;
		}

		if ((isset($_GET["ajax_flag"]) && $_GET["ajax_flag"] == 1) || (isset($_POST["ajax_flag"]) && $_POST["ajax_flag"] == 1))
			echo "Location: ".html_entity_decode($location);
		else
			header("Location: $location");
	}

	function sanitize(&$var, $force = false) {
		if (is_array($var)) {
			while (list($key) = each($var))
				sanitize($var[$key], $force);
		} else {
			if ($force)
				$var = stripslashes($var);
			if ($force || !get_magic_quotes_gpc())
				$var = addslashes($var);
		}
	}

	function checkAuthHash($auth) {
		return ($auth == _L("C_AUTH_STR"));
	}

	function accessDenied($print = false, $file = false, $line = false) {
		if (GEKKO_ENABLE_DEBUG && $file && $line) {
			echo "$file:$line\n";
		}
		if ($print) {
			echo _L("E_ACCESS_DENIED");
		} else {
			if (function_exists("createMessageBox"))
				return createMessageBox("error", _L("E_ACCESS_DENIED"));
			return _L("E_ACCESS_DENIED");
		}
	}

	function gdebug($text, $exit = 1) {
		header("content-type: text/plain");
		print_r($text);
		if ($exit) die;
	}

	### DATE HANDLING FUNCTIONS
	
	function fromUnixtime($unixtime = null) {
		return date("Y-m-d H:i:s", $unixtime ? $unixtime : time());
	}
	function fromDate($sqldate) {

		$sqldate = preg_replace("/[^0-9]/", "", $sqldate);

		$y = substr($sqldate, 0, 4);
		$m = substr($sqldate, 4, 2);
		$d = substr($sqldate, 6, 2);
		$h = substr($sqldate, 8, 2);
		$i = substr($sqldate, 10, 2);
		$s = substr($sqldate, 12, 2);

		return mktime((int)$h, (int)$i, (int)$s, (int)$m, (int)$d, (int)$y);
	}

	function getFileExtension($file) {
		return strtolower(substr($file = basename($file), (strpos(strrev($file), ".")*-1)));
	}

	function getLocalReferer() {
		if (isset($_SERVER["HTTP_REFERER"])) {
			preg_match("/http:\/\/.*?\/(.*)/", $_SERVER["HTTP_REFERER"], $m);
			return "/".$m[1];
		} else {
			return "";
		}
	}
	
	/**
		timeMark(yyyy.mm.dd)
		@returns: unix timestamp range
	*/
	function timeMark($date) {
		if ($date) {
			$date = explode("-", $date);
			if (isset($date[2])) {
				// year-month-day
				$lo = mktime(0, 0, 0, (int)$date[1], (int)$date[2], (int)$date[0]);
				$hi = mktime(23, 59, 59, (int)$date[1], (int)$date[2], (int)$date[0]);
			} else if (isset($date[1])) {
				// year-month
				$lo = mktime(0, 0, 0, (int)$date[1], 1, (int)$date[0]);
				$hi = mktime(23, 59, 59, (int)$date[1], date("t", $lo), (int)$date[0]);
			} else if (isset($date[0])) {
				// year
				$lo = mktime(0, 0, 0, 1, 1, (int)$date[0]);
				$hi = mktime(23, 59, 59, 12, 31, (int)$date[0]);
			} else {
				return false;
			}
			return array($lo, $hi);
		}
		return false;
	}
	function appEnableSSL() {
		if ((!defined("GEKKO_NO_SSL") || GEKKO_NO_SSL != true) && !isset($_GET["no_ssl"]) && !isset($_SERVER["HTTPS"])) {
			// trying to determine if this server supports ssl
			if (function_exists("apache_get_modules")) {
				$ap_mods = apache_get_modules();
				// if it does, redirecting user to SSL mode.
				if (in_array("mod_ssl", $ap_mods)) {
					if (!defined("IN-INSTALL"))
						$GLOBALS["AccessTable"]->dropEntry("core-exec-preban");
					appRedirect("https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
					appAbort(null, false);
				}
			}
		}
	}
	function appDisableSSL(&$return) {
		// using ssl only for specifical tasks
		if (isset($_SERVER["HTTPS"])) {
			if ($return) {
				$return = "http://".$_SERVER["HTTP_HOST"].dirname($_SERVER["SCRIPT_URL"])."/".$return;
			} else {
				$return = "http".substr($_SERVER["HTTP_REFERER"], 5);
			}
		}
	}

	/* makes a filename looking cute */
	function prettyFileName($file) {
		$title = basename($file);
		$title = substr($title, 0, strlen($title) - strpos(strrev($title), ".") - 1);
		$title = ucwords(preg_replace("(_|-)", " ", $title));
		return $title;
	}

	function isUser() {
		return appAuthorize("$");
	}
	/*
		WARNING: This is a RELATIVE isAdmin() function, will return true
		when user owns administrator privileges for the main requested module.
		Use with care.
	*/
	function isAdmin($absolute = false) {
	
		$auth = "admin";
		if (!$absolute) {
			if (defined('GEKKO_ACTION_MODULE')) {
				$auth = GEKKO_ACTION_MODULE;
			} else if (defined('GEKKO_REQUEST_MODULE')) {
				$auth = GEKKO_REQUEST_MODULE;
			}
		}
		
		return appAuthorize($auth);
	}
	function appShutdown($exit = true) {

		$db =& $GLOBALS["db"];

		if (isset($GLOBALS["db"]))
			$GLOBALS["db"]->close();
		
		appSessionSave();

		if ($exit)
			exit();
	}

	function appSessionStart() {
		require_once GEKKO_SOURCE_DIR."lib/blowfish.lib.php";

		session_start();

		// getting the sess key
		if (isset($_COOKIE["sess"]) && isset($_SESSION["data"])) {
			// never trust the client side...
			$_COOKIE["sess"] = substr(preg_replace("/[^a-zA-Z0-9]/", '', $_COOKIE["sess"]),0, 48);

			$bf = new Crypt_Blowfish(substr($_COOKIE["sess"], 0, 48));
			$data = unserialize(function_exists('gzuncompress') ? gzuncompress($bf->decrypt($_SESSION["data"])) : $bf->decrypt($_SESSION["data"]));
			$_SESSION = $data ? $data : array();

		} else {
			$_SESSION = array();
			$_SESSION["no_data"] = "xx";
		}
	}

	function appSessionSave() {
		if (!defined("GEKKO_SESSION_SAVED")) {
			require_once GEKKO_SOURCE_DIR."lib/blowfish.lib.php";

			if (!isset($_COOKIE["sess"])) {
				// creating a new random key (length = 48)
				$_COOKIE["sess"] = createRandomKey(48);
				setcookie("sess", $_COOKIE["sess"], time()+84600, '/');
			}

			// never trust the client side...
			$_COOKIE["sess"] = substr(preg_replace("/[^a-zA-Z0-9]/", '', $_COOKIE["sess"]), 0, 48);

			$bf = new Crypt_Blowfish($_COOKIE["sess"]);

			$data = function_exists('gzcompress') ? gzcompress(serialize($_SESSION)) : serialize($_SESSION);
			$_SESSION = array();
			$_SESSION["data"] = $bf->encrypt($data);

			session_write_close();

			define("GEKKO_SESSION_SAVED", true);
		}
	}

	function absolutePath($filename) {
		if (isset($_SERVER["WINDIR"])) {
			return preg_match("/^[A-Z]:\\.*?/i", $filename);
		} else {
			return ($filename[0] == '/');
		}
	}

	function appErrorHandler($num, $message, $file, $line, $vars) {
		ob_start();
		switch ($num) {
			case E_ERROR: case E_PARSE:
				echo "Fatal <b>error</b>: {$message}. This doesn't seem to be CMS' fault.<br />\n";
				die;
			break;
			case E_USER_ERROR:
				echo "<div style=\"opacity: 0.8; color: #000; font-size: x-small; background: #fff; padding: 10px\">\n";
				echo "<b>Oops! an error has ocurred</b>: <span style=\"color: #f00\">{$message}</span><br />\n";
				
				if (GEKKO_ENABLE_DEBUG)
					echo "<b>File:</b> <span style=\"color: #00f\">$file</span>:<span style=\"color: #00f\"><b>$line</b></span><br />";

				echo "If you think this is a <b>unusual behaviour</b>, please <a href=\"mailto:gekkoware-bugs@lists.sourceforge.net\">contact</a> the <a href=\"http://www.gekkoware.org\">Gekko</a> developers and tell them about this error. You may attach the <i>temp/logs/error.log</i> file.";
				echo "</div>\n";
				
				appWriteLog("\"$message\" $file:$line", "error");
			break;
		}
		$err = ob_get_contents();
		ob_end_clean();

		if ($err)
			appSetMessage("error", $err);
	}

	//set_error_handler("appErrorHandler");

	sanitize($_GET);
	sanitize($_POST);
	sanitize($_COOKIE);
	sanitize($_SERVER, true);
?>
