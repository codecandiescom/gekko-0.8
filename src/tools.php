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

	
	define("IN-GEKKO", true);

	define ("GEKKO_USE_ABSOLUTE_URLS", true);
	
	require_once "conf.php";

	appLoadLibrary (
		"template.lib.php", "actions.lib.php"
	);

	dbInit();

	unset($file);

	varImport("action", "module");

	if ($action) {
		urlCheckVarType("alpha", $action);
		if ($module) {
			urlCheckVarType("alpha", $module);
			$file = GEKKO_MODULE_DIR."$module/tools/$action.php";
		} else {
			$file = GEKKO_LIB_DIR."tools/$action.php";
		}
	}
	
	if (isset($file) && file_exists($file)) {
		include $file;
	} else {
		appShutdown(_L("L_DISABLED_OR_UNEXISTENT_MODULE"));
	}

	dbExit();
?>
