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

	Class Packages {
	
		/**
		* checkVersion([required version], [provided version]);
		* @returns: -1, 0, o 1 (provided version is minor, equal, major than provided)
		*/
		function checkVersion($required, $provided) {
			$required = explode(".", $required);
			$provided = explode(".", $provided);
	
			for ($i = 0; isset($required[$i]); $i++) {
				// when version is not set, using 0
				if (!isset($provided[$i]))
					$provided[$i] = 0;
	
				if (substr($provided[$i], 0, 1) == "0")
					$provided[$i] = "0.".substr($provided[$i], 1);
	
				if (substr($required[$i], 0, 1) == "0")
					$required[$i] = "0.".substr($required[$i], 1);
	
				$p = floatval($provided[$i]);
				$r = floatval($required[$i]);
	
				if ($p > $r)
					// provided version is major than required
					return 1;
				elseif ($p < $r)
					// provided version is minor than required
					return -1;
			}
			// version is equal
			return 0;
		}
	
		/**
		* parseModuleXML(module name);
		* @returns: Parsed XML file from specified module
		*/
		function parseModuleXML($module) {
			if (file_exists($file = GEKKO_SOURCE_DIR."modules/{$module}/package.xml")) {
				$xml = "";
				Packages::parseXML($file, $xml);
				return $xml;
			}
			return false;
		}
	
		/**
		* checkInstall(xml module information);
		* @returns: false if module is available for install, or a requirements
		* list if it cannot be installed
		*/
		function checkInstall(&$xml) {
	
			$inform = false;
	
			// checking provided php version
			if (Packages::checkVersion($xml["module"]["phpver"], phpversion()) < 0) {
				$inform .= "Requires PHP >= ".$xml["module"]["phpver"]." (Provided: ".phpversion().")\n";
			}
	
			// checking required libraries and modules
			foreach ($xml["requires"] as $key => $info) {
				switch ($key) {
					case "module":
						foreach ($info as $module) {
							if (!file_exists(GEKKO_SOURCE_DIR."modules/$module")) {
								$inform .= " - Required module: $module\n";
							}
						}
					break;
					case "file":
						foreach ($info as $file) {
							if (!file_exists(GEKKO_SOURCE_DIR.$file)) {
								$inform .= " - Required file: $file\n";
							}
						}
					break;
				}
			}
			if ($inform) {
				$title = "Module '".$xml["module"]["name"]."' couldn't be installed:";
				$title .= "\n".str_repeat("-", strlen($title))."\n";
				$inform = $title.$inform."\n\n";
			}
			return $inform;
		}
	
		/**
		* parseXML(xml file, xml);
		* @returns: parses the specified file and saves it in the
		* second argument
		*/
		function parseXML($xml_file, &$pkg_xml) {

			$xml_parser = xml_parser_create("ISO-8859-1");
			$xml_struct = array();
			xml_parse_into_struct($xml_parser, loadFile($xml_file), $xml_struct);
	
			$pkg_xml = Array("version" => "1.0", "type" => "");
			$mtag = Array();
			while ($item = array_shift($xml_struct)) {
	
				switch ($pkg_xml["version"]) {
					// version 1.0
					case "1.0":
						switch ($item["tag"]) {
							case "PACKAGE":
								if (isset($item["attributes"])) {
									$pkg_xml["version"] = $item["attributes"]["VERSION"];
									$pkg_xml["type"] = $item["attributes"]["TYPE"];
								}
							break;
						}
						// switching package time
						switch ($pkg_xml["type"]) {
							// smiley package
							case "smileyset":
								if (isset($mtag["PKGINFO"])) {
									if (!isset($pkg_xml["pkginfo"])) $pkg_xml["pkginfo"] = Array();
									switch ($item["tag"]) {
										case "NAME": case "AUTHOR": case "HOMEPAGE": case "PACKAGER": case "NOTES": case "TEMPLATE":
										if (isset($item["value"])) $pkg_xml["pkginfo"][strtolower($item["tag"])] = $item["value"];
									}
								}
								if (isset($mtag["EMOTICON-MAP"])) {
									if (isset($item["attributes"]["FILE"])) $smfile = $item["attributes"]["FILE"];
									if (isset($mtag["EMOTICON"])) {
										if (!isset($pkg_xml["emoticon-map"])) $pkg_xml["emoticon-map"] = Array();
										switch ($item["tag"]) {
											case "PATTERN": case "SAMPLE":
												if (!isset($pkg_xml["emoticon-map"][$smfile])) $pkg_xml["emoticon-map"][$smfile] = Array();
												$pkg_xml["emoticon-map"][$smfile][strtolower($item["tag"])] = $item["value"];
											break;
										}
									}
								}
							break;
							// module package
							case "module":
								if (isset($mtag["MODULE"])) {
									if (!isset($pkg_xml["module"])) $pkg_xml["module"] = Array();
									switch ($item["tag"]) {
										case "NAME": case "GROUP": case "AUTHOR": case "HOMEPAGE": case "PHPVER":
											$pkg_xml["module"][strtolower($item["tag"])] = $item["value"];
										break;
									}
								}
								if (isset($mtag["RELEASE"])) {
									// setting release as true, we'll only take care of the first release item, it must be the newest version
									if (!isset($release)) {
										$release = false;
									} elseif ($release == true) {
										// only parsing the first <release> item occurence
										break;
									}
									// when <requires> is opened...
									if (isset($mtag["REQUIRES"])) {
										switch ($item["tag"]) {
										case "MODULE": case "FILE":
											if (!isset($pkg_xml["requires"][strtolower($item["tag"])])) $pkg_xml["requires"][strtolower($item["tag"])] = Array();
											$pkg_xml["requires"][strtolower($item["tag"])][] = $item["value"];
										break;
										}
										break;
									}
									switch ($item["tag"]) {
										case "VERSION": case "AUTHOR": case "NOTES": case "CHANGELOG":
											$pkg_xml["release"][strtolower($item["tag"])] = $item["value"];
										break;
									}
									if ($item["tag"] == "RELEASE") {
										if (isset($item["type"]) && ($item["type"] == "close")) {
											// avoiding parsing of the next <release> items (older versions)
											$release = true;
										}
									}
								}
							break;
						}
					break;
				}
				// this will save information about when a tag is open or not
				if (isset($item["type"]) && ($item["type"] == "open" || $item["type"] == "close")) {
					if ($item["type"] == "open") {
						$mtag[$item["tag"]] = true;
					} else {
						unset($mtag[$item["tag"]]);
					}
				}
			}
		}
	
		/**
		* cgdbRegen();
		* @returns: regenerated version of cgdb.php
		*/
		function cgdbRegen() {
			// regenerating cgdb.php contents
			$data = Array();
			natsort($GLOBALS["cgdb"]);
			foreach ($GLOBALS["cgdb"] as $module => $id) {
				$data[] = "\t\t\"{$module}\" => {$id}";
			}
			return "<?php\n\t\$cgdb = Array (\n".implode(",\n", $data)."\n\t);\n?>";
		}
	}

?>
