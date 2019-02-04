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
			<title>Menu</title>
			<description>Shows a menu giving its ID</description>
			<author><![CDATA[xiam &lt;<a href="mailto:xiam@users.sourceforge.net">xiam@users.sourceforge.net</a>&gt;]]></author>
			<homepage><![CDATA[<a href="http://www.gekkoware.org">http://www.gekkoware.org</a>]]></homepage>
			<variable name="id">Menu ID</variable>
		</info>
		<variable name="id">1</variable>
	</blockscript>
	<%%>
	*/

	appLoadLibrary (
		"menu-editor"
	);

	$db =& $GLOBALS["db"];

	$q = $db->query("SELECT title, hide_icons, dropdown
	FROM ".dbTable("menu_block")." WHERE id = '{$id}'",
	__FILE__, __LINE__, true);

	$parent = $db->fetchRow($q);

	$block["title"] = $parent["title"];

	$q = $db->query ("
		SELECT
			type, title, link, tooltip, icon, auth_access, level, is_absolute
		FROM ".dbTable("menu_link")."
		WHERE menu_id = '{$id}' ORDER BY order_id ASC
	", __FILE__, __LINE__, true);

	$return = Array();

	appLoadLibrary("menu.lib.php");

	$menu = new Gekko_Menu();

	$menu->set("dropdown", $parent["dropdown"]);

	if ($db->numRows($q)) {

		while ($row = $db->fetchRow($q)) {

			if (appAuthorize($row["auth_access"])) {

				// requires an icon?
				$row["icon"] = ($parent["hide_icons"] ? '' : createIcon($row["icon"], 16));

				// creating html link
				$row["link"] = sprintf('<a href="%s" title="%s">%s</a>',
					Menu_Editor::evalLink($row["link"], $row["is_absolute"]),
					$row["tooltip"] ? $row["tooltip"] : $row["title"],
					$row["icon"].lang::parse($row["title"])
				);

				// adding item to menu
				$menu->additem($row["link"], $row["level"]);
			}
		}

	}

	// compiling menu
	$return = $menu->make();

	// dont want this menu to be
	//$block["blockskeleton"] = "contentonly.block";
?>