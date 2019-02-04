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

	define ("IN-GEKKO", true);

	require_once "../../conf.php";

	appLoadLibrary (
		"conf"
	);

	require_once GEKKO_SOURCE_DIR."lib/auth.inc.php";

	varImport("action", "id", "return");

	if (isAdmin()) {
		switch ($action) {
			case "looknfeel":
				varImport("request", "theme");

				switch ($request) {
					case "set_theme":
						varRequire("theme");
						
						$theme = explode(":", $theme);

						if (file_exists(GEKKO_TEMPLATE_DIR."{$theme[0]}/_themes/{$theme[1]}/theme.css")) {
							conf::setKey("core", "site.template", $theme[0]);
							conf::setKey("core", "site.stylesheet", $theme[1]);
						}
					break;
				}

			break;
			case "delete_key":
				execWithEach($id, "conf::delKeyById");

				appWriteLog("conf: deleted key(s) '".(is_array($id) ? implode(',', $id) : $id)."'");
			break;
			case "plugins":
			
				varImport("plugin", "request");

				varRequire("plugin", "request");

				if (preg_match("/^(enable|disable)$/", $request))
					conf::setKey("core", "plugins.".strtolower($plugin).".disable", intval($request == "disable"), 'b');

				appSetMessage("success", _L("L_UPDATED_PLUGIN"));
				
			break;
			case "quickedit":

				// I'm using a keylist because when a checkbox is unchecked it is prevented
				// from being sent, therefore we cannot know if this checkbox was active and now is
				// required to be inactive.

				varImport("key", "keylist");

				varRequire("keylist");

				$keys = explode(",", $keylist);

				foreach ($keys as $id) {
					$id = trim($id);

					$value = isset($key[$id]) ? $key[$id] : false;

					$q = $db->query("SELECT keytype
					FROM ".dbTable("conf")." WHERE id='{$id}'");

					if ($db->numRows($q)) {
						$row = $db->fetchRow($q);
						$value = conf::fixValue($value, $row["keytype"]);
						$db->query("UPDATE ".dbTable("conf")." SET
							keyvalue='{$value}'
						WHERE id = '{$id}' AND keyvalue != '{$value}'", __FILE__, __LINE__);
					}

					appWriteLog("conf: modified key id = '$id'");
				}
			break;
			case "create_key":

				varImport("module", "keytype", "keyname", "keyvalue", "locked");

				varRequire("module", "keyname");

				if (conf::checkKeyName($keyname))
					formInputError("{E_INVALID_CHARSET}", "focus: keyname");

				$keyvalue = conf::fixValue($keyvalue, $keytype);

				$q = $db->query ("SELECT id FROM ".dbTable("conf")."
				WHERE keyname = '{$keyname}' AND module = '{$module}'",
				__FILE__, __LINE__);

				if (!$db->numRows($q)) {

					$db->query("
						INSERT INTO ".dbTable("conf")." (
							id,
							module,
							keyname,
							keyvalue,
							keytype,
							date_created,
							locked
						) VALUES (
							".$db->findFreeId("conf").",
							'{$module}',
							'{$keyname}',
							'{$keyvalue}',
							'{$keytype}',
							CURRENT_TIMESTAMP(),
							'{$locked}'
						)
					", __FILE__, __LINE__);

					appWriteLog("conf: new key '$module/$keyname'");

				} else {
					// modifying key
					$row = $db->fetchRow($q);

					$db->query ("
						UPDATE ".dbTable("conf")." SET
							module = '{$module}',
							keyname = '{$keyname}',
							keyvalue = '{$keyvalue}',
							keytype = '{$keytype}',
							locked = '{$locked}'
						WHERE id = '{$row["id"]}'
					", __FILE__, __LINE__);

					appWriteLog("conf: modified key '$module/$keyname'");
				}
			break;
			case "edit_key":

				varImport("id", "module", "keytype", "keyname", "keyvalue", "locked");

				varRequire("id", "module", "keyname");

				// checking for duplicated key name
				$q = $db->query ("SELECT id FROM ".dbTable("conf")."
				WHERE keyname = '{$keyname}' AND module = '{$module}' AND id != '$id'",
				__FILE__, __LINE__);

				if (!$db->numRows($q)) {

					$db->query ("
						UPDATE ".dbTable("conf")." SET
							module = '{$module}',
							keyname = '{$keyname}',
							keyvalue = '".conf::fixValue($keyvalue, $keytype)."',
							keytype = '$keytype',
							locked = '{$locked}'
						WHERE id = '{$id}'
					", __FILE__, __LINE__);

					appWriteLog("conf: modified key '$module/$keyname'");

				} else {
					formInputError("{E_DUPLICATED_ENTRY}", "focus: keyname");
				}
			break;
		}
	}

	appRedirect($return);

	dbExit();
?>
