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

	// advanced configuration
	$Panel->AddIcon(
		$Panel->Icon(
			"conf.png",
			"index.php/module=admin/base=conf",
			_L("L_CONF")
		),
		_L("L_ADVANCED_CONFIGURATION")
	);

	// advanced configuration
	$Panel->AddIcon(
		$Panel->Icon(
			"plugins.png",
			"index.php/module=admin/base=conf/action=plugins",
			_L("L_PLUGINS")
		),
		_L("L_ADVANCED_CONFIGURATION")
	);	

	// setup
	$Panel->AddIcon(
		$Panel->Icon(
			"setup.png",
			"index.php/module=admin/base=conf/action=setup",
			_L("L_BASIC_SETUP")
		),
		_L("L_ADMIN_TASKS")
	);


	// clean cache
	$Panel->AddIcon(
		$Panel->Icon(
			"cache.png",
			"index.php/module=admin/base=conf/action=clean_cache",
			_L("L_CLEAN_CACHE")
		),
		_L("L_ADMIN_TASKS")
	);

	// clean cache
	$Panel->AddIcon(
		$Panel->Icon(
			"looknfeel.png",
			"index.php/module=admin/base=conf/action=looknfeel",
			_L("L_LOOKNFEEL")
		),
		_L("L_ADMIN_TASKS")
	);
?>
