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

	/*
		Those are core functions supposed for being used in every
		script.
	*/

	if (!defined("IN-GEKKO")) die("Get a life!");
	
	appLoadLibrary("format.lib.php", "file.lib.php");

	function varImportDate($var) {
		
		$date =& $_POST[$var];
		$es = array('Y', 'M', 'D', 'h', 'm', 's');

		foreach ($es as $e) {
			if (!isset($date[$e]))
				$date[$e] = 0;
		}

		$return = array();
		$return[] = implode('-', array($date['Y'], $date['M'], $date['D']));
		$return[] = implode(':', array($date['h'], $date['m'], $date['s']));

		$GLOBALS[$var] = implode(' ', $return);
		
	}

	function varImportFile($module, $variable, $type = null) {

		if ($_FILES["{$variable}_local"]["size"]) {
			$upload = saveUploadedFile("{$variable}_local", $module);
			if ($upload)
				$GLOBALS[$variable] = $upload["rel_loc"];
			else
				formInputError("{E_UPLOAD_FAILED}", "focus: {$variable}_local");
		}

		varImport($variable);

		return true;
	}

	// returns a error message and instructions.
	// useful for ajax requests, please read core.js:gekkoAjaxFormSubmit();
	function formInputError($message, $instruction = null) {
		appAbort(($instruction ? "$instruction\n" : "").$message);
	}

	function varImport() {
		$args = func_get_args();

		foreach ($args as $arg) {

			unset($match);

			// default value passed as argument "variable|default"
			if (preg_match("/^(.*?)\|(.*?)$/", $arg, $match))
				$arg = $match[1];

			$GLOBALS[$arg] = null;

			if (isset($_POST[$arg])) {
				$GLOBALS[$arg] = $_POST[$arg];
				continue;
			}

			if (isset($_GET[$arg])) {
				$GLOBALS[$arg] = $_GET[$arg];
				continue;
			}

			// default value
			if (!$GLOBALS[$arg] && isset($match[2]))
				$GLOBALS[$arg] = $match[2];

		}
	}
	function execWithEach(&$var, $exec) {
		if (is_array($var)) {
			foreach ($var as $key => $tmp) {
				execWithEach($var[$key], $exec);
			}
		} else {
			eval("$exec(\$var);");
		}
	}
	function varRequire() {
		$args = func_get_args();
		foreach ($args as $arg) {
			if (!isset($GLOBALS[$arg]) || !$GLOBALS[$arg])
				appAbort(_L('E_INSUFFICIENT_DATA'));
		}
	}
	function varGetValue($index) {
		$val = null;
		if (isset($_GET[$index]))
			$val = $_GET[$index];
		if (isset($_POST[$index]))
			$val = $_POST[$index];
		return $val;
	}
?>
