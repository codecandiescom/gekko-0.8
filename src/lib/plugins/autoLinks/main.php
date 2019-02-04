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

	Class autoLinksPlugin {
		 var $events = Array(
		 	"format/afterTextFormat"
		 );
		 function Invoke ($event, $buff) {
		 	switch ($event) {
				case "format/afterTextFormat":
					$buff = preg_replace_callback (
						"/(<a.*?\/a>)|(<[^>]*?>)|((http|ftp):\/\/(([a-z0-9\/\.~@:]+)(\?[a-z+0-9\-\/\.~%&=@:;#]+)?))/i",
						create_function('$a', 'return isset($a[3]) ? "<a href=\"{$a[0]}\">{$a[0]}</a>" : $a[0];'),
						$buff
					);

				break;
			}
			return $buff;
		}
	}
?>
