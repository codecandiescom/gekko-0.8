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
		"conf"
	);

	appLoadJavascript("modules/conf/main.js");

	Extract (
		urlImportMap("null/null/action=alpha/package=alpha?id=int&page=page&option=alpha")
	);

	switch ($action) {
		case "looknfeel":
			$tpl = new contentBlock();

			$tpl->insert("BLOCK_CONTENT", "conf/admin.looknfeel.tpl");
			
			$options = new optionBox($tpl, $option);
				$options->add (null, "index.php/module=admin/base=packages", "{L_INSTALL}", "install.png");
			$options->make();

			// listing available stylesheets

			$themes = Array();
			$templates = listDirectory(GEKKO_TEMPLATE_DIR);

			foreach ($templates as $template) {
				if ($template[0] != '.') {
					$files = listDirectory(GEKKO_TEMPLATE_DIR."$template/_themes");
					foreach ($files as $file) {
						$filename = GEKKO_TEMPLATE_DIR."$template/_themes/$file/theme.css";
						if ($file[0] != '.' && file_exists($filename)) {
							
							if (!isset($themes[$template]))
								$themes[$template] = array();
							$themes[$template][] = $file;

							$data = getThemeInfo($filename);

							foreach ($data as $var => $val)
								$tpl->set("INFO_$var", $val, "THEME");
			
							$buttonBox = new tplButtonBox();

							$buttonBox->add (
								createLink("modules/conf/actions.php?action=looknfeel&request=set_theme&theme={$template}:{$file}&auth={C_AUTH_STR}", _L("L_USE_THEME"), "ok.png")
							);

							$buttonBox = $buttonBox->make();

							$tpl->setArray(
								Array (
									"NAME"			=> $file,
									"FILENAME"		=> "templates/$template/_themes/$file/theme.css",
									"SCREENSHOT"	=> _L("C_SITE.REL_URL")."templates/$template/_themes/$file/thumb.png",
									"TEMPLATE"		=> $template,
									"ACTIONS"		=> $buttonBox
								), "THEME"
							);

							$tpl->saveBlock("THEME");

						}
					}
				}
			}

			$tpl->setArray(
				Array(
					"BLOCK_TITLE"	=> _L("L_THEMES"),
					"BLOCK_ICON"	=> createIcon("conf.png", 16)
				)
			);

			$mcBuff = $tpl->make();
		break;
		case "clean_cache":
			cacheClean(GEKKO_CACHEDIR, 0);
			appRedirect("index.php/module=admin");
			appShutdown();
		break;
		case "setup":

			$tpl = new contentBlock();

			$tpl->insert ("BLOCK_CONTENT", "conf/admin.setup.tpl");

			// this class will store module configuration variables
			Class modConf {
				var $items = Array();
				function set($name, $value = false) {
					if (is_array($name)) {
						foreach ($name as $n => $v) {
							$this->set($n, $v);
						}
					} else {
						($name); // <-- php bug? I don't know why... but some servers stop responding without this line
						$this->items[$name] = $value;
					}
					return true;
				}
				function items() {
					return $this->items;
				}
				function clean() {
					$this->items = Array();
				}
			}

			$modConf = new modConf();

			$modules = gekkoModule::getList();

			// I want "conf" module to be listed as the first
			foreach ($modules as $i => $mod) {
				if ($mod == "conf")
					unset($modules[$i]);
			}
			array_unshift($modules, "conf");

			$keylist = Array();

			foreach ($modules as $mod) {

				$conf_file = GEKKO_SOURCE_DIR."modules/$mod/include/conf.php";
				if (file_exists($conf_file)) {

					$tpl->reset("MODULE.KEY");

					// cleaning old keys
					$modConf->clean();

					// loading configuration language file (conf. key descriptions)
					$Lang->loadFromModule($mod, "conf");

					// fetching most important configuration keys
					include $conf_file;

					// listing keys specified by included file
					foreach ($modConf->items() as $key => $keyinput) {

						list($module, $keyname) = explode(":", $key);

						// querying stored value for specified key
						$q = $db->query("SELECT id, keyvalue, keytype FROM ".dbTable("conf")."
						WHERE locked='0' AND module = '{$module}' AND keyname = '{$keyname}'",
						__FILE__, __LINE__);

						if ($db->numRows($q)) {

							$row = $db->fetchRow($q);

							saneHTML($row);

							if ($keyinput) // custom key input
								$row["KEYVALUE.INPUT"] = str_replace("%id", $row["id"], $keyinput);

							// key description
							$row["keyname"] = $keyname;
							$row["keydesc"] = _L("K_".strtoupper($keyname));

							$tpl->setArray (
								$row,
								"MODULE.KEY"
							);

							$tpl->saveBlock("MODULE.KEY");

							$keylist[] = $row["id"];
						} else {
							// fatal error, this is developer's fault! blame him! :P
							trigger_error("Key $module/$keyname doesn't exists.", E_USER_ERROR	);
						}
					}

					$tpl->setArray (
						Array (
							"MODULE"		=> $module,
							"MODULE_NAME"	=> _L("L_MODULE_".strtoupper($module))
						)
					, "MODULE");

					$tpl->saveBlock("MODULE");
				}
			}

			$tpl->setArray (
				Array (
					"BLOCK_TITLE"	=> _L("L_BASIC_SETUP"),
					"BLOCK_ICON"	=> createIcon("setup.png", 16),
					"ACTION"		=> "edit",
					"MODULE"		=> "",
					"ID"			=> 0,
					"RETURN"		=> "",
					"KEYLIST"		=> implode(",", $keylist)
				)
			);

			$mcBuff = $tpl->make();
		break;
		case "lang":
			$tpl = new contentBlock();

			$tpl->insert("BLOCK_CONTENT", "conf/admin.lang.tpl");

			// including language codes database
			require GEKKO_SOURCE_DIR."lang/codes.php";

			// getting enabled languages
			$enabled = conf::getKey("core", "site.enabled_languages", conf::getKey("core", "site.lang"), "s");

			$enabled = explode(",", str_replace(" ", "", $enabled));

			foreach ($lang_db as $code => $name) {
				if ($code != conf::getkey("core", "site.lang")) {
					$buttons = new tplButtonBox();

					if (in_array($code, $enabled))
						$buttons->add(createLink("modules/conf/actions.php?action=lang&code=$code&request=uninstall&auth={C_AUTH_STR}", _L("L_UNINSTALL"), "uninstall.png", true));
					else
						$buttons->add(createLink("modules/conf/actions.php?action=lang&code=$code&request=install&auth={C_AUTH_STR}", _L("L_INSTALL"), "install.png", true));
					
					$tpl->setArray (
						Array (
							"NAME"		=> $name,
							"ENABLED"	=> in_array($code, $enabled),
							"BUTTONS"	=> $buttons->make()
						), "LANG"
					);
					$tpl->saveBlock("LANG");
				}
			}
			
			$tpl->setArray (
				Array (
					"TITLE"			=> _L("L_LANGUAGES"),
					"RETURN"		=> "index.php/module=admin"
				)
			);

			$mcBuff = $tpl->make();
		break;
		case "edit_key":

			$tpl = new contentBlock();

			$tpl->insert("BLOCK_CONTENT", "conf/admin.edit_key.tpl");

			$options = new optionBox($tpl, $option);
				$options->add (null, "index.php/module=admin/base=conf", "{L_LIST}", "conf.png");
				$options->add (null, "index.php/module=admin/base=conf/action=new_key", "{L_NEW_KEY}", "new.png");
				$options->add (null, "modules/conf/actions.php?action=delete_key&id=$id&auth={C_AUTH_STR}", "{L_DELETE}", "delete.png", true);
			$options->make();

			$q = $db->query ("
				SELECT
					id, module, keyname, keyvalue, keytype, date_modified, locked
				FROM ".dbTable("conf")."
				WHERE id = '{$id}'
			", __FILE__, __LINE__);

			if ($db->numRows($q)) {

				$row = $db->fetchRow($q);

				saneHTML($row);

				$tpl->setArray($row);

				$tpl->setArray (
					Array (
						"BLOCK_TITLE"		=> _L("L_CONF"),
						"BLOCK_ICON"		=> createIcon("setup.png", 16),
						"ACTION"			=> "edit_key",
						"RETURN"			=> "index.php/module=admin/base=conf".($package ? "/package=$package" : "")
					)
				);

				$mcBuff = $tpl->make();

			} else {
				$mcBuff = _L("E_UNKNOWN_KEY");
			}
		break;
		case "new_key":

			$tpl = new contentBlock();

			$tpl->insert("BLOCK_CONTENT", "conf/admin.new_key.tpl");

			$options = new optionBox($tpl, $option);
				$options->add (null, "index.php/module=admin/base=conf", "{L_LIST}", "conf.png");
			$options->make();

			$tpl->setArray(
				Array (
					"BLOCK_TITLE"	=> _L("L_CONF"),
					"BLOCK_ICON"	=> createIcon("conf.png", 16),
					"MODULE"		=> "core",
					"KEYTYPE"		=> "",
					"KEYVALUE"		=> "",
					"KEYNAME"		=> "",
					"ACTION"		=> "new_key",
					"LOCKED"		=> false
				)
			);

			$mcBuff = $tpl->make();
		break;
		case "plugins":
			$tpl = new contentBlock();

			$tpl->insert("BLOCK_CONTENT", "conf/admin.plugins.tpl");

			// options menu
			$options = new optionBox($tpl, $option);
				$options->add (null, "index.php/module=admin/base=conf", "{L_CONF}", "conf.png");
			$options->make();

			$plugins = Array();
			// normal plugins
			$files = listDirectory(GEKKO_PLUGIN_DIR);
			foreach ($files as $file) {
				$path = GEKKO_PLUGIN_DIR."{$file}";
				if (file_exists("$path/main.php")) {
					$plugins[] = Array(
						"name" => $file,
						"path" => $path
					);
				}
			}

			$files = listDirectory(GEKKO_MODULE_DIR);
			foreach ($files as $file) {
				$path = GEKKO_MODULE_DIR."{$file}/plugin.php";
				if (file_exists($path)) {
					$plugins[] = Array(
						"name" => "{$file}Plugin",
						"path" => $path
					);
				}
			}

			foreach ($plugins as $plugin) {
				
				$enabled = !conf::getKey("core", "plugins.".strtolower($plugin["name"]).".disable");
			
				$plugin["filename"] = is_dir($plugin["path"]) ? "{$plugin["path"]}/main.php" : $plugin["path"];

				if (is_dir($plugin["path"]) && file_exists("{$plugin["path"]}/version.php")) {
					include "{$plugin["path"]}/version.php";
				} else {
					$version = Array (
						"AUTHOR"		=> "Gekko authors",
						"VERSION"		=> date("Y.m.d.h.i.s", filemtime($plugin["filename"])),
						"DESCRIPTION"	=> "",
						"HOMEPAGE"		=> "<a href=\"http://www.gekkoware.org\">http://www.gekkoware.org</a>",
						"LICENSE"		=> "GNU/GPL"
					);
				}
				
				$version["FILENAME"] = $plugin["filename"];

				$tpl->setArray($version, "PLUGIN");

				$actions = new tplButtonBox();
				if ($enabled)
					$actions->add(createLink("modules/conf/actions.php?action=plugins&request=disable&plugin={$plugin["name"]}&auth={C_AUTH_STR}", "{L_DISABLE}", "delete.png"));
				else
					$actions->add(createLink("modules/conf/actions.php?action=plugins&request=enable&plugin={$plugin["name"]}&auth={C_AUTH_STR}", "{L_ENABLE}", "ok.png"));
				$actions = $actions->make();
				
				$tpl->setArray (
					Array (
						"PLUGIN"	=> $plugin["name"],
						"ACTIONS"	=> $actions,
						"ENABLED"	=> $enabled
					),
					"PLUGIN"
				);
				
				$tpl->saveBlock("PLUGIN");
			}

			$tpl->setArray (
				Array (
					"BLOCK_TITLE"	=> _L("L_PLUGINS"),
					"BLOCK_ICON" 	=> createIcon("plugins.png", 16),
					"RETURN"		=> ""
				)
			);

			$mcBuff = $tpl->make();
		break;
		default:

			if (!$package)
				$package = "core";

			$tpl = new contentBlock ();

			$tpl->insert("BLOCK_CONTENT", "conf/admin.default.tpl");

			$options = new optionBox($tpl, $option);
				$options->add (null, "index.php/module=admin/base=conf/action=new_key", "{L_NEW_KEY}", "new.png");
			$options->make();

			$q = $db->query ("SELECT module FROM ".dbTable("conf")." ORDER BY module ASC");
			$modules = $db->numRows($q);

			$passed = Array();
			$passed[$module] = true;
			while ($row = $db->fetchRow($q)) {
				if (!isset($passed[$row["module"]])) {

					$tpl->setArray (
						Array (
							"MODULE"		=> $row["module"],
							"MODULE_LINK"	=> createLink("index.php/module=admin/base=conf/action=list/package={$row["module"]}", $row["module"])
						),
						"MODULE"
					);

					$tpl->saveBlock("MODULE");

					$passed[$row["module"]] = true;
				}
			}

			$tpl->setArray (
				Array (
					"BLOCK_TITLE"		=> _L("L_CONF"),
					"BLOCK_SUBTITLE"	=> _L("L_CREATE"),
					"BLOCK_ICON"		=> createIcon("setup.png", 16),
					"RETURN"			=> ""
				)
			);

			$mcBuff = $tpl->make();
		break;
	}
?>
