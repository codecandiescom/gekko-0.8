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

	appLoadLibrary ("groups");

	function createAuthHash() {
		$gekko_key = conf::getKey("core", "gekko.key", createRandomKey(32), 's');

		$hash = (defined("AUTH") && isset($GLOBALS["USER"])) ? $GLOBALS["USER"]["username"].$GLOBALS["USER"]["password"] : '';

		$auth_key = $gekko_key.$hash.$_SERVER["HTTP_USER_AGENT"].IP_ADDR;
		
		return md5($auth_key);
	}

	unset($auth_key, $auth_hash);

	$cookie = explode(":", isset($_COOKIE[GEKKO_COOKIE_PREFIX."auth"]) ? $_COOKIE[GEKKO_COOKIE_PREFIX."auth"] : '');
	
	if (isset($cookie[1]))
		list($auth_key, $auth_hash) = $cookie;

	$USER = Array (
		"id"		=> "0",
		"username"	=> "",
		"groups"	=> "0"
	);

	if (isset($auth_key) && isset($auth_hash)) {

		$q = $db->query("SELECT id, password FROM ".dbTable("user")."
		WHERE auth_key = '{$auth_key}'", __FILE__, __LINE__);

		if ($db->numRows($q)) {

			$row = $db->fetchRow($q);

			if ($auth_hash == md5($row["password"].IP_ADDR)) {

				$db->query("
					SELECT
						id,
						username,
						password,
						realname,
						email,
						status,
						groups,
						preferences
					FROM ".dbTable("user")."
					WHERE id = '{$row["id"]}'
				", __FILE__, __LINE__);

				$USER = $db->fetchRow();

				saneHTML($USER);

				$USER["groups"] = implode(',', Groups::fetchAll($USER["groups"]));

				define("AUTH", true);

			} else {
				appWriteLog("Visitor sent a suspicious cookie.", "warning", 5);
			}
		}
	}
	// setting auth string
	_L("C_AUTH_STR", createAuthHash());

	// checking if this authentication was invoked from a module action
	preg_match("/(modules)\/([a-zA-Z0-9\-_.]*)\/([a-zA-Z0-9\-_.]*)/", $_SERVER['REQUEST_URI'], $act_info);

	if (strpos($_SERVER['REQUEST_URI'], 'index.php') == false && count($act_info) >= 4) {

		list($act_path, $act_reqdir, $act_module, $act_action) = $act_info;

		switch ($act_reqdir) {
			case "modules":
				// cannot allow actions for privileged users when Gekko runs in 'demo' mode
				if (defined("AUTH") && GEKKO_DEMO_MODE)
					appAbort(_L("E_DEMO_MODE"));

				// contains special functions for module actions
				appLoadLibrary("actions.lib.php");

				// checking auth string validity
				if (!checkAuthHash(varGetValue("auth"))) {
					appWriteLog("Using an invalid auth string.", "warning", 2);
					appAbort(_L("L_JAVASCRIPT_REQUIRED"));
				}

				// does the user have enough privileges to execute actions in this module?
				$q = $db->query("SELECT auth_exec, auth_manage FROM ".dbTable("module")."
				WHERE module = '{$act_module}'");
				$row = $db->fetchRow($q);

				if (!appAuthorize($row["auth_exec"]))
					appAbort(accessDenied());

				cacheCleanAfterPost();
			break;
			default:
				appAbort("wtf?");
			break;
		}
	}

	if (!appAuthorize('#') && conf::getKey("core", "magic_blacklist", 1, 'b') && $AccessTable->check("core-exec-requests", conf::getKey("core", "exec_requests.interval", 5, 'i'), conf::getKey("core", "exec_requests.requests", 13, 'i'), 1)) {

		if ($AccessTable->check("core-exec-preban", 60, 3, 1)) {

			$banlist[] = array (
				'ip_addr' => IP_ADDR,
				'ban_start' => time(),
				'ban_time' => 0,
				'ban_reason' => ''
			);

			// saving the new entry in the banlist
			$fh = fopen(GEKKO_BLACKLIST, 'w');
			fwrite($fh, serialize($banlist));
			fclose($fh);

			// showing him/her/it an non-friendly error message
			header("HTTP/1.1 403 Forbidden");
			echo "<h1>Too many requests!</h1>
			Do you have a girlfriend? I'm sorry, you're temporary blacklisted.
			<hr /><address>Powered by <a href=\"http://www.gekkoware.org\">Gekko</a></address>
			";
			appWriteLog("Blacklisted IP (too many requests)", "warning", 1);
			appAbort();

		}

		// keeping flooders and annoying scripts away
		echo "<h1>Please wait</h1>";
		echo "I'm sorry but you've made <b>too many requests</b> within a very short
		period of time, please be kind and wait a minute to reload this page (just hit
		the reload button or press F5).<br />
		<b>You're warned</b>!
		<hr /><address>Powered by <a href=\"http://www.gekkoware.org\">Gekko</a></address>";

		appWriteLog("User is reaching execution interval limit", "warning", 2);
		appAbort();
	}

	unset($banlist);

	appWriteLog('', "access");

	// setting antibot obfuscated string
	if (function_exists("jscriptObfuscateString"))
		_L("C_ANTIBOT", jscriptObfuscateString(_L("C_AUTH_STR")));

?>
