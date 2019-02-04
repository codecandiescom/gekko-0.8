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

	appLoadLibrary (
		"groups", "users", "format.lib.php"
	);

	Extract (
		urlImportMap("null/null/action=alpha?page=page&id=int")
	);

	switch ($action) {
		case "edit":
			if ($id) {

				$tpl = new contentBlock();

				$tpl->insert("BLOCK_CONTENT", "groups/admin.edit.tpl");

				$q = $db->query("
					SELECT
						id, groupname, description, status, includegroups
					FROM ".dbTable("group")." WHERE id = '{$id}'
				");

				if ($db->numRows($q)) {

					$row = $db->fetchRow($q);

					saneHTML($row);

					$row["includegroups"] = groups::humanReadableGroups($row["includegroups"]);

					$tpl->setArray($row);

					$tpl->setArray (
						Array (
							"BLOCK_TITLE"		=> _L("L_GROUPS"),
							"BLOCK_ICON"		=> createIcon("groups", 16),
							"ACTION"			=> "edit",
							"RETURN"			=> "index.php/module=admin/base=groups"
						)
					);

					$mcBuff = $tpl->make();
				}
			}
		break;
		default:

			$tpl = new contentBlock();

			$tpl->insert("BLOCK_CONTENT", "groups/admin.default.tpl");

			$q = $db->query ("
				SELECT id, groupname, description, status, includegroups
				FROM ".dbTable("group")."
				ORDER BY id, groupname DESC
			", __FILE__, __LINE__);

			while ($row = $db->fetchRow($q)) {

				saneHTML($row);

				$row["actions"] = new tplButtonBox();
				$row["actions"]->add (
					createLink("index.php/module=admin/base=groups/action=edit?id={$row["id"]}", _L("L_EDIT"), "edit.png"),
					createLink("modules/groups/actions.php?action=delete&id={$row["id"]}&auth={C_AUTH_STR}", _L("L_DELETE"), "delete.png", true)
				);
				if (!$row["status"])
					$row["actions"]->add(createLink("modules/groups/actions.php?action=enable&id={$row["id"]}&auth={C_AUTH_STR}", _L("L_ENABLE")));
				$row["actions"] = $row["actions"]->make();

				$row["includegroups"] = Groups::createList(Groups::fetchAll($row["includegroups"]));

				if (!$row["includegroups"])
					$row["includegroups"] = "("._L("L_NONE").")";

				$tpl->setArray(
					$row,
					"GROUP"
				);

				$tpl->saveBlock("GROUP");
			}

			$tpl->setArray(
				Array (
					"BLOCK_TITLE"		=> _L("L_GROUPS"),
					"BLOCK_ICON"		=> createIcon("groups.png", 16),
					"GROUPNAME"			=> "",
					"DESCRIPTION"		=> "",
					"ACTION"			=> "create",
					"RETURN"			=> "",
					"STATUS"			=> 1,
					"INCLUDEGROUPS"		=> ""
				)
			);

			$mcBuff = $tpl->make();
		break;
	}
?>