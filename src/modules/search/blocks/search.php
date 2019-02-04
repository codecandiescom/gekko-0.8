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
			<title>Search</title>
			<description>Shows a form to search within this site.</description>
			<author><![CDATA[xiam &lt;<a href="mailto:xiam@users.sourceforge.net">xiam@users.sourceforge.net</a>&gt;]]></author>
			<homepage><![CDATA[<a href="http://www.gekkoware.org">http://www.gekkoware.org</a>]]></homepage>
		</info>
	</blockscript>
	<%%>
	*/

	appLoadLibrary ("search");

	$tpl = new BlockWidget();
	$tpl->load("search/search.block.tpl");
	$tpl->setArray (
		Array (
			"SEARCH_TERMS"	=> "",
			"BASE"			=> "*"
		)
	);
	$return = $tpl->make();
?>