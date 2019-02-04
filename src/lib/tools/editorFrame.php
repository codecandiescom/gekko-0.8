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

	varImport("auth", "path", "editor_id", "data");

	if ($auth) {
		$cache_key = md5($path).'-'.$editor_id.'@'.getIP().".cache";
		$fp = fopen(GEKKO_CACHEDIR.$cache_key, "w");
		fwrite($fp, gzcompress(stripslashes($data)));
		fclose($fp);
		dbExit();
		exit();
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<link rel="stylesheet" type="text/css" href="templates/default/_layout/main.css">
	<link rel="stylesheet" type="text/css" href="templates/<?= _L("C_SITE.TEMPLATE"); ?>/_themes/<?= _L("C_SITE.STYLESHEET"); ?>/theme.css">
	<script type="text/javascript">
		document.editorId = <?=intval($_GET["editor_id"])?>;
	</script>
</head>
<body onload="window.parent.gekkoEditor.designMode(document)" style="cursor:text;margin:0px;padding:5px;background:#fff;font-size:small;">
</body>
</html>
