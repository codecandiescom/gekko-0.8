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
	
	if (!GEKKO_SUBDOMAIN_MODE) {
		$Panel->AddIcon(
			$Panel->Icon(
				"packages.png",
				"index.php/module=admin/base=packages",
				_L("L_MODULE_PACKAGES")
			),
			_L("L_ADVANCED_CONFIGURATION")
		);
	}

?>
