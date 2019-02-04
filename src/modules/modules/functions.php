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

	Class Modules {
		function humanReadableList($list) {
			$list = str_replace(',', ', ', $list);
			return $list;
		}
		function getName($id) {
			$db =& $GLOBALS["db"];

			$q = $db->query("SELECT module FROM ".dbTable("module")." WHERE id = '{$id}'",
			__FILE__, __LINE__);

			if ($db->numRows($q)) {
				$row = $db->fetchRow($q);
				return $row["module"];
			}
			return false;
		}
		/**
		* mkChooser(selected modules id, [boolean] show all?);
		* @returns: an html formatted module selector
		*/
		function mkChooser($selected = null, $all = true) {
			$return = Array();
			$db =& $GLOBALS["db"];

			if ($all)
				$return["*"] = createCheckBox("modules[]", _L("L_ANY"), true, "*");

			$res = $db->query("SELECT module FROM ".dbTable("module")."
			WHERE status = '1'
			ORDER BY module ASC", __FILE__, __LINE__, true);

			$selected = explode(",", $selected);
			while ($row = $db->fetchRow($res)) {
				if (file_exists(GEKKO_SOURCE_DIR."modules/{$row["module"]}/main.php")) {
					$return[] = createCheckBox("modules[]", $row["module"], in_array($row["module"], $selected), $row["module"]);
				}
			}

			return implode("<br />\n", $return);
		}
	}
?>