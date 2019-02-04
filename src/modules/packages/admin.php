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

	Extract (
		urlImportMap("null/null/action=alpha?page=page&id=int")
	);

	switch ($action) {
		default:

			appLoadLibrary ("remote.lib.php");

			// we are going to use important passwords, it's better if we don't have
			// to worry about sniffers
			appEnableSSL();

			$banner = false;
			$constat = true;

			$test = new remoteConnection(conf::getkey("core", "ftp.host", "127.0.0.1", 's'), 21);

			if (!$test->status())
				$constat = false;
			else {
				$banner = $test->read();

				if (preg_match("/220\s(.*)/", $banner, $match) && isset($match[1])) {
					$banner = $match[1];
				} else {
					$constat = false;
				}
			}
			$test->close();

			$tpl = new contentBlock();

			$tpl->insert("BLOCK_CONTENT", "packages/admin.default.tpl");

			$actions = new tplButtonBox();
				$actions->add(createLink("index.php/module=admin/base=packages/action=repository", _L("L_MENU-EDITOR"), "menu-editor.png"));
			$actions = $actions->make();

			$tpl->setArray (
				Array (
					"BLOCK_TITLE"		=> _L("L_PACKAGES"),
					"BLOCK_ICON"		=> createIcon("packages.png", 16),
					"ACTION"			=> "install",
					"CONN_TEST_STATUS"	=> $constat,
					"SERVER_BANNER"		=> $banner,
					"ACTIONS"			=> $actions,
					"RETURN"			=> ""
				)
			);

			$mcBuff = $tpl->make();
		break;
	}
?>
