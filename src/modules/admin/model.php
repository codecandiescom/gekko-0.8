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

	Class gekkoModule_Admin {
		function createMenu() {
			// creating custom menu link
			Menu_Editor::insertLink(2, "{L_ADMIN}", "index.php/module=admin", "admin.png", "#");
			return true;
		}
		function configure() {
			conf::getkey('admin', 'strict_panel_permissions', 0, 'b');
			conf::getkey('blocks', 'admin_ignore_blocks', 'L,R', 's');
		}
		function install() {
			// registering 'admin' group
			gekkoModule::register($GLOBALS["cgdb"]["admin"], "admin", "#", $GLOBALS["cgdb"]["admin"]);
		}
		function uninstall() {

		}
		function upgrade($provided_v) {

		}
		function externalModuleUninstall($module) {

		}
	}
?>