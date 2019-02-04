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

	appLoadLibrary (
		"template.lib.php", "gzenc.lib.php", "users"
	);

	varImport("selected", "input_id", "popup_id", "icon_id");

	$tpl = new blockWidget();

	$tpl->beginCapture();
?>
<form action="" onsubmit="document.buff.get('{V_ICON_ID}').src = document.buff.get('{V_INPUT_ID}').value = document.getElementById('avatar_url').value; gekkoPopup.close('{V_POPUP_ID}'); return false;">
	<h4>{L_CUSTOM_AVATAR}</h4>
	<div class="tbox-1">
		<label>
			{L_URL}:
			<input id="avatar_url" type="text" class="text" value="{V_SELECTED}" />
		</label>
		<div class="buttons">
			<button type="submit">{=createIcon("ok.png")=} {L_OK}</button>
		</div>
	</div>
	<h4>{L_LOCAL_AVATARS}</h4>
	<table style="border: none; margin: 0px; padding: 0px;">
	<tr><td>
	<!--{bgn: AVATAR}-->
	<div class="{V_SW_CLASS}" style="text-align: center; display:block; width: 80px; height: 170px; float: left; <!--if($V_SELECTED)-->border: 1px solid #000;<!--endif-->">
		<img class="avatar" style="cursor: pointer" alt="{V_AVATAR}" src="{=users::getAvatarURL("{V_AVATAR}")=}" width="50" height="50" />
		<small>{V_DESCRIPTION}</small>
	</div>
	<!--{end: AVATAR}-->
	</td></tr></table>
</form>
<?php
	$tpl->endCapture();

	$avatars = users::listAvatars();

	foreach ($avatars as $avatar => $desc) {
		$tpl->setArray (
			Array (
				"AVATAR" => $avatar,
				"SELECTED" => ($avatar == $selected),
				"DESCRIPTION" => $desc
			),
			"AVATAR"
		);
		$tpl->saveBlock("AVATAR");
	}

	$tpl->setArray (
		Array (
			"INPUT_ID" => intval($input_id),
			"ICON_ID" => intval($icon_id),
			"POPUP_ID" => intval($popup_id),
			"SELECTED" => $selected
		)
	);

	gzinit();
		header("content-type: text/html; charset=UTF-8");
		echo $tpl->make();
	gzoutput();
?>