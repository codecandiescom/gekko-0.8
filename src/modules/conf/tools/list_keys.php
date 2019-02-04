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

	require_once GEKKO_SOURCE_DIR."lib/auth.inc.php";

	if (!appAuthorize("conf"))
		die(accessDenied());

	appLoadLibrary ("gzenc.lib.php");

	varImport("from");

	gzinit();

		header("content-type: text/html; charset=UTF-8");

		$q = $db->query("SELECT id, keyname, keyvalue, keytype FROM ".dbTable("conf")."
		WHERE module = '$from' ORDER BY keyname", __FILE__, __LINE__);

		// too lazy to use XML
		while ($row = $db->fetchRow($q))
			echo $row["id"]."|".rawurlencode($row["keyname"])."|".rawurlencode($row["keyvalue"])."|".rawurlencode($row["keytype"])."\n";

	gzoutput();
?>