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

	appLoadLibrary(
		"core.lib.php", "groups"
	);

	dbInit();

	require_once GEKKO_SOURCE_DIR."lib/auth.inc.php";

	varImport("action", "id", "return");

	if (isAdmin()) {
		switch ($action) {
			case "delete":
				execWithEach($id, "Groups::deleteItem");
			break;
			case "enable":
				Groups::setItemStatus($id, 1);
			break;
			case "create":

				varImport("groupname", "description", "status", "includegroups");

				varRequire("groupname");

				if (preg_match("/[^a-zA-Z0-9]/", $groupname))
					formInputError("{E_INVALID_CHARSET} (a-zA-Z0-9)", "focus: groupname");

				$q = $db->query("SELECT id FROM ".dbTable("group")."
				WHERE groupname = '{$groupname}'", __FILE__, __LINE__);

				if ($db->numRows($q))
					formInputError("{E_DUPLICATED_ENTRY}", "focus: groupname");

				$includegroups = Groups::checkList(groups::extractFromInput($includegroups), $id);

				$db->query ("
				INSERT INTO ".dbTable("group")."
					(
						id,
						groupname,
						description,
						status,
						includegroups
					)
						values
					(
						'".$db->findFreeId("group", "id", 401, 499)."',
						'{$groupname}',
						'{$description}',
						'{$status}',
						'{$includegroups}'
					)
				", __FILE__, __LINE__);

				appWriteLog("groups: created group '$groupname'", "actions", 1);
			break;
			case "edit":

				varImport("id", "groupname", "description", "status", "includegroups");

				varRequire("id", "groupname");

				if (preg_match("/[^a-zA-Z0-9]/", $groupname))
					formInputError("{E_INVALID_CHARSET} (a-zA-Z0-9)", "focus: groupname");

				$includegroups = Groups::checkList(groups::extractFromInput($includegroups), $id);

				$q = $db->query("SELECT id FROM ".dbTable("group")."
				WHERE groupname = '$groupname' AND id != '{$id}'", __FILE__, __LINE__);

				if ($db->numRows($q))
					formInputError("{E_DUPLICATED_ENTRY}", "focus: groupname");

				$db->query("
					UPDATE ".dbTable("group")." SET
						groupname = '{$groupname}',
						description = '{$description}',
						status = '{$status}',
						includegroups = '{$includegroups}'
					WHERE id = '{$id}'
				", __FILE__, __LINE__);

				appWriteLog("groups: group '$groupname' was updated", "actions", 1);
			break;
			default:
				appAbort(_L("L_UNDEFINED_ACTION"));
			break;
		}
	}

	appRedirect($return);

	dbExit();
?>