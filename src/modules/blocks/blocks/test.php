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

	// please keep this line, it's a security check
	if (!defined("IN-BLOCK")) die();

	/*
	<%%>
	<?xml version="1.0" encoding="UTF-8"?>
	<blockscript>
		<!-- You may use another language code for the information below -->
		<info lang="en">
			<title>Test Block</title>
			<description>This is a test block for programmers, try to set different variable values</description>
			<author><![CDATA[xiam &lt;<a href="mailto:xiam@users.sourceforge.net">xiam@users.sourceforge.net</a>&gt;]]></author>
			<homepage><![CDATA[<a href="http://www.gekkoware.org">http://www.gekkoware.org</a>]]></homepage>
		</info>
		<!-- Those variables are setted by default -->
		<variable name="foo"><![CDATA[<b>script</b>]]></variable>
		<variable name="bar"><![CDATA[<b>working</b>]]></variable>
	</blockscript>
	<%%>
	*/

	// yes, that's all!, you only need to set this block output in
	// $return variable, isn't it simple?.

	$return = "This $foo is $bar!";
?>