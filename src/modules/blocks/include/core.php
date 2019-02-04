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

	define("IN-BLOCK", 1);

	// loading API
	appLoadLibrary (
		"blocks"
	);

	// creating block buffers
	$bpos = explode(",", conf::getkey("blocks", "block_positions"));
	foreach ($bpos as $pos)
		if (($pos = trim($pos)) != null)
			$L["M_BLOCK_".$pos] = "";

	if (!isset($_GET["hide_blocks"])) {

		// loading blocks for this module
		$q = $db->query ("
		SELECT
			id,icon, title, content, modules, position, scriptpath, scriptvars, csstext,
			blockclass, auth_access, author_id
		FROM ".dbTable("block")."
		WHERE (status = '1' AND (modules LIKE '%".GEKKO_REQUEST_MODULE."%' OR modules LIKE '%*%'))
		ORDER BY order_id ASC
		", __FILE__, __LINE__, true);

		// fetching ignored blocks for this module
		$ignore = explode(',', conf::getkey('blocks', GEKKO_REQUEST_MODULE.'_ignore_blocks', '', 's'));

		// filling block buffers
		while ($row = $db->fetchRow($q)) {

			// is this block ignored?
			if (in_array($row["position"], $ignore))
				continue;

			// checking permissions
			if (Blocks::checkAuth(GEKKO_REQUEST_MODULE, $row["modules"], $row["auth_access"])) {

				// we don't want those variables to be escaped
				saneHTML($row, "title,content,scriptvars,scriptpath");

				// privilege based html filtering
				//format::style($row["title"], $row["author_id"]);
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

				$row["contextmenu"] = "";
				if (appAuthorize("blocks")) {

					$actions = new tplButtonBox();
					$actions->separator = '';
						$actions->add(createLink("index.php/module=admin/base=blocks/action=create?return=".urlencode($_SERVER["REQUEST_URI"]), _L("L_NEW"), "new.png"));
						$actions->add(createLink("index.php/module=admin/base=blocks/action=edit?id={$row["id"]}&return=".urlencode($_SERVER["REQUEST_URI"]), _L("L_EDIT"), "edit.png"));
						$actions->add(createLink("modules/blocks/actions.php?action=delete&id={$row["id"]}&auth={C_AUTH_STR}&return=".urlencode($_SERVER["REQUEST_URI"]), _L("L_DELETE"), "delete.png", true));
					$actions = $actions->make();
					
					$row["contextmenu"] = "<div class=\"gekkoContextMenu\">".$actions."</div>";
				}

				// block template
				$btpl = new contentBlock("block", "blocks/".$row["blockskeleton"]);

				$btpl->vren("BLOCK_TITLE", "TITLE");
				$btpl->vren("BLOCK_CONTENT", "CONTENT");
				$btpl->vren("BLOCK_ICON", "ICON");

				$btpl->setArray($row);

				$L["M_BLOCK_".$row["position"]] .= Lang::Parse($btpl->make());
			}
		}
	}
?>