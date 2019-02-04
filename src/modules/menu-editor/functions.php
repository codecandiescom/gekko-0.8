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

	Class Menu_Editor {
		function getPossibleOrder($menu = 0) {
			if ($menu) {

				$order = Array(0);

				$q = $GLOBALS["db"]->query("SELECT order_id FROM ".dbTable("menu_link")."
				WHERE menu_id = '{$menu}'
				ORDER BY order_id ASC",
				__FILE__, __LINE__, true);


				while ($row = $GLOBALS["db"]->fetchRow($q))
					$order[$row["order_id"]] = $row["order_id"];

			} else {
				$order = Array(_L("L_TOP"), _L("L_BOTTOM"));
			}
			return $order;

		}

		function getLevels() {
			return array("0", "1", "2", "3");
		}

		function getLinkTypes() {
			return array(_L("L_NORMAL"), _L("L_STRIKE"), _L("L_HIGHLIGHT"));
		}
		function getMenuList() {
			$menus = Array();

			$q = $GLOBALS["db"]->query("SELECT id, title
			FROM ".dbTable("menu_block")."
			ORDER BY title DESC", __FILE__, __LINE__, true);

			while ($row = $GLOBALS["db"]->fetchRow($q))
				$menus[$row["id"]] = $row["title"];

			return $menus;
		}
		/**
		* evalLink(link, [is absolute?]);
		* @returns: modified version of link according to the current path
		*
		*/
		function evalLink($link, $is_absolute) {

			$link = str_replace("%index%/", "", $link);

			if (preg_match("/^([a-z]*):\/\/.*?/", $link)) {
				return $link;
			} else if ($is_absolute) {
				return ($link[0] == '/') ? $link : "/$link";
			} else {
				return urlEvalPrototype($link);
			}
		}

		/**
		* insertLink(menu id, title, link, icon, auth access, level)
		* Adds a link on the given menu
		*
		*/
		function insertLink($menu, $title, $link, $icon = "link.png", $auth_access = "*", $level = 0) {

			// getting order_id for appending at the bottom
			$q = $GLOBALS["db"]->query("SELECT order_id FROM ".dbTable("menu_link")." WHERE
			menu_id = '{$menu}' ORDER BY order_id DESC LIMIT 1", __FILE__, __LINE__, true);

			$order_id = 0;
			if ($GLOBALS["db"]->numRows($q)) {
				$row = $GLOBALS["db"]->fetchRow($q);
				$order_id = $row["order_id"];
			}
			$order_id++;

			// inserting into database
			$GLOBALS["db"]->query("
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
				auth_access
			) VALUES (
				'".$GLOBALS["db"]->findFreeId("menu_link")."',
				'{$menu}',
				'{$order_id}',
				'0',
				'{$level}',
				'{$title}',
				'{$link}',
				'',
				'{$icon}',
				'{$auth_access}'
			)
			", __FILE__, __LINE__, true);
		}

		/**
		* deleteLink(link)
		* Removes a link from any menu
		*
		*/
		function deleteLink($link) {
			return $GLOBALS["db"]->query("DELETE FROM ".dbTable("menu_link")."
			WHERE link = '{$link}'", __FILE__, __LINE__, true);
		}
	}

?>
