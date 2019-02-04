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
			<title>Feed reader</title>
			<description>Read feeds from a given RSS or ATOM url</description>
			<author><![CDATA[xiam &lt;<a href="mailto:xiam@users.sourceforge.net">xiam@users.sourceforge.net</a>&gt;]]></author>
			<homepage><![CDATA[<a href="http://www.gekkoware.org">http://www.gekkoware.org</a>]]></homepage>
		</info>
		<variable name="items">5</variable>
		<variable name="type">rss</variable>
	</blockscript>
	<%%>
	*/

	appLoadLibrary (
		"feedreader.lib.php"
	);

	$db =& $GLOBALS["db"];

	$return = null;

	$cache_key = cacheAssignKey($url, $items);
	if (($life = cacheCheckLifetime($cache_key)) !== false) {
		if ((time()-$life) < 60*30) {
			cacheRead($cache_key, $return);
		}
	}

	if (!$return) {
		if ($url && ($type == "rss" || $type == "atom")) {

			$feed = ($type = "rss") ? new rss_reader : new atom_reader;

			$feed->set_limit($items);

			$feed->sync($url);

			$return = $feed->html;

			cacheSave($cache_key, $return);
		}
	}

?>
