<?php

	/*
	*	Gekko - Open Source Web Development Framework
	*	------------------------------------------------------------------------
	*	Copyright (C) 2004-2006, J. Carlos Nieto <xiam@users.sourceforge.net>
	*	This program is Free Software.
	*
	*	@package	Gekko CMS
	*	@license	http://www.gnu.org/copyleft/gpl.html GNU/GPL License 2.0
	*	@author		J. Carlos Nieto <xiam@users.sourceforge.net>
	*	@link		http://www.gekkoware.org
	*/

	if (!defined("IN-GEKKO")) die("Get a life!");

	Class gekkoModule_Modules {
		function createMenu() {
			// don't want a menu entry
			return false;
		}
		function configure() {
			// default module
			$defmod = conf::getKey("modules", "default", "blog", "string");
			$try = array($defmod, "blog", "pages", "categories");
			for ($i = 0; isset($try[$i]) && !file_exists(GEKKO_SOURCE_DIR."modules/".$try[$i]);) {
				$defmod = isset($try[++$i]) ? $try[$i] : "";
			}
			conf::setKey("modules", "default", $defmod, "string");
		}

		function install() {
			// table structure
			$GLOBALS["db"]->query("
			CREATE TABLE ".dbTable("module")." (
				id SMALLINT (8) UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY (id),
				module VARCHAR (64) NOT NULL default '',
				auth_exec VARCHAR (255) NOT NULL default '',
				auth_manage VARCHAR (255) NOT NULL default '',
				modified INT (11) UNSIGNED NOT NULL default '0',
				hidden ENUM('0','1') NOT NULL default '0',
				status ENUM('0','1') NOT NULL default '1'
			)
			",__FILE__, __LINE__, true);

			if (defined("IN-INSTALL")) {
				// inserting basic modules

				$GLOBALS["db"]->query("
					INSERT INTO ".dbTable("module")." (
						id, module, auth_exec, auth_manage
					) VALUES (
						'{$GLOBALS["cgdb"]["modules"]}', 'modules', '#', '{$GLOBALS["cgdb"]["modules"]}'
					)
				",__FILE__, __LINE__, true);

				$GLOBALS["db"]->query("
					INSERT INTO ".dbTable("module")." (
						id, module, auth_exec, auth_manage
					) VALUES (
						'{$GLOBALS["cgdb"]["conf"]}', 'conf', '#', '{$GLOBALS["cgdb"]["conf"]}'
					)
				",__FILE__, __LINE__, true);

			} else {
				gekkoModule::register($GLOBALS["cgdb"]["admin"], "admin", "#", $GLOBALS["cgdb"]["admin"]);
			}
		}
		function uninstall() {
			$GLOBALS["db"]->query("DROP TABLE IF EXISTS ".dbTable("module"),__FILE__, __LINE__, true);
		}
		function upgrade($provided_v) {
			if (Packages::checkVersion("0.5.7", $provided_v) < 0) {
				$GLOBALS["db"]->query("ALTER TABLE ".dbTable("module")."
				CHANGE COLUMN status status ENUM('0','1') NOT NULL default '1',
				CHANGE COLUMN hidden hidden ENUM('0','1') NOT NULL default '0'",
				__FILE__, __LINE__);
			}
		}
		function externalModuleUninstall($module) {

		}
	}

?>