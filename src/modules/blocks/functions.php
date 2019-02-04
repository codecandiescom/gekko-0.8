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

	Class Blocks {
		/**
		* hideAll();
		* Hides all blocks
		*/
		function hideAll() {
			$positions = conf::getKey("blocks", "block_positions");
			$positions = explode(',', $positions);
			foreach ($positions as $position) {
				$position = trim($position);
				$GLOBALS['L']['M_BLOCK_'.$position] = '';
			}
		}
		function hide($places) {
			$places = explode(',', $places);
			foreach ($places as $position) {
				$position = trim($position);
				$GLOBALS['L']['M_BLOCK_'.$position] = '';
			}
		}
		function renameScript($oldName, $newName) {
			$db =& $GLOBALS["db"];
			$db->query("UPDATE ".dbTable("block")." SET scriptpath = '{$newName}'
			WHERE scriptpath = '{$oldName}'", __FILE__, __LINE__, true);
		}
		function getStatus($id = false) {
			$status = Array (
				_L("L_DISABLED"),
				_L("L_ENABLED")
			);
			if ($id === false)
				return $status;
			else
				return $status[$id];
		}
		/**
		* getClasses();
		* @returns: all possible block's classes
		*/
		function getClasses() {
			// block classes
			$classes = Array();
			$tmp = explode(",", conf::getKey("blocks", "block_classes"));
			foreach ($tmp as $class)
				if (($class = trim($class)) !== false)
					$classes[$class] = $class;
			return $classes;
		}

		/**
		* scriptExists($module/$block);
		* @return: true if the given block exists, false otherwise
		*
		*/
		function scriptExists($scriptpath) {
			if ($scriptpath) {
				$block_info = explode("/", $scriptpath);
				if (isset($block_info[1])) {
					list($module, $block) = $block_info;
					if (file_exists(GEKKO_SOURCE_DIR."modules/{$module}/blocks/{$block}"))
						return true;
				}
			}
			return false;
		}
		/**
		* getData("$modulename/$blockname")
		* @return: and array containing xml parsed block information and variables
		*
		*/
		function getData($block) {

			$return = Array (
				"info"		=> array(),
				"variables"	=> array()
			);

			$block_info = explode("/", $block);

			if (!isset($block_info[1]))
				return false;

			list($module, $block) = $block_info;

			$script = GEKKO_SOURCE_DIR."modules/{$module}/blocks/{$block}";

			if (file_exists($script)) {

				$contents = loadFile($script);

				preg_match("/<%%>(.*?)<%%>/s", $contents, $struct);
				$data = trim($struct[1]);

				// creating xml parser
				$parser = xml_parser_create();
				if (!xml_parse_into_struct($parser, $data, $xml)) {
					trigger_error("XML: \"".xml_error_string(xml_get_error_code($parser))."\" line: ".xml_get_current_line_number($parser)."", E_USER_ERROR);
				}
				xml_parser_free($parser);

				$info = Array();
				$vars = Array();

				$ib = Array();

				foreach ($xml as $tag) {
					switch ($tag["level"]) {
						case 1:

						break;
						case 2:
							switch ($tag["tag"]) {
								case "INFO":
									switch ($tag["type"]) {
										case "open":
											// new language
											$lang = $tag["attributes"]["LANG"];
											$info[$lang] = Array();
										break;
										case "close":
											$lang = null;
										break;
									}
								break;
								case "VARIABLE":
									// default values for variables
									if ($tag["type"] == "complete") {
										$vars[$tag["attributes"]["NAME"]] = isset($tag["value"]) ? $tag["value"] : false;
									}
								break;
							}
						break;
						case 3:
							$info[$lang][strtolower($tag["tag"])] = $tag["value"];
						break;
					}
				}

				// default language is "en"
				while (list($attr) = each($info["en"])) {
					$info[$attr] = isset($info[getLang()][$attr]) ? $info[getLang()][$attr] : $info["en"][$attr];
				}

				if (!isset($info[getLang()]))
					$info[getLang()] = $info["en"];


				$return["info"] = $info[getLang()];
				$return["variables"] = $vars;

				return $return;

			} else {
				return false;
			}
		}

		/**
		* Load(block id)
		* @return: content created and returned by the given block
		*
		*/
		function Load($id) {
			$content = false;

			$q = $GLOBALS["db"]->query("SELECT content, scriptvars, scriptpath, author_id
			FROM ".dbTable("block")." WHERE id = '{$id}'", __FILE__, __LINE__, true);

			if ($GLOBALS["db"]->numRows($q)) {

				$row = $GLOBALS["db"]->fetchRow($q);

				format::style($row["content"], $row["author_id"]);
				$content = $row["content"];

				if ($row["scriptpath"])
					$content .= Blocks::LoadScript($row);
			}

			return $content;
		}
		/**
		* loadScript(array('scriptpath'=>path,'scriptvars'=>serialized variables))
		* @returns: content created by the given block _script_
		*
		*/
		function loadScript(&$block) {
			$return = "";

			$scriptpath = $block["scriptpath"];
			$scriptvars = $block["scriptvars"];

			// getting block info
			$info = cacheFunc("Blocks::getData", "$scriptpath");

			// extracting default variables
			if (is_array($info["variables"]))
				extract($info["variables"], EXTR_OVERWRITE);

			// extracting (and overwriting) variables from serialized data
			$scriptvars = @unserialize($scriptvars);
			if (is_array($scriptvars)) {
				$toextract = array();
				foreach ($scriptvars as $key => $val) {
					if (trim($val) != '')
						$toextract[$key] = addslashes($val);
				}
				extract($toextract, EXTR_OVERWRITE);
			}

			list($module, $blockname) = explode("/", $scriptpath);

			// loading block language file (modules/$module/$block.block.php)
			if (file_exists($lang_file = GEKKO_MODULE_DIR."{$module}/lang/".getLang()."/".substr($blockname, 0, strpos($blockname, ".")).".block.php"))
				include_once $lang_file;

			if (file_exists(GEKKO_MODULE_DIR."{$module}/blocks/{$blockname}")) {
				// loading blockscript
				include (GEKKO_MODULE_DIR."{$module}/blocks/{$blockname}");
			} else {
				$return = createMessageBox("error", "Missing block!");
			}

			// $return variable is blockscript's output
			return $return;
		}

		// deleteItem(block id)
		function deleteItem($id) {
			appWriteLog("blocks: block '$id' was deleted", "actions", 1);
			return $GLOBALS["db"]->query ("DELETE FROM ".dbTable("block")." WHERE id='{$id}'", __FILE__, __LINE__, true);
		}
		// setItemStatus(block id, boolean status);
		function setItemStatus($id, $status) {
			return $GLOBALS["db"]->query ("UPDATE ".dbTable("block")." SET status = '".$status."' WHERE id='".$id."'", __FILE__, __LINE__, true);
		}
		/**
		* simpleCreate(title, content, module/block, serialized array of scriptvars);
		* Creates a block with default values
		* @returns: recently created block id
		*
		*/
		function simpleCreate($title, $content = null, $scriptpath = null, $scriptvars = null, $icon = "blocks.png", $position = "L") {

			if (!Blocks::scriptExists($scriptpath))
				$scriptpath = "";

			$scriptvars = $scriptvars ? Blocks::varSerialize($scriptvars) : "";

			// appeding to bottom left position
			$GLOBALS["db"]->query ("SELECT order_id FROM ".dbTable("block")."
			WHERE position = 'L' ORDER BY order_id DESC LIMIT 1",
			__FILE__, __LINE__);
			$row = $GLOBALS["db"]->fetchRow();
			$order_id = $row["order_id"]+1;

			// inserting into database
			$GLOBALS["db"]->query ("
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
					auth_access
				)
					VALUES
				(
					'".($id = $GLOBALS["db"]->findFreeId("block"))."',
					'{$icon}',
					'{$position}',
					'1',
					'{$title}',
					'{$content}',
					'*',
					'{$scriptpath}',
					'{$scriptvars}',
					CURRENT_TIMESTAMP(),
					'{$order_id}',
					'',
					'block',
					'*'
				)
			", __FILE__, __LINE__);

			return $id;
		}

		/**
		* checkAuth(module to check, allowed groups, group to check);
		* @returns: true if module is matched in allowed groups (or an * is
		* found in allowed groups) and if user is granted with $auth_check privileges
		*/
		function checkAuth($module_check, $allowed, $auth_check) {
			// variable below is just for matching a pattern
			$m = "\s*(".(($module_check != "*") ? $module_check."|": "")."\*)\s*";
			return (preg_match("/^$m$|^$m,|,$m,|,$m$/", $allowed) && appAuthorize($auth_check));
		}

		/**
		* getPositions()
		* @returns: an array of 'human readable' block positions, may be used in
		* conjunction with createDropDown();
		*/
		function getPositions() {
			$rreturn = array();

			$bpos = explode(",", conf::getKey("blocks", "block_positions"));

			foreach ($bpos as $pos)
				$return[$pos] = _L("L_POSITION_".$pos);

			return $return;
		}

		/**
		* getScripts();
		* @returns: an array of human readable blockscripts files for use
		* in conjuction with createDropDown()
		*
		*/
		function getScripts() {
			$modules = gekkoModule::getList();
			$scripts = Array("" => _L("L_NO_SCRIPT"));
			foreach ($modules as $module) {
				$blockdir = GEKKO_SOURCE_DIR."modules/$module/blocks/";

				if (file_exists($blockdir) && is_dir($blockdir)) {
					$dp = opendir($blockdir);
					while (($block = readdir($dp)) !== false) {
						if ($block[0] != ".") {
							if (!isset($last) || $last != $module)
								$scripts[$module] = "&raquo;&nbsp;".ucfirst($module);
							$scripts["{$module}/{$block}"] = "&nbsp;&nbsp;&nbsp;".ucfirst($module)."::".prettyFileName($block);
							$last = $module;
						}
					}
					closedir($dp);
				}
			}
			return $scripts;
		}


		/**
		* varSerialize(text variables)
		* @returns: serialized array version of the given text/array variables
		*
		*/
		function varSerialize($scriptvars) {
			$return = Array();
			if (is_array($scriptvars)) {
				// when $scriptvars are in array form
				foreach ($scriptvars as $var => $val)
					$return[$var] = trim($val);
			} else {
				// converting from text format
				$scriptvars = explode("\n", $scriptvars);
				foreach ($scriptvars as $line) {
					$line = trim($line);
					// variable = my value\n
					if ($line && preg_match("/^(.*?)\s*?=\s*?(.*?)$/", $line, $match))
						$return[$match[1]] = trim($match[2]);
				}
			}
			return serialize($return);
		}


		/**
		* varUnserialize(serialized array variables);
		* @return: text version of the given serialized array
		*/
		function varUnserialize($scriptvars) {
			$return = "";
			$scriptvars = unserialize($scriptvars);
			foreach ($scriptvars as $key => $value)
				$return .= "$key=$value\n";
			return $return;
		}
	}
?>
