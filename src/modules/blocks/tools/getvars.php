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

	appLoadLibrary ("blocks");

	if (!appAuthorize("blocks"))
		appAbort(accessDenied(true));

	varImport("block");

	if (isset($block)) {
		if (count($info = cacheFunc("Blocks::getData", "$block")) > 1) {
			$vars = Array();
			foreach ($info["variables"] as $var => $val) {
				$vars[] = "<label>$var:\n<input class=\"text\" type=\"text\" name=\"scriptvars[$var]\" size=\"40\" value=\"".htmlspecialchars($val)."\" /></label>";
			}
			echo implode("<hr />", $vars);
		}
	}
?>