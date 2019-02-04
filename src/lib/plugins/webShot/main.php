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

	Class webShotPlugin {
		var $events = Array (
			"template/beforeParse"
		);
		function Invoke ($event, $buff) {
			switch ($event) {
				case "template/beforeParse":
					appLoadJavascript("js/bubble.js");
					appLoadJavascript("lib/plugins/webShot/webShot.js");
				break;
			}
			return $buff;
		}
	}
?>
