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

	if (!appAuthorize("#"))
		appAbort(accessDenied());

	appLoadLibrary (
		"template.lib.php", "gzenc.lib.php"
	);

	varImport("selected", "object_id", "popup_id", "authwizard");

	define ("TABLE_MAX_ROWS", 4);

	// selected groups
	$selected = explode(",", $selected);

	// requesting the groups and theirs descriptions
	$q = $db->query (
		"SELECT id, groupname, description FROM ".dbTable("group")." ORDER BY id ASC",
		__FILE__, __LINE__
	);

	$rows = Array();
	while ($row = $db->fetchRow($q)) {
		if ($authwizard && (intval($row["id"]) >= 500))
			continue;
		$rows[] = $row;
	}

	$n = count($rows);

	if ($authwizard) {
		$default = Array (
			Array ("id" => "*", "groupname" => _L("L_GROUP_ALL"), "description" => _L("L_GROUP_ALL")),
			Array ("id" => "#", "groupname" => _L("L_GROUP_MANAGEMENT"), "description" => _L("L_GROUP_MANAGEMENT")),
			Array ("id" => "$", "groupname" => _L("L_GROUP_ACCOUNT"), "description" => _L("L_GROUP_ACCOUNT")),
			Array ("id" => "?", "groupname" => _L("L_GROUP_VISITOR"), "description" => _L("L_GROUP_VISITOR")),
		);
		$rows = array_merge($default, $rows);
	}


	$tpl = new blockWidget();

	$tpl->beginCapture();
?>
<form action="" onsubmit="return false;">
	<table>
	<!--{bgn: ROW}-->
		<tr>
		<!--{bgn: GROUP}-->
			<td style="vertical-align: middle; text-align: center">
			{=createCheckBox("group[]", "", "{V_CHECKED}","{V_GROUPNAME} ({V_ID})")=}
			</td>
			<td class="{V_SW_CLASS}"><i>({V_ID})</i> - <b>{V_GROUPNAME}</b><br /><span class="small">{V_DESCRIPTION}</span></td>
		<!--{end: GROUP}-->
		</tr>
	<!--{end: ROW}-->
	</table>
	<div class="buttons">
		<button type="button" onclick="gekkoForms.checkboxUpdate(this, '{V_OBJECT_ID}'); gekkoPopup.close('{V_POPUP_ID}')">{=createIcon("ok.png")=} {L_OK}</button>
		<button type="reset">{=createIcon("reset.png")=} {L_RESET}</button>
		<button type="button" onclick="gekkoPopup.close('{V_POPUP_ID}'); return false;">{=createIcon("cancel.png")=} {L_CANCEL}</button>
	</div>
</form>
<?php
	$tpl->endCapture();

	// table cells
	$i = 0;
	foreach ($rows as $row) {
		if ($i == TABLE_MAX_ROWS) {
			$tpl->saveBlock("ROW");
			$tpl->reset("ROW.GROUP");
			$i = 0;
		}
		$row["CHECKED"] = in_array($row["id"], $selected);
		$tpl->setArray($row, "ROW.GROUP");
		$tpl->saveBlock("ROW.GROUP");
		$i++;
	}
	if ($i)
		$tpl->saveBlock("ROW");

	$tpl->setArray (
		Array (
			"OBJECT_ID" => htmlspecialchars($object_id),
			"POPUP_ID" => htmlspecialchars($popup_id)
		)
	);

	gzinit();
		header("content-type: text/html; charset=UTF-8");
		echo $tpl->make();
	gzoutput();
?>