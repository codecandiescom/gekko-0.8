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

	// Loading API
	appLoadLibrary (
		"blocks", "modules"
	);

	// URL Prototype
	Extract (
		urlImportMap("null/null/action=alpha?id=int&test_module=alpha&return=data&position=alpha")
	);

	switch ($action) {
		case "block_placer":

		if (!$test_module)
			$test_module = conf::getkey('modules', 'default');

			blocks::hideAll();
			$admin_toolbar = null;

			$tpl = new contentBlock();
			$tpl->insert("BLOCK_CONTENT", "blocks/admin.block_placer.tpl");

			$positions = explode(',', conf::getkey("blocks", "block_positions"));
			foreach ($positions as $position) {
				$position = trim($position);
				if (!$L["M_BLOCK_$position"])
					$L["M_BLOCK_$position"] = '<span></span>';
			}

			$q = $db->query("
			SELECT
				id,icon, title, content, modules, position, scriptpath, scriptvars, csstext,
				blockclass, auth_access, author_id
			FROM ".dbTable("block")."
			WHERE (status = '1' AND (modules LIKE '%{$test_module}%' OR modules LIKE '%*%'))
			ORDER BY order_id ASC
			", __FILE__, __LINE__, true);

			// filling block buffers
			while ($row = $db->fetchRow($q)) {

				// checking permissions
				if (blocks::checkAuth($test_module, $row["modules"], $row["auth_access"])) {

					// we don't want those variables to be escaped
					saneHTML($row, "title,content,scriptvars,scriptpath");

					// privilege based html filtering
					format::style($row["content"], $row["author_id"]);

					// default block template (must be under templates/[$template]/blocks/*.block)
					$row["blockskeleton"] = "default.block";

					// block icon
					$row["icon"] = createIcon($row["icon"]);

					// css style in a single line
					$row["csstext"] = preg_replace("/\n|\t/", "", $row["csstext"]);

					if (Blocks::scriptExists($row["scriptpath"])) {
						$blockinfo = explode("/", $row["scriptpath"]);
						if (gekkoModule::isInstalled($blockinfo[0])) {
							$content = Blocks::LoadScript($row);
							if ($content == -1)
								continue;
							else
								$row["content"] .= $content;
						}
					} elseif ($row["scriptpath"]) {
						$row["content"] .= createMessageBox("error", "<b>Missing block:</b> \"{$row["scriptpath"]}\".");
					}

					$actions = new tplButtonBox();
					$actions->separator = '';
						$actions->add(createLink("index.php/module=admin/base=blocks/action=create", _L("L_NEW"), "new.png"));
						$actions->add(createLink("index.php/module=admin/base=blocks/action=edit?id={$row["id"]}", _L("L_EDIT"), "edit.png"));
						$actions->add(createLink("modules/blocks/actions.php?action=delete&id={$row["id"]}&auth={C_AUTH_STR}", _L("L_DELETE"), "delete.png", true));
					$actions = $actions->make();
					
					$row["contextmenu"] = "<div class=\"gekkoContextMenu\">".$actions."</div>";
					
					// block template
					$btpl = new contentBlock("block", "blocks/{$row["blockskeleton"]}");

					$btpl->vren("BLOCK_TITLE", "TITLE");
					$btpl->vren("BLOCK_CONTENT", "CONTENT");
					$btpl->vren("BLOCK_ICON", "ICON");

					$btpl->setArray($row);

					$L["M_BLOCK_".$row["position"]] .= Lang::Parse($btpl->make());
				}
			}

			$modules = gekkoModule::getList();

			foreach ($modules as $module) {
				if (file_exists(GEKKO_SOURCE_DIR."modules/{$module}/main.php"))
					$modlist[$module] = $module;
			}

			$tpl->setArray (
				Array (
					"BLOCK_TITLE"	=> _L("L_MODULE").": "._L("L_MODULE_".strtoupper($test_module)),
					"BLOCK_ICON"	=> createIcon("{$test_module}.png", 16),
					"TEST_MODULES"	=> serialize($modlist),
					"TEST_MODULE"	=> $test_module,
					"DELETE"		=> str_replace("'", "\'", html_entity_decode(createLink("modules/blocks/actions.php?action=delete&id=__id__&auth="._L("C_AUTH_STR"), _L("L_DELETE"), "delete.png", true))),
					"EDIT"			=> str_replace("'", "\'", html_entity_decode(createLink("index.php/module=admin/base=blocks/action=edit?id=__id__", _L("L_EDIT"), "edit.png"))),
					"CREATE"		=> str_replace("'", "\'", html_entity_decode(createLink("index.php/module=admin/base=blocks/action=create?position=__position__", _L("L_CREATE"), "blocks.png"))),
					"RETURN"		=> urlEvalPrototype("index.php/module=admin/base=blocks")

				)
			);

			$mcBuff = $tpl->make();
		break;
		case "edit":
			if ($id) {

				$tpl = new contentBlock();

				$tpl->insert("BLOCK_CONTENT", "blocks/admin.generic_form.tpl");

				// main query
				$q = $db->query("
					SELECT
						id, modules, icon, title, content, scriptpath, scriptvars,
						order_id, position, blockclass, date_created, date_modified, auth_access,
						csstext, status
					FROM ".dbTable("block")." WHERE id = '{$id}'
				", __FILE__, __LINE__);

				if ($db->numRows($q)) {

					$row = $db->fetchRow($q);

					// we don't want 'scriptvars' to be modified
					saneHTML($row, "scriptvars,content");

					// parsing scriptvars
					$vars = unserialize($row["scriptvars"]);
					if ($row["scriptpath"]) {
						$bdata = cacheFunc("Blocks::getData", $row["scriptpath"]);

						// human readable variables
						$row["scriptvars"] = array();
						// variables defined within block (using default values if needed)
						foreach ($bdata["variables"] as $name => $value) {
							$row["scriptvars"][] = "<label>$name:<input class=\"text\" type=\"text\" name=\"scriptvars[$name]\" size=\"40\" value=\"".(isset($vars[$name]) ? htmlspecialchars($vars[$name]) : "")."\" /><label>";
						}
						$row["scriptvars"] = implode("<hr />", $row["scriptvars"]);
					}

					// looking for brothers
					$order = Array();
					$db->query ("
						SELECT order_id FROM ".dbTable("block")."
						WHERE position = '{$row["position"]}'
						ORDER BY order_id ASC
					", __FILE__, __LINE__);

					while ($row2 = $db->fetchRow())
						$order[$row2["order_id"]] = $row2["order_id"];

					// passing variables
					$tpl->setArray($row);

					$actions = new tplButtonBox();
					$actions->add (
						createLink("index.php/module=admin/base=blocks", _L("L_BLOCKS"), "blocks.png"),
						createLink("index.php/module=admin/base=blocks/action=block_placer", _L("L_BLOCK_PLACER"), "move.png"),
						createLink("index.php/module=admin/base=blocks/action=create", _L("L_CREATE"), "blocks.png")

					);
					$actions = $actions->make();

					// passing (and modifying) some variables to template engine
					$tpl->setArray (
						Array (
							"BLOCK_TITLE"		=> _L("L_BLOCKS"),
							"FORM_TITLE"		=> _L("L_EDIT"),
							"BLOCK_ICON"		=> createIcon("blocks.png"),
							"ORDERS"			=> serialize($order),
							"ACTION"			=> "edit",
							"ACTIONS"			=> $actions,
							"RETURN"			=> $return ? $return : "index.php/module=admin/base=blocks"
						)
					);

					$mcBuff = $tpl->make();
				}
			}

		break;
		case "hide_show":

			$tpl = new contentBlock();

			$tpl->insert("BLOCK_CONTENT", "blocks/admin.hide_show.tpl");

			$actions = new tplButtonBox();
			$actions->add (
				createLink("index.php/module=admin/base=blocks", _L("L_BLOCKS"), "blocks.png", false),
				createLink("index.php/module=admin/base=blocks/action=block_placer", _L("L_BLOCK_PLACER"), "move.png", false)
			);
			
			$actions = $actions->make();
			$positions = explode(',', conf::getkey("blocks", "block_positions"));

			$modules = $mc->getList();
			foreach ($modules as $module) {
				if (file_exists(GEKKO_MODULE_DIR."$module/main.php")) {
					$hidden_positions = explode(',', conf::getkey('blocks', $module.'_ignore_blocks', '', 's'));
	
					while (list($i) = each($hidden_positions))
						$hidden_positions[$i] = trim($hidden_positions[$i]);
	
					$options = Array();
					foreach ($positions as $position) {
						$position = trim($position);
						$options[] = createCheckBox("hidden_blocks[$module][$position]", _L("L_POSITION_".$position), in_array($position, $hidden_positions));
					}
	
					$row = Array (
						"MODULE"	=> $module,
						"OPTIONS"	=> implode("<hr />", $options)
					);
	
					$tpl->setArray($row, "MODULE");
					$tpl->saveBlock("MODULE");
				}
			}

			$tpl->setArray (
				Array (
					"BLOCK_TITLE"	=> _L("L_BLOCKS"),
					"BLOCK_ICON"	=> createIcon("blocks.png"),
					"ACTIONS"		=> $actions,
					"RETURN"		=> $return ? $return : "index.php/module=admin/base=blocks"
				)
			);

			$mcBuff = $tpl->make();
		break;
		case "create":

			$tpl = new contentBlock();

			$tpl->insert("BLOCK_CONTENT", "blocks/admin.generic_form.tpl");

			$actions = new tplButtonBox();
			$actions->add (
				createLink("index.php/module=admin/base=blocks", _L("L_BLOCKS"), "blocks.png", false),
				createLink("index.php/module=admin/base=blocks/action=block_placer", _L("L_BLOCK_PLACER"), "move.png", false)
			);
			$actions = $actions->make();

			$tpl->setArray (
				Array (
					"BLOCK_TITLE"	=> _L("L_BLOCKS"),
					"BLOCK_ICON"	=> createIcon("blocks.png"),
					"FORM_TITLE"	=> _L("L_CREATE"),
					"ORDERS"		=> serialize(array(_L("L_TOP"), _L("L_BOTTOM"))),
					"ORDER"			=> 1,
					"ACTION"		=> "create",
					"BLOCKCLASS"	=> "block",
					"STATUS"		=> true,
					"POSITION"		=> $position ? $position : "L",
					"TITLE"			=> "",
					"ICON"			=> "blocks.png",
					"CONTENT"		=> "",
					"MODULES"		=> "*",
					"AUTH_ACCESS"	=> "*",
					"CSSTEXT"		=> "",
					"SCRIPTPATH"	=> "",
					"SCRIPTVARS"	=> "",
					"ACTIONS"		=> $actions,
					"RETURN"		=> $return ? $return : "index.php/module=admin/base=blocks"
				)
			);

			$mcBuff = $tpl->make();
		break;
		default:

			$tpl = new contentBlock();

			$tpl->insert("BLOCK_CONTENT", "blocks/admin.default.tpl");

			$q = $db->query ("
				SELECT
					id, icon, title, position, auth_access, status,
					modules, scriptpath, blockclass
				FROM ".dbTable("block")."
				ORDER BY modules, order_id ASC
			", __FILE__, __LINE__);

			if ($db->numRows($q)) {

				while ($row = $db->fetchRow($q)) {
					saneHTML($row, "title");
					$row["icon"] = createIcon($row["icon"], 16);
					$row["actions"] = new tplButtonBox();
					$row["actions"]->add (
						createLink("index.php/module=admin/base=blocks/action=edit?id={$row["id"]}", _L("L_EDIT"), "edit.png"),
						createLink("modules/blocks/actions.php?action=delete&id={$row["id"]}&auth={C_AUTH_STR}", _L("L_DELETE"), "delete.png", true)
					);
					if (!$row["status"])
						$row["actions"]->add(createLink("modules/blocks/actions.php?action=enable&id={$row["id"]}&auth={C_AUTH_STR}", _L("L_ENABLE"), "unlock.png", true));
					$row["actions"] = $row["actions"]->make();

					$row["modules"] = modules::humanReadableList($row["modules"]);
					$row["auth_access"] = groups::humanReadableGroups($row["auth_access"]);
					$tpl->setArray ($row, "BLOCK");
					$tpl->saveBlock("BLOCK");
				}
			}

			$actions = new tplButtonBox();
			$actions->add (
				createLink("index.php/module=admin/base=blocks/action=hide_show", _L("L_HIDE_AND_SHOW"), "move.png"),
				createLink("index.php/module=admin/base=blocks/action=block_placer", _L("L_BLOCK_PLACER"), "move.png"),
				createLink("index.php/module=admin/base=blocks/action=create", _L("L_CREATE"), "blocks.png")

			);
			$actions = $actions->make();

			$tpl->setArray (
				Array (
					"BLOCK_TITLE"		=> _L("L_BLOCKS"),
					"BLOCK_SUBTITLE"	=> _L("L_CREATE_BLOCK"),
					"BLOCK_ICON"		=> createIcon("blocks.png"),
					"ACTIONS"			=> $actions
				)
			);

			$mcBuff = $tpl->make();

		break;
	}
?>