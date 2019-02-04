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
		"users", "groups"
	);

	Extract (
		urlImportMap("null/null/action=alpha?id=int&page=page&sort=alpha&mode=alpha&option=alpha")
	);

	if (defined("IN-TEMPLATE"))
		appLoadJavascript("modules/users/main.js");

	switch ($action) {
		case "edit":
			if ($id) {

				appEnableSSL();

				$tpl = new contentBlock();

				$tpl->insert("BLOCK_CONTENT", "users/admin.edit.tpl");

				$q = $db->query("
					SELECT
					id, username, realname, password, email, date_registered, date_login,
					auth_key, status, groups, preferences
					FROM ".dbTable("user")." WHERE id = '{$id}'
				", __FILE__, __LINE__);

				if ($db->numRows($q)) {

					$row = $db->fetchRow($q);
					saneHTML($row);

					$options = new optionBox($tpl, $option);
						$options->add (null, "index.php/module=admin/base=users", "{L_LIST}", "users.png");
						$options->add (null, "index.php/module=users/action=profile/user={$row["username"]}?edit=1", "{L_PROFILE}", "profile.png");
						if (!$row["status"])
							$options->add (null, "modules/users/actions.php?action=enable&id={$row["id"]}&auth={C_AUTH_STR}", "{L_ENABLE}", "unlock.png");
						$options->add (null, "modules/users/actions.php?action=delete&id={$row["id"]}&auth={C_AUTH_STR}", "{L_DELETE}", "delete.png", true);
					$options->make();

					$tpl->setArray($row);

					$tpl->setArray(
						Array (
							"BLOCK_TITLE"		=> _L("L_USERS"),
							"BLOCK_ICON"		=> createIcon("users.png", 16),
							"ACTION"			=> "edit",
							"RETURN"			=> "index.php/module=admin/base=users"
						)
					);

					$mcBuff = $tpl->make();
				}
			}
		break;
		case "register":

			appEnableSSL();

			$tpl = new contentBlock();

			$tpl->insert("BLOCK_CONTENT", "users/admin.register.tpl");

			$options = new optionBox($tpl, $option);
				$options->add (null, "index.php/module=admin/base=users", "{L_LIST}", "users.png");
			$options->make();

			$tpl->setArray (
				Array (
					"BLOCK_TITLE"		=> _L("L_USERS"),
					"BLOCK_ICON"		=> createIcon("users.png", 16),
					"STATUS"			=> 1,
					"ACTION"			=> "register",
					"USERNAME"			=> "",
					"PASSWORD"			=> "",
					"EMAIL"				=> "",
					"REALNAME"			=> "",
					"GROUPS"			=> "500",
					"ID"				=> 0,
					"RETURN"			=> "index.php/module=admin/base=users"
				)
			);
			$mcBuff = $tpl->make();
		break;
		default:

			if (!$sort || !in_array($sort, array("id","username","email")))
				$sort = "username";

			if (!$mode || !in_array($mode, array("asc", "desc")))
				$mode = "asc";

			$tpl = new contentBlock();

			$tpl->insert("BLOCK_CONTENT", "users/admin.default.tpl");

			// options
			$options = new optionBox($tpl, $option);
				$options->add (null, "index.php/module=search?base=users", "{L_SEARCH}", "search.png");
				$options->add (null, "index.php/module=admin/base=users/action=register", "{L_REGISTER}", "new.png");
			$options->make();

			$pager = $db->pager (
				"SELECT id FROM ".dbTable("user")."",
				"index.php/module=admin/base=users?page=%p&sort=$sort&mode=$mode#manage",
				$page, conf::getkey("users", "users_per_page", 16, "i")
			);

			$db->query ("
				SELECT
					id, username, realname, email, status
				FROM ".dbTable("user")."
				ORDER BY $sort $mode {$pager["sql"]}
			", __FILE__, __LINE__);

			while ($row = $db->fetchRow()) {

				saneHTML($row);

				$row["actions"] = new tplButtonBox();
					$row["actions"]->add(createLink("index.php/module=admin/base=users/action=edit?id={$row["id"]}", "{L_EDIT}", "edit.png"));
					if (!$row["status"])
						$row["actions"]->add(createLink("modules/users/actions.php?action=enable&id={$row["id"]}&auth={C_AUTH_STR}", "{L_ENABLE}", "unlock.png"));
					$row["actions"]->add(createLink("modules/users/actions.php?action=delete&id={$row["id"]}&auth={C_AUTH_STR}", "{L_DELETE}", "delete.png", true));
				$row["actions"] = $row["actions"]->make();

				$row["avatar"] = cacheFunc("users::createAvatar", $row["id"]);

				$tpl->setArray($row, "USER");
				$tpl->saveBlock("USER");
			}

			$tpl->setArray (
				Array (
					"BLOCK_TITLE"		=> _L("L_USERS"),
					"BLOCK_ICON"		=> createIcon("users.png", 16),
					"STATUS"			=> 1,
					"ACTION"			=> "register",
					"USERNAME"			=> "",
					"PASSWORD"			=> "",
					"EMAIL"				=> "",
					"REALNAME"			=> "",
					"SESLEN"			=> "",
					"SORT"				=> $sort,
					"MODE"				=> $mode,
					"GROUPS"			=> "500",
					"ID"				=> 0,
					"RETURN"			=> "",
					"PAGER"				=> $pager["html"]
				)
			);

			$mcBuff = $tpl->make();
		break;
	}
?>