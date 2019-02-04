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
		"menu-editor"
	);

	Extract (
		urlImportMap("null/null/action=alpha?page=page&id=int&menu_id=int")
	);

	switch ($action) {
		case "edit_menu":
			if ($id) {

				$tpl = new contentBlock();

				$tpl->insert ("BLOCK_CONTENT", "menu-editor/admin.$action.tpl");

				$q = $db->query ("
					SELECT
						id, title, dropdown, hide_icons
					FROM ".dbTable("menu_block")." WHERE id = '{$id}'
				", __FILE__, __LINE__);

				$row = $db->fetchRow($q);

				if ($row) {

					$actions = new tplButtonBox();
						$actions->add(createLink("index.php/module=admin/base=menu-editor", _L("L_MENU-EDITOR"), "menu-editor.png"));
						$actions->add(createLink("index.php/module=admin/base=menu-editor/action=create_link?menu_id={$row["id"]}", _L("L_CREATE_LINK"), "new.png"));
					$actions = $actions->make();

					$tpl->setArray($row);

					$tpl->setArray(
						Array (
							"BLOCK_TITLE"		=> _L("L_MENU-EDITOR"),
							"BLOCK_ICON"		=> createIcon("menu-editor.png", 16),
							"ACTION"			=> $action,
							"ACTIONS"			=> $actions,
							"RETURN"			=> "index.php/module=admin/base=menu-editor"
						)
					);

					$mcBuff = $tpl->make();
				}
			}
		break;
		case "create_menu":

			$tpl = new contentBlock();

			$tpl->insert("BLOCK_CONTENT", "menu-editor/admin.$action.tpl");

			$actions = new tplButtonBox();
				$actions->add(createLink("index.php/module=admin/base=menu-editor", _L("L_MENU-EDITOR"), "menu-editor.png"));
			$actions = $actions->make();

			$tpl->setArray (
				Array (
					"BLOCK_TITLE"	=> _L("L_MENU-EDITOR"),
					"BLOCK_ICON"	=> createIcon("menu-editor.png", 16),
					"TITLE"			=> "",
					"ACTION"		=> $action,
					"ACTIONS"		=> $actions,
					"HIDE_ICONS"	=> 0,
					"DROPDOWN"		=> 0,
					"RETURN"		=> "index.php/module=admin/base=menu-editor"
				)
			);

			$mcBuff = $tpl->make();
		break;
		case "create_link":

			$tpl = new contentBlock();

			$tpl->insert("BLOCK_CONTENT", "menu-editor/admin.$action.tpl");

			$q = $db->query("SELECT id, title FROM ".dbTable("menu_block")."
			ORDER BY title ASC", __FILE__, __LINE__);

			$menus = Array();
			while ($row = $db->fetchRow($q)) {
				saneHTML($row);
				$menus[$row["id"]] = $row["title"];
			}
			$actions = new tplButtonBox();
				$actions->add(createLink("index.php/module=admin/base=menu-editor", _L("L_MENU-EDITOR"), "menu-editor.png"));
				$actions->add(createLink("index.php/module=admin/base=menu-editor/action=create_menu", _L("L_CREATE_MENU"), "add.png"));
			$actions = $actions->make();

			$tpl->setArray (
				Array (
					"BLOCK_TITLE"		=> _L("L_MENU-EDITOR"),
					"BLOCK_ICON"		=> createIcon("menu-editor.png", 16),
					"ARR_MENU"			=> serialize($menus),
					"MENU_ID"			=> $menu_id,
					"AUTH_ACCESS"		=> '*',
					"TITLE"				=> '',
					"ICON"				=> "link.png",
					"ORDER_ID"			=> 1,
					"TYPE"				=> 0,
					"TOOLTIP"			=> '',
					"IS_ABSOLUTE"		=> '',
					"LEVEL"				=> 0,
					"LINK"				=> '',
					"RETURN"			=> "index.php/module=admin/base=menu-editor",
					"ACTIONS"			=> $actions,
					"ACTION"			=> $action

				)
			);

			$mcBuff = $tpl->make();
		break;

		case "edit_link":

			$tpl = new contentBlock();

			$tpl->insert("BLOCK_CONTENT", "menu-editor/admin.$action.tpl");

			$q = $db->query("SELECT id, title FROM ".dbTable("menu_block")."
			ORDER BY title ASC", __FILE__, __LINE__);

			$menus = Array();
			while ($row = $db->fetchRow($q)) {
				saneHTML($row);
				$menus[$row["id"]] = $row["title"];
			}

			$actions = new tplButtonBox();
				$actions->add(createLink("index.php/module=admin/base=menu-editor", _L("L_MENU-EDITOR"), "menu-editor.png"));
				$actions->add(createLink("index.php/module=admin/base=menu-editor/action=create_menu", _L("L_CREATE_MENU"), "add.png"));
			$actions = $actions->make();

			$q = $db->query("
				SELECT id, menu_id, auth_access, title, icon, order_id, type, tooltip, level, link,
				is_absolute
				FROM ".dbTable("menu_link")." WHERE id = '{$id}'
			", __FILE__, __LINE__);

			$row = $db->fetchRow($q);

			$tpl->setArray ($row);
			$tpl->setArray (
				Array (
					"BLOCK_TITLE"		=> _L("L_MENU-EDITOR"),
					"BLOCK_ICON"		=> createIcon("menu-editor.png", 16),
					"ARR_MENU"			=> serialize($menus),
					"RETURN"			=> "index.php/module=admin/base=menu-editor",
					"ACTIONS"			=> $actions,
					"ACTION"			=> $action

				)
			);

			$mcBuff = $tpl->make();
		break;
		default:

			$tpl = new contentBlock();

			$tpl->insert("BLOCK_CONTENT", "menu-editor/admin.default.tpl");

			// requesting menus
			$q = $db->query("SELECT id, title, dropdown FROM ".dbTable("menu_block")."
			ORDER BY title ASC", __FILE__, __LINE__);

			while ($row = $db->fetchRow($q)) {
				saneHTML($row);

				$q2 = $db->query ("
				SELECT
					id, icon, title, link, level
				FROM ".dbTable("menu_link")."
				WHERE menu_id = '{$row["id"]}' ORDER BY order_id ASC
				", __FILE__, __LINE__);

				$tpl->reset("MENU.LINK");
				while ($row2 = $db->fetchRow($q2)) {

					$actions = new tplButtonBox();
						$actions->add(createLink("javascript:gekkoLinkPlacer.moveLeft(this)", _L("L_MOVE_LEFT"), "left.png", false, "icon"));
						$actions->add(createLink("javascript:gekkoLinkPlacer.moveRight(this)", _L("L_MOVE_RIGHT"), "right.png", false, "icon"));
						$actions->add(createLink("index.php/module=admin/base=menu-editor/action=edit_link?id={$row2["id"]}", _L("L_EDIT"), "edit.png", false, "icon"));
						$actions->add(createLink("modules/menu-editor/actions.php?action=delete_link&id={$row2["id"]}&auth={C_AUTH_STR}", _L("L_DELETE"), "delete.png", true, "icon"));
					$actions = $actions->make();

					$row2["actions"] =& $actions;

					$tpl->setArray($row2, "MENU.LINK");
					$tpl->saveBlock("MENU.LINK");
				}

				$actions = new tplButtonBox();
					$actions->add(createLink("index.php/module=admin/base=menu-editor/action=edit_menu?id={$row["id"]}", _L("L_EDIT"), "edit.png"));
					$actions->add(createLink("modules/menu-editor/actions.php?action=delete_menu&id={$row["id"]}&auth={C_AUTH_STR}", _L("L_DELETE"), "delete.png", true));
					$actions->add(createLink("index.php/module=admin/base=menu-editor/action=create_link?menu_id={$row["id"]}", _L("L_CREATE_LINK"), "new.png"));
					$actions->add(createLink("modules/menu-editor/actions.php?action=create_block&id={$row["id"]}&auth={C_AUTH_STR}", _L("L_CREATE_BLOCK"), "blocks.png", true));
				$actions = $actions->make();

				$row["actions"] =& $actions;

				$tpl->setArray ($row, "MENU");

				$tpl->saveBlock("MENU");
			}

			$actions = new tplButtonBox();
				$actions->add(createLink("index.php/module=admin/base=menu-editor/action=create_menu", _L("L_CREATE_MENU"), "add.png"));
			$actions = $actions->make();

			$tpl->setArray (
				Array (
					"BLOCK_TITLE"		=> _L("L_MENU-EDITOR"),
					"BLOCK_ICON"		=> createIcon("menu-editor.png", 16),
					"ACTIONS"			=> $actions
				)
			);

			$mcBuff = $tpl->make();
		break;
	}
?>