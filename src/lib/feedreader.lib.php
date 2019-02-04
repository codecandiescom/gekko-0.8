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

	require_once GEKKO_SOURCE_DIR."lib/file.lib.php";
	require_once GEKKO_SOURCE_DIR."lib/format.lib.php";

	class news_reader {
		var $strip_html = 0;
		var $contents_mxl = 100;
		var $title_mxl = 32;
		var $limit = 0;
		var $html = "";
		var $hide = Array();

		function set_limit($i) {
			$this->limit = $i;
		}

		function make_html() {

			if (is_array($this->feed)) {

				if (isset($this->feed["title"])) $this->html .= "<h4><a href=\"".$this->feed["link"]["href"]."\" title=\"".(isset($this->feed["link"]["title"]) ? $this->feed["link"]["title"]: $this->feed["title"])."\">".$this->feed["title"]."</a></h4>";
				$i = 0;

				if (isset($this->feed["entry"]) && is_array($this->feed["entry"])) {
					foreach ($this->feed["entry"] as $id => $buff) {

						if ($this->limit && ($i >= $this->limit))
							break;

						if ($this->strip_html)
							$buff["content"] = stripHTML($buff["content"]);

						if (!conf::getkey("core", "feed_reader.show_full_content", 0, "b")) {
							$buff["content"] = isset($buff["content"]) ? mkIntro($buff["content"], $this->contents_mxl) : "";
						}

						$buff["title"] = mkIntro($buff["title"], $this->title_mxl);
						$this->html .= "<a href=\"".$buff["link"]["href"]."\" title=\"".(isset($buff["link"]["title"]) ? $buff["link"]["title"]: $buff["title"])."\"><b>".$buff["title"]."</b></a>";
						$this->html .= "\n";
						$this->html .= $buff["content"]."<hr />";
						$i++;
					}
				}
			}
			return $this->html;
		}

		function array_to_xml($data) {
			# buffer
			$buff = array();
			# attributes into string
			$buff["att"] = "";
			if (is_array($data["attributes"])) {
				foreach ($data["attributes"] as $att => $val) {
					$buff["att"][] = strtolower($att)."=\"$val\"";
				}
				$buff["att"] = " ".implode(" ", $buff["att"]);
			}
			$data["tag"] = strtolower($data["tag"]);
			switch ($data["type"]) {
				case "complete": {
					return "<".$data["tag"]."".$buff["att"].($data["value"] ? "": "/").">".$data["value"].($data["value"] ? "</".$data["tag"].">": "");
					break;
				}
				case "open": {
					return "<".$data["tag"]."".$buff["att"].">".$data["value"];
					break;
				}
				case "close": {
					return "</".$data["tag"].">";
					break;
				}
				case "cdata": {
					return $data["value"];
					break;
				}
			}
		}
	}

	class rss_reader extends news_reader {
		/*
			Usage:
				$rss = rss_reader;
				echo $rss->sync("http://localhost/rss.xml");
		*/
		var $version = "2.0";
		var $url;
		var $data;
		var $entries = -1;
		var $feed = array();

		function sync($url, $version = "2.0") {
			$this->url = $url;
			$this->version = $version;

			# Note: getFile() is a Gekko specific function
			if (($this->data = getFile($this->url)) != false) {
				// get encoding
				preg_match("/<\?xml.*?encoding=\"(.*?)\".*\?>/", $this->data, $enc);
				$this->parse(null, isset($enc[1]) ? strtoupper($enc[1]) : null);
				return news_reader::make_html($this->feed);
			} else {
				return "There was an error while contacting this news feed.";
				//trigger_error("Error while contacting RSS Feeder", E_USER_WARNING);
			}
			return false;
		}

		function parse($data = null, $encoding = null) {

			$parser = xml_parser_create($encoding);
			if (!xml_parse_into_struct($parser, $data ? $data : $this->data, $arr_values)) {
				return xml_error_string(xml_get_error_code($parser));
			}
			xml_parser_free($parser);

			switch ($this->version) {

				case "2.0": default: {

					$rss = Array();
					$c = null;

					foreach ($arr_values as $value) {
						switch ($value["level"]) {
							case 1: break;
							case 2: break;
							case 3: {
								switch ($value["tag"]) {
									case "LINK": {
										$this->feed["link"]["href"] = $value["value"];
										break;
									}
									case "ITEM": {
										if ($value["type"] == "open") {
											$catch = true;
											$this->entries++;
										} elseif ($value["type"] == "close") {
											$catch = false;
										}
										break;
									}
									default: {
										if (isset($value["value"]) && trim($value["value"])) {
											$this->feed[strtolower($value["tag"])] = $value["value"];
										}
									}
								}
								break;
							}
							case 4: {
								switch ($level[3]) {
									case "IMAGE": {
										$this->feed["image"][strtolower($value["tag"])] = $value["value"];
										break;
									}
									case "ITEM": {
										switch ($value["tag"]) {
											case "DESCRIPTION": {
												$value["tag"] = "content";
												break;
											}
											case "LINK": {
												$this->feed["entry"][$this->entries]["link"]["href"] = $value["value"];
												break(2);
											}
										}
										if (isset($value["value"])) $this->feed["entry"][$this->entries][strtolower($value["tag"])] = $value["value"];
									}
								}
								break;
							}
						}
						switch ($value["type"]) {
							case "open": {
								$level[$value["level"]] = $value["tag"];
								break;
							}
							case "close": {
								$level[$value["level"]] = "";
								break;
							}
						}
					}
					break;
				}
			}
		}
	}

	class atom_reader extends news_reader {
		/*
			Usage:
				$atom = new atom_reader;
				echo $atom->sync("http://localhost/atom.xml");
		*/
		var $version = "0.3";
		var $entries = -1;
		var $html;
		var $feed = array();
		var $data;
		var $url;

		function sync($url, $version = "0.3") {
			$this->url = $url;
			$this->version = $version;
			# Note: getFile() is a Gekko specific function
			if (($this->data = getFile($this->url)) != false) {
				$this->parse();
				return news_reader::make_html();
			} else {
				//trigger_error("Error while contaction Atom feeder", E_USER_WARNING);
			}
			return false;
		}

		function parse($data = null, $encoding = null) {

			$parser = xml_parser_create($encoding);
			if (!xml_parse_into_struct($parser, $data ? $data : $this->data, $arr_values)) {
				return xml_error_string(xml_get_error_code($parser));
			}
			xml_parser_free($parser);


			switch ($this->version) {
				case "0.3": {
					foreach ($arr_values as $value) {
						# type
						switch ($value["level"]) {
							case "1": break;
							case "2": {
								switch ($level[1]) {
									case "FEED": {
										switch ($value["tag"]) {
											case "TITLE": {
												$this->feed["title"] = $value["value"];
												break;
											}
											case "LINK": {
												$this->feed["link"]["href"] = $value["attributes"]["HREF"];
												$this->feed["link"]["title"] = $value["attributes"]["TITLE"];
												break;
											}
											case "MODIFIED": {
												$this->feed["modified"] = $value["value"];
												break;
											}
											case "ENTRY": {
												if ($value["type"] == "open") {
													$catch = true;
													$this->entries++;
												} elseif ($value["type"] == "close") {
													$catch = false;
												}
												break;
											}
										}

										break;
									}
									default: {
										if ($this->strict) trigger_error("ATOM Parse Error", E_USER_ERROR);
									}
								}
								break;
							}
							case "3": {
								switch ($level[2]) {
									case "AUTHOR": {
										switch ($value["tag"]) {
											case "NAME": {
												$this->feed["author"]["name"] = $value["value"];
												break;
											}
										}

										break;
									}
									case "ENTRY": {
										switch ($value["tag"]) {
											case "TITLE": {
												$this->feed["entry"][$this->entries]["title"] = $value["value"];
												break;
											}
											case "LINK": {
												$this->feed["entry"][$this->entries]["link"]["href"] = $value["attributes"]["HREF"];
												$this->feed["entry"][$this->entries]["link"]["title"] = $value["attributes"]["TITLE"];
												break;
											}
											default: {
												if (trim($value["value"])) {
													$this->feed["entry"][$this->entries][strtolower($value["tag"])] = $value["value"];
												}
											}
										}
										break;
									}
									default: {

										if ($this->strict) trigger_error("Unknown tag: \"".$value["tag"]."\"", E_USER_ERROR);
									}
								}
								break;
							}
							case "4": {
								switch ($level[3]) {
									case "AUTHOR": {
										if (trim($value["value"])) {
											$this->feed["entry"][$this->entries]["author"] = $value["value"];
										}
									}
								}
							}
							default: {
								if ($catch) {
									if ($level[3] == "CONTENT") {
										$this->feed["entry"][$this->entries]["content"] .= news_reader::array_to_xml($value);
									}
								}
							}
						}
						switch ($value["type"]) {
							case "open": {
								$level[$value["level"]] = $value["tag"];
								break;
							}
							case "close": {
								$level[$value["level"]] = "";
								break;
							}
						}
					}
					break;
				}
				default :{
					trigger_error("Unknown Atom version", E_USER_ERROR);
				}
			}
		}
	}



?>
