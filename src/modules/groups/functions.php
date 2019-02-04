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

	if (defined("IN-TEMPLATE"))
		appLoadJavascript("modules/groups/main.js");

	Class Groups {
		function humanReadableGroups($grouplist) {
			if ($grouplist) {
				$includegroups = explode(',', $grouplist);
				foreach ($includegroups as $i => $id) {
					$id = trim($id);
					$includegroups[$i] = cacheFunc("groups::getName", $id)." ($id)";
				}
				$grouplist = implode(', ', $includegroups);
			}
			return $grouplist;
		}
		/**
		* checkList(Group id list, [Group ID])
		* @return: the same group list stripping non-existent and duplicated groups
		*
		*/
		function checkList($groups, $groupid = null) {
			$db =& $GLOBALS["db"];

			$result = array();
			$groups = explode(",", $groups);
			foreach ($groups as $group) {
				$i = intval(trim($group));
				if ($i && (!$groupid || $groupid != $i)) {
					/*
						Checking that:
							* Every Group has an integer value
							* A Group is not inside itself
							* Included Group already exists
					*/
					$q = $db->query("SELECT id FROM ".dbTable("group")."
					WHERE id = '$i'", __FILE__, __LINE__, true);
					if ($db->numRows($q))
						$result[] = $i;
				}
			}
			$result = implode(",", $result);
			return $result;
		}

		/**
		* mkList((string) input name, (string) selected groups);
		* @returns: html formatted group chooser
		* @example: Groups::mkList("groupchooser", "1,2,3");
		*/
		function mkList($name, $groups = null) {
			$db =& $GLOBALS["db"];

			$groups = explode(",", $groups);
			$return = Array();
			$q = $db->query("SELECT id, groupname, description
			FROM ".dbTable("group")." ORDER BY id ASC", __FILE__, __LINE__, true);
			while ($row2 = $db->fetchRow($q)) {
				$return[] = createCheckBox("{$name}[]", $row2["groupname"], in_array($row2["id"], $groups), $row2["id"])."<div class=\"whisper\">{$row2["description"]}</div>";
			}
			return "<div class=\"info\">\n".implode("<hr />\n", $return)."</div>\n";
		}

		/**
		* fetchGroups(main group, excluded groups array)
		* @returns: An array of groups that are linked to the main group (at
		* first level)
		* @example: Groups::fetchGroups("1");
		*/
		function fetchGroups($groupid = null) {
			$groups = Array();

			if ($groupid) {
				$db =& $GLOBALS["db"];

				$q = $db->query("SELECT includegroups FROM ".dbTable("group")." WHERE
				id='{$groupid}' AND status='1'", __FILE__, __LINE__, true);
				$r = $db->fetchRow($q);

				if ($db->numRows($q))
					$groups = explode(",", $r["includegroups"]);

			}
			return $groups;
		}

		/**
		* fetchAll(group string)
		* @returns: An array of groups that are linked to the given groups string,
		* group string must be separated by commas (deep mode)
		* @example: Groups::fetchAll("1,2,3");
		*/
		function fetchAll($groups_string) {

			$groups = explode(",", $groups_string);

			for ($i = 0; $i < count($groups); $i++) {
				$group =& $groups[$i];

				$included = cacheFunc("Groups::fetchGroups", $group);
				foreach ($included as $test)
					if ($test && !in_array($test, $groups))
						$groups[] = $test;
			}

			sort($groups, SORT_NUMERIC);

			return $groups;
		}

		/**
		* extractFromInput(humanReadableGroupList)
		* @returns: a comma separated list of groups id
		*/
		function extractFromInput($includegroups) {
			if ($includegroups) {
				$includegroups = explode(',', $includegroups);

				foreach ($includegroups as $i => $name) {
					preg_match("/\(.*?\)$/", trim($name), $match);
					$includegroups[$i] = substr($match[0], 1, -1);
				}
				return implode(',', $includegroups);
			}
			return null;
		}

		/**
		* getID(groupName)
		* @returns: the matching group's id
		*/
		function getID($name) {
			$db =& $GLOBALS["db"];
			$q = $db->query("SELECT id FROM ".dbTable("group")."
			WHERE groupname = '$name'", __FILE__, __LINE__, true);

			if ($db->numRows($q)) {
				$r = $db->fetchRow($q);
				return $r["id"];
			} else {
				return $name;
			}
		}

		/**
		* getName(group id or code, [get description instead of name?])
		* @returns: group name/description for the given numeric id or group code
		*/
		function getName($id, $description = false) {
			$db =& $GLOBALS["db"];

			if (is_integer(intval($id)) && $id > 0) {
				// group id
				$field = ($description) ? "description": "groupname";
				$q = $db->query("SELECT $field FROM ".dbTable("group")."
				WHERE id = '$id'", __FILE__, __LINE__, true);
				$r = $db->fetchRow($q);
				return $r[$field];
			} else {
				// group code
				switch ($id) {
					case "*": return _L("L_GROUP_ALL"); break;
					case "$": return _L("L_GROUP_ACCOUNT"); break;
					case "?": return _L("L_GROUP_VISITOR"); break;
					case "#": return _L("L_GROUP_MANAGEMENT"); break;
					case "@": return _L("L_GROUP_MANAGEMENT_CORE"); break;
				}
			}
		}
		/**
		* createList(group string or array, [return as a string?])
		* @returns: an array/string of group names
		*
		*/
		function createList($groups, $as_string = true) {
			if (!is_array($groups)) {
				// first argument could be a string but we cannot work with such data
				$groups = explode(',', $groups);
				return Groups::createList($groups);
			} else {
				// receiving an array as first argument
				$return = Array();
				foreach ($groups as $group) {
					if (($name = Groups::getName($group)) != null) {
						$return[] = $name;
					}
				}
				return ($as_string) ? implode(", ", $return) : $return;
			}
		}

		/**
		* deleteItem(groupId)
		*/
		function deleteItem($id) {
			appWriteLog("groups: deleted group id '$id'", "actions", 1);
			return $GLOBALS["db"]->query ("DELETE FROM ".dbTable("group")." WHERE id='{$id}'", __FILE__, __LINE__, true);
		}

		/**
		* Groups::setItemStatus(groupId, booleanStatus)
		*/
		function setItemStatus($id, $status) {
			return $GLOBALS["db"]->query ("UPDATE ".dbTable("group")." SET status = '{$status}' WHERE id='{$id}'", __FILE__, __LINE__, true);
		}
	}
?>