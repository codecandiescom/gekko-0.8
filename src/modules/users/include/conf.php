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

	// configuration stuff to be included in conf module
	
	if (is_object($modConf)) {
		$modConf->set (
			Array (
				"users:account.enable_self_registration" => false,
				"users:login.multi_session" => false,
				"users:account.require_confirmation" => false
			)
		);
	} else {
		trigger_error("\$modConf", E_USER_ERROR);
	}

?>