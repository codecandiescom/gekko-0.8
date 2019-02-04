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

	define("IN-TEMPLATE", true);

	## TEMPLATE MARKS ##
	// Encloses subtitle
	define("SUBTITLE_LEFT", "(");
	define("SUBTITLE_RIGHT", ")");

	// Encloses maintitle
	define("TITLE_LEFT", "");
	define("TITLE_RIGHT", " :: ");

	// Title separator (title [separator] website name)
	define("TITLE_SEPARATOR", " | ");

	// open templates/*/_layout/index.tpl to add marks
	$marks = Array("HEAD", "FOOTER", "TITLE", "SUBTITLE", "STYLE");
	foreach ($marks as $mark)
		$GLOBALS["L"]["V_DOC_{$mark}"] = "";

	require(GEKKO_LIB_DIR."view.lib.php");

	## TEMPLATE ENGINE ##
	/**
	* GekkoTemplateEngine
	* Gekko's class for parsing dynamic .tpl files.
	*/
	Class GekkoTemplateEngine {

		var $templates_dir;
		var $gzip_output;
		var $buff;
		var $template;

		function GekkoTemplateEngine($gzip = false) {
			$this->templates_dir = GEKKO_TEMPLATE_DIR._L("C_SITE.TEMPLATE")."/";
		}

		/**
		* evalStatement(statement, [array vars])
		* @returns: true of false depending on the result of evaluating statement.
		* You can use regular PHP statements. Keys of array variables passed in $vars
		* will be taken as variable names in the statement. Let's suppose you have this
		* array:
		* $foo = Array ('key' => 'bar');
		* Then the following function should return _true_:
		* evalStatement('"bar" == $key', $foo);
		*
		*/
		function evalStatement(&$statement, &$vars) {

			if ($statement) {
				$eval = preg_replace('/\$([\w\-\_\.]*)/e', 'isset($vars["\\1"]) ? \'$vars["\\1"]\' : "null"', $statement);

				$func = create_function (
					'$vars',
					'return ('.$eval.');'
				);

				return $func($vars);
			} else {
				return true;
			}
		}

		/**
		* splitBlocks(template);
		*
		* Converts the given template into an array of blocks
		*
		*	...some code...
		*	<!--{bgn: BLOCKNAME}-->
		*		...
		*		Block content
		*		<!--{bgn: BLOCK2}-->
		*			Nested block
		*		<!--{end: BLOCK2}-->
		*		...
		*	<!--{end: BLOCKNAME}-->
		*	...some code...
		*/
		function splitBlocks($buff) {
			$ret = Array();

			if (is_array($buff))
				$buff = $buff["CORE"];

			// locating block tags
			preg_match_all("/<!--{\s*bgn:\s*(\w*)\s*}-->/", $buff, $bpat);

			$ret["CORE"] = $buff;

			foreach ($bpat[1] as $i => $block_name) {

				// matching the whole block and its contents
				preg_match("/".preg_quote($bpat[0][$i], "/")."(.*?)<!--{\s*end:\s*$block_name\s*}-->/s", $ret["CORE"], $block);

				if (isset($block[0])) {
					// block names must be uppercased
					$block_name = strtoupper($block_name);

					// replacing whole block content with {%block_name}, an new child array will be added
					// to our $ret, this new array will contain a key named "CORE" (where the block contents
					// will be placed)
					$ret["CORE"] = str_replace($block[0], "{%{$block_name}}", $ret["CORE"]);

					// recursion for evaluating nested blocks
					$ret[$block_name] = $this->splitBlocks($block[1]);
				}
			}
			return $ret;
		}
		function escape($str) {
			return preg_replace('/[{}\"]/', "\\\\\\0", $str);
		}
		function unescape($str) {
			return preg_replace('/\\\([{}\"])/', "\\1", $str);
		}

		/**
		* evalCondition(template, variables);
		* @returns: template buffer
		*
		* Will show or remove specific parts of the code based in a condition
		* Example:
		*
		* <!--if (1 == 2)-->
		*	This will not be shown
		* <!--endif-->
		*/
		function evalCondition($buff, $vars = array()) {
			$stack = Array();

			if (preg_match_all("/<!--\s*if\s*\(.*\)-->|<!--endif-->/", $buff, $matches)) {

				$fpos = $offset = 0;
				foreach ($matches[0] as $i => $statement) {
					preg_match("/<!--\s*([a-z]*).*/", $statement, $sub);

					// position of the pattern
					$fpos = strpos($buff, $sub[0], $offset);
					$offset = $fpos + strlen($sub[0]);

					switch ($sub[1]) {
						case "if":
							array_push($stack, $fpos);
						break;
						case "endif":
							if (empty($stack)) {
								//gdebug($buff);
								trigger_error("Unmatched condition '{$sub[0]}'.", E_USER_ERROR);
							} else
								$ipos = array_pop($stack);
								$fpos = $fpos - $ipos + strlen($sub[0]);

								if (empty($stack)) {
									// this is a first level conditional block

									preg_match (
										"/^(<!--\s*if\s*\((.*?)\)\s*-->)(.*?)(<!--\s*endif\s*-->)$/s",
										substr($buff, $ipos, $fpos),
										$matches
									);

									list($whole, $begin, $condition, $content, $end) = $matches;

									if ($this->evalStatement($condition, $vars))
										$content = $this->evalCondition($content, $vars);
									else
										$content = "";

									$buff = substr($buff, 0, $ipos).$content.substr($buff, $fpos+$ipos);

									// new offset
									$offset = $ipos+strlen($content);
								}
						break;
					}
				}
			}
			return $buff;
		}

		/**
		* replaceConstants(template, @variables)
		* @returns: template buffer
		* Replaces template constants with values of variables
		* passed in @variables array.
		*/
		function replaceConstants(&$buff, &$vars) {
			preg_match_all("/{([\w\.\_\-]*?)}/", $buff, $matches);

			foreach ($matches[1] as $i => $lang_constant)
				if (array_key_exists($lang_constant, $vars))
					$buff = str_replace($matches[0][$i], $this->escape($vars[$lang_constant]), $buff);

			return $buff;
		}
		/*
			parsePlain(&$buff, array &$vars, bool &$dinamic);

			Compiles a template buffer in plain mode ($data must
			not be passed as an array). Executes all conditional
			blocks (when $dinamic is true), performs variable
			replacement...
		*/
		function parsePlain(&$buff, $vars, $dinamic) {

			if ($dinamic) {

				$buff = $this->evalCondition($buff, $vars);

				$plain = preg_match_all("/{=(.*?)=}/s", $buff, $match);

				if ($match[1]) {
					foreach ($match[1] as $i => $func) {

						// if you're having problems check that your template variable doesn't
						// has a dot, or something similar...
						$func = preg_replace('/{([\w\_\-]*)}/e', 'isset($vars["\\1"]) ? \'$\\1\' : \'{\\1}\'', $func);

						$lambda = create_function (
							'$vars',
							'extract($vars); return '.$func.';'
						) or die(gdebug("FUNCTION: ".$func));

						$buff = str_replace($match[0][$i], $lambda($vars), $buff);
					}
				}
			}

			// replacing language constants
			$buff = $this->replaceConstants($buff, $vars);
		}
		/*
			parseDynamic(&$buff, array &$vars, bool $dinamic);

			Compiles a template block array or a plain template
			buffer executing conditional blocks and performs
			variable replacement in recursive mode.
		*/
		function parseDynamic(&$buff, $vars, $dinamic = false) {
			foreach ($buff as $key => $data) {
				if (is_array($data)) {
					$this->parseDynamic($buff[$key], $vars, $dinamic);
				} else {
					$this->parsePlain($buff[$key], $vars, $dinamic);
				}
			}
		}
		/*
			parse (buff, array vars, bool dinamic);

			Compiles and returns the given template buffer, that
			could be a block array or a plain string.

			Please refer ro parseDynamic and parsePlain functions
		*/
		function parse($buff, $vars, $dinamic = false) {
			if (is_array($buff)) {
				$this->parseDynamic($buff, $vars, $dinamic);
			} else {
				$this->parsePlain($buff, $vars, $dinamic);
			}
			return $buff;
		}
		function error($msg, $type) {
			trigger_error("Gekko Template Engine: ".$msg." (".$this->template.")", $type);
		}
		function coreEval(&$buff) {
			// template functions {function("argument")}
			while (preg_match("/{([a-z0-9]*)\s*\(['\"](.*?)['\"]\)}/", $buff, $match)) {
				switch ($match[1]) {
					case "include":
						$buff = str_replace($match[0], loadFile(dirname($this->template)."/".$match[2]), $buff);
					break;
					default:
						$this->error("Template Function '{$match[1]}' is not defined.", E_USER_ERROR);
						// break(2);
					break;
				}
			}
		}

		/*
			load(filename, bool lang, bool dinamic);

			Reads a template file into memory.
			Returns the loaded buffer

			Templates are stored by default in ./templates/default
			You must either specify an absolute path (beginning with "/") or
			a relative one (relative to $this->templates_dir)

			When $lang is true, after loading this block it will be parsed
			agains Gekko language variables.
		*/
		function load ($template, $parse_lang = true, $dynamic = false) {

			// is passing a full path?
			$template_file = absolutePath($template) ? $template : $this->templates_dir.$template;

			if (!file_exists($template_file))
				$template_file = GEKKO_TEMPLATE_DIR."default/$template";

			if (!file_exists($template_file))
				$template_file = GEKKO_MODULE_DIR.preg_replace("/([^\/]*)\//", "\\1/view/", $template);

			$this->template = $template_file;

			if (file_exists($template_file)) {

				// loading template contents to be parsed
				$buff = loadFile($template_file);

				// this might be useful...
				// $this->syntaxCheck();

				// evaluating gtpl core functions
				$this->coreEval($buff);

				$buff = $this->splitBlocks($buff);

				if ($parse_lang)
					$buff = $this->parse($buff, $GLOBALS["L"], $dynamic);

			} else {
				trigger_error("Couldn't load template: '$template_file'", E_USER_ERROR);
			}
			return $buff;
		}
		/*
			tar(blocks buffer);

			Performs the last step on template compilation, this function
			converts from a block buffer to a single string and returns it.
		*/
		function tar(&$buff) {
			if (is_array($buff)) {
				if (count($buff)) {
					preg_match_all("/{%(.*?)}/", $buff["CORE"], $matches);
					foreach ($matches[0] as $i => $void) {
						$buff["CORE"] = str_replace($matches[0][$i], $this->tar($buff[$matches[1][$i]]), $buff["CORE"]);
					}
				}
				return $buff["CORE"];
			} else {
				return $buff;
			}
		}
		/*
			make(template buffer);

			Returns a compiled and "ready-to-output" version of the given
			template buffer.
		*/
		function make(&$buff) {
			return trim($this->unescape($this->tar($buff)));
		}
		/*
			output(template buffer, bool fix_code);

			Outputs the compiled template buffer to user's
			browser and can perform some code improvements
			when fix_code is 1.
		*/
		function output(&$buff, $trigger_plugins = true) {
			$appPluginHandler =& $GLOBALS["appPluginHandler"];

			if (!$buff)
				$buff = $this->buff;

			$buff = $this->make($buff);

			if ($trigger_plugins)
				$appPluginHandler->triggerEvent("template/afterParse", $buff);

		}
	}


	Class BlockWidget {

		var $widget_template;
		var $widget_variable = Array();
		//var $gte;

		var $icondir;
		var $cache;
		var $blocks = Array();
		var $bvars = Array();
		var $swcls = Array();

		function beginCapture() {
			ob_start();
		}
		function endCapture($initvars = false) {
			$gtpl =& $GLOBALS["T"];

			$capture = ob_get_contents();

			$gtpl->coreEval($capture);

			$buff = $gtpl->splitBlocks($capture);

			$buff = $gtpl->parse($buff, $GLOBALS["L"]);

			$this->split($buff);

			ob_clean();
		}
		/*
			BlockWidget(template, array initialization variables)
			Can load a template file using initialization variables.

			Please refer to $this->load(); function.
		*/
		function BlockWidget ($template = null, $initvars = null) {
			if ($template)
				$this->load($template, $initvars);
			return true;
		}
		function block($block, $set = null, $buff = false) {
			$path = explode(".", $block);
			$block = &$this->blocks;
			foreach ($path as $p) {
				$block = &$block[$p];
			}
			$k = ($buff) ? "BUFF" : "CORE";
			$p = &$block[$k];
			if ($set !== null) $p = rtrim($set);
			return $p;
		}
		function split($buff) {
			if (is_array($buff)) {
				$this->block_tpl_core = $buff["CORE"];
				unset($buff["CORE"]);
				foreach ($buff as $block => $content) {
					$this->blocks[$block] = $content;
					$this->bvars[$block] = Array();
				}
			} else {
				$this->block_tpl_core = $buff;
			}
		}
		/*
			load (template file, initizalization vars);

			Loads into cache the given template file using
			initialization variables.
		*/
		function load ($template = null, $initvars = null) {
			$gtpl =& $GLOBALS["T"];

			if ($this->cache) {
				// we don't need to reload this file.
				$this->widget_template = $this->cache;
			} else {

				// loading file using GekkoTemplateEngine
				$buff = $gtpl->load($template, true);

				if ($initvars)
					$buff = $gtpl->parse($buff, $initvars);

				$this->split($buff);
			}
		}
		/*
			vren (old name, nre name, [block]);

			Renames a template variable for the given block or in the
			main buffer when $block argument is null
		*/
		function vren($old_name, $new_name, $block = null) {
			if ($block) {
				$this->block($block, str_replace("{".$this->vkey($old_name)."}", "{".$this->vkey($new_name)."}", $this->block($block)));
			} else {
				$this->block_tpl_core = str_replace("{".$this->vkey($old_name)."}", "{".$this->vkey($new_name)."}", $this->block_tpl_core);
			}
		}
		/*
			insert (where, template file, [initialization variables], [block]);

			Replaces $where variable with a (non-compiled) template buffer.

			Since this template buffer is not compiled you can insert templates
			that uses blocks.
		*/
		function insert ($where, $template, $initvars = false, $block = null) {
			$where = "{".$this->vkey($where)."}";

			// loading template
			$tmp = $GLOBALS["T"]->load($template, 1);

			if ($initvars)
				$tmp = $GLOBALS["T"]->parse($tmp, $initvars, true);

			if (is_array($tmp)) {
				// inserting a block buffer
				if ($block) {
					$this->block($block, str_replace($where, $tmp["CORE"], $this->block($block)));
				} else {
					$this->block_tpl_core = str_replace($where, $tmp["CORE"], $this->block_tpl_core);
				}
				unset($tmp["CORE"]);
				foreach ($tmp as $block => $content) {
					$this->blocks[$block] = $content;
				}
			} else {
				// inserting a plain template buffer
				if ($block) {
					$this->block($block, str_replace($where, $tmp, $this->block($block)));
				} else {
					$this->block_tpl_core = str_replace($where, $tmp, $this->block_tpl_core);
				}
			}
			return true;
		}
		/*
			saveBlock(block, clean variables)

			Compiles and appends the given block to its corresponding
			output buffer
		*/
		function saveBlock ($block, $clearvars = true) {

			// alternate class added by default
			if (!isset($this->swcls[$block]))
				$this->swcls[$block] = 0;

			$this->bvars[$block]["V_SW_CLASS"] = sw_class($this->swcls[$block]);

			// appending block to its buffer, removing leading spaces
			$buff = $this->block($block, null, true).$this->make($block);
			$buff = rtrim($buff);

			$path = explode(".", $block);
			if (count($path) > 1) {
				$bname = $path;
				array_pop($bname);
				$this->bvars[implode(".", $bname)]["BUFFER.".implode(".", $path)] = 1;
			} else {
				$this->widget_variable["BUFFER.$block"] = 1;
			}

			$this->block($block, rtrim($buff), 1);

			if ($clearvars) {
				$this->bvars[$block] = Array();
			}
		}
		function compileBlock($block) {
			$this->make($block);
			return $this->blocks[$block]["BUFF"];
		}
		/*
			mkBlocks (block name, [bool core]);

			Converts the given block into a plain string. Useful when
			compiling blocks.
		*/
		function mkBlocks($block, $core = false) {
			$buff = Array();
			if (isset($block["CORE"])) {
				if (isset($block["BUFF"]) && $block["BUFF"] && !$core) {
					// hidden blocks
					$buff["CORE"] = $block["BUFF"];
					//$buff["CORE"] = ($block["BUFF"] == BLOCK_HIDE_FLAG) ? "": $block["BUFF"];
				} else {
					$buff["CORE"] = $block["CORE"];
				}
			}
			unset($block["CORE"], $block["BUFF"]);
			foreach ($block as $key => $val) {
				// isn't a pretty recursion?
				$buff[$key] = $this->mkBlocks($block[$key]);
			}
			return $buff;
		}
		/*
			make ([block], [bool is_core], [exclude list]);

			Compiles the given block (if any), or the whole block
			buffer (if no block is given).
		*/
		function make ($block = null, $core = false, $exclude = array()) {
			$buff = "";
			if ($block) {
				// simple block compilation
				$buff = $this->blocks;

				$path = explode(".", $block);

				foreach ($path as $p)
					$buff = $buff[$p];

				$tmp = $this->mkBlocks($buff, 1);
				$buff = $GLOBALS["T"]->make($tmp);

				$lang = isset($this->bvars[$block]) ? $this->bvars[$block] : array();
				$buff = $GLOBALS["T"]->unescape($GLOBALS["T"]->parse($buff, $lang, true));
			} else {
				if (count($this->blocks)) {
					$buff = Array();

					$buff["CORE"] = $GLOBALS["T"]->parse($this->block_tpl_core, $this->widget_variable, true);

					$blocks = $this->mkBlocks($this->blocks);

					$buff = array_merge($buff, $blocks);

					$buff = $GLOBALS["T"]->make($buff);

				} else {
					$buff = $GLOBALS["T"]->unescape($GLOBALS["T"]->parse($this->block_tpl_core, $this->widget_variable, true));
				}
			}

			return $buff;
		}

		// returns template variable name
		function vkey ($vname) { return "V_".strtoupper($vname); }

		// gets $what variable from $block
		function get ($what, $block = null) {
			if ($block) {
				return ($b = $this->bvars[$block][$this->vkey($what)]) ? $b : "{E_UNDEFINED}";
			} else {
				return ($b = $this->widget_variable[$this->vkey($what)]) ? $b : "{E_UNDEFINED}";
			}
		}

		// sets $what variable in $block buffer
		function set ($what, $value, $block = null) {
			if ($block) {
				if (!isset($this->bvars[$block]))
					$this->bvars[$block] = array();
				$this->bvars[$block][$this->vkey($what)] = $value;
			} else {
				$this->widget_variable[$this->vkey($what)] = $value;
			}
			return true;
		}

		// clears $what variable in $block buffer
		function clear ($what, $block = null) {
			if ($block) {
				$this->bvars[$block][$this->vkey($what)] = null;
			} else {
				$this->widget_variable[$this->vkey($what)] = null;
			}
		}

		// sets an array of variables in $block
		function setArray ($vars, $block = null, $destroy = true) {
			foreach ($vars as $key => $val) {
				$this->set($key, $val, $block);
			}
			return true;
		}
		/*
			reset([block], [bool keep buffer]);

			An attemp to revert a block to its original status (before
			any work with it)
		*/
		function reset ($block = null, $keep_buffer = false) {
			if ($block) {
				$this->bvars[$block] = array();
				unset($this->swcls[$block]);
				// cleaning buffer
				if (!$keep_buffer)
					$this->block($block, " ", true);
				// reverting to original
				$this->block($block, $this->block($block));
			} else {
				// using cache
				$this->block_tpl_core = $this->cache;
			}
		}

		// saves main buffer into cache
		function save () {
			$this->cache = $this->block_tpl_core;
		}
	}


	function getThemeInfo($theme) {
		$data = file_get_contents($theme);

		$tags = Array("author", "license", "acknowledgements");
		$info = Array();

		foreach ($tags as $tag) {
			preg_match("/<$tag>(.*)<\/$tag>/s", $data, $match);
			if (isset($match[1]))
				$info[strtoupper($tag)] = trim($match[1]);
		}
		return $info;
	}

	Class contentBlock extends BlockWidget {
		function contentBlock ($class = "contentBlock", $source = "_layout/contentBlock.tpl") {
			$this->widget_variable = Array (
				"V_BLOCK_ICON" => createIcon("block.png", 16),
				"V_BLOCK_TITLE" => "New Block",
				"V_BLOCK_CONTENT" => "Block Content",
				"V_BLOCK_CLASS" => $class
			);
			$this->load($source);
		}
	}

	Class Panel {
		var $icons = Array();
		var $blocks = Array();
		var $icon = "panel";
		var $title = "New Panel";
		var $isize = 48;
		var $emblem = "";
		function panel($title, $icon = "panel", $size = "48") {
			$this->isize = $size;
			$this->icon = $icon;
			$this->title = $title;
			$this->icons[""] = Array();
		}
		function addIcon($icon, $category = null) {
			if (!isset($this->icons[urlencode($category)])) $this->icons[$category] = Array();
			$this->icons[urlencode($category)][] = Array($icon, $this->emblem);
		}
		function addBlock($block) {
			$this->blocks[] = $block;
		}
		function iconSetEmblem($emblem) {
			$this->emblem = $emblem;
		}
		function iconClearEmblem() {
			$this->iconSetEmblem('');
		}
		function icon($icon, $href, $text) {
			return createLink($href, createIcon($icon, $this->isize)." $text");
		}
		function getIconTitle($url) {
			preg_match("/<br\s\/>(.*?)<\/a>/", $url, $match);
			return $match[1];
		}
		function make() {
			$tpl = new contentBlock();
			$tpl->insert("BLOCK_CONTENT", "_layout/panel.tpl");
			$tpl->setArray (
				Array (
					"BLOCK_TITLE" => $this->title,
					"BLOCK_ICON" => createIcon($this->icon, 16)
				)
			);
			foreach ($this->icons as $key => $icons) {
				if (count($icons)) {
					$tpl->reset("CATEGORY.ICON");
					foreach ($icons as $_icon) {
						$icon = $_icon[0];
						$emblem = $_icon[1];
						$tpl->setArray(
							Array (
								"ICON" => $icon,
								"EMBLEM" => $emblem ? createIcon($emblem, 16) : ''
							), "CATEGORY.ICON"
						);
						$tpl->saveBlock("CATEGORY.ICON");
					}
					$tpl->setArray (
						Array ("CATEGORY" => urldecode($key)), "CATEGORY"
					);
					$tpl->saveBlock("CATEGORY");
				}
			}
			foreach ($this->blocks as $block) {
				$content = blocks::loadScript($block);
				if ($content) {
					$tpl->setArray(
						Array (
							"TITLE" => $block["title"],
							"CONTENT" => $content
						), "BLOCK"
					);
					$tpl->saveBlock("BLOCK");
				}
			}
			return $tpl->make();
		}
	}

	##  MARKS ##
	function pageSetMarkContent($tag, $value) {
		$GLOBALS["L"]["V_DOC_{$tag}"] .= "$value";
	}

	function pageSetTitle($title, $type = 0) {
		pageSetMarkContent("TITLE", TITLE_LEFT.$title.TITLE_RIGHT);
	}

	function pageSetSubtitle($subtitle) {
		pageSetMarkContent("SUBTITLE", SUBTITLE_LEFT.$subtitle.SUBTITLE_RIGHT);
	}

	##  JAVASCRIPT ENHACEMENTS ##

	function appLoadJavascript() {
		$args = func_get_args();
		foreach ($args as $arg)
			pageSetMarkContent("HEAD", '<script type="text/javascript" src="'._L("C_SITE.REL_URL").$arg.'"></script>');
	}

	function jscriptObfuscateString($string) {
		// Obfuscates a single string for use as Javascript code.
		// The main pourpose for doing this is to avoid lowlife people
		// that create simple spam spiders to be attacking our website.
		// Commonly, those spiders doesn't evaluate javascript (they don't
		// need to do such a thing), so I think this is simple and useful
		// method to keep those annoying spiders out from our website.
		$len = strlen($string);
		$mod = Array();
		$i = 0;
		while ($i < $len) {
			$o = rand(1, $len - $i);
			$part = substr($string, $i, $o);

			switch (rand(0, 2)) {
				case 0:
					$mod[] = "\"".$part."\"";
				break;
				case 1:
					$buff = "";
					for ($j = 0; $j < strlen($part); $j++) {
						$buff .= "%".dechex(ord(substr($part, $j, 1)));
					}
					$mod[] = "unescape(\"$buff\")";
				break;
				case 2:
					switch (rand(0, 7)) {
						case 0: $cmp = "<"; break;
						case 1: $cmp = ">"; break;
						case 2: $cmp = "&&"; break;
						case 3: $cmp = "=="; break;
						case 4: $cmp = "!="; break;
						case 5: $cmp = "||"; break;
						case 6: $cmp = "<="; break;
						case 7: $cmp = ">="; break;
					}
					$buff = Array();
					for ($j = 0; $j < strlen($part); $j++) {
						$eval = "(".rand(0, 10).$cmp.rand(0, 10).")";
						eval("\$val = $eval;");
						$t = substr($part, $j, 1);
						$f = dechex(rand(0,16));
						$buff[] = "($eval?\"".($val ? $t : $f)."\":\"".($val ? $f : $t)."\")";
					}
					$mod[] = implode("+", $buff);
				break;
			}
			$i += $o;
		}
		return implode("+", $mod);
	}

	## HTML ##
	/**
	* For displaying action buttons
	*/
	Class tplButtonBox {
		var $separator = ' | ';
		var $links = Array();
		function add() {
			$args = func_get_args();
			foreach ($args as $arg) {
				// language constant replacement
				$this->links[] = preg_replace("/{([\w\-\_\.]*?)}/e", "_L('\\1')", $arg);
			}
		}
		function make($style = null) {
			$buttons = $this->links;
			if ($buttons) {
				switch ($style) {
					case "iconbox":
						return '<div class="buttons">'.trim(implode(" ", $buttons)).'</div>';
					break;
					default:
						return '<div class="buttons">'.trim(implode($this->separator, $buttons)).'</div>';
					break;
				}
			} else {
				return false;
			}
		}
	}

	Class optionBox {
		var $options = array();
		var $selected = "";
		var $base = "";
		var $tpl;
		function add($option, $link, $text, $icon, $confirm = false) {
			$buff = createLink(sprintf($link, $option), $text, $icon, $confirm);

			if ($option)
				$this->options[$option] = $buff;
			else
				array_push($this->options, $buff);

		}
		function make($skip_selected = true) {
			$buttons = new tplButtonBox();
			foreach ($this->options as $option => $link)
				if (!$this->selected || ($option != $this->selected))
					$buttons->add($link);

			$this->tpl->set("OPTION", $this->selected);
			$this->tpl->set("OPTIONS", $buttons->make());
		}
		function optionBox(&$tpl, $selected) {
			$this->selected = $selected;
			$this->tpl =& $tpl;
		}
	}

	/**
	* relativeLink(href);
	* @returns: relative or absolute href (depending on if it has a prefix like http://)
	*/
	function relativeLink($href) {
		if (!preg_match("/([a-z0-9]*):\/\/.*/i", $href)) {
			if (substr($href, 0, 1) == "/") {
				$href = _L("C_SITE.URL").substr($href, 1);
			} else {
				$href = _L("C_SITE.REL_URL").$href;
			}
		}
		return $href;
	}

	function absoluteURL($href) {
		if (strpos($href, "://") == false)
			$href = _L("C_SITE.URL").$href;
		return $href;
	}

	function safeURL($url) {
		if (!$url)
			return null;

		if (preg_match("/([a-zA-Z]*):.*/", $url, $m)) {
			$proto = strtolower($m[1]);
			switch ($proto) {
				// this protocols in a user submitted link doesn't seems to be friendly
				case "javascript": $url = null; break;
				case "vbs": $url = null; break;
			}
		}
		if (!isset($proto) || !$proto) {
			if (preg_match("/^[a-zA-Z0-9\.\-\_]*@[a-zA-Z0-9\.\-\_]*$/", $url, $m))
				$url = "mailto:".$url;
			else
				$url = "http://$url";
		}
		return $url;
	}

	/*
		safeLink(url to check, [url text]);
		Returns a safe <a href=""></a> tag by identifying undesired
		protocols such as javascript:someLeetFunction();
	*/
	function safeLink($url, $text = null) {
		return (($href = safeURL($url)) == true) ? "<a href=\"$href\">".($text?$text:$href)."</a>" : $text;
	}

	// returns a link for the specified icon
	function fetchIcon($icon, $size, $relpath = null) {
		if (!$relpath)
			$relpath = _L("C_SITE.REL_URL");

		$icondir = "media/icons/"._L("C_SITE.ICONTHEME")."/{$size}/";

		if (file_exists(GEKKO_SOURCE_DIR.$icon)) {
			// provided gekko-relative path
			$icon = _L("C_SITE.URL").$icon;
		} elseif (file_exists(GEKKO_SOURCE_DIR.$icondir.$icon)) {
			$icon = $relpath.$icondir.$icon;
		} else {
			if (file_exists(GEKKO_SOURCE_DIR."media/icons/default/{$size}/{$icon}")) {
				$icon = $relpath."media/icons/default/{$size}/{$icon}";
			} else {
				$icon = $relpath.$icondir."default.png";
			}
		}
		if (defined("GEKKO_USE_ABSOLUTE_URLS") && "GEKKO_USE_ABSOLUTE_URLS")
			$icon = _L("C_SITE.URL").$icon;

		return $icon;
	}

	// createIconChooser(icon size, [default icon], [obj id]);
	function createIconChooser($size, $default = "default.png", $id = "imgIcon") {
		switch ($size) {
			case "16":
				return '<img src="'.fetchIcon($default, 16).'" width="16" height="16" alt="icon" /><input readonly="readonly" onclick="gekkoForms.icon(this, 16)" class="text" type="text" name="icon" size="12" value="'.$default.'" />';
			break;
			case "48":
				return '<img src="'.fetchIcon($default, 48).'" width="48" height="48" alt="icon" /><br /><input readonly="readonly" onclick="gekkoForms.icon(this, 48)" class="text" type="text" name="icon" size="12" value="'.$default.'" />';
			break;
		}
	}

	// createIcon(icon name, icon size, html style, relative path);
	function createIcon($icon, $size = 16, $style = null, $relpath = null) {
		return '<img src="'.fetchIcon($icon, $size, $relpath).'" width="'.$size.'" height="'.$size.'" alt="'.basename($icon).'"'.($style ? ' style="'.$style.'"' : '').' />';
	}

	// createDropDown(array options, selected option index, html object name, html attributes)
	function createDropDown($options, $selected = null, $name = null, $attributes = null) {
		$buff = null;
		foreach ($options as $value => $option) {
			$buff .= '<option value="'.$value.'"'.(($selected && $selected == $value) ? ' selected="selected" class="selected"' : "").'>'.$option.'</option>';
		}
		return $name ? '<select name="'.$name.'"'.($attributes ? " $attributes" : "").'>'.$buff.'</select>' : $buff;
	}

	// createCheckBox(obj name, descriptive text, [bool checked], [value]);
	function createCheckBox($name, $text, $checked = false, $value = "1") {
		return '<span><input type="checkbox" name="'.$name.'" value="'.$value.'"'.($checked ? ' checked="checked"' : '').' /> '.$text.'</span>';
	}

	// createRadio(obj name, descripive text, [bool checked], [value]);
	function createRadio($name, $text, $checked = false, $value = "1") {
		return '<span><input type="radio" name="'.$name.'" value="'.$value.'"'.($checked ? ' checked="checked"' : '').' /> '.$text.'</span>';
	}

	// mkInputBox(obj name, [input value]);
	function mkInputBox($name, $value = "") {
		return '<input class="text" type="text" name="'.$name.'" value="'.$value.'" />';
	}

	function createAntispam($text) {
		return '<script type="text/javascript">document.write(unescape("'.rawurlencode($text).'"))</script>';
	}

	function createFeedIcon($url) {
		return '<div class="feed_icon" style="vertical-align: top; font-size: x-small; text-align: right"><a href="'.$url.'"><img src="'.urlEvalPrototype("/media/button-feed.png").'" width="40" height="19" alt="RSS Feed" /></a></div>';
	}
	
	function createDateInput($format, $name, $selected = null, $unrestricted = null, $limits_override = null) {
		$limits = array (
			"D" => array(1, 31),
			"M" => array(1, 12),
			"Y" => array(date("Y")-50, date("Y")),
			"h" => array(0, 23),
			"m" => array(0, 59),
			"i" => array(0, 59)
		);

		$select = array();

		foreach ($limits as $key => $value) {
			if (isset($limits_override[$key]))
				$value = $limits_override[$key];
			for ($i = $value[0]; $i <= $value[1]; $i++) {
				if ($key == "M")
					$select[$key][$i] = strftime("%B", mktime(0, 0, 0, $i, 1, date("Y")));
				else
					$select[$key][$i] = sprintf("%02.d", $i);
			}
		}

		list($Y, $M, $D, $h, $m, $s) = preg_split("/[-\/: ]/", $selected);
		
		$buff = null;

		if (!isset($Y) || !$Y)
			$Y = date('Y');

		for ($i = 0; $i < strlen($format); $i++) {
			$c = substr($format, $i, 1);
			if (preg_match("/[YMDhms]/", $c)) {
				if (!isset($unrestricted[$c]))
					$buff .= createDropDown($select[$c], isset(${$c}) ? ${$c} : 0, "{$name}[{$c}]");
				else
					$buff .= createInput("{$name}[{$c}]", isset(${$c}) ? ${$c} : 0, array("size" => 5));
			} else {
				$buff .= $c;
			}
		}

		return $buff;
	}
	
	function createInput($name, $value = null, $attr = array()) {
		return "<input type=\"text\" name=\"$name\" value=\"$value\" ".htmlJoinAttributes($attr)." />";
	}

	// deprecated
	// createDateSelector(obj name, [selected date string (yyyy-mm-dd hh:ii::ss)])
	function createDateSelector($name, $selected = null) {

		$days = Array();
		for ($i = 1; $i <= 31; $i++) { $days[$i] = $i; }
		$months = Array();
		for ($i = 1; $i <= 12; $i++) { $months[$i] = strftime("%B", mktime(0,0,0,$i,1,date("Y"))); }
		$years = Array();
		for ($i = (date("Y")-50); $i <= date("Y"); $i++) { $years[$i] = $i; }

		if (strpos($selected, " ")) {
			preg_match("/(\d*)\-(\d*)-(\d*)\s(\d*):(\d*):(\d*)/", $selected, $match);
			list($selected,$y,$m,$d,$h,$i,$s) = $match;
		} else {
			$d = intval(substr($selected, 6, 2));
			$m = intval(substr($selected, 4, 2));
			$y = substr($selected, 0, 4);
		}

		$return = Array();
		$return[] = createDropDown($days, intval($d), $name."[d]");
		$return[] = createDropDown($months, intval($m), $name."[m]");
		$return[] = createDropDown($years, intval($y), $name."[y]");

		return implode("/", $return);
	}

	// createLetter(array (module, template letter), [array variables])
	function createLetter($tpl, $vars = Array()) {
		$tpl = explode("/", $tpl);
		$tpl = GEKKO_MODULE_DIR."{$tpl[0]}/lang/"._L("C_SITE.LANG")."/{$tpl[1]}.letter";
		$obj = new BlockWidget($tpl);
		$obj->setArray($vars);
		return $obj->make();
	}

	// createBooleanStatus([bool enable], [obj name]);
	function createBooleanStatus($opt = 1, $name = "status") {
		return createDropDown(Array(_L("L_DISABLE"), _L("L_ENABLE")), $opt, $name);
	}

	// createAuthSelector([default auth string], [obj name])
	function createAuthSelector($value = "*", $name = "auth_access") {
		return '<textarea readonly="readonly" cols="16" rows="2" onclick="groupChooser(this, true)" name="'.$name.'">'.groups::humanReadableGroups($value).'</textarea>';
	}

	/*
		createPager (total pages, current page, url, amplitude)
		url = http://myscript/content?page=%p
		Where "%p" is our page variable
	*/
	function createPager($total, $current, $link, $amp=3) {
		// returns a pretty xhtml formated pager
		$pager = "";
		if ($current <= 0) $current = 1;

		if ($total > 1) {
			for ($i = $current+$amp*-1; $i <= $current+$amp && $i <= $total; $i++) {
				if ($i > 0)
					$pager .= ($current == $i) ? "<b>[$i]</b>": createLink(str_replace("%p", $i, $link), "[$i]");
			}
			if ($current-$amp > 0) {
				if ($current-$amp > 2)
					$pager = "...".$pager;
				if ($current-$amp > 1)
					$pager = createLink(str_replace("%p", 1, $link), "[1]").$pager;
				$pager = "<b>".createLink(str_replace("%p", $current-1, $link), "&laquo;")."</b> ".$pager;
			}
			if ($total >= $i && $current+1 <= $total) {
				if ($total != $i)
					$pager .= "...";
				$pager .= createLink(str_replace("%p", $total, $link), "[$total]")."</b>";

				$pager .= " <b>".createLink(str_replace("%p", $current+1, $link), "&raquo;")."</b>";
			}
			return '<div class="pager"><b>'._L("L_PAGES").'</b>: '.$pager.'</div>';
		}
	}

	function createFlashObject($url, $width, $height) {
		$html = '
			<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="'.$width.'" height="'.$height.'">
			<param name="movie" value="'.$url.'">
			<param name="quality" value="high">
			<embed src="'.$url.'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="'.$width.'" height="'.$height.'"></embed>
			</object>
		';
		return $html;
	}

	function createMessageBox($type, $message, $custom_icon = "") {
		switch ($type) {
			case "success": $class = "info"; $icon = "ok.png"; break;
			case "info": $class = "info"; $icon = "info.png"; break;
			case "warning": $class = "error"; $icon = "warning.png"; break;
			case "error": $class = "error"; $icon = "error.png"; break;
			case "hint": $class = "info"; $icon = "hint.png"; break;
			default: $class = $type; $icon = $type.".png"; break;
		}
		return "<div class=\"$class\"><table style=\"padding:3;margin:0;\"><tr><td>".createIcon($custom_icon ? $custom_icon : $icon, 48)."</td><td>$message</td></tr></table></div>\n";
	}

	###  CLASSES ###
	function sw_class(&$i){
		return "sw_class_".($i = (($i) ? --$i : ++$i));
	}

	### MISC ###
	$L["B_SUBMIT"] = "<button type=\"submit\">".createIcon("submit.png", 16)." "._L("L_SUBMIT")."</button>";
	$L["B_RESET"] = "<button type=\"reset\">".createIcon("reset.png", 16)." "._L("L_RESET")."</button>";
	$L["B_CANCEL"] = "<button type=\"reset\">".createIcon("cancel.png", 16)." "._L("L_CANCEL")."</button>";

	// Generic template engine
	$T = new GekkoTemplateEngine();

	if (!isset($GLOBALS["T"])) $GLOBALS["T"] = $T; // Template engine (Object)
	if (!isset($GLOBALS["L"])) $GLOBALS["L"] = $L; // Language constants (Array)
?>
