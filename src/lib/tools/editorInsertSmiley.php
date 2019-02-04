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

	appLoadLibrary ("packages", "format.lib.php");

	varImport("editor_id");

	if (!file_exists(GEKKO_SOURCE_DIR."media/smileys/"._L("C_SITE.SMILEYSTHEME")))
		_L("C_SITE.SMILEYSTHEME", "default");

	$abs_smileydir = GEKKO_SOURCE_DIR."media/smileys/"._L("C_SITE.SMILEYSTHEME")."/";
	$rel_smileydir = _L("C_SITE.REL_URL")."media/smileys/"._L("C_SITE.SMILEYSTHEME")."/";

	$xml = Array();

	if (file_exists($xmlfile = "{$abs_smileydir}package.xml")) {

		Packages::parseXML($xmlfile, $xml);

		$gbb = new GBBCodeParser($USER["groups"]);
		$i = 0;
		echo "<div class=\"gekkoEditorBox\" style=\"text-align: center\">";

		foreach ($xml["emoticon-map"] as $smiley) {

			$sample = preg_replace("/\s.*/", "", $smiley["sample"]);

			echo str_replace("<img ", "<img class=\"smiley\" ", $gbb->parse(htmlspecialchars($sample)))." ";

			if ($i%6 == 5)
				echo "<br />";
			$i++;
		}
		echo "</div>";

		echo "<small style=\"display: block\">{$xml["pkginfo"]["name"]} - {$xml["pkginfo"]["homepage"]}</small>";
	} else {
		trigger_error("Missing icon theme.");
	}
?>
