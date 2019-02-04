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

	Class gekkoModule_Blocks {
		function createMenu() {
			// don't want a menu entry to be created
			return false;
		}
		function configure() {
			conf::getKey("blocks", "block_positions", "U,L,R,TC,DC,D,T,H", "s");
			conf::getKey("blocks", "admin_ignore_blocks", "L,R", "s");
			conf::getKey("blocks", "default_position", "L", "s");
			conf::getKey("blocks", "block_classes", "block,info,error,tbox1,tbox2,tbox3", "s");
		}
		function install() {

			$GLOBALS["db"]->query("
			CREATE TABLE ".dbTable("block")." (
				id SMALLINT (5) UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY (id),
				modules VARCHAR (255),

				icon VARCHAR (64),
				title VARCHAR (126),
				content TEXT,

				scriptpath VARCHAR (126),
				scriptvars TEXT,

				order_id SMALLINT (5) NOT NULL default 0,
				position VARCHAR (3),

				blockclass VARCHAR (32),

				csstext VARCHAR (128),

				author_id MEDIUMINT (8) UNSIGNED NOT NULL default 1,

				date_created DATETIME NOT NULL default '0000-00-00 00:00:00',
				date_modified TIMESTAMP,

				auth_access VARCHAR (255),

				status ENUM('0','1') NOT NULL default '1'

			)
			", __FILE__, __LINE__, true);

			gekkoModule::register($GLOBALS["cgdb"]["blocks"], "blocks", "#", $GLOBALS["cgdb"]["blocks"]);
		}
		function uninstall() {
			$GLOBALS["db"]->query("DROP TABLE IF EXISTS ".dbTable("block"), __FILE__, __LINE__, true);
		}
		function upgrade($provided_v) {
			$db =& $GLOBALS["db"];
			if (Packages::checkVersion("0.5.6", $provided_v) < 0) {
				conf::setKey("blocks", "block_positions", "U,L,R,TC,DC,D", "s");
			}
			if (Packages::checkVersion("0.5.9", $provided_v) < 0) {
				$db->query("ALTER TABLE ".dbTable("block")."
				CHANGE COLUMN status status ENUM('0','1') NOT NULL default '1'",
				__FILE__, __LINE__);
			}
			if (Packages::checkVersion("0.5.10", $provided_v) <= 0) {
				$db->query("ALTER TABLE ".dbTable("block")."
				ADD COLUMN authorid MEDIUMINT (8) UNSIGNED NOT NULL default 1 AFTER csstext",
				__FILE__, __LINE__);
			}
			if (packages::checkVersion("0.6.5", $provided_v) < 0) {
				$db->query("ALTER TABLE ".dbTable("block")."
				ADD COLUMN date_created DATETIME NOT NULL default '0000-00-00 00:00:00' AFTER creationtime,
				ADD COLUMN date_modified TIMESTAMP AFTER date_created,
				CHANGE COLUMN authorid author_id MEDIUMINT (8) UNSIGNED NOT NULL default 1,
				CHANGE COLUMN orderid order_id SMALLINT (5) NOT NULL default 0",
				__FILE__, __LINE__, true);

				$db->query("UPDATE ".dbTable("block")." SET date_created = FROM_UNIXTIME(creationtime)", __FILE__, __LINE__, true);

				$db->query("ALTER TABLE ".dbTable("block")."
					DROP COLUMN creationtime
				", __FILE__, __LINE__, true);
			}
		}
		function externalModuleUninstall($module) {

		}
	}
?>
