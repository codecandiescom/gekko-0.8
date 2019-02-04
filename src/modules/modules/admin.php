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

	appLoadLibrary (
		"modules", "packages", "groups"
	);

	Extract (
		urlImportMap("null/null/action=alpha/package=alpha")
	);

	switch ($action) {
		case "edit":

			$tpl = new contentBlock();
			$tpl->insert("BLOCK_CONTENT", "modules/admin.edit.tpl");

			$q = $db->query("SELECT id, module, auth_exec, auth_manage,
			modified, status, hidden
			FROM ".dbTable("module")." WHERE module = '$package'", __FILE__, __LINE__);

			if ($db->numRows($q)) {

				$row = $db->fetchRow($q);
				saneHTML($row);

				$row["ACTIONS"] = new tplButtonBox();
					$row["ACTIONS"]->add(createLink("modules/modules/actions.php?action=manage&opt=U&id={$row["id"]}&auth={C_AUTH_STR}", _L("L_UNINSTALL"), "delete.png", true));
					$row["ACTIONS"]->add(createLink("modules/modules/actions.php?action=manage&opt=R&id={$row["id"]}&auth={C_AUTH_STR}", _L("L_REINSTALL"), "redo.png", true));
					$row["ACTIONS"]->add(createLink("modules/modules/actions.php?action=manage&opt=P&id={$row["id"]}&auth={C_AUTH_STR}", _L("L_UPGRADE"), "up.png", true));
				$row["ACTIONS"] = $row["ACTIONS"]->make();

				$tpl->setArray ($row);
				$tpl->setArray (
					Array (
						"BLOCK_TITLE"		=> _L("L_MODULES"),
						"BLOCK_ICON"		=> createIcon("modules.png", 16),
						"RETURN"			=> "index.php/module=admin/base=modules"
					)
				);

				$mcBuff = $tpl->make();
			}
		break;
		default:
			$tpl = new contentBlock();

			$tpl->insert("BLOCK_CONTENT", "modules/admin.default.tpl");

			// fetching installed modules
			$q = $db->query ("
				SELECT
					id, module, auth_exec, auth_manage, status
				FROM ".dbTable("module")."
				ORDER BY module ASC
			", __FILE__, __LINE__);

			// getting default module
			$default_mod = conf::getKey("modules", "default");

			$installed = Array();

			while ($row = $db->fetchRow($q)) {

				saneHTML($row);

				if (file_exists(GEKKO_SOURCE_DIR."modules/{$row["module"]}/main.php")) {
					// main.php exists, that means that this module could be placed
					// as candidate for being chosen as default
					$row["DEFAULT.RADIO"] = createRadio("default", '', ($default_mod == $row["module"]), $row["id"]);
				} else {
					$row["DEFAULT.RADIO"] = "";
				}

				// getting module information
				$xml_file = GEKKO_SOURCE_DIR."modules/{$row["module"]}/package.xml";

				if (file_exists($xml_file)) {

					$pkg_xml = Array();

					// parsing xml file
					Packages::parseXML($xml_file, $pkg_xml);

					$tpl->setArray (
						Array (
							"AUTHOR"			=> $pkg_xml["module"]["author"],
							"HOMEPAGE"			=> $pkg_xml["module"]["homepage"],
							"VERSION"			=> $pkgver = $pkg_xml["release"]["version"],
							"RELEASE_AUTHOR"	=> $pkg_xml["release"]["author"],
							"NOTES"				=> texttohtml($pkg_xml["release"]["notes"]),
							"CHANGELOG"			=> texttohtml($pkg_xml["release"]["changelog"])
						),
					"MODULE");

				} else {
					trigger_error("Module '{$row["module"]}' doesn't has a package.xml file!", E_USER_WARNING);
				}

				// checking differences between package version and installed version
				$modver = conf::getkey($row["module"], "_version");

				if (($diff = packages::checkVersion($pkgver, $modver)) != 0) {
					if ($diff > 0) {
						$row["MESSAGE"] = createMessageBox("error", _L("L_OUTDATED_PACKAGE"));
					} else {
						$row["MESSAGE"] = createMessageBox("info", _L("L_MODULE_UPGRADE"));
					}
				}

				$row["ACTIONS"] = new tplButtonBox();
					$row["ACTIONS"]->add(createLink("index.php/module=admin/base=modules/action=edit/package={$row["module"]}", _L("L_EDIT"), "edit.png"));
					if ($row["status"]) {
						$row["ACTIONS"]->add(createLink("modules/modules/actions.php?action=status&set=0&id={$row["id"]}&auth={C_AUTH_STR}", _L("L_DISABLED"), "disabled.png"));
					} else {
						$row["ACTIONS"]->add(createLink("modules/modules/actions.php?action=status&set=1&id={$row["id"]}&auth={C_AUTH_STR}", _L("L_ENABLED"), "enabled.png"));
					}
				$row["ACTIONS"] = $row["ACTIONS"]->make();

				$tpl->setArray (
					$row,
					"MODULE"
				);

				$tpl->saveBlock("MODULE");
				$tpl->clear("MODULE");

				// saving module's version in the <installed> array
				$installed[$row["module"]] = $pkg_xml["release"]["version"];
			}

			// fetching non-installed modules
			$modules = gekkoModule::getPackages();

			foreach ($modules as $module) {
				if (!isset($installed[$module])) {
					// this module is not installed
					$tpl->setArray (
						Array (
							"MODULE"	=> $module,
							"TIMESTAMP"	=> dateFormat(filemtime(GEKKO_SOURCE_DIR."modules/$module"))
						),
						"PACKAGE"
					);
					$tpl->saveBlock("PACKAGE");
				}
			}

			$tpl->setArray (
				Array (
					"BLOCK_TITLE"		=> _L("L_MODULES"),
					"BLOCK_SUBTITLE"	=> _L("L_MODULE_MANAGER"),
					"BLOCK_ICON"		=> createIcon("modules.png", 16),
					"RETURN"			=> "index.php/module=admin/base=modules"
				)
			);

			$mcBuff = $tpl->make();

			// still has deprecated modules?
			if (file_exists(GEKKO_SOURCE_DIR."modules/tagboard") || file_exists(GEKKO_SOURCE_DIR."modules/friends") || file_exists(GEKKO_SOURCE_DIR."modules/phrases")) {
				$mcBuff = createMessageBox("warning", sprintf(_L("L_DEPRECATED_MODULES"), "tagboard, friends, phrases")).$mcBuff;
			}
		break;
	}
?>
