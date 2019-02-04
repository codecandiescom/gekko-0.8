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

	Class relativeURLsPlugin {
		var $events = Array(
			"template/afterParse"
		);
		function Invoke ($event, $buff) {
		 	switch ($event) {
				case "template/afterParse":
					if (!defined("GEKKO_USE_ABSOLUTE_URLS") || !GEKKO_USE_ABSOLUTE_URLS) {
						// replacing absolute uris with relative ones
						$buff = preg_replace("/(<[^>]*)=\"(".preg_quote(_L("C_SITE.URL"), "/").")([^>]*>)/",
						"\\1=\""._L("C_SITE.REL_URL")."\\3", $buff);
					}
				break;
			}
			return $buff;
		}
	}
?>
