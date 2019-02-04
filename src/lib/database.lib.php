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

	define("DB_DEFAULT_TYPE", "mysql");

	// database error definitions
	define("DB_ERR_QUERY_SYNTAX", 0);
	define("DB_ERR_LOADING_DRIVER", 1);

	Class Database_Common {

		var $driver, $type, $link, $host, $user, $pass, $name, $query, $result, $errstr;

		function Database_Common () {
			return true;
		}
		function error($err = DB_ERR_QUERY_SYNTAX, $file = null, $line = null) {
			switch ($err) {
				case DB_ERR_QUERY_SYNTAX:
					$errstr = "SQL query syntax error in \"$file\" at line $line.\n-> ".Database::getError().".\n";
					break;
				case DB_ERR_LOADING_DRIVER:
					$errstr = "Error while loading database driver.\n";
					break;
			}
			trigger_error("$errstr", E_USER_ERROR);
		}
		// returns key value from $dbinfo global variable.
		function info($key) {
			return $GLOBALS["dbinfo"][$key];
		}
	}

	function dbLoadDriver() {
		$driver = GEKKO_SOURCE_DIR."lib/db/".$GLOBALS["dbinfo"]["type"].".driver";
		if (file_exists($driver)) {
			require $driver;
		} else {
			trigger_error("Database driver '{$driver}' doesn't exists!", E_USER_ERROR);
		}
	}

	function dbInit () {
		$db =& $GLOBALS["db"];
		$dbinfo =& $GLOBALS["dbinfo"];
		if (!$db) {
			$db = new Database;
			$db->open (
				$dbinfo["host"],
				$dbinfo["user"],
				$dbinfo["pass"],
				$dbinfo["name"]
			) or trigger_error("Couldn't connect to database!", E_USER_ERROR);
		}
		return true;
	}

	function dbExit () {
		$db =& $GLOBALS["db"];
		cacheDump();
		return $db->close();
	}

	function dbTable($name, $use_lang = true) {
		$dbinfo =& $GLOBALS["dbinfo"];

		switch ($name) {
			case 'user': case 'conf': case 'group': case 'module':
				$use_lang = false;
			break;
		}
		
		return "`{$dbinfo["pref"]}".(($use_lang && GEKKO_LANGUAGE_ID) ? GEKKO_LANGUAGE_ID.'_': '')."$name`";
	}
	function dbGetField($table, $item_id, $field = "*") {
		$db =& $GLOBALS["db"];
		$return = false;

		$q = $db->query("SELECT {$field} FROM ".dbTable($table)." WHERE id = '{$item_id}'",
			__FILE__, __LINE__, true);

		if ($db->numRows($q)) {
			$row = $db->fetchRow($q);
			if (is_array($row))
				$return = $row;
		}

		return $return ? ($field ? $return[$field] : $return) : false;
	}

	// sql date lock
	function dbDateLock($field, $date = 0) {
		$time = timemark($date);
		if ($time)
			return "($field >= FROM_UNIXTIME('$time[0]') AND $field <= FROM_UNIXTIME('$time[1]'))";
		return 1;
	}
	
	Class dbMacro {
		function swapItemOrder($table, $item_id, $new_order_id) {
			$db =& $GLOBALS["db"];

			$q = $db->query ("SELECT order_id FROM ".dbTable($table)."
			WHERE id = '{$item_id}'",
			__FILE__, __LINE__, true);

			if ($db->numRows($q)) {

				$row = $db->fetchRow($q);

				if ($new_order_id != $row["order_id"]) {

					$db->query ("UPDATE ".dbTable($table)."
					SET order_id = '{$row["order_id"]}'
					WHERE order_id = '{$new_order_id}'",
					__FILE__, __LINE__, true);

					$db->query ("UPDATE ".dbTable($table)."
					SET order_id = '{$new_order_id}'
					WHERE id = '{$item_id}'",
					__FILE__, __LINE__, true);

				}

			}
		}
	}

	// loading database driver
	if (!defined("IN-INSTALL")) {
		dbLoadDriver();
	}
?>