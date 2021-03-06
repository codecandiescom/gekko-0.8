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

	$data = isset($_POST["data"]) ? $_POST["data"] : appAbort(accessDenied(true, __FILE__, __LINE__));
	$data = stripslashes($data);

	require_once GEKKO_SOURCE_DIR."lib/auth.inc.php";
	require_once GEKKO_SOURCE_DIR."lib/format.lib.php";

	format::style($data, $USER["id"], true);

	echo $data;
?>