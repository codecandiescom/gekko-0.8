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

	Class ajaxLinksPlugin {
		var $events = Array(
			// "template/afterParse"
		);
		function Invoke ($event, $buff) {
			switch ($event) {
				case "template/afterParse":
					$buff = preg_replace_callback (
							"/<a([^>]*)>/",
							create_function (
								'$a', 'return ajaxLinksPlugin::anchorAddOnClick($a);'
							),
							$buff);
				break;
			}
			return $buff;
		}

		function anchorAddOnClick($buff) {
			$attr = htmlSplitAttributes($buff[1]);
			if (isset($attr["onclick"])) {
				$attr["onclick"] .= ";";
			} else {
				$attr["onclick"] = "";
			}
			$attr["onclick"] .= "return gekkoAjaxLink(this);";
			return "<a ".htmljoinattributes($attr).">";
		}
	}
?>
