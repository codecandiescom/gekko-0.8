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

	define("IN-GEKKO", true);
	define("IN-INSTALL", true);
	define("DB_DEFAULT_DRIVER", "mysql.driver");
	define("SITE_DEFAULT_LANGUAGE", "es");
	define("SITE_DEFAULT_LANG_CODE", "es");
	define("SITE_DEFAULT_COUNTRY_CODE", "MX");
	define("GEKKO_REQUEST_MODULE", "");
	define("GEKKO_LANGUAGE_ID", "");

	require_once "conf.php";

	$Lang = new Lang(GEKKO_LANG);
	$Lang->loadCoreFiles();

	require_once "install/lang/".GEKKO_LANG."/main.php";
	require_once "lib/gzenc.lib.php";
	require_once "lib/template.lib.php";
	require_once "lib/actions.lib.php";
	require_once "lib/network.lib.php";
	require_once "lib/file.lib.php";
	require_once "lib/plugins.lib.php";

	require_once GEKKO_SOURCE_DIR."modules/packages/functions.php";
	require_once GEKKO_SOURCE_DIR."modules/menu-editor/functions.php";

	appEnableSSL();

	function installerCacheClean() {
		// cleaning cache
		$cachedir = GEKKO_SOURCE_DIR."temp/cache";
		if (file_exists($cachedir)) {
			$files = listDirectory($cachedir);
			foreach ($files as $file) {
				unlink($cachedir."/$file");
			}
		}
	}

	function installerCheckDirectories() {
		$check_dirs = Array("temp", "data");
		$writable = Array();
		foreach ($check_dirs as $dir) {
			if (!is_writable(GEKKO_USER_DIR."$dir"))
				return true;
		}
		return false;
	}

	function installerChangePermissions() {

		// writable files
		$check_dirs = Array("temp", "data");
		$writable = Array();

		foreach ($check_dirs as $dir) {
			$files = listDirectory($dir, true);
			foreach ($files as $key => $test) {
				if (is_writable(GEKKO_SOURCE_DIR."{$dir}/".$test)) {
					unset($files[$key]);
				} else {
					$files[$key] = $dir."/".$test;
				}
			}
			$writable = array_merge($writable, $files);
		}

		// fixing file permissions using FTP
		$ftp = new ftpSession (
			$_SESSION["sess_ftp"]["host"],
			$_SESSION["sess_ftp"]["port"],
			$_SESSION["sess_ftp"]["user"],
			$_SESSION["sess_ftp"]["pass"]
		);

		if ($ftp->connect()) {
			if ($ftp->login()) {
				// checking if this is Gekko's root
				if ($ftp->cd($_SESSION["sess_ftp"]["path"]) && $ftp->mtime("conf.php")) {
					$ftp->chmod(GEKKO_USER_DIR."dbconf.php", "0777");
					$ftp->chmod(GEKKO_USER_DIR."temp", "0777");
					$ftp->chmod(GEKKO_USER_DIR."data", "0777");

					foreach ($writable as $file)
						$ftp->chmod($file, "0777");
				}
			} else {
				trigger_error("FTP: Authentication failed.", E_USER_ERROR);
			}
		} else {
			trigger_error("FTP: Connection refused by server.", E_USER_ERROR);
		}
		return true;
	}

	function dbCreateConf() {
	
		$dbinfo =& $GLOBALS["dbinfo"];
		
		$dbinfo = Array (
			"host" => $_SESSION["sess_db"]["host"],
			"name" => $_SESSION["sess_db"]["database"],
			"user" => $_SESSION["sess_db"]["user"],
			"pass" => $_SESSION["sess_db"]["pass"],
			"pref" => $_SESSION["sess_db"]["prefix"],
			"type" => substr($_SESSION["sess_db"]["driver"], 0, strpos($_SESSION["sess_db"]["driver"], "."))
		);

		return "<?php\n\t\$dbinfo = Array (\n\t\t'host' => '{$dbinfo["host"]}',\n\t\t'name' => '{$dbinfo["name"]}',\n\t\t'user' => '{$dbinfo["user"]}',\n\t\t'pass' => '{$dbinfo["pass"]}',\n\t\t'pref' => '{$dbinfo["pref"]}',\n\t\t'type' => '{$dbinfo["type"]}'\n\t);\n?>";
	}

	varImport("step");

	// default configuration keys
	$defkeys = Array (
		"site.gekko_version"			=> GEKKO_VERSION,
		"site.title:string"				=> "Site title",
		"site.name"						=> "Site name",
		"site.slogan"					=> "Site slogan",
		"site.description"				=> "Site description",
		"site.show_text_title:boolean"	=> 1,
		"site.icontheme"				=> "default",
		"site.template"					=> "default",
		"site.stylesheet"				=> "default",
		"site.gzip_output:boolean"		=> 1,
		"site.copyright"				=> "&amp;copy; ".date("Y")." example.org",
		"site.footer"					=> "Powered by <a href=\"http://www.gekkoware.org\">Gekko</a>",
		"site.lang"						=> SITE_DEFAULT_LANG_CODE,
		"site.primary_lang"				=> SITE_DEFAULT_LANG_CODE,
		"site.primary_country"			=> SITE_DEFAULT_COUNTRY_CODE,
		"site.contact_mail"				=> "webmaster@example.com",
		"site.cookie.prefix"			=> "gekkocms_",
		"site.cookie.path"				=> dirname($_SERVER["SCRIPT_NAME"]),
		"site.cookie.life:int"			=> 60*60*24*1,
		"site.hour_difference:int"		=> 0,
		"ftp.user"						=> "",
		"ftp.pass"						=> "",
		"ftp.port"						=> "21",
		"ftp.save_pass:boolean"			=> 0,
		"ftp.host"						=> "localhost",
		"ftp.path"						=> "",
		"smtp.host"						=> "localhost",
		"smtp.port"						=> "25",
		"smtp.user"						=> "",
		"smtp.pass"						=> "",
		"smtp.enable:boolean"			=> 0,
		"html_filter:boolean"			=> 1,
		"plugins.msiepngfix.disable:boolean" => 0,
		"plugins.webshot.disable:boolean" => 1,
		"rtbeditor:boolean"				=> 1,
		"rtbeditor.default:boolean"		=> 1,
		"gbbcode:boolean"				=> 1,
		"gbbcode.smileys:boolean"		=> 1,
		"magic_blacklist:boolean"		=> 1
	);

	$nextstep = $step;

	switch ($step) {
		case "lang":
			varImport("lang");

			varRequire("lang");

			$_SESSION = Array();

			$_SESSION["sess_lang"] = $lang;

			header("Location: install.php");

			exit();
		break;
		case "install":

			varImport("ftp_pass", "db_pass");

			// checking passwords
			if ($_SESSION["sess_db"]["pass"] != $db_pass) {
				$_SESSION["error"] = _L("E_DB_PASSWORD_MISMATCH");
				break;
			}

			if (isset($_SESSION["sess_ftp"]) && $_SESSION["sess_ftp"]["pass"] != $ftp_pass) {
				$_SESSION["error"] = sprintf(_L("E_ACCESS_DENIED"), "FTP");
				break;
			}

			// database connection
			require_once GEKKO_SOURCE_DIR."lib/db/{$_SESSION["sess_db"]["driver"]}";

			$db = new Database();

			if (!($db->open("{$_SESSION["sess_db"]["host"]}:{$_SESSION["sess_db"]["port"]}", $_SESSION["sess_db"]["user"], $_SESSION["sess_db"]["pass"], $_SESSION["sess_db"]["database"]))) {
				appAbort(sprintf(_L("E_ACCESS_DENIED".": %s"), "Database", $db->getError()));
			}

			/* checking file permissions */
			if (isset($_SESSION["sess_ftp"])) {
				installerChangePermissions();
				$nextstep = "login";
			} else {
				$nextstep = "manual_config";
			}

			/* creating dbconf.php */
			writeFile(GEKKO_USER_DIR."dbconf.php", dbCreateConf());

			/* changing permissions after writing dbconf.php */
			@chmod(GEKKO_USER_DIR."dbconf.php", 0775);

			/* loading dbinfo.php */
			include GEKKO_USER_DIR."dbconf.php";

			/* database options */
			if ($_SESSION["sess_db"]["clean_database"]) {
				$db->query("DROP DATABASE IF EXISTS `{$_SESSION["sess_db"]["database"]}`",
				__FILE__, __LINE__);
				$db->query("CREATE DATABASE `{$_SESSION["sess_db"]["database"]}`",
				__FILE__, __LINE__);
				$db->query("USE `{$_SESSION["sess_db"]["database"]}`",
				__FILE__, __LINE__);
			} elseif ($_SESSION["sess_db"]["clean_prefix"]) {
				$db->query("SHOW TABLES");
				$plen = strlen($_SESSION["sess_db"]["prefix"]);
				while ($row = $db->fetchRow()) {
					foreach ($row as $table) {
						if (substr($table, 0, $plen) == $_SESSION["sess_db"]["prefix"]) {
							$db->query("DROP TABLE `$table`", __FILE__, __LINE__, true);
						}
					}
				}
			}

			/* those modules must be installed first */
			$modlist = Array ("conf", "modules", "groups", "packages");

			/* listing the remaining modules */
			$d = opendir(GEKKO_SOURCE_DIR."modules/");
			while (($m = readdir($d)) !== false) {
				if ($m != "." && $m != "..") {
					if (file_exists(GEKKO_SOURCE_DIR."modules/$m/model.php") && !in_array($m, $modlist)) {
						$modlist[] = $m;
					}
				}
			}
			closedir($d);

			$inform = "";
			/* checking dependencies */
			foreach ($modlist as $module) {
				$modxml = Packages::parseModuleXML($module);
				$inform .= Packages::checkInstall($modxml);
			}
			if ($inform) {
				header("content-type: text/plain");
				echo $inform;
				exit();
			}

			/* installing */
			foreach ($modlist as $module) {
				gekkoModule::install($module, "I");
			}

			/* executing additional installation queries */
			$GLOBALS["db"]->query("
			CREATE TABLE ".dbTable("control")." (
				ip VARCHAR (32),
				accesstype VARCHAR (128),
				counter TINYINT (5),
				time INT (11)
			)", __FILE__, __LINE__);

			// using default configuration keys
			$corekeys = Array();
			foreach ($defkeys as $key => $value) {
				$key = explode(":", $key);

				$type = isset($key[1]) ? $key[1] : "string";
				$key = $key[0];
				conf::getKey("core", $key, $value, $type);

				$corekeys[$key] = $value;
			}

			// overwriting default configuration with user's configuration
			foreach ($_SESSION["sess_conf"] as $conf) {
				conf::setKey($conf["module"], $conf["keyname"], $conf["value"]);
				if ($conf["module"] == "core") {
					unset($corekeys[$conf["keyname"]]);
				}
			}

			/* creating user */
			$db->query("INSERT INTO ".dbTable("user")." (
					id,
					username,
					realname,
					password,
					email,
					date_registered,
					status,
					groups
				) VALUES (
					'".$db->findFreeId("user")."',
					'{$_SESSION["sess_user"]["user"]}',
					'{$_SESSION["sess_user"]["realname"]}',
					'".md5(strtolower($_SESSION["sess_user"]["user"]).$_SESSION["sess_user"]["pass"])."',
					'{$_SESSION["sess_user"]["email"]}',
					CURRENT_TIMESTAMP(),
					'1',
					'".$cgdb["admin"]."'
				)
			", __FILE__, __LINE__);

			if ($_SESSION["sess_options"]["create_menu"]) {

				/* base menu (id = 1) */
				$db->query("INSERT INTO ".dbTable("menu_block")."
				(ID, Title) VALUES
				('1', '{L_MAIN}')",
				__FILE__, __LINE__);

				/* account menu (id = 2) */
				$db->query("INSERT INTO ".dbTable("menu_block")."
				(id, title) VALUES
				('2','{L_MY_ACCOUNT}')",
				__FILE__, __LINE__);

				Menu_Editor::insertLink(1, "{L_INDEX}", "index.php", "home.png", "*");

				$default_blocks = Array();

				/* Main menu */
				$default_blocks[] = Array (
					"modules"				=> "*",
					"icon"					=> "menu.png",
					"title"					=> "{L_MENU}",
					"scriptpath"			=> "menu-editor/view.php",
					"scriptvars"			=> "a:1:{s:2:\"id\";s:1:\"1\";}",
					"order_id"				=> "0",
					"position"				=> "L",
					"blockclass"			=> "block",
					"csstext"				=> "",
					"auth_access"			=> "*"
				);

				/* Account menu */
				$default_blocks[] = Array (
					"modules"				=> "*",
					"icon"					=> "account.png",
					"title"					=> "{L_MY_ACCOUNT}",
					"scriptpath"			=> "menu-editor/view.php",
					"scriptvars"			=> "a:1:{s:2:\"id\";s:1:\"2\";}",
					"order_id"				=> "1",
					"position"				=> "L",
					"blockclass"			=> "block",
					"csstext"				=> "",
					"auth_access"			=> "$"
				);

				/* Who am I? */
				$default_blocks[] = Array(
					"modules"				=> "*",
					"icon"					=> "profile.png",
					"title"					=> "{L_MY_ACCOUNT}",
					"scriptpath"			=> "users/whoami.php",
					"scriptvars"			=> "a:0:{}",
					"order_id"				=> "1",
					"position"				=> "R",
					"blockclass"			=> "block",
					"csstext"				=> "",
					"auth_access"			=> "*"
				);

				/* translating array into sql */

				foreach ($default_blocks as $query_info) {
					$sql_fields = $sql_values = Array();
					foreach ($query_info as $field => $value) {
						$sql_fields[] = $field;
						$sql_values[] = "'$value'";
					}
					// inserting main menu in a new block
					$db->query("INSERT INTO ".dbTable("block")."
					(
						id,
						".implode("\n,", $sql_fields)."
					)
					VALUES
					(
						'".$db->findFreeId("block")."',
						".implode("\n,", $sql_values)."
					)", __FILE__, __LINE__);
				}
			}

			foreach ($modlist as $module) {

				$modclass = gekkoModule::getCoreClass($module);
				$modclass = new $modclass;

				/* creating additional menu links */
				if ($_SESSION["sess_options"]["create_menu"]) {
					if (!$modclass->createMenu() && file_exists(GEKKO_SOURCE_DIR."modules/$module/main.php")) {
						Menu_Editor::insertLink(1, "{L_MODULE_".strtoupper($module)."}", "index.php/module=$module", "$module.png", "*");
					}
				}

				unset($modclass);
			}

			if (isset($_SESSION["sess_ftp"])) {
				/* saving some ftp information */
				conf::setKey("core", "ftp.host", $_SESSION["sess_ftp"]["host"]);
				conf::setKey("core", "ftp.path", $_SESSION["sess_ftp"]["path"]);
				conf::setKey("core", "ftp.port", $_SESSION["sess_ftp"]["port"]);
			}

			$db->close();

			installerCacheClean();
		break;
		case "siteconf":
			varImport("conf", "option");

			varRequire("conf");

			// setting some unchecked checkboxes to 0 (they aren't sent since they're not checked)
			$temp = loadFile(GEKKO_SOURCE_DIR."install/view/index.tpl");
			preg_match_all("/<input.*type=\"checkbox\".*name=\"conf\[(.*?)\]\".*>/", $temp, $match);
			foreach ($match[1] as $key) {
				if (!isset($conf[$key])) $conf[$key] = 0;
			}

			$_SESSION["sess_conf"] = Array();
			foreach ($conf as $key => $value) {
				$module = substr($key, 0, $d = strpos($key, ":"));
				$keyname = substr($key, $d+1);
				$_SESSION["sess_conf"][] = Array(
					"module" => $module,
					"keyname" => $keyname,
					"value" => $value
				);
			}

			$_SESSION["sess_options"] = $option ? $option : Array();

			$nextstep = "install";

		break;
		case "createuser":

			varImport("user", "realname", "email", "pass", "pass2");

			varRequire("user", "pass");

			if ($pass && ($pass == $pass2)) {

				$_SESSION["sess_user"] = Array (
					"user" => $user,
					"realname" => $realname,
					"email" => $email,
					"pass" => $pass,
				);
				$nextstep = "siteconf";

			} else {
				$_SESSION["error"] = _L("L_PASSWORD_MISMATCH");
			}

		break;
		case "upgrade":

			define("IN-UPGRADE", true);

			varImport("new_modules");

			if (isset($_SESSION["sess_ftp"]))
				installerChangePermissions();

			writeFile(GEKKO_USER_DIR."dbconf.php", dbCreateConf());

			@chmod(GEKKO_USER_DIR."dbconf.php", 0755);

			dbLoadDriver();

			dbInit();

			// installed modules
			$q = $db->query("SELECT module FROM ".dbTable("module")."", __FILE__, __LINE__);

			$installed = Array();

			while ($row = $db->fetchRow($q))
				$installed[$row["module"]] = true;

			if ($new_modules) {

				// getting a list of module packages
				$packages = Array();
				$dh = opendir(GEKKO_SOURCE_DIR."modules/");
				while (($md = readdir($dh)) !== false) {
					if ($md != "." && $md != "..") {
						$modpath = GEKKO_SOURCE_DIR."modules/$md";
						if (is_dir($modpath)) {
							$packages[] = $md;
						}
					}
				}
				closedir($dh);

				foreach ($packages as $pkg) {
					if (!isset($installed[$pkg])) {
						// setting as false means to do a clean install for this module
						$installed[$pkg] = false;
					}
				}
			}

			foreach ($installed as $module => $status) {
				if (file_exists(GEKKO_SOURCE_DIR."modules/$module/model.php"))
					gekkoModule::install($module, $status ? "P" : "I");
			}

			// verifying default configuration keys

			$corekeys = Array();
			foreach ($defkeys as $key => $value) {
				$key = explode(":", $key);
				$type = isset($key[1]) ? $key[1] : "string";
				$key = $key[0];
				conf::getKey("core", $key, $value, $type);
				$corekeys[$key] = $value;
			}

			dbExit();

			header("Location: index.php");

			installerCacheClean();
			
			exit();
		break;
		case "install_type":
			varImport("clean_prefix", "clean_database", "install");

			$_SESSION["sess_db"]["clean_prefix"] = $clean_prefix;
			$_SESSION["sess_db"]["clean_database"] = $clean_database;

			$nextstep = ($install == 'upgrade') ? "upgrade": "createuser";
		break;
		case "dbconf":

			varImport("driver", "host", "port", "database", "user", "pass", "prefix");

			varRequire("driver", "host", "database");

			if (!ereg("\.\.", $driver)) {

				require_once GEKKO_SOURCE_DIR."lib/db/{$driver}";

				$db = new Database();

				if (@$db->open("$host:$port", $user, $pass, $database)) {

					$_SESSION["sess_db"] = Array (
						"driver" => $driver,
						"host" => $host,
						"port" => $port,
						"database" => $database,
						"user" => $user,
						"pass" => $pass,
						"prefix" => $prefix
					);

					$nextstep = "install_type";
				} else {
					appAbort($db->getError());
				}
			} else {
				die("Get a life!");
			}
		break;
		case "ftp":

			$Lang->loadFromModule("packages");
			// ftp test phase

			varImport("host", "port", "path", "user", "pass");

			varRequire("host", "user");

			$ftp = new ftpSession($host, $port, $user, $pass);

			if ($ftp->connect()) {

				if ($ftp->login()) {

					if ($ftp->cd($path) && $ftp->mtime("conf.php")) {
						// saving valid data
						$_SESSION["sess_ftp"] = Array(
							"host" => $host,
							"port" => $port,
							"user" => $user,
							"pass" => $pass,
							"path" => $path,
						);
						// next step is to configure the database
						$nextstep = "dbconf";
					} else {

						// don't know where I am
						$_SESSION["error"] = _L("L_WRONG_FTP_PATH");

						// trying to guess Gekko's root
						$pwd = $ftp->pwd();

						// expected FTP response: "/foo/bar" is current directory.
						preg_match("/\"(.*?)\"/", $pwd, $mwd);

						if (isset($mwd[1])) {
							$pwd = $mwd[1];
							if (substr(GEKKO_SOURCE_DIR, 0, strlen($pwd)) == $pwd)
								$prob_path = substr(GEKKO_SOURCE_DIR, strlen($pwd));
						}
						if (isset($prob_path))
							$_SESSION["hint"] = sprintf(_L("L_PROBABLE_FTP_PATH"), $prob_path);
					}
				} else {
					$_SESSION["error"] = _L("L_AUTHENTICATION_FAILED");
				}
			} else {
				$_SESSION["error"] = _L("L_CONNECTION_REFUSED");
			}
		break;
		default: {
			if (!isset($_SESSION["sess_lang"])) {
				$nextstep = "lang";
				break;
			} else {
				if ($step == "manual_config") {
					if (!installerCheckDirectories() && !is_writable(GEKKO_USER_DIR."dbconf.php") && filesize(GEKKO_USER_DIR."dbconf.php")) {
						die(header("Location: index.php"));
					} else {
						$nextstep = "manual_config";
					}
				} else {
					$nextstep = (installerCheckDirectories() || !is_writable(GEKKO_USER_DIR."dbconf.php")) ? "ftp" : "dbconf";
				}
			}
		}
	}

	switch ($nextstep) {
		case "manual_config":
			$data = Array (
				"V_STEP"			=> $nextstep,
				"V_HIDE_BUTTONS"	=> true
			);
		break;
		case "install":
			$data = Array(
				"V_STEP" => $nextstep,
				"V_FTP" => isset($_SESSION["sess_ftp"])
			);
		break;
		case "siteconf":
			require GEKKO_SOURCE_DIR."lang/codes.php";

			$data = Array (
				"V_STEP"				=> $nextstep,
				"V_ARR_LANG"			=> serialize($lang_db),
				"V_ARR_LANG_CODE"		=> serialize($ISO_639),
				"V_ARR_COUNTRY_CODE"	=> serialize($ISO_3166),
				"V_SELECTED_LANG"		=> $_SESSION["sess_lang"]
			);

			foreach ($defkeys as $key => $value) {
				$keyname = (($d = strpos($key, ":")) !== false) ? substr($key, 0, $d) : $key;
				$data["V_CORE_".strtoupper($keyname)] = $value;
			}
		break;
		case "createuser":
			$data = Array (
				"V_STEP" => $nextstep
			);
		break;
		case "upgrade":
			$data = Array (
				"V_STEP" => $nextstep
			);
		break;
		case "install_type":
			$data = Array (
				"V_STEP"			=> $nextstep,
				"V_HIDE_BUTTONS"	=> true
			);
		break;
		case "dbconf":
			$drivers = listDirectory(GEKKO_SOURCE_DIR."lib/db");
			$driver_sel = Array();
			foreach ($drivers as $driver) {
				if (getFileExtension($driver) == "driver") {
					$driver_sel[$driver] = substr($driver, 0, strpos($driver, "."));
				}
			}
			$data = Array (
				"V_STEP"			=> $nextstep,
				"V_ARR_DB_DRIVERS"	=> serialize($driver_sel)
			);
		break;
		case "ftp":
			$data = Array (
				"V_STEP" => $nextstep
			);
		break;
		case "lang":
			require GEKKO_SOURCE_DIR."lang/codes.php";
			$data = Array (
				"V_STEP" => $nextstep,
				"V_ARR_LANGUAGES" => serialize($lang_db)
			);
		break;
		case "login":
			header("Location: index.php");
			exit();
		break;
	}

	$data["V_MESSAGES"] = "";
	$data["V_USING_IE"] = (preg_match("/MSIE.*[456]\./", $_SERVER["HTTP_USER_AGENT"]) && !preg_match("/opera/i", $_SERVER["HTTP_USER_AGENT"]));

	$messages = array("error", "warning", "info", "hint");
	foreach ($messages as $type) {
		if (isset($_SESSION[$type])) {
			$data["V_MESSAGES"] .= createMessageBox($type, $_SESSION[$type]);
			unset($_SESSION[$type]);
		}
	}
	
	appSessionSave();

	gzinit();
		$T = new GekkoTemplateEngine();
		$buff = $T->load(GEKKO_SOURCE_DIR."install/view/index.tpl", true);
		$buff = $T->parse($buff, $data, true);
		$T->output($buff);

		echo $buff;
	gzoutput();
?>
