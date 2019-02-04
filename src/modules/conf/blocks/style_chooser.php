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

	if (!defined("IN-BLOCK")) die();

	/*
	<%%>
	<?xml version="1.0" encoding="UTF-8"?>
	<blockscript>
		<info lang="en">
			<title>Style</title>
			<description>Allows the visitor to change Gekko's stylesheet (for his own browsing, preferences are stored in a cookie)</description>
			<author><![CDATA[xiam &lt;<a href="mailto:xiam@users.sourceforge.net">xiam@users.sourceforge.net</a>&gt;]]></author>
			<homepage><![CDATA[<a href="http://www.gekkoware.org">http://www.gekkoware.org</a>]]></homepage>
		</info>
		<variable name="items">3</variable>
	</blockscript>
	<%%>
	*/

	appLoadJavascript("js/list.js", "js/scroll.js");

	if (!isset($GLOBALS["style_chooser"])) {
		$script = "
		function styleChooserPopulate(myString) {
			var imgArray = myString.split('$');

			var imgList = new Array(imgArray.length);

			for (i = 0; i < imgArray.length; i++) {
				imgInfo = imgArray[i].split(':');

				var newElement = document.createElement('img');
				newElement.src = '"._L("C_SITE.REL_URL")."templates/'+imgInfo[0]+'/_themes/'+imgInfo[1]+'/thumb.png';
				newElement.stylePath = imgInfo[0]+'/'+imgInfo[1];
				newElement.title = imgInfo[1];
				newElement.width = imgInfo[2];
				newElement.height = imgInfo[3]
				newElement.className = 'photo';
				newElement.style.display = 'block';
				newElement.style.cursor = 'pointer';

				newElement.onclick = function() { location.href = '".urlSetArgs("style='+this.stylePath+'")."' };

				imgList[i] = newElement;
			}
			imgList = listCreate(imgList);

			return imgList;
		}
		";

		pageSetMarkContent("HEAD", "<script type=\"text/javascript\">\n{$script}\n</script>");

		$GLOBALS["style_chooser"] = 0;
	}

	$style_chooser = intval($GLOBALS["style_chooser"]);

	$return = array();
	$templates = listDirectory(GEKKO_TEMPLATE_DIR);
	
	$index = $selected = 0;
	foreach ($templates as $template) {
		$styles = listDirectory(GEKKO_TEMPLATE_DIR.$template."/_themes");

		foreach ($styles as $style) {
			if ($style[0] != '.') {
				$thumb = GEKKO_TEMPLATE_DIR."$template/_themes";
				$size = @getimagesize($thumb);

				if ($size) {
					list($width, $height) = $size;
				} else {
					$width = 140;
					$height = 103;
				}
				if (isset($_GET["style"])) {
					if ("$template/$style" == $_GET["style"]) {
						$selected = $index;
					}
				} else if (_L("C_SITE.TEMPLATE")."/"._L("C_SITE.STYLESHEET") == $style)
					$selected = $index;

				$return[] = "$template:$style:$width:$height";

				$index++;
			}
		}
	}

	$return = implode("$", $return);
	
	$return = "
	<div style=\"text-align:center\">
		<button onclick=\"gekkoScrollMove('styleChooser_{$style_chooser}', styleChooserContents_{$style_chooser}, 1, {$items})\">".createIcon("up.png")."</button>
		<button onclick=\"gekkoScrollMove('styleChooser_{$style_chooser}', styleChooserContents_{$style_chooser}, -1, {$items})\">".createIcon("down.png")."</button>

		<div style=\"text-align: center\" id=\"styleChooser_{$style_chooser}\"></div>

		<button onclick=\"gekkoScrollMove('styleChooser_{$style_chooser}', styleChooserContents_{$style_chooser}, 1, {$items})\">".createIcon("up.png")."</button>
		<button onclick=\"gekkoScrollMove('styleChooser_{$style_chooser}', styleChooserContents_{$style_chooser}, -1, {$items})\">".createIcon("down.png")."</button>
	</div>
	<script type=\"text/javascript\">
		styleChooserContents_{$style_chooser} = styleChooserPopulate('$return');
		gekkoScrollDisplay('styleChooser_{$style_chooser}', styleChooserContents_{$style_chooser}, {$items});
		gekkoScrollMove('styleChooser_{$style_chooser}', styleChooserContents_{$style_chooser}, ".(-1*$selected).", {$items});
	</script>
	";

	$GLOBALS["style_chooser"]++;

?>
