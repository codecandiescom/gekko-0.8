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

	require_once GEKKO_SOURCE_DIR."lib/format.lib.php";

	Class msiePNGFixPlugin {

		var $events = Array(
			"template/afterParse"
		);

		function Invoke ($event, $buff) {
			switch ($event) {
				case "template/afterParse":
					// ugly hacks for an ugly browser
					if (ereg("MSIE", $_SERVER["HTTP_USER_AGENT"]) && !ereg("Opera", $_SERVER["HTTP_USER_AGENT"])) {
						$buff = preg_replace_callback(
							"/<img(.*?)>/",
							create_function (
								'$a',
								'return msiePNGFixPlugin::fixImage($a);'
							),
							$buff);
					}
				break;
			}
			return $buff;
		}

		function fixImage($buff) {

			$attr = htmlsplitattributes(stripslashes($buff[1]));

			if (isset($attr["src"]) && getFileExtension($attr["src"]) == "png") {
				// image is png, adding garbage for msie to apply alpha channel

				// avoid problems when style attribute is set
				if (isset($attr["style"])) {
					if (substr($attr["style"], -1) != ";") $attr["style"] .= ";";
				} else {
					$attr["style"] = "";
				}
				// dimensions
				foreach (Array("width", "height") as $d) {
					if (isset($attr[$d]) && !ereg("$d\:", $attr["style"])) {
						$attr["style"] .= "$d:{$attr["$d"]}px;";
					}
				}
				$attr["style"] .= "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{$attr["src"]}', sizingMethod='crop');";
				// reemplazing src with a transparent 1x1 gif
				$attr["src"] = _L("C_SITE.REL_URL")."media/blank.gif";
			}
			return "<img ".htmljoinattributes($attr)."/>";
		}
	}
?>
