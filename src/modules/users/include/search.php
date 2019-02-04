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

	$p = $db->query("SELECT u.id, u.username, u.realname, u.date_login
	FROM ".dbTable("user")." u
	WHERE
	(u.realname LIKE '{$keywords}' OR u.username LIKE '{$keywords}')
	ORDER BY u.id",
	__FILE__, __LINE__, true);

?>