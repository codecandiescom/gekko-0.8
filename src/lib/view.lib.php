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

	Class htmlWidget {
		var $items = array();
		var $attributes = array();
		function addItem($html, $attr = array()) {
			$this->items[] = array(
				"content" => $html,
				"attributes" => $attr
			);
		}
		function drawAttr($attr) {
			$buff = array();
			foreach ($attr as $name => $value)
				$buff[] = "$name=\"".str_replace('"', '\"', $value)."\"";
			if ($buff)
				return ' '.implode(' ', $buff);
			return null;
		}
		function drawElement($tag, $data = array()) {
			$attributes = isset($data["attributes"]) ? $this->drawAttr($data["attributes"]) : null;
			
			$content = isset($data["content"]) ? (is_array($data["content"]) ? implode("\n", $data["content"]) : $data["content"]) : null;

			$buff = "<{$tag}{$attributes}>\n";
			$lines = explode("\n", $content);
			foreach ($lines as $line)
				$buff .= "\t".rtrim($line)."\n";
			$buff .= "</$tag>";
			
			return $buff;
		}
	}

	Class htmlTable extends htmlWidget {
		var $rowSpan = 5;
		function htmlTable($attr = array()) {
			$this->attributes = $attr;
		}
		function compile() {
			$tr = $td = array();

			$total_tds = count($this->items);
			for ($i = 1; $i <= $total_tds; $i++) {
				$item =& $this->items[$i-1];
				$td[] = $this->drawElement("td", $item);
				if (($i%$this->rowSpan) == 0 || $i == $total_tds) {
					$tr[] = $this->drawElement("tr", array("content" => $td));
					$td = array();
				}
			}

			return $this->drawElement("table", array("content" => $tr, "attributes" => $this->attributes));
		}
	}

	Class gekkoModuleController {
		
		var $urlMapVars;
		var $urlMap;
		var $libRequire;
		var $view;
		var $moduleName;
		var $action;
		var $viewType = "main";

		function url($type, $map, $title = null, $icon = null, $confirm = false, $style = null) {
			switch ($type) {
				case "admin":
					$map = "index.php/module=admin/base=".$this->moduleName."/{$map}";
				break;
				case "action":
					$map = "modules/".$this->moduleName."/actions.php?auth={C_AUTH_STR}&{$map}";
				break;
				default:
					$map = "index.php/module=".$this->moduleName."/{$map}";
				break;
			}
			if ($title || $icon)
				return createLink($map, $title, $icon, $confirm, $style);
			return urlEvalPrototype($map);
		}

		function standardView() {
			$this->view = new gekkoView();
			$this->view->tpl->insert("BLOCK_CONTENT", "{$this->moduleName}/".$this->viewType.".".($this->action == "index" ? "default" : $this->action).".tpl");
			$this->view->tpl->setArray (
				array (
					"block_title"	=> _l("L_".strtoupper($this->moduleName)),
					"block_icon"	=> createIcon($this->moduleName.".png", 16),
					"action"		=> $this->action,
					"return"		=> ''
				)
			);
			$this->view->moduleName = $this->moduleName;
		}

		function widgetView() {
			$this->view = new gekkoView();
			$this->view->tpl->load("{$this->moduleName}/".$this->viewType.".".($this->action == "index" ? "default" : $this->action).".tpl");	
			$this->view->moduleName = $this->moduleName;
		}

		function gekkoModuleController() {

			switch ($this->viewType) {
				case "main":
					$this->urlMap = "null/action=alpha";
				break;
				case "admin":
					$this->urlMap = "null/null/action=alpha";
				break;
			}

			if ($this->libRequire) {
				foreach ($this->libRequire as $lib)
					appLoadLibrary($lib);	
			}
			
			$this->urlMapVars = urlImportMap($this->urlMap);
			extract($this->urlMapVars);

			if (!$action)
				$action = "index";

			$this->action = $action;

			if (substr($action, 0, 1) != '_' && !method_exists("gekkoModuleController", $action)) {

				if (!method_exists($this, $action)) {
					$action = $this->action = "index";
				}

				$this->moduleName = substr(get_class($this), 0, -10);

				$this->$action();

				if ($this->view)
					$this->view->draw();

			} else {
				echo accessDenied();
			}
		}
		function urlMap($map) {
			return urlImportMap("null/action=alpha/".ltrim($map, '/'));
		}
	}

	Class gekkoView {
		var $db;
		var $tpl;
		var $rows = array();
		var $pager = null;
		var $blocks = array();
		var $core = array();
		var $actions = array();
		var $moduleName;

		function gekkoView() {
			$this->db =& $GLOBALS["db"];
			$this->tpl = new contentBlock();
		}
		
		function pager($url_prototype, $total, $current) {
			$this->pager = array(
				"total" => $total,
				"current" => $current,
				"url_prototype" => $url_prototype
			);
		}

		function query($sql, $file, $line, $callback = null) {
			
			$sql = trim($sql);

			if ($this->pager) {
				if (strtolower(substr($sql, 0, 6)) == "select") {

					preg_match("/SELECT(.*)FROM\s*([^\s]*)(.*)/is", $sql, $match);

					$pager = $this->db->pager (
						"
						SELECT
							count(*) as total_items
						FROM
							{$match[2]}
						{$match[3]}
						",
						$this->pager["url_prototype"],
						$this->pager["current"],
						$this->pager["total"]
					);

					foreach ($pager as $key => $val)
						$this->pager[$key] = $val;

					$sql .= " ".$this->pager["sql"];
				} else {
					trigger_error("Can't use pagination without a select statement!", E_USER_ERROR);
				}
			}
			
			$res = $this->db->query($sql, $file, $line, true);
			
			$rows = array();
		
			if ($this->db->numRows($res)) {
				while ($row = $this->db->fetchRow($res)) {

					$htmlExceptions = array();
					while (list($key) = each($row)) {
						if (preg_match("/_([a-z]*)_(.*)/", $key, $match)) {
							list(,$process, $field) = $match;
							switch ($process) {
								case "format":
									$htmlExceptions[] = $field;
									$row[$field] = $row[$key];
									format::style($row[$field], $row["_style_author_id"]);
									unset($row[$key]);
								break;
								case "edit":
									$htmlExceptions[] = $field;
									$row[$field] = $row[$key];
									format::htmlFilter($row[$field], $row["_style_author_id"]);
									unset($row[$key]);
								break;
								case "userlink":
									$htmlExceptions[] = $field;
									$row[$field] = users::createUserLink($row[$key]); 
									unset($row[$key]);
								break;
							}
						}
					}

					saneHTML($row, implode(',', $htmlExceptions));

					if ($callback)
						$row = $callback($row);
					
					if (isset($row["actions"]) && is_array($row["actions"]))
						$row["actions"] = $this->buttonBox($row["actions"]);

					if ($row)
						$rows[] = $row;
				}
				return $rows;
			} else {
				return null;
			}
		}
		
		function buttonBox($actions) {
			$buttonBox = new tplButtonBox();
			foreach ($actions as $action) {
				if (is_array($action)) {
					switch ($action["action"]) {
						case "edit":
							$buttonBox->add(createLink("index.php/module=admin/base=".$this->moduleName."/action=edit?id={$action["id"]}", _L("L_EDIT"), "edit.png"));
						break;
						case "create":
							$buttonBox->add(createLink("index.php/module=admin/base=".$this->moduleName."/action=create", _L("L_CREATE"), "new.png"));
						break;
						case "delete":
							$buttonBox->add(createLink("modules/".$this->moduleName."/actions.php?auth={C_AUTH_STR}&action=delete&id={$action["id"]}", _L("L_DELETE"), "delete.png", true));
						break;
					}
				} else {
					$buttonBox->add($action);
				}
			}
			return $buttonBox->make();
		}

		function draw($output = true) {

				foreach ($this->blocks as $block => $data) 
					$this->compile($data, strtoupper($block));

				if (count($this->actions))
					$this->tpl->set("actions", $this->buttonBox($this->actions));
				else
					$this->tpl->set("actions", "");

				if (isset($this->core[0]))
					$this->tpl->setArray($this->core[0]);
				else if (count($this->core))
					$this->tpl->setArray($this->core);
		
				if ($this->pager)
					$this->tpl->set("pager", $this->pager["html"]);

				if ($output)
					echo $this->tpl->make();
				else
					return $this->tpl->make();
		}
		function compile($rows, $block = null) {
			if (isset($rows[0])) {
				if ($block) {
					foreach ($rows as $row) {
						$this->tpl->setArray($row, $block);
						$this->tpl->saveBlock($block);
					}
				} else {
					$this->tpl->setArray($rows[0]);
				}
				if ($this->pager) {
					$this->tpl->set("pager", $this->pager["html"]);
				}
				return $this->tpl->make();
			}
			return null;
		}
	}

	Class gekkoXMLFeed extends gekkoView {
		function gekkoXMLFeed($type = null) {
			
			if (!defined("GEKKO_USE_ABSOLUTE_URLS"))
				define("GEKKO_USE_ABSOLUTE_URLS", true);

			$type = preg_replace("/[^0-9a-zA-Z.]/", '', $type);
			$type = preg_replace("/\..*/", '', $type);
			
			if (!$type)
				$type = "rss";
			
			if (file_exists(GEKKO_TEMPLATE_DIR."default/_layout/$type.feed.tpl")) {
				$this->gekkoView();
				$this->tpl->load("_layout/$type.feed.tpl");
			} else {
				abort("Unknown feed type.");
			}
		}
		function feedQuery($sql, $file, $line, $callback = null) {
			$this->blocks["item"] = $this->query($sql, $file, $line, $callback);
		}
		function serve() {

			$lastBuildTime = 0;

			while (list($i) = each($this->blocks["item"])) {
				$row =& $this->blocks["item"][$i];

				if ($row["creationtime"] > $lastBuildTime)
					$lastBuildTime = $row["creationtime"];

				$tmp = cacheFunc("users::fetchInfo", $row["author_id"], "email, realname");
				$row["author_name"] = $tmp["realname"] ? $tmp["realname"] : "unknown";
				$row["author_email"] = $tmp["email"] ? $tmp["email"] : "unknown@example.com";
			}

			$this->tpl->setArray(
				array(
					"lastbuildtime" => $lastBuildTime
				)
			);

			header("Content-Type: text/xml; charset=utf-8");
		
			gzInit();

			$buff = $this->draw(false);
			
			cacheSave(GEKKO_PAGE_CACHE_KEY, $buff);
			
			echo $buff;

			gzOutput();

			appShutdown();
		}
	}
?>
