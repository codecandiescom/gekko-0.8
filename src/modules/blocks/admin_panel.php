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

	// custom icon
	$Panel->AddIcon(
		$Panel->Icon(
			"blocks.png",
			"index.php/module=admin/base=blocks",
			_L("L_MODULE_BLOCKS")
		),
		_L("L_SITE_CONTENTS")
	);

	$Panel->addBlock(
		Array (
			"title"			=> "Project news",
			"scriptpath"	=> "blocks/feed_reader.php",
			"scriptvars"	=> serialize (
				Array (
					"url"	=> "http://sourceforge.net/export/rss2_projnews.php?group_id=117004",
					"items"	=> 3,
					"type"	=> "rss"
				)
			)
		)
	);
?>
