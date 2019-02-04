<?php

	/*
	*	Gekko - Open Source Web Development Framework
	*	------------------------------------------------------------------------
	*	Copyright (C) 2004-2006, J. Carlos Nieto <xiam@users.sourceforge.net>
	*	This Program is Free Software.
	*
	*	@package	Gekko
	*	@license	http://www.gnu.org/copyleft/gpl.html GNU/GPL License 2.0
	*	@author		J. Carlos Nieto <xiam@users.sourceforge.net>
	*	@link		http://www.gekkoware.org
	*/

	if (!defined("IN-GEKKO")) die("Get a life!");

	// URL Prototype
	Extract (
		urlImportMap("null/base=alpha")
	);

	// default conf. variables
	$strict_permissions = conf::getkey('admin', 'strict_panel_permissions', 1, 'b');

	// this will check for administrative privileges
	if (appAuthorize("#")) {
		if ($base) {
			if ($mc->exists($base, false)) {

				$q = $db->query("
				SELECT
					auth_manage
				FROM ".dbTable("module")."
				WHERE module = '{$base}'", __FILE__, __LINE__);

				$row = $db->fetchRow($q);

				$isAdmin = appAuthorize($row["auth_manage"]);

				$modinc = GEKKO_SOURCE_DIR."modules/$base/admin.php";

				$stop = false;
				if (file_exists($modinc)) {

					// this user is not an admin for this module
					if (!$isAdmin) {
						if ($strict_permissions) {
							// strict permissions
							$mc->save(createMessageBox("error", _L("E_ACCESS_DENIED")));
							$stop = true;
						} else {
							// letting him to know about his limited privileges
							$mc->save(createMessageBox("info", _L("E_READ_ONLY_ACCESS")));
						}
					}
					
					if (!$stop) {
						
						// loading module language files
						$Lang->loadFromModule($base);
						
						// attaching module title to main page's title
						pageSetSubtitle(_L("L_MODULE_".strtoupper($base)));
						
						// including admin module
						ob_start();

						include $modinc;

						if (class_exists("{$base}Controller")) {
							$class = "{$base}Controller";
							$control = new $class;
						}

						$mcBuff .= ob_get_contents();

						ob_end_clean();
					}

				} else {
					$mcBuff .= createMessageBox("error", _L("E_ACCESS_DENIED"));
				}
			}
		} else {

			pageSetSubtitle(_L("L_ADMIN_PANEL"));

			$mods = Array();

			$db->query("SELECT module, auth_manage
			FROM ".dbTable("module")."
			WHERE hidden != '1'", __FILE__, __LINE__);

			// installed modules
			while ($row = $db->fetchRow()) {
				if (!$strict_permissions || appAuthorize($row['auth_manage']))
					$mods[$row["module"]] = true;
			}

			$Panel = new Panel(_L("L_ADMIN_PANEL"), "admin.png");

			$moddir = gekkoModule::getList();

			natsort($moddir);

			$default = conf::getkey("modules", "default");
					
			foreach  ($moddir as $module) {
				$modpath = GEKKO_SOURCE_DIR."modules/$module";
				if (file_exists($modpath."/admin.php") && isset($mods[$module])) {

					if ($module == $default)
						$Panel->iconSetEmblem('home.png');
					else
						$Panel->iconClearEmblem();
					
					// loading icon (if exists) or creating a generic one
					if (file_exists($modpath."/admin_panel.php")) {
						include $modpath."/admin_panel.php";
					} else {
						// generic icon
						$Panel->AddIcon (
							$Panel->Icon (
								"$module.png",
								"index.php/module=admin/base=$module",
								_L("L_MODULE_".strtoupper($module))
							),
							_L("L_ADMIN_TASKS")
						);
					}
				}
			}

			$mcBuff .= $Panel->make();
		}
	} else {
		$mcBuff .= createMessageBox("error", _L("E_ACCESS_DENIED"));
	}

	if (file_exists(GEKKO_SOURCE_DIR."install") || file_exists(GEKKO_SOURCE_DIR."install.php")) {
		if (!GEKKO_ENABLE_DEBUG) {
			// error for non-developers
			$mcBuff = createMessageBox("warning", _L("L_PLEASE_DELETE_INSTALLER")).$mcBuff;
		}
	}

	if (!is_writable(GEKKO_TEMP_DIR) || !is_writable(GEKKO_DATA_DIR)) {
		$mcBuff = createMessageBox("warning", _L("L_CHECK_WRITABLE_DIRECTORIES")).$mcBuff;
	}

	// default toolbar
	$tpl = new contentBlock();
	$tpl->load("admin/_admin.toolbar.inc.tpl");
	$admin_toolbar = $tpl->make();

	$mcBuff = $admin_toolbar.$mcBuff;
?>
