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

	Class gekkoModule_Menu_Editor {
		function createMenu() {
			// don't want a menu entry to be created
			return false;
		}
		function configure() {
			blocks::renameScript("menu-editor/menu-loader.php", "menu-editor/view.php");
		}
		function install() {

			$GLOBALS["db"]->query("
			CREATE TABLE ".dbTable("menu_block")." (
				id SMALLINT (5) UNSIGNED NOT NULL default '0',
				title VARCHAR (128),
				dropdown ENUM('0', '1') default '0',
				hide_icons ENUM('0','1') NOT NULL default '0'
			)
			",__FILE__, __LINE__, true);

			$GLOBALS["db"]->query("
			CREATE TABLE ".dbTable("menu_link")." (
				id MEDIUMINT (8) UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY (ID),
				menu_id SMALLINT (5) UNSIGNED NOT NULL default '0',
				order_id SMALLINT (5),
				type TINYINT (1) UNSIGNED NOT NULL default '0',
				level TINYINT (1) UNSIGNED NOT NULL default '0',
				title VARCHAR (128),
				link VARCHAR (255),
				tooltip VARCHAR (255),
				icon VARCHAR (32),
				is_absolute ENUM('0', '1') default '0',
				auth_access VARCHAR (255) NOT NULL default '*'
			)
			",__FILE__, __LINE__, true);

			gekkoModule::register($GLOBALS["cgdb"]["menu-editor"], "menu-editor", "#", $GLOBALS["cgdb"]["menu-editor"]);
		}
		function uninstall() {
			$GLOBALS["db"]->query("DROP TABLE IF EXISTS ".dbTable("menu_link"),__FILE__, __LINE__, true);
			$GLOBALS["db"]->query("DROP TABLE IF EXISTS ".dbTable("menu_block"),__FILE__, __LINE__, true);
		}
		function upgrade($provided_v) {
			$db =& $GLOBALS["db"];

			if (Packages::checkVersion("0.5.6", $provided_v) < 0) {
				$db->query("ALTER TABLE ".dbTable("menu_block")."
				ADD opt_noicons ENUM('0','1') NOT NULL default '0' AFTER type",
				__FILE__, __LINE__);
			}
			if (Packages::checkVersion("0.5.9", $provided_v) < 0) {
				$db->query("ALTER TABLE ".dbTable("menu_block")."
				CHANGE COLUMN opt_noicons opt_noicons ENUM('0','1') NOT NULL default '0'",
				__FILE__, __LINE__);
			}
			if (Packages::checkVersion("0.5.20", $provided_v) < 0) {

				// adding a new column
				$db->query("ALTER TABLE ".dbTable("menu_link")."
				ADD is_absolute ENUM('0', '1') default '0' AFTER icon",
				__FILE__, __LINE__);

				// link format has changed to switch easily between pretty and normal URLs
				$db->query("SELECT id, link FROM ".dbTable("menu_link")."",
				__FILE__, __LINE__);

				while ($row = $db->fetchRow()) {
					$link = $row["link"];
					if (preg_match("/%index%/", $link)) {
						$link = str_replace("%index%/", "", $link);

						$link = explode("/", $link);
						if (isset($link[1])) $link[1] = "module={$link[1]}";
						if (isset($link[2])) $link[2] = "action={$link[2]}";
						$link = implode("/", $link);

						$db->query("UPDATE ".dbTable("menu_link")."
						SET link='{$link}' WHERE id = '{$row["id"]}'", __FILE__, __LINE__,
						true);
					}
				}

				$db->query("ALTER TABLE ".dbTable("menu_block")."
				ADD dropdown ENUM('0','1') NOT NULL default '0' AFTER title,
				DROP COLUMN type",
				__FILE__, __LINE__);
			}
			if (packages::checkVersion("0.6.1", $provided_v) < 0) {

				$db->query("ALTER TABLE ".dbTable("menu_block")."
					CHANGE COLUMN opt_noicons hide_icons ENUM('0','1') NOT NULL default '0'
				", __FILE__, __LINE__);

				$db->query("ALTER TABLE ".dbTable("menu_link")."
					CHANGE COLUMN menulink menu_id SMALLINT (5) UNSIGNED NOT NULL default '0',
					CHANGE COLUMN orderid order_id SMALLINT (5)
				", __FILE__, __LINE__);

			}
		}
		function externalModuleUninstall($module) {

		}
	}
?>
