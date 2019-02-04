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

	appLoadLibrary("plugins.lib.php");

	varImport("size", "selected");

	$size = intval($size);

	$buff = "";

	$cache_key = _L("C_SITE.ICONTHEME").$size;
	$cache_key = cacheAssignKey($cache_key);

	$default_icons = Array();

	if (!cacheRead($cache_key, $default_icons)) {
		// fetching default icons
		$buff = listDirectory(GEKKO_SOURCE_DIR."media/icons/default/{$size}", true, true);
		foreach ($buff as $i) {
			if (preg_match("/^.*\.(png|gif|jpg)$/", $i))
				$default_icons[$i] = true;
		}
		// fetching icons within this theme
		$buff = listDirectory(GEKKO_SOURCE_DIR."media/icons/"._L("C_SITE.ICONTHEME")."/{$size}", true, true);
		foreach ($buff as $i)
			if (preg_match("/^.*\.(png|gif|jpg)$/", $i))
				$default_icons[$i] = true;
		cacheSave($cache_key, $default_icons);
	}

	$buff = "";
	$e = 0;
	$buff .= "<div class=\"iconchooser\">\n";
	while (list($icon) = each($default_icons)) {
		$url = fetchIcon($icon, $size);
		$buff .= "<img src=\"{$url}\" width=\"{$size}\" height=\"{$size}\" alt=\"{$icon}\" ".(($selected == $icon) ? " class=\"selected\" ": "")."/>";
		if ($e%16 == 15)
			$buff .= "<br />\n";
		$e++;
	}
	$buff .= "</div>";

	$appPluginHandler =& $GLOBALS["appPluginHandler"];
	$appPluginHandler->triggerEvent("template/afterParse", $buff);

	echo $buff;
?>