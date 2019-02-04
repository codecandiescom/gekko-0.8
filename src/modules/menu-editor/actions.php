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

	dbInit();

	require_once GEKKO_SOURCE_DIR."lib/auth.inc.php";

	varImport("action", "id", "return");

	if (isAdmin()) {
		switch ($action) {
			// inserts specified menu in a block
			case "create_block":
				if ($id) {
					appLoadLibrary("blocks");

					$block_id = Blocks::simpleCreate(_L("L_MENU"), "", "menu-editor/menu-loader.php", "id={$id}", "menu.png");

					$return = urlEvalPrototype("index.php/module=admin/base=blocks/action=edit?id={$block_id}", false, false);
				}
			break;
			case "update_position":
				varImport("positions");

				varRequire("positions");

				$menus = explode('|', $positions);

				foreach ($menus as $menu) {
					$menu = explode(':', $menu);
					$menu_id = $menu[0];

					$menu_links = isset($menu[1]) ? explode(',', $menu[1]) : array();

					$order_id = 0;
					foreach ($menu_links as $menu_link) {
						$menu_link = explode('-', $menu_link);
						$menu_link_id = $menu_link[0];
						$menu_link_level = $menu_link[1];

						$db->query("UPDATE ".dbTable("menu_link")." SET
							menu_id = '{$menu_id}',
							order_id = '{$order_id}',
							level = '{$menu_link_level}'
							WHERE id = '{$menu_link_id}'

						", __FILE__, __LINE__);

						$order_id++;
					}
				}

			break;
			case "delete_link":
				varRequire("id");

				// getting menu_id for redirection
				$q = $db->query("SELECT menu_id
				FROM ".dbTable("menu_link")." WHERE id = '{$id}'",
				__FILE__, __LINE__);
				$row = $db->fetchRow($q);

				// deleting item
				$db->query("DELETE FROM ".dbTable("menu_link")."
				WHERE id = '{$id}'", __FILE__, __LINE__);

				appWriteLog("menu-editor: deleted link (id='$id')", "actions", 2);
			break;
			case "create_link":

				varImport("menu", "auth_access", "icon", "title", "type",
				"link", "tooltip", "level", "order_id", "is_absolute");

				varRequire("menu", "title", "link");

				// allocating new element at bottom or top of the others
				$q = $db->query("SELECT order_id FROM ".dbTable("menu_link")." WHERE
				menu_id = '{$menu}' ORDER BY order_id ".(($order_id) ? "DESC": "ASC")." LIMIT 1");

				if ($db->numRows($q)) {
					$row = $db->fetchRow($q);
					// place this element at (order_id ? bottom : top)
					$order_id = $row["order_id"] + ($order_id ? 1 : -1);
				} else {
					// is the first element in this menu
					$order_id = 1;
				}

				$db->query("
					INSERT INTO ".dbTable("menu_link")." (
						id,
						menu_id,
						order_id,
						type,
						level,
						title,
						link,
						tooltip,
						icon,
						auth_access,
						is_absolute
					) VALUES (
						'".($id = $db->findFreeId("menu_link"))."',
						'{$menu}',
						'{$order_id}',
						'{$type}',
						'{$level}',
						'{$title}',
						'{$link}',
						'{$tooltip}',
						'{$icon}',
						'".groups::extractFromInput($auth_access)."',
						'{$is_absolute}'
					)
				", __FILE__, __LINE__);

				appWriteLog("menu-editor: created link (id='$id')", "actions", 2);
			break;
			case "edit_link":

				varImport("id", "menu", "auth_access", "icon", "title",
				"link", "tooltip", "level", "order_id", "is_absolute", "type");

				varRequire("id", "menu", "title", "link");

				// getting some information before modifying
				$q = $db->query("SELECT order_id, menu_id
				FROM ".dbTable("menu_link")."
				WHERE id = '{$id}'",
				__FILE__, __LINE__);

				$row = $db->fetchRow($q);

				if ($menu != $row["menu_id"]) {
					// menu_id was changed, placing new link at the bottom
					$q2 = $db->query("SELECT order_id FROM ".dbTable("menu_link")." WHERE
					menu_id = '{$menu}' ORDER BY order_id DESC LIMIT 1",
					__FILE__, __LINE__);

					if ($db->numRows($q2)) {
						$row = $db->fetchRow($q2);
						$order_id = $row["order_id"]+1;
					}
				} elseif ($order_id != $row["order_id"]) {
					// menu_id is still the same, order_id was changed.
					// swapping order_id with other elements to avoid order_id
					// duplication.
					$db->query("
					UPDATE ".dbTable("menu_link")." SET
						order_id = '{$row["order_id"]}'
					WHERE order_id = '{$order_id}'
					", __FILE__, __LINE__);
				}

				// updating
				$db->query("
					UPDATE ".dbTable("menu_link")." SET
						menu_id = '{$menu}',
						order_id = '{$order_id}',
						type = '{$type}',
						level = '{$level}',
						title = '{$title}',
						link = '{$link}',
						tooltip = '{$tooltip}',
						icon = '{$icon}',
						auth_access = '".groups::extractFromInput($auth_access)."',
						is_absolute = '{$is_absolute}'
					WHERE id = '{$id}'
				", __FILE__, __LINE__);

				appWriteLog("menu-editor: updated menu link (id='$id')", "actions", 2);
			break;
			case "create_menu":

				varImport("title", "hide_icons", "dropdown");

				varRequire("title");

				$db->query("
					INSERT INTO ".dbTable("menu_block")." (
						id,
						title,
						hide_icons
					) VALUES (
						".$db->findFreeId("menu_block").",
						'{$title}',
						'{$hide_icons}'
					)
				", __FILE__, __LINE__);

				appWriteLog("menu-editor: created menu (id='$id')", "actions", 1);
			break;
			case "edit_menu":

				varImport("id", "title", "hide_icons", "dropdown");

				varRequire("id", "title");

				$db->query("
					UPDATE ".dbTable("menu_block")." SET
						title = '{$title}',
						dropdown = '{$dropdown}',
						hide_icons = '{$hide_icons}'
					WHERE id = '{$id}'
				", __FILE__, __LINE__);

				appWriteLog("menu-editor: modified menu (id='$id')", "actions", 4);
			break;
			case "delete_menu":

				varRequire("id");

				// deleting menu
				$db->query("DELETE FROM ".dbTable("menu_block")."
				WHERE id = '{$id}'", __FILE__, __LINE__);

				// deleting associated links
				$db->query("DELETE FROM ".dbTable("menu_link")."
				WHERE menu_id = '{$id}'", __FILE__, __LINE__);

				$return = urlEvalPrototype("index.php/module=admin/base=menu-editor", false, false);

				appWriteLog("menu-editor: deleted menu id '$id'", "actions", 1);

			break;
			default:
				appAbort(_L("L_UNDEFINED_ACTION")." $action");
			break;
		}
	}

	appRedirect($return);

	dbExit();
?>
