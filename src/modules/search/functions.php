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

	Class Search {
		function getModules() {
			$return = array("*" => _L("L_ANY"));
			$modules = gekkoModule::getList();

			foreach ($modules as $module)
				if (file_exists(GEKKO_SOURCE_DIR."modules/$module/include/search.php"))
					$return[$module] = _L("L_MODULE_".strtoupper($module));
			return $return;
		}
		function hightlight(&$text, $keywords = array(), $radio = 80) {
			$text = GekkoHTMLFilter::Strip($text);

			$func = create_function('&$a', '$a = preg_quote($a, "/");');
			array_walk($keywords, $func);

			$pattern = "/(.*)(.{{$radio}}?)(".implode("|", $keywords).")(.{{$radio}}?)(.*)/s";

			// higlight
			$func = create_function('$a',
				'return "...{$a[2]}<span class=\"highlight\">{$a[3]}</span>{$a[4]}...";');
			$text = preg_replace_callback($pattern, $func, $text);
		}
	}
?>
