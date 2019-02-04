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

	Class ajaxFormsPlugin {
		var $events = Array (
			"template/afterParse"
		);
		function Invoke($event, $buff) {
			switch ($event) {
				case "template/afterParse":
					$buff = preg_replace_callback (
							"/<form([^>]*)>/",
							create_function(
								'$a', 'return ajaxFormsPlugin::formAddOnSubmit($a);'
							),
							$buff);
				break;
			}
			return $buff;
		}
		function formAddOnSubmit($buff) {
			$attr = htmlSplitAttributes($buff[1]);
			if (isset($attr["onsubmit"])) {
				$attr["onsubmit"] .= ";";
			} else {
				$attr["onsubmit"] = "";
			}
			$attr["onsubmit"] .= "return gekkoAjaxFormSubmit(this);";
			return "<form ".htmljoinattributes($attr).">";
		}
	}
?>
