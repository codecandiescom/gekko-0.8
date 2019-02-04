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
			<title>Powered by</title>
			<description><![CDATA[Shows banners for people and projects involved in creating Gekko]]></description>
			<author><![CDATA[xiam &lt;<a href="mailto:xiam@users.sourceforge.net">xiam@users.sourceforge.net</a>&gt;]]></author>
			<homepage><![CDATA[<a href="http://www.gekkoware.org">http://www.gekkoware.org</a>]]></homepage>
		</info>
	</blockscript>
	<%%>
	*/

	$banners = Array (
		Array (
			'url' => "http://www.php.net",
			'image' => 'media/powered-by-php.gif',
			'alt' => "Powered by PHP"
		),
		Array (
			'url' => "http://www.mysql.com",
			'image' => 'media/powered-by-mysql.gif',
			'alt' => "Powered by MySQL"
		),
		Array (
			'url' => "http://www.gimp.org",
			'image' => 'media/powered-by-gimp.png',
			'alt' => "Powered by The GIMP"
		),
		Array (
			'url' => "http://www.gekkoware.org",
			'image' => 'media/powered-by-gekko.png',
			'alt' => "Powered by Gekko"
		)
	);

	$return = Array();

	foreach ($banners as $banner) {
		$return[] = sprintf('<a href="%s"><img src="'._L('C_SITE.REL_URL').'%s" width="88" height="31" alt="%s" /></a>', $banner['url'], $banner['image'], $banner['alt']);
	}

	$return = sprintf('<div style="text-align: center">%s</div>', implode("\n ", $return));

?>