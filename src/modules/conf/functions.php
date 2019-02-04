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

	Class Conf {
		/**
		* Load(module)
		*
		* Loads configuration variables for specified module
		*/
		function Load($module = "core") {
			$db =& $GLOBALS["db"];
			$cache = $GLOBALS["_CACHE"];
			if (!isset($GLOBALS["conf"][$module])) {
				$q = $db->query("SELECT keyname, keyvalue
				FROM ".dbTable("conf")." WHERE module = '$module'", __FILE__, __LINE__, true);
				while ($row = $db->fetchRow($q)) {
					$cache[conf::getCacheName($module, $row["keyname"])] = $row["keyvalue"];
					$GLOBALS["conf"][$module] = true;
				}
			}
			return true;
		}

		// returns the name for a cached configuration key
		function getCacheName($module, $key) {
			return md5("variable.conf/{$module}/{$key}");
		}

		/**
		* delKey(module, key)
		*
		* Deletes configuration key for specified module. If key is equal to "*", deletes
		* all configuration keys for that module
		*/
		function delKey($module, $key) {
			$db =& $GLOBALS["db"];

			$db->query("DELETE FROM ".dbTable("conf")."
			WHERE module = '$module'".(($key != "*") ? "" : " AND keyname = '$key'"),
			__FILE__, __LINE__, true);

			// updating cache
			unset($GLOBALS["_CACHE"][conf::getCacheName($module, $key)]);
		}

		function renameKey($module, $oldName, $newName) {
			$db =& $GLOBALS["db"];
			$cache =& $GLOBALS["_CACHE"];

			$db->query("UPDATE ".dbTable("conf")."
			SET keyname = '$newName'
			WHERE module = '$module' AND keyname='$oldName'",
			__FILE__, __LINE__, true);

			// updating cache
			$oldKey = conf::getCacheName($module, $oldName);
			if (isset($cache[$oldKey])) {
				$cache[conf::getCacheName($module, $newName)] = $cache[$oldKey];
				unset ($cache[$oldKey]);
			}
		}

		/**
		* setKey(module, key, value, type)
		*
		* Sets a configuration key in module (overwrites it if exists)
		*/
		function setKey($module, $key, $value = null, $type = "s") {
			$db =& $GLOBALS["db"];
			$cache =& $GLOBALS["_CACHE"];

			switch (trim(strtolower(substr($type, 0, 1)))) {
				/*
					"i" stands for "integer" type
					"s" stands for "string" type
					"b" stands for "boolean" type
				*/
				case "i": $type = 1; break;
				case "b": $type = 2; break;
				// default type is "string"
				default: $type = 0; break;
			}

			$q = $db->query("SELECT id FROM ".dbTable("conf")." WHERE
			module = '$module' AND keyname= '$key'", __FILE__, __LINE__, true);

			if ($type)
				$value = conf::fixValue($value, $type); // modifying value according to type

			if ($db->numRows($q)) {
				// key exists
				$row = $db->fetchRow($q);
				$db->query("UPDATE ".dbTable("conf")."
				SET keyvalue = '$value'
				WHERE module = '$module' AND keyname= '$key'",
				__FILE__, __LINE__, true);
			} else {
				// inserting new key
				$db->query("INSERT INTO ".dbTable("conf")."
				(id, module, keyname, keyvalue, keytype)
				VALUES('".($db->findFreeId("conf"))."','$module', '$key', '$value', '$type')",
				__FILE__, __LINE__, true);
			}

			// updating cached keys
			$cache[conf::getCacheName($module, $key)] = $value;
		}
		/**
		* getKey(module, key, default value, type)
		* @returns: configuration key value
		*
		* Gets a configuration key, if it doesn't exists and its default value and type
		* are specified creates it and returns default value.
		* If it doesn't exists and default value is not specified returns false.
		*/
		function getKey($module, $key, $default_value = false, $type = false) {
			$db =& $GLOBALS["db"];

			$cache =& $GLOBALS["cache"];
			$cache_key = conf::getCacheName($module, $key);

			if (!isset($cache[$cache_key])) {
				$q = $db->query("SELECT keyvalue
				FROM ".dbTable("conf")." WHERE module = '$module' AND keyname = '$key'",
				__FILE__, __LINE__, true);
				if ($db->numRows($q)) {
					// key exists
					$row = $db->fetchRow($q);
					$cache[$cache_key] = $row["keyvalue"];
				} else {
					// key doesn't exists
					$cache[$cache_key] = $default_value;
					// adding key if it doesn't exists
					if ($default_value !== false)
						conf::setKey($module, $key, $default_value, ($type) ? $type : (is_int($default_value) ? "i" : "s"));
				}
			}
			return $cache[$cache_key];
		}

		/**
		* checkKeyName(key)
		* @returns: true if key name is valid, false otherwise
		*
		*/
		function checkKeyName($keyname) {
			return preg_match("/[^a-zA-Z0-9._\-]/", $keyname);
		}

		/**
		* getKeyType(type id)
		* @returns: human readable key type
		*
		*/
		function getKeyType($id) {
			switch ($id) {
				case "0": $type = _L("L_STRING"); break;
				case "1": $type = _L("L_INTEGER"); break;
				case "2": $type = _L("L_BOOLEAN"); break;
			}
			return strtolower($type);
		}

		/**
		* deleteKey(id)
		* Deletes a configuration key for the specified id
		*
		*/
		function delKeyById($id) {
			$db =& $GLOBALS["db"];
			$q = $db->query("SELECT module, keyname FROM ".dbTable("conf")."
			WHERE id = '$id'",
			__FILE__, __LINE__, true);
			if ($db->numRows($q)) {
				$row = $db->fetchRow($q);
				conf::delKey($row["module"], $row["keyname"]);
			}
		}

		/**
		* fixValue(value, key type);
		* @returns: modified version of 'value' according to 'type'.
		*/
		function fixValue($value, $type = 0) {
			switch ($type) {
				case "0":
					return "$value"; // string
				break;
				case "1":
					return intval ($value); // integer
				break;
				case "2":
					return ($value) ? true : false; // boolean
				break;
			}
		}
	}
?>
