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

	Class gekkoModule_Groups {
		function createMenu() {
			return false;
		}
		function configure() {

		}
		function install() {

			$GLOBALS["db"]->query("
			CREATE TABLE ".dbTable("group")." (
				id SMALLINT (5) UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY (id),
				groupname VARCHAR (32),
				description VARCHAR (128),
				status ENUM('0','1') NOT NULL default '1',
				includegroups TEXT
			)
			",__FILE__, __LINE__, true);

			foreach ($GLOBALS["cgdb"] as $group => $id) {
				if (file_exists(GEKKO_SOURCE_DIR."modules/$group") || $id >= 500) {
					$GLOBALS["db"]->query("
						INSERT INTO ".dbTable("group")." (
							id,
							groupname,
							description
						) VALUES (
							'{$id}',
							'{$group}',
							'".ucfirst($group)."'
						)
					", __FILE__, __LINE__, true);
				}
			}

			gekkoModule::register($GLOBALS["cgdb"]["groups"], "groups", "#", $GLOBALS["cgdb"]["groups"]);
		}
		function uninstall() {
			$GLOBALS["db"]->query("DROP TABLE IF EXISTS ".dbTable("group"),__FILE__, __LINE__, true);
		}
		function upgrade($provided_v) {
			if (Packages::checkVersion("0.5.7", $provided_v) < 0) {
				$GLOBALS["db"]->query("ALTER TABLE ".dbTable("group")."
				CHANGE COLUMN status status ENUM('0','1') NOT NULL default '1'");
			}
		}
		function externalModuleUninstall($module) {

		}
	}
?>