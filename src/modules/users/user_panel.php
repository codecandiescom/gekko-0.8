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

	# Account icon
	$Panel->AddIcon(
		$Panel->Icon(
			"account.png",
			"index.php/module=users/action=account",
			_L("L_USER_SETTINGS")
		),
		_L("L_MY_ACCOUNT")
	);

	# Profile icon
	$Panel->AddIcon(
		$Panel->Icon(
			"profile.png",
			"index.php/module=users/action=profile",
			_L("L_MY_PROFILE")
		),
		_L("L_MY_ACCOUNT")
	);
?>