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

	// icon for being showed in user panel
	if (appAuthorize("#")) {
		$Panel->AddIcon(
			$Panel->Icon(
				"admin.png",
				"index.php/module=admin",
				_L("L_ADMIN_PANEL")
			),
			_L("L_ADMIN")
		);
	}
?>