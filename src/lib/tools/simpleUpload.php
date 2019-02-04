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

	require_once GEKKO_SOURCE_DIR."lib/auth.inc.php";

	// yes, you're required to be an administrator :)
	if (!appAuthorize("#"))
		appAbort(accessDenied());

	appLoadLibrary ("gzenc.lib.php");

	varImport("path", "buff", "browser");

	if (!preg_match("/\.\./", $path) && isset($_FILES["file"])) {

		appLoadLibrary ("file.lib.php");

		$file = $_FILES["file"];

		$basename = allocateBasename(GEKKO_DATA_DIR."$path", $file["name"]);

		move_uploaded_file($file["tmp_name"], GEKKO_DATA_DIR."$path/$basename");

		die ('
		<script type="text/javascript">
			window.parent.gekkoBrowser.reload(window.parent.document.buff.get('.(int)$browser.'));
		</script>'
		);
	}

	$tpl = new blockWidget();

	$tpl->beginCapture();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<link rel="stylesheet" type="text/css" href="templates/default/_layout/main.css">
	<link rel="stylesheet" type="text/css" href="templates/{C_SITE.TEMPLATE}/_themes/{C_SITE.STYLESHEET}/theme.css">
</head>
<body style="padding:0px;margin:0px;">
	<div style="text-align: center; padding: 10%; background: #fff;">
		<h1>{L_UPLOAD_FILE}</h1>
		<form action="{C_SITE.URL}tools.php?action=simpleUpload&amp;path={V_PATH}&amp;buff={V_BUFF}&amp;browser={V_BROWSER}" method="post" enctype="multipart/form-data">
			<input class="input" type="file" name="file" />
			<div class="buttons">
				<button type="submit">{=createIcon("upload.png")=} {L_SUBMIT}</button>
				<button type="button" onclick="window.parent.gekkoBrowser.reload(window.parent.document.buff.get({V_BROWSER}))">{=createIcon("cancel.png")=} {L_CANCEL}</button>
			</div>
		</form>
	</div>
</body>
</html>
<?php
	$tpl->endCapture();

	$tpl->setArray (
		Array (
			"PATH" => $path,
			"BUFF" => $buff,
			"BROWSER" => $browser
		)
	);

	gzinit();
		echo $tpl->make();
	gzoutput();
?>
