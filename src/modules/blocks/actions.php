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

	define ("IN-GEKKO", true);

	require_once "../../conf.php";

	appLoadLibrary ("core.lib.php", "format.lib.php", "blocks");

	dbInit();

	require_once GEKKO_SOURCE_DIR."lib/auth.inc.php";

	varImport ("action", "id", "return");

	if (isAdmin()) {

		switch ($action) {
			case "delete":
				execWithEach($id, "blocks::deleteItem");
				appWriteLog("blocks: deleted block '".(is_array($id) ? implode(',', $id) : $id)."'");
			break;
			case "hide_show":
				varImport("hidden_blocks");

				foreach ($hidden_blocks as $module => $blocks) {
					$hidden = array();
					while (list($block) = each($blocks))
						$hidden[] = $block;
						
					conf::setkey('blocks', $module.'_ignore_blocks', implode(',', $hidden), 's');
				}
				
			break;
			case "enable":
				blocks::setItemStatus($id, 1);
				appWriteLog("blocks: enabled block '$id'");
			break;
			case "block_placer":

				varImport("positions");

				if ($positions) {
					$places = explode('|', $positions);
					foreach ($places as $place) {
						list($position, $blocks) = explode(':', $place);
						$blocks = explode(',', $blocks);
						foreach ($blocks as $order_id => $id) {
							$db->query("UPDATE ".dbTable("block")." SET
								order_id = '{$order_id}',
								position = '{$position}'
								WHERE id = '{$id}'", __FILE__, __LINE__);
							appWriteLog("blocks: updated position of block '$id', moved to '$order_id' at position '$position'");
						}
					}
				}

				$return = "index.php/module=admin/base=blocks";
			break;
			case "create":

				varImport (
					"icon", "position", "status", "title", "content", "modules",
					"scriptpath", "scriptvars", "order_id", "csstext", "blockclass",
					"auth_access"
				);

				varRequire (
					"title", "position", "modules"
				);

				editorApplyFixes($content);

				// joining modules
				$modules = is_array($modules) ? implode(',', $modules) : '';

				$auth_access = groups::extractFromInput($auth_access);

				// script existence
				if (!blocks::scriptExists($scriptpath))
					$scriptpath = '';

				// serializing submitted variables for store
				$scriptvars = $scriptvars ? Blocks::varSerialize($scriptvars) : '';

				// appending block (top or bottom)
				$q = $db->query ("SELECT order_id FROM ".dbTable("block")."
				WHERE position = '{$position}' ORDER BY order_id
				".(($order_id) ? "DESC" : "ASC")." LIMIT 1",
				__FILE__, __LINE__);

				$row = $db->fetchRow($q);
				$order_id = $row["order_id"]+ ($order_id ? 1 : -1);

				// inserting into database
				$db->query ("
				INSERT INTO ".dbTable("block")."
					(
						id,
						icon,
						position,
						status,
						title,
						content,
						modules,
						scriptpath,
						scriptvars,
						date_created,
						order_id,
						csstext,
						blockclass,
						auth_access,
   						author_id
   					)
						VALUES
					(
						'".($id = $db->findFreeId("block"))."',
						'{$icon}',
						'{$position}',
						'{$status}',
						'{$title}',
						'{$content}',
						'{$modules}',
						'{$scriptpath}',
						'{$scriptvars}',
						CURRENT_TIMESTAMP(),
						'{$order_id}',
						'{$csstext}',
						'{$blockclass}',
						'{$auth_access}',
						'{$USER["id"]}'
					)
				", __FILE__, __LINE__);

				appWriteLog("blocks: created block with id '$id'");

			break;
			case "edit":

				varImport("id", "icon", "position", "status", "title", "content",
				"modules","scriptpath", "scriptvars", "order_id", "csstext",
				"blockclass", "auth_access");

				varRequire("id", "title", "position", "modules");

				// fixing content created with rtbEditor
				editorApplyFixes($content);

				// joining modules
				$modules = is_array($modules) ? implode(',', $modules) : '';

				$auth_access = groups::extractFromInput($auth_access);

				// script existence
				if (!blocks::scriptExists($scriptpath))
					$scriptpath = null;

				// working with variables
				$scriptvars = $scriptvars ? Blocks::varSerialize($scriptvars) : '';

				// updating order_id if position has changed
				$q = $db->query("SELECT position, order_id FROM ".dbTable("block")."
				WHERE id = '{$id}'
				", __FILE__, __LINE__);

				$row = $db->fetchRow($q);

				if ($position != $row["position"]) {

					$q = $db->query("SELECT order_id FROM ".dbTable("block")." WHERE
					position = '{$position}' ORDER BY order_id DESC LIMIT 1",
					__FILE__, __LINE__);

					$row2 = $db->fetchRow($q);
					$order_id = $row2["order_id"]+1;

				} elseif ($order_id != $row["order_id"]) {
					$q = $db->query("
					UPDATE ".dbTable("block")." SET
						order_id = '{$row["order_id"]}'
					WHERE order_id = '{$order_id}'", __FILE__, __LINE__);
				}

				$db->query("
				UPDATE ".dbTable("block")." SET
					icon = '{$icon}',
					position = '{$position}',
					status = '{$status}',
					title = '{$title}',
					content = '{$content}',
					modules = '{$modules}',
					scriptpath = '{$scriptpath}',
					scriptvars = '{$scriptvars}',
					order_id = '{$order_id}',
					csstext = '{$csstext}',
					blockclass = '{$blockclass}',
					auth_access = '{$auth_access}'
				WHERE id = '{$id}'",
				__FILE__, __LINE__);

				appWriteLog("blocks: updated block '$id'");
			break;
		}
	}

	appRedirect($return);

	dbExit();
?>