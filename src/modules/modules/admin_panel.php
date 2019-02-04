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

	$db =& $GLOBALS["db"];

	$q = $db->query("SELECT module FROM ".dbTable("module")."
	WHERE status = '1'", __FILE__, __LINE__, true);

	while ($row = $db->fetchRow($q)) {
		if (file_exists($xml = GEKKO_SOURCE_DIR."modules/{$row["module"]}/package.xml")) {
			preg_match("/<version>(.*?)<\/version>/", loadFile($xml), $pkg_version);
			if (isset($pkg_version[1])) {
				$pkg_version = $pkg_version[1];
				$mod_version = conf::getKey($row["module"], "_version");
				if (Packages::checkVersion($pkg_version, $mod_version)) {
					if (file_exists(GEKKO_SOURCE_DIR."install.php")) 
						appRedirect("install.php?mode=upgrade");
					else 
						appRedirect("index.php/module=admin/base=modules?message=".urlencode(_L("L_MODULE_UPGRADE_REQUIRED"))."&type=error");
					appAbort();
				}
			}
		}
	}

	$Panel->AddIcon(
		$Panel->Icon(
			"modules.png",
			"index.php/module=admin/base=modules",
			_L("L_MODULE_MODULES")
		),
		_L("L_ADVANCED_CONFIGURATION")
	);

?>
