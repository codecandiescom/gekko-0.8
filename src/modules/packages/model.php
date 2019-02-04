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

	Class gekkoModule_Packages {
		function createMenu() {
			// don't create a menu entry
			return false;
		}
		function configure() {
			conf::getKey("packages", "ftp_test_passed", false, "b");
		}
		function install() {
			gekkoModule::register($GLOBALS["cgdb"]["packages"], "packages", "#", $GLOBALS["cgdb"]["packages"]);
		}
		function uninstall() {
		}
		function upgrade($provided_v) {

		}
		function externalModuleUninstall($module) {

		}
	}
?>