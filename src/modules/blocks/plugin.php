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

	appLoadLibrary ("blocks");

	Class blocksModulePlugin {
		var $events = Array(
			"format/textFormat"
		);
		function Invoke ($event, $buff) {
			if (gekkoModule::isInstalled("blocks")) {
				switch ($event) {
					case "format/textFormat":
						if (appAuthorize("blocks", $buff["groups"])) {
							$buff["content"] = preg_replace_callback("/\[block\](\d*)\[\/block\]/s",
								create_function('$a', 'return blocks::load($a[1]);'),
								$buff["content"]
							);
						}
					break;
				}
			}
			return $buff;
		}
	}
?>
