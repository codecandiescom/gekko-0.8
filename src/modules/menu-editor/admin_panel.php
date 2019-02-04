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

	// administration panel icon
	$Panel->AddIcon(
		$Panel->Icon(
			"menu-editor.png",
			"index.php/module=admin/base=menu-editor",
			_L("L_MODULE_MENU-EDITOR")
		),
		_L("L_SITE_CONTENTS")
	);

?>