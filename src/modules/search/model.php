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

	Class gekkoModule_Search {
		function createMenu() {
			return false;
		}
		function configure() {

		}
		function install() {
			gekkoModule::register($GLOBALS["cgdb"]["search"], "search", "*", $GLOBALS["cgdb"]["search"]);
		}
		function uninstall() {
		}
		function upgrade($provided_v) {
		
		}
		function externalModuleUninstall($module) {

		}
	}

?>