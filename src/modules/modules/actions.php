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
		"core.lib.php", "conf", "modules"
	);

	dbInit();

	require_once GEKKO_SOURCE_DIR."lib/auth.inc.php";

	varImport("action", "id", "return");

	if (isAdmin()) {
		switch ($action) {
			case "manage":

				varImport("opt");

				varRequire("id", "opt");

				$module = modules::getName($id);

				if ($module) {

					gekkoModule::install($module, $opt);

					$return = "index.php/module=admin/base=modules";

					appWriteLog("modules: mainteinance instruction '$opt' executed for '$module'");
				}
			break;
			case "set_default":
				varImport("default");

				varRequire("default");

				$q = $db->query("SELECT module FROM ".dbTable("module")." WHERE id = '{$default}'");

				if ($db->numRows($q)) {
					$row = $db->fetchRow($q);
					conf::setKey("modules", "default", $row["module"], 's');
				}

 				appWriteLog("modules: module '".modules::getName($id)."' set as default");
			break;
			case "status":

				varImport("set");

				varRequire("id");

				$db->query("UPDATE ".dbTable("module")."
				SET status = '{$set}'
				WHERE id = '$id'");

				appWriteLog("modules: status for '".modules::getName($id)."' set to '$set'");
			break;
			case "modify":

				varImport("id", "auth_exec", "auth_manage", "hidden", "status");

				varRequire("id");

				$db->query ("
					UPDATE ".dbTable("module")." SET
						auth_exec = '".groups::extractFromInput($auth_exec)."',
						auth_manage = '".groups::extractFromInput($auth_manage)."',
						hidden = '{$hidden}',
						status = '{$status}',
						modified = '".time()."'
					WHERE id = '{$id}'",
					__FILE__, __LINE__);

 				appWriteLog("modules: updated information for module '".modules::getName($id)."'");
			break;
			case "install":

				varImport("module");

				varRequire("module");

				if (!is_array($module))
					$module = array($module);

				foreach ($module as $tobeinstalled) {
					$q = $db->query("SELECT id
					FROM ".dbTable("module")." WHERE
					module = '$tobeinstalled'",__FILE__, __LINE__);

					if (gekkoModule::exists($tobeinstalled) && !$db->numRows($q)) {
						gekkoModule::install($tobeinstalled, "I");
						appWriteLog("modules: module '$tobeinstalled' was installed", "actions", 1);
					}
				}
			break;
		}
	}

	appRedirect($return);

	dbExit();
?>