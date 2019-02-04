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

	require_once GEKKO_SOURCE_DIR."modules/users/functions.php";
	require_once GEKKO_SOURCE_DIR."modules/packages/functions.php";

	/**
	*	Class GekkoHTMLFilter
	*
	*	I wrote this class by myself, but part of the code (of earlier versions)
	*	was based on the reading of kses 0.2.2 (http://kses.sourceforge.net).
	*	Thanks Ulf Harnhammar <metaur@users.sourceforge.net>
	*	kses 0.2.2 was released under the terms of the GNU/GPL.
	*
	*	This filter aims to strip out all malicious HTML code for an
	*	unprivileged user, leaving trusted users to write whatever
	*	they want.
	*/

	Class GekkoHTMLFilter {

		var $stack = Array();

		var $tag_whitelist = Array (
			0 => 'a|b|u|i|s|br|code|pre|strong|strike|em|p|span|div',
			1 => 'a|b|u|i|s|br|em|hr|sub|sup|small|strong|pre|code|p|strike|img|span|div',
			2 => 'a|b|u|i|s|br|em|hr|div|span|ul|ol|li|pre|code|table|tr|td|sub|strong|strike|sup|small|strong|strike|img|embed|object|h[0-6]|p',
			3 => '\w*'
		);

		var $tag_attr_blacklist = Array (
			0 => "on.*|class|id",
			1 => "on.*|class|id",
			2 => "on.*",
			3 => null
		);

		var $css_attr_whitelist = Array (
			0 => 'color|background|font-\w*|list-\w*|text-\w*',
			1 => 'color|background|font-\w*|list-\w*|text-\w*',
			2 => 'color|background|font-\w*|list-\w*|text-\w*',
			3 => '[\w\-]*'
		);

		var $css_value_blacklist = Array (
			0 => "url.*\(.*",
			1 => "url.*\(.*",
			2 => "url.*\(.*",
			3 => null
		);

		var $userlevel = 0;

		// initializing html filter for the given $usergroups
		function GekkoHTMLFilter ($usergroups = null) {
			$this->userlevel = $this->editLevel(($usergroups === null) ? $GLOBALS["USER"]["groups"] : $usergroups);
		}

		// returns edit level for the given user groups
		function editLevel($usergroups) {
			$level = 0;
			if ($usergroups) {
				if (appAuthorize("admin", $usergroups)) {
					// allowed level for administrator
					$level = 3;
				} elseif (appAuthorize("#", $usergroups)) {
					// allowed level for those who own some administration privileges
					$level = 2;
				} elseif (appAuthorize("$", $usergroups)) {
					// allowed level for registered users
					$level = 1;
				}
			}
			return $level;
		}

		function tagProccess($tag) {

			preg_match("/<([^\s]*)\s*(.*)>/", $tag, $matches);

			if (isset($matches[2])) // has attributes?
				$matches[2] = rtrim($matches[2], ' /');

			if (isset($matches[1])) {
				// i hate uppercase tags
				$node = strtolower($matches[1]);

				if (preg_match("/^(".$this->tag_whitelist[$this->userlevel].")$/", $node)) {

					// this is an opening tag, adding to stack
					array_push($this->stack, $node);

					// splitting original attributes
					//if (htmlallowattributes($node))
						$attr = htmlsplitattributes($matches[2]);
					//else
					//	$attr = null;

					// verifying that the attribute doesn't match the tag_attr_blacklist
					$clean_attr = Array();
					if ($attr) {
						while (list($key) = each($attr))
							if (!$this->tag_attr_blacklist[$this->userlevel] || !preg_match("/".$this->tag_attr_blacklist[$this->userlevel]."/i", $key))
								$clean_attr[$key] = $attr[$key];
					}

					// attribute "style" requires another special validation
					if (isset($clean_attr["style"])) {

						$brute_css = explode(';', $clean_attr["style"]);
						$valid_css = Array();

						// verifying css values and attributes
						foreach ($brute_css as $element) {
							preg_match("/\s*([a-z\-]*)\s*:\s*(.*)\s*/", $element, $match);
							if (isset($match[2])) {
								$css_property = $match[1];
								$css_value = $match[2];
								if (preg_match("/".$this->css_attr_whitelist[$this->userlevel]."/i", $css_property) && (!$this->css_value_blacklist[$this->userlevel] || !preg_match("/".$this->css_value_blacklist[$this->userlevel]."/i", $css_value)))
									$valid_css[] = "$css_property: $css_value";
							}
						}

						$clean_attr["style"] = implode(';', $valid_css);

						if (!$clean_attr["style"])
							unset($clean_attr["style"]);
					}

					if (isset($clean_attr["src"]))
						$clean_attr["src"] = safeURL($clean_attr["src"]);

					if (isset($clean_attr["href"]))
						$clean_attr["href"] = safeURL($clean_attr["href"]);

					// joining clean attributes
					$attr = htmljoinattributes($clean_attr);

				} elseif ((substr($node, 0, 1) == '/') && (preg_match("/^(".$this->tag_whitelist[$this->userlevel].")$/i", substr($node, 1)))) {

					// this is a closing tags, making sure it is closing something that is already open
					if ($this->stack) {
						$element = null;
						$nodename = substr($node, 1);
						while ($this->stack) {
							$pop = array_pop($this->stack);
							if ($pop == $nodename)
								break;
							else if (!htmlissingle($pop)) {
								// attemping to close a tag that is not open, or is in another nest level
								array_push($this->stack, $pop);
								return htmlspecialchars($matches[0]);
							}
						}
					} else {
						// there is nothing to close!
						return htmlspecialchars($matches[0]);
					}
				} else {
					// this is not an html tag
					return htmlspecialchars($matches[0]);
				}

				// returning a clean html tag
				return "<$node".((isset($attr) && $attr) ? " $attr" : '').(htmlissingle($node) ? ' /': '').">";
			}
		}

		function strip($data) {
			$data = preg_replace("/\<[^>]*?>/", "", $data);
			return $data;
		}

		function applyFilters($data) {

			// using absolute URLs
			$data = preg_replace_callback('<.*?(src|href)=\"(.*?)\".*?>',
				create_function('$a', '
				if (strpos($a[2], "://") === false)
					return str_replace($a[2], _L("C_SITE.URL")."{$a[2]}", $a[0]);
				return $a[0];
				'),
			$data);

			// verifying that this is not plain text
			if (strpos($data, '<') === false)
				return textToHtml($data);

			// cleaning stack
			$this->stack = array();

			// extracting an cleaning all html tags
			for ($i = 0; isset($data[$i]);) {
				if ($data[$i] == '<') {
					$begin = ++$i;
					$cmp = 0;
					while (isset($data[$i])) {
						// the end of the tag
						if ($data[$i] == '>') {
							// proccessing extracter tag
							$tag = $this->tagProccess(substr($data, $begin-1, $i-$begin+2));

							$data = substr($data, 0, $begin - 1).$tag.substr($data, $i+1);

							$i = ($begin-1)+strlen($tag);

							$cmp = 1;
							break;
						} else {
							// recognizing html comment
							if ($data[$i] == "!" && substr($data, $i, 3) == "!--") {
								// this is a comment
								$end = strpos($data, "-->", $i);
								if ($end == false)
									return htmlspecialchars($data);
								$i = $end+3;
								$cmp = 1;
								break;
							}
							$i++;
						}
					}
					if (!$cmp)
						return htmlspecialchars($data); // unclosed tag
				} else
					$i++;
			}

			// dumping stack contents (just in case, stack must be already empty)
			while ($this->stack) {
				$unclosed = array_pop($this->stack);
				if (!htmlissingle($unclosed))
					$data .= "</$unclosed>";
			}

			return $data;
		}
	}

	/**
	* Class GBBCodeParser
	*
	* This class stands for translating some common bulletin board code (bbcode) and smileys
	* into HTML (GBBCode, Gekko Bulletin Board Code). Currently not all common bbcode tags are
	* supported, this is mainly because Gekko is trying to use (and filter) HTML instead of such
	* tags.
	*/

	Class GBBCodeParser {

		var $gbbTags;
		var $smileyMap;
		var $usergroups;

		/**
		* GBBCodeParser(user_groups)
		* Initializes parser variables.
		*/
		function GBBCodeParser($usergroups = 0) {

			$this->usergroups = $usergroups;

			// current smileys theme
			$this->loadSmileyMap();

			// unlocking locked code (for avoiding being parsed in the same cycle)
			$this->gbbTags["pattern"][] = "/ \[lock\](.*?)\[\/lock\] /s";
			$this->gbbTags["callback"][] = create_function('$a', 'return gbbcodeparser::unlockContent($a[1]);');
		}

		/**
		* loadSmileyMap()
		* Will load the current smiley theme map
		*/
		function loadSmileyMap() {

			$cache = "smileyMap";

			$smileyMap =& $this->smileyMap;

			if ($smileyMap == false) {
				
				if (!file_exists(GEKKO_SOURCE_DIR."media/smileys/"._L("C_SITE.SMILEYSTHEME")."")) {
					appWriteLog("Smiley theme '"._L("C_SITE.SMILEYSTHEME")."' does not exists or it's unaccesible. Falling back to default theme.");
					_L("C_SITE.SMILEYSTHEME", "default");
				}

				// smileys directory
				$abs_smileydir = GEKKO_SOURCE_DIR."media/smileys/"._L("C_SITE.SMILEYSTHEME")."/";
				$rel_smileydir = "media/smileys/"._L("C_SITE.SMILEYSTHEME")."/";

				$smileyMap = Array("pattern" => Array(), "replace" => Array());

				$xml = Array();

				if (file_exists($xmlfile = "{$abs_smileydir}package.xml")) {

					Packages::parseXML($xmlfile, $xml);

					foreach ($xml["emoticon-map"] as $file => $buff) {
						// default smiley measures
						$width = $height = 18;

						$path = $abs_smileydir.$file;

						if (file_exists($path)) {
							// getting smiley size
							$size = @getimagesize($path, $xml["pkginfo"]["template"]);
							if (isset($size[3]))
								list($width, $height) = $size;

							//$smileyMap["pattern"][] = "/(?<=.\W|\W.|^\W|^){$buff["pattern"]}(?=.\W|\W.|\W$|$)/s";
							$smileyMap["pattern"][] = "/(<[^>]*>)|((?<=.\W|\W.|^\W|^){$buff["pattern"]}(?=.\W|\W.|\W$|$))/s";

							$smileyMap["callback"][] = create_function('$a', 'return isset($a[2]) ? "<img src=\"'._L("C_SITE.URL").$rel_smileydir.$file.'\" width=\"'.$width.'\" height=\"'.$height.'\" alt=\"".htmlencode($a[0])."\" />" : $a[0];');
						} else {
							// cannot continue
							trigger_error("Smiley \"$path\" doesn't exists.", E_USER_WARNING);
							return;
						}
					}

				} else {
					trigger_error("Seems like smiley theme \""._L("C_SITE.SMILEYSTHEME")."\" doesn't exists.", E_USER_WARNING);
				}
			}
		}

		/**
		* lockContent(data);
		* Prevents content from being parsed
		* @returns: Content that can't be evaluated as GBBCode
		*/
		function lockContent($buff) {
			return " [lock]".rawurlencode($buff)."[/lock] ";
		}

		/**
		* unlockContent(data);
		* This is the inverse of lockContent();
		*/
		function unlockContent($buff) {
			return rawurldecode($buff);
		}

		/**
		* parse(data);
		* Parses the given data as GBBCode
		*/
		function parse($data) {

			$stack = array();

			$begin = -1;
			$offset = 0;

			while (preg_match("/(.*?)(\[code[^\]]*?\]|\[\/code\])/s", substr($data, $offset), $buff)) {

				if ($begin < 0)
					$begin = strlen($buff[1]);

				if (preg_match("/^\[code[^\]]*?\]$/", $buff[2])) {
					array_push($stack, 1);
				} else {
					if (!empty($stack))
						array_pop($stack);
					else
						return;
				}

				$offset += strlen($buff[0]);

				if (!count($stack)) {

					$block = preg_replace_callback("/^\[code([^\]]*?)\](.*?)\[\/code\]$/s",
					create_function('$a', 'return gbbcodeparser::lockContent(gbbcodeparser::parseCodeTag($a[1], $a[2]));'),
					substr($data, $begin, $offset-$begin));

					$data = substr($data, 0, $begin).$block.substr($data, $offset);

					$offset = 0;
					$begin = -1;
				}
			}

			// applying smileys before unlocking code
			$data = $this->smile($data);

			foreach ($this->gbbTags["pattern"] as $i => $pattern)
				$data = preg_replace_callback($pattern, $this->gbbTags["callback"][$i], $data);

			$data = array("groups" => $this->usergroups, "content" => $data);

			// plugins
			$appPluginHandler =& $GLOBALS["appPluginHandler"];
			$appPluginHandler->triggerEvent("format/textFormat", $data);

			return $data["content"];
		}

		/**
		* stripTags(data);
		* @returns: data without GBBCode tags
		*/
		function stripTags($data) {
			$data = preg_replace("/\[[^\]]*?\]/", "", $data);
			return $data;
		}

		/**
		* smile(data)
		*/
		function smile($data) {

			foreach ($this->smileyMap["pattern"] as $i => $pattern)
				$data = preg_replace_callback($pattern, $this->smileyMap["callback"][$i], $data);

			return $data;
		}

		/**
		* splitAttributes(plain_attributes);
		* @returns: an array of attributes (attribute=value)
		*/
		function splitAttributes($attr) {
			$buff = Array();
			preg_match_all("/([a-z0-9]*)=([^\s]*)/", stripslashes($attr), $attr);
			foreach ($attr[1] as $i => $var)
				$buff[$var] = trim($attr[2][$i], '"');
			return $buff;
		}

		/**
		* For parsing [code][/code] using GeSHI
		*/
		function parseCodeTag($attr, $buff) {

			$attr = gbbCodeParser::splitAttributes($attr);

			$buff = preg_replace("/(^(<br[^>]*>)*|(<br[^>]*>)*$)/s", '', trim($buff));

			if (isset($attr["lang"])) {
				// GeSHI is a f*cking good piece of code!
				appLoadLibrary ("third_party/geshi/geshi.php");

				if (!isset($attr["text"]))
					$buff = html_entity_decode(preg_replace("/<br[^>]*>/", "\n", str_replace("&nbsp;", " ", $buff)));

				$buff = geshi_highlight(trim($buff, "\n\r"), $attr["lang"]);

			}

			return "<code>$buff</code>";
		}
	}

	Class Format {
		function extractIntro(&$data, $clear = false) {
			if ($clear) {
				$data = preg_replace("/\[intro\](.*?)\[\/intro\]/s", "", $data);
			} elseif (preg_match("/\[intro\](.*?)\[\/intro\]/s", $data, $match)) {
				$data = $match[1];
			}
			$data = preg_replace("/(^<br>|^<br \/>|<br>$|<br \/>$)*/", "", $data);
		}
		function introStyle(&$data, $user_id) {
			format::style($data, $user_id, false, null, true);
		}
		function Style(&$data, $user_id, $nocache = false, $append_key = null, $intro = false) {

			// extracting [intro] tags
			format::extractIntro($data, !$intro);

			if ($append_key)
				$append_key = ".$append_key";

			if (appAuthorize("#", $user_id) && strpos($data, "[nocache]") !== false) {
				// do not use cache
				$data = str_replace("[nocache]", "", $data);
				$nocache = true;
			}

			if ($nocache || !cacheRead($cache_key = cacheAssignKey($data, $user_id.$append_key), $data)) {

				$data = textFormat (
					$data,
					conf::getKey("core", "html_filter", 1),
					conf::getKey("core", "gbbcode", 1),
					conf::getKey("core", "gbbcode.smileys", 1),
					cacheFunc("users::fetchInfo", $user_id, "groups")
				);

				$GLOBALS["appPluginHandler"]->triggerEvent("format/afterTextFormat", $data);

				if (!$nocache)
					cacheSave($cache_key, $data);
			}
		}
		function htmlFilter(&$data, $author_id) {
			$filter = new GekkoHtmlFilter(cacheFunc("users::fetchInfo", $author_id, "groups"));
			$data = $filter->applyFilters($data);
		}
	}

	function textToHtml($data) {

		$data = str_replace("\n", "<br />", trim($data));
		// TODO: replace "http://www.example.org" with "<a href="http://www.example.org">http://www.example.org</a>"
		return $data;
	}

	function htmlencode($string) {
		return preg_replace_callback("/./s", create_function('$a', 'return "&#x".dechex(ord($a[0])).";";'), $string);
	}

	/**
	*	textFormat(String data, [bool HTML filter], [bool GBBCode], [bool Smileys]);
	*	@returns: a "safe" formatted string that can contain non-harmful HTML tags
	*/
	function textFormat($data, $html_filter = true, $gbbcode = true, $smileys = true, $usergroups = null) {

		$gbbp = new GBBCodeParser($usergroups);

		$filter = new GekkoHTMLFilter($usergroups);

		if ($html_filter)
			$data = $filter->applyFilters($data);

		if ($gbbcode)
			$data = $gbbp->parse($data);

		return $data;
	}

	/*
		mkIntro(String data, Int offset);
		@returns: a unformatted string limited to $len chars.
	*/
	function mkIntro($text, $textlen = 255, $wordlen = 30) {
		$return = "";
		$text = preg_replace("/<br[^>]*?>/", "\n", $text);
		$text = preg_replace("/\n+/", "\n", $text);
		if (!cacheRead($cachekey = cacheAssignKey($text, $textlen), $return)) {

			$text = GekkoHTMLFilter::strip(GBBCodeParser::stripTags($text));

			// too large text
			$return = ((strlen($text) > $textlen) ? substr($text, 0, $textlen + ((($e = strpos(substr($text, $textlen), " ")) < 30) ? $e : 0))."..." : $text);

			// too large words
			$return = preg_replace("/[^\s]{{$wordlen}}[^\s]*/e", "substr(stripslashes('\\0'), 0 ,".($wordlen-3).").'...'", $return);

			$return = nl2br($return);

			cacheSave($cachekey, $return);
		}
		return $return;
	}

	function textblockTruncate($text, $textlen = 255) {
		if (strlen($text) > $textlen)
			$text = substr($text, 0, $textlen-3)."...";
		return $text;
	}

	function breakWords($text, $mwl = 22) {
		// TODO: use a more elegant form, with patterns
		$text = explode(" ", $text);
		while (list($i) = each($text)) {
			if (strlen($text[$i]) > $mwl) {
				$text[$i] = substr($text[$i], 0, $mwl - 5)."[...]";
			}
		}
		return join(" ", $text);
	}

	// human readable date from unix timestamp
	function dateFormat($timestamp, $format = false) {

		if (preg_match("/^\d*$/", $timestamp) && strlen($timestamp) != 14) {

			$diff = 3600*((!defined("IN-INSTALL")) ? conf::getkey("core", "site.hour_difference", 0, 'i') : 0);

			$timestamp += $diff;
			$localtime = time()+$diff;

			// unix time
			if ($format) {

				return strftime($format, $timestamp);

			} elseif (appAuthorize("$")) {

				$age = ($localtime - $timestamp);

				if ($age >= 0) {

					// past event
					if ($age < 60) {
						return strftime(_L("L_SECONDS_AGO"), $age);
					} elseif ($age < 3600) {
						return strftime(_L("L_MINUTES_AGO"), $age);
					} else {

						$today = explode(":", date("n:d:Y", $localtime));
						// today began at...
						$today_s = mktime(0, 0, 0, $today[0], $today[1], $today[2]);
						// today ends at...
						$today_e = $today_s + 86399;
						// yesterday began at...
						$yesterday_s = $today_s - 86399;

						if (($timestamp > $today_s) && ($timestamp < $today_e)) {
							return strftime(_L("L_TODAY_AT"), $timestamp);
						} elseif (($timestamp < $today_s) && ($timestamp > $yesterday_s)) {
							return strftime(_L("L_YESTERDAY_AT"), $timestamp);
						}

					}
				} else {
					$age *= -1;

					// future
					if ($age < 60) {
						return strftime(_L("L_NEXT_SECONDS"), $age);
					} elseif ($age < 3600) {
						return strftime(_L("L_NEXT_MINUTES"), $age);
					} elseif ($age < 86400) {
						return strftime(_L("L_TODAY_AT"), $age);
					} else {

						$today = explode(":", date("n:d:Y"));
						// today began at...
						$today_s = mktime(0, 0, 0, $today[0], $today[1], $today[2]);
						$tomorrow_e = $today_s + 86399*2;

						$interval = $tomorrow_e - $timestamp;
						if ($interval > 0 && $interval < 86400) {
							return strftime(_L("L_TOMORROW_AT"), 86400-$age);
						}
					}
				}
			}

			return strftime(_L("L_FULL_DATE"), $timestamp);

		} else {
			return dateFormat(fromDate($timestamp), $format);
		}
	}

	/*
		saneHTML(data, [comma separated data index exceptions (when data is array)])

		Sanitizes an HTML string or array
	*/
	function saneHTML(&$data, $exclude_keys = false) {
		if (is_array($data)) {
			if ($exclude_keys !== false) $exclude_keys = explode(",", $exclude_keys);
			foreach ($data as $key => $null) {
				if (!$exclude_keys || !in_array($key, $exclude_keys)) {
					saneHTML($data[$key], $exclude_keys ? $exclude_keys : false);
				}
			}
		} else {
			$data = htmlspecialchars($data);
		}
	}

	/**
	*	rtbEditor
	*	Based on http://www.mozilla.org/editor/midas-spec.html
	*
	*	I know you may say WYSIWYG editors are evil, but this editor
	*	can reduce the time I use to blog ;). The main problem is that
	*	this editor doesn't ouput valid XHTML 1.0 code (at least on the
	*	Firefox version I'm using), so I need to clean up the submitted
	*	data before inserting it to the database to make sure it is
	*	XHTML 1.0 compliant.
	*
	*	Gekko is fully configurable, so, if you're feeling really geek
	*	you can disable rtbEditor from the "config" module. It will
	*	leave you with a nice and clean <textarea> where you can play with
	*	the cold HTML code :)
	*/

	Class rtbEditor {

		var $tpl, $id, $filter, $level;

		var $headings = Array (
			"h1",
			"h2",
			"h3",
			"h4",
			"h5"
		);

		var $fonts = Array (
			"Sans",
			"Serif",
			"Sans-Serif",
			"Trebuchet MS",
			"Verdana",
			"Monotype",
		);

		function isCompatible() {
			// compatible with Gecko rendering engine (Mozilla Firefox).
			// compatible with IE 6.0+ (money is money :))
			if (isset($_SERVER["HTTP_USER_AGENT"])) {
				if (preg_match("/gecko/i", $_SERVER["HTTP_USER_AGENT"])) {
					return true;
				} else if (0 && preg_match("/msie/i", $_SERVER["HTTP_USER_AGENT"]) && !preg_match("/opera/i", $_SERVER["HTTP_USER_AGENT"])) {
					// for some extrange reason Opera says that it is MSIE 6.0 compatible
					return true;
				}
			}
			return false;
		}

		function setSize($width, $height) {
			$this->tpl->set("WIDTH", "{$width}px");
			$this->tpl->set("HEIGHT", "{$height}px");
		}
		function mkHeadings() {
			$headings = "<option value=\"\">("._L("L_HEADINGS").")</option>";
			for ($i = 0; isset($this->headings[$i]); $i++)
				$headings .= "<option value=\"".$this->headings[$i]."\">"._L("L_HEADING_".($i+1))."</option>";
			return $headings;
		}
		function mkFontSel() {
			natsort($this->fonts);
			$fonts = "<option value=\"\">("._L("L_FONT_NAME").")</option>";
			foreach ($this->fonts as $font)
				$fonts .= "<option style=\"font-family:$font\" value=\"$font\">$font</option>";
			return $fonts;
		}
		function rtbEditor($content = "", $field = "content", $user_id = 0) {

			if (!isset($GLOBALS["RTBEDITOR"]))
				$GLOBALS["RTBEDITOR"] = 0;

			$this->id = ++$GLOBALS["RTBEDITOR"];

			// using filter
			$this->filter = new GekkoHTMLFilter();

			$enable = false;
			// editor level
			if ($this->isCompatible() && conf::getKey("core", "rtbeditor")) {
				$this->level = $this->filter->editLevel($user_id ? cacheFunc("users::fetchInfo", $user_id, "groups") : $GLOBALS["USER"]["groups"]);
				$enable = true;
			}

			if ($enable) {

				$editor = conf::getKey("core", "rtbeditor.default");

				$this->tpl = new blockWidget("_layout/gekkoEditor.tpl");

				$this->tpl->setArray(
					Array (
						"BUTTONS"			=> $this->mkButtons(),
						"FONTS.SEL"			=> $this->mkFontSel(),
						"HEADINGS"			=> $this->mkHeadings(),
						"EDITOR.DISPLAY"	=> ($editor ? "block" : "none"),
						"SOURCE.DISPLAY"	=> ($editor ? "none" : "block")
					)
				);

			} else {
				$this->tpl = new blockWidget("_layout/gekkoEditorSimple.tpl");
			}

			$this->tpl->setArray(
				Array (
					"ID"			=> $this->id,
					"FIELD"			=> $field,
					"CONTENT"		=> $content,
					"WIDTH"			=> "400px",
					"HEIGHT"		=> "300px",
					"LEVEL"			=> $this->level,
					"ALLOWED_TAGS"	=> (($tags = $this->filter->tag_whitelist[$this->filter->userlevel]) != null) ? "&lt;".str_replace(",", "&gt;, &lt;", $tags)."&gt;" : false
				)
			);
		}

		function mkButtons() {

			$buttons = array();

			// relations between commands and html tags
			$relations = Array (
				"span"			=> "bold,underline,italic,strikethrough,forecolor,hilitecolor,indent,outdent",
				"div"			=> "justifyleft,justifycenter,justifyright,justifyfull",
				"code"			=> "code",
				"a"				=> "createlink",
				"ol"			=> "insertorderedlist",
				"ul"			=> "insertunorderedlist",
				"blockquote"	=> "quote",
				"img"			=> "insertimage",
				"hr"			=> "inserthorizontalrule",
				"table"			=> "table",
				"sub"			=> "subscript",
				"sup"			=> "superscript",
				"hr"			=> "inserthorizontalrule"
			);

			// insertfile is a special command, only for the administrator
			if ($this->filter->userlevel == 3)
				$relations["img"] = "insertfile";

			foreach ($relations as $tag => $add_buttons) {
				$add_buttons = explode(',', $add_buttons);
 				if (preg_match("/^(".$this->filter->tag_whitelist[$this->filter->userlevel].")$/", $tag))
					foreach ($add_buttons as $button)
						$buttons[] = $button;
			}

			$normal_buttons = Array("smiley", "redo", "undo", "removeformat", "fullscreen");
			foreach ($normal_buttons as $button)
				$buttons[] = $button;

			$output = "";
			foreach ($buttons as $button) {
				$icondir = file_exists(GEKKO_SOURCE_DIR."media/icons/"._L("C_SITE.ICONTHEME")."/toolbar/{$button}.png") ? _L("C_ICON_DIR") : _L("C_SITE.URL")."media/icons/default";

				$output .= "<div title=\""._L("L_FORMAT_".strtoupper($button))."\" class=\"button\" onclick=\"gekkoEditor.exec('{$this->id}', '{$button}')\"><img alt=\"{$button}\" src=\"$icondir/toolbar/{$button}.png\" width=\"16\" height=\"16\" /></div>\n";
			}

			return $output;

		}
		function setOption($option, $value) {
			$this->tpl->set("OPTIONS_".strtoupper($option), $value);
		}
		// returns a compiled gekkoEditor
		function make() {
			return $this->tpl->make();
		}
	}
	// --------
	function htmlSplitAttributes2($tmp) {
		$attr = Array();
		preg_match_all("/([a-z]*)\=\\\"(.*?)\\\"/", " $tmp ", $match);
		foreach ($match[1] as $k => $v)
			$attr[$v] = $match[2][$k];
		return $attr;
	}
	function htmlSplitAttributes($attr) {

		$attr = trim($attr, " /");

		$attr_arr = array();

		$key = $val = null;
		$step = 0;

		for ($i = 0; isset($attr[$i]); ) {

			while (isset($attr[$i]) && (preg_match("/[\s\n\r\t]/", $attr[$i])))
				$i++;

			switch ($step) {
				case 0:
					while (isset($attr[$i]) && $step == 0) {
						if (preg_match("/[a-zA-Z0-9]/", $attr[$i])) {
							$key .= $attr[$i];
						} else {
							if ($key && $attr[$i] == '=') {
								$step = 1;
							} elseif ($attr[$i] != ' ')
								return null; // malformed
						}
						$i++;
					}
				break;
				case 1:

					$enclose = $attr[$i++];

					if ($enclose == '"' || $enclose == "'") {

						while (isset($attr[$i]) && $attr[$i] != $enclose) {

							$val .= $attr[$i];

							if (isset($attr[$i+1]) && $attr[++$i] == '\\') {
								if (isset($attr[++$i])) {
									if (isset($attr[$i+1]))
										$val .= $attr[$i++];
									elseif ($attr[$i] == $enclose)
										break;
								} else {
									return null; // expecting character
								}
							}
						}

						if ($attr[$i] == $enclose) {
							$attr_arr[$key] = $val;
							$key = $val = null;
							$step = 0;
						}
						$i++;
					} else if (preg_match("/[a-zA-Z0-9]/", $enclose)) {
						$val .= $enclose; // string

						for (; isset($attr[$i]) && $step == 1 && preg_match("/[a-zA-Z0-9]/", $attr[$i]); $i++)
							$val .= $attr[$i];

						if ($val) {
							$attr_arr[$key] = $val;
							$key = $val = null;
							$step = 0;
						} else
							return null;

					} else
						return null; // malformed string
				break;
			}
		}
		return $attr_arr;
	}
	function htmlJoinAttributes($attr) {
		$buff = Array();

		if (is_array($attr))
			foreach ($attr as $a => $v)
				$buff[] = "$a=\"".str_replace("\"", "\\\"", $v)."\"";

		return implode(' ', $buff);
	}

	function editorTagReassemble($a) {
		
		$node = $a[1];

		// getting referer document path
		$base = $_SERVER["HTTP_REFERER"];

		preg_match("/^([a-z]*:\/\/[^\/]*)\/(.*?)(index.php|$)/i", $base, $url);

		$host = $url[1];
		$docroot = '/'.(($url[3]) ? dirname($url[2]).'/' : $url[2]);

		$attr = htmlSplitAttributes(isset($a[2]) ? $a[2] : null);

		// checking resources
		$src_arr = array('src', 'href', 'action');
		foreach ($src_arr as $src)
			if (isset($attr[$src])) {

				if (substr($attr[$src], 0 ,2) == "..") {
					$path = explode('/', $docroot);

					if (($del = strpos($attr[$src], "?")) !== false)
						$relative = substr($attr[$src], 0, $del);

					$rel = explode('/', isset($relative) ? $relative : $attr[$src]);

					for ($i = 0; $rel[$i] == ".."; $i++)
						array_pop($path);

					$attr[$src] = (count($path) ? implode('/', $path).'/' : '').substr($attr[$src], $i*3);
				}
			}


		switch ($node) {
			case "img":

				if (!isset($attr["alt"]))
					$attr["alt"] = htmlspecialchars(basename($attr["src"]));

				if (!isset($attr["width"]) || !isset($attr["height"]))
					$attr["width"] = $attr["height"] = 0;

				if (substr($attr["src"], 0, 7) != "http://" || substr($attr["src"], 0, strlen($_SERVER["HTTP_HOST"])) == $_SERVER["HTTP_HOST"]) {

					$src = urldecode($attr["src"]);

					// first guess
					$local_src = $_SERVER["DOCUMENT_ROOT"].'/'.ltrim($src, '/');

					if (!file_exists($local_src)) {
						// second guess
						$relative = explode('/', trim(preg_replace("/\/modules\/.*/", "", $_SERVER["PHP_SELF"]), '/'));
						$local = explode('/', trim(preg_replace("/\/modules\/.*/", '', $_SERVER["SCRIPT_FILENAME"]), '/'));

						while (true) {
							$local_e = array_pop($local);
							$relative_e = array_pop($relative);
							if ($local_e != $relative_e) {
								array_push($local, $local_e);
								array_push($relative, $relative_e);
								break;
							}
						}
						$local = implode('/', $local);
						$relative = implode('/', $relative);

						$local_src = str_replace($relative, $local, $src);
					}
					if (!file_exists($local_src)) {
						// third guess... works fine under windows...
						$local_src = GEKKO_USER_DIR.ltrim($src, '/');
					}

					if (file_exists($local_src)) {
						$size = @getimagesize($local_src);
						if (isset($size[0]) && isset($size[1])) {
							$attr["width"] = $size[0];
							$attr["height"] = $size[1];
						}
					}
				}

				// this line breaks the standards... but surely the user don't cares (she/he didn't provide a with nor a height attribute)
				if (0 == ($attr["width"] + $attr["height"]))
					unset ($attr["width"], $attr["height"]);
			break;
		}

		$attr = htmljoinattributes($attr);

		return '<'.$node.($attr ? " $attr": "").(htmlissingle($node) ? ' /': '').'>';
	}

	function stripHTML($text) {
		return preg_replace("/<(.*?)>(.*?)/", "\\2", $text);
	}
	
	// returns true if closing tags are not mandatory for the given tag
	function htmlissingle($node) {
		return preg_match("/^embed|param|br|img|hr|link|input$/i", $node);
	}

	// returns true is the tag can handle attributes
	function htmlallowattributes($node) {
		return !($node == 'br' || $node == 'hr');
	}

	function editorApplyFixes(&$buff) {

		$buff = stripslashes(trim($buff));

		if (preg_match("/^<br[^>]*?>$/", $buff)) {

			$buff = '';

		} else {

			$buff = preg_replace_callback("/<(\/?\w*)[\s\t\n\r]([^>]*?)\/?>/sm",
				create_function('$a', 'return (substr($a[0], 0, 4) == "<!--" && substr($a[0], -3) == "-->") ? $a[0] : editorTagReassemble($a);'),
				$buff
			);

		}

		$buff = addslashes($buff);

		return true;
	}
	// -----------

	function getAge($sqltime) {
		$age = 0;

		preg_match("/^(\d*)-(\d*)-(\d*)\s.*$/", $sqltime, $buff);

		if (isset($buff[3])) {

			list( , $year1, $month1, $day1) = $buff;
			list($day2, $month2, $year2) = explode(":", date("j:n:Y"));

			$age = $year2-$year1-1;

			if (($age < 0) ||($month2 > $month1) || ($month2 == $month1 && $day2 >= $day1))
				$age++;

			return $age;
		}
		return false;
	}

	function textEditor($content = "", $name = "content", $width = 350, $height = 200, $mini = false) {

		if (!isset($GLOBALS["RTBEDITOR"]))
			$GLOBALS["RTBEDITOR"] = 0;

		if (!$content) {
			// restoring auto-saved content
			$id = (int)$GLOBALS["RTBEDITOR"]+1;

			$cache_key = md5($_SERVER["REQUEST_URI"]).'-'.$id.'@'.getIP().".cache";

			$cache_file = GEKKO_CACHEDIR.$cache_key;
			if (file_exists($cache_file)) {
				$fp = fopen($cache_file, 'r');
				$content = gzuncompress(fread($fp, filesize($cache_file)));
				fclose($fp);
				@unlink($cache_file);
			}
		}

		$content = htmlSpecialChars($content);
		$rtb = new rtbEditor($content, $name);

		$rtb->setSize($width, $height);
		$rtb->setOption("mini", $mini);
		return $rtb->make();
	}
?>
