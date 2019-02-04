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

	require_once "conf.php";
	
	define("GEKKO_XML_OUTPUT", (isset($_POST["ajax"]) && $_POST["ajax"]) || (isset($_GET["ajax"]) && $_GET["ajax"]));

	if (GEKKO_XML_OUTPUT) {
		define ("GEKKO_USE_ABSOLUTE_URLS", true);
		_L("C_SITE.REL_URL", "");
	}

	require_once "lib/template.lib.php";

	require_once "lib/format.lib.php";
	require_once "lib/gzenc.lib.php";

	// initializing database connection
	dbInit();

	require_once "lib/auth.inc.php";

	if (!GEKKO_VORAZ_CACHE || (isUser() || !cacheRead(GEKKO_PAGE_CACHE_KEY, $L))) {

		$mc = new gekkoModule();

		// Loading core.php script from each allowed module.
		$allowed_modules = $mc->getList();
		foreach ($allowed_modules as $mod) {
			$core = GEKKO_MODULE_DIR."{$mod}/include/core.php";
			if (file_exists($core))
				require_once $core;
		}

		if (defined("GEKKO_REQUEST_MODULE") && GEKKO_REQUEST_MODULE) {
			if ($mc->auth(GEKKO_REQUEST_MODULE)) {
			
				// loading main language and configuration options
				$mcBuff = "";

				$Conf->Load(GEKKO_REQUEST_MODULE);
				$Lang->loadFromModule(GEKKO_REQUEST_MODULE);

				if (!defined("GEKKO_DEFAULT_VIEW"))
					pageSetSubtitle(_L("L_".strtoupper(GEKKO_REQUEST_MODULE)));

				ob_start();
				
				require ($mc->srcPath(GEKKO_REQUEST_MODULE)."main.php");
				
				if (class_exists(GEKKO_REQUEST_MODULE."Controller")) {
					$class = GEKKO_REQUEST_MODULE."Controller";
					$control = new $class;
				}

				$mcBuff .= ob_get_contents();
				
				ob_end_clean();

				$mc->save($mcBuff);
				// including main
			} else {
				// access denied, this user has not enough privileges
				$mc->save(createMessageBox("error", _L("E_ACCESS_DENIED")));
			}

		} else {
			$mc->save("
			<div class=\"contentBlock\">
			<h1>Gekko | Web Development Framework</h1>
			"._L("L_GEKKO_WELCOME")."
			</div>
			");
		}

		$L["M_BLOCK_C"] = "";

		// session variables
		$sess_msgs = Array("error", "info", "success", "warning", "hint");
		foreach ($sess_msgs as $msg) {
			if (isset($_SESSION[$msg])) {
				$L["M_BLOCK_C"] .= createMessageBox($msg, $_SESSION[$msg]);
				unset($_SESSION[$msg]);
			}
		}

		// when working in ssl mode
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "off")
			$L["M_BLOCK_C"] .= createMessageBox("warning", _L("L_SSL_ENABLED"), "network.png");

		// displaying message
		if (isset($_GET["message"]))
			$L["M_BLOCK_C"] .= createMessageBox((isset($_GET["type"]) ? $_GET["type"] : "info"), preg_replace("/(<|>)/e", "htmlspecialchars('\\1')", $_GET["message"]));

		if ($mc->buff) {
			$L["M_BLOCK_C"] .= $mc->buff;	
		} else {
			$L["M_BLOCK_C"] .= createMessageBox("info", isAdmin(true) ? _L("L_ADMIN_EMPTY_PAGE") : _L("L_EMPTY_PAGE"));
		}

		// setting the correct colspan variables for "framework" table
		$L["V_FW_COLSPAN"] = 1+($L["M_BLOCK_L"] != "")+($L["M_BLOCK_R"] != "");

		// plugin call
		$appPluginHandler->triggerEvent("template/beforeParse", $buff);
	
		// some stylish stuff: title separator
		if ($L["V_DOC_TITLE"]) $L["V_DOC_TITLE"] .= TITLE_SEPARATOR;

		// caching page display (for non-users)
		if (GEKKO_VORAZ_CACHE && !isUser())
			cacheSave(GEKKO_PAGE_CACHE_KEY, $L);
	}

	appSessionSave();

	gzInit();

	if (is_array($L)) {

		// those variables are different for each user
		$L["C_AUTH_STR"] = createAuthHash();
		$L["C_ANTIBOT"] = jscriptObfuscateString($L["C_AUTH_STR"]);

		// application statistics
		$L["V_SCRIPT_EXEC_TIME"] = substr((getmicrotime() - GEKKO_START_TIME), 0, 5);
		$L["V_MEMORY_USAGE"] = (function_exists('memory_get_usage') && GEKKO_ENABLE_DEBUG) ? round(memory_get_usage()/pow(1024, 2), 2)."Mb" : "";
		$L["V_DATABASE_QUERIES"] = $db->counter();

		// parsing template
		if (GEKKO_XML_OUTPUT) {

			header("Content-Type: text/xml; charset=UTF-8");

			$buff = new BlockWidget("_layout/index.xml.tpl");

			foreach ($L as $key => $content) {
				if (substr($key, 0, 2) == "M_") {
					$vars = Array();
					$vars["POSITION"] = strtoupper(substr($key, 2));
					$vars["POSITION"] = str_replace("_", "", $vars["POSITION"]);
					$vars["POSITION"] = strtolower(substr($vars["POSITION"], 0, 5)).substr($vars["POSITION"], 5);
					$vars["CONTENT"] = $content;
					$buff->setArray($vars, "BLOCK");
					$buff->saveBlock("BLOCK");
				}
			}

			$buff = $buff->make();

			$T->output($buff, true);

			echo $buff;
		} else {
			// normal page output
			header("Content-Type: text/html; charset=UTF-8");

			$buff = $T->load("_layout/index.tpl", true, true);

			$T->output($buff, true);

			echo $buff;
		}

	} else {
		// cached XML document (it might be a feed)
		header("Content-type: text/xml; charset=UTF-8");

		echo $L;
	}

	if (GEKKO_ENABLE_DEBUG) {
		echo "
			<table class=\"debug\">
			<thead>
				<td>Query</td>
				<td>Error</td>
				<td>Affected rows</td>
			</thead>
		";
		foreach ($db->query as $query) {
			if (!$query["file"])
				$query["file"] = "<span style=\"font-color: #f00\">Missing</span>";
			echo "
				<tr>
					<td colspan=\"3\" class=\"file\">
						{$query["file"]}:".intval($query["line"])."
					</td>
				</tr>
				<tr>
					<td>".htmlspecialchars($query["query"])."</td>
					<td>{$query["error"]}</td>
					<td>{$query["affected_rows"]}</td>
				</tr>
			";
		}
		echo "</table>";
	}

	gzOutput();

	dbExit();
?>
