<?php

	/*
	*	Gekko - Open Source Web Development Framework
	*	------------------------------------------------------------------------
	*	Copyright (C) 2004-2006, J. Carlos Nieto <xiam@users.sourceforge.net>
	*	This program is Free Software.
	*
	*	@package	Gekko
	*	@subpackage	/lib
	*	@license	http://www.gnu.org/copyleft/gpl.html GNU/GPL License 2.0
	*	@author		J. Carlos Nieto <xiam@users.sourceforge.net>
	*	@link		http://www.gekkoware.org
	*/

	if (!defined("IN-GEKKO")) die("Get a life!");

	/*
		The main pourpose of this Class is to keep a log for "who did what,
		when and how many times?". This is very useful for regulating access
		to specific events, like login attempts or comments (preventing
		bruteforce attacks, flood and those lame things)
	*/

	Class accessControl {

		var $rules = Array();

		function accessControl() {
			// Deleting entries older than 36 hours (I think no module would
			// want to keep track of actions within such a time interval)
			$GLOBALS["db"]->query("DELETE FROM ".dbTable("control")." WHERE time < ".(time()-129600)."",
			__FILE__, __LINE__, true);
		}
		
		/**
		* dropEntry(type, how_many)
		* Deletes specified number of logged actions for this user in descendent mode 
		*/
		function dropEntry($type, $how_many = 1) {
			// very util for redirections
			$GLOBALS["db"]->query("DELETE FROM ".dbTable("control")." WHERE
			ip = '".IP_ADDR."' AND accesstype = '{$type}' ORDER BY time DESC LIMIT '{$how_many}'",
			__FILE__, __LINE__, true);
		}
		/*

		
			check(String id, Int time interval, Int requests limit, Int persistent entry)

			Manages an entry for each ID to identify requests based on time.
			This could be used as an antiflood control.

				$this->check("requestControl", 3, 5, 1);

				The above example will return false if more than 5 requests
				has been made within 3 seconds. And also it's persistent, so, if
				client continues doing requests, this will continue returning
				false until client waits 3 seconds doing nothing.
		*/

		/**
		* @check(action type, time interval, maximum requests limit, [bool] persistent, [bool] readonly)
		* @returns: "false" when the user HAS NOT reached the specified actions limit, "false" otherwise
		* When ($persistent == true) and the user has not reached the limit the "time" field is updated,
		* so this is a very restrictive way of counting actions
		*/
		function check($type, $time_interval, $req_limit = 1, $persistent = false, $readonly = false) {

			$db =& $GLOBALS["db"];
			
			// deleting expired entries
			$db->query("DELETE FROM ".dbTable("control")." WHERE
			ip = '".IP_ADDR."' AND accesstype = '{$type}' AND time <= '".(time()-$time_interval)."'",
			__FILE__, __LINE__, true);

			// checking for events the user performed recently
			$q = $db->query("SELECT counter FROM ".dbTable("control")."
			WHERE ip = '".IP_ADDR."' AND accesstype = '{$type}'", __FILE__, __LINE__, true);

			if ($db->numRows($q)) {
				// an existent entry has been found
				// how many times the user has performed the same thing?
				
				$row = $db->fetchRow($q);

				// increasing counter by one
				if (!$readonly)
					$db->query("UPDATE ".dbTable("control")."
					SET counter = counter + 1 ".(($persistent) ? ", time = '".time()."'": "")."
					WHERE ip = '".IP_ADDR."' AND accesstype = '$type'", __FILE__, __LINE__,
					true);

				// this time counts too!
				return (($req_limit > 0) && ($row["counter"]+1 > $req_limit)) ? true : false;

			} elseif (!$readonly) {
				// there was not found an existent entry for this type of action
				$db->query("INSERT ".dbTable("control")." (ip, accesstype, counter, time)
				VALUES('".IP_ADDR."', '{$type}', '1', '".time()."')", __FILE__, __LINE__,
				true);
				return false;
			}
			return false;
		}
		
		/**
		* setRule(id, interval, requests, persistent);
		* This function defines a rule for using in conjunction with loadRule(), so you
		* don't need to use check() directly. Check function loadRule()
		*
		*/
		function setRule($id, $time, $requests, $persistent = false) {
			// appending a easy-to-identify string
			$id .= ".rule";
			// creating new rule
			$this->rules[$id] = Array (
				"time"			=> $time,
				"requests"		=> $requests,
				"persistent"	=> $persistent
			);
		}

		/*
			loadRule((str) rule id, (str) abort message [leave as false if you don't want script to abort], (bool) read only)
			Checks a rule and returns false when is *not* matched or reached
		*/
		/**
		* loadRule(id, [bool] abort, [bool] readonly);
		* @returns: The same as check() but using the (already) specified rule. If $abort != false, then
		* Gekko aborts execution and displays _L($abort). You can also use $readonly flag the same way as
		* if you were using check()
		*
		*/
		function loadRule($rule, $abort = false, $readonly = false) {
			// rule naming convention
			$rule .= ".rule";
			if (isset($this->rules[$rule])) {
				$check = $this->check($rule, $this->rules[$rule]["time"], $this->rules[$rule]["requests"], $this->rules[$rule]["persistent"], $readonly);
				return ($abort && $check) ? appAbort(_L($abort)) : $check;
			} else {
				trigger_error("Rule '$id' has not been set", E_USER_ERROR);
			}
		}

		/**
		* setGenericRule(prefix, type)
		*
		* When you think a rule can be useful for many other modules you can write it below
		*/
		function setGenericRule($id, $type) {
			switch ($type) {
				case "post":
					// generic rule for "http post" event (whe user submits data for to be inserted
					// into database)
					$t = conf::getKey("core", "http_post.interval", 20);
					$r = conf::getKey("core", "http_post.requests", 3);
					$p = true;
				break;
				case "auth":
					// generic "auth protection" rule (for avoiding bruteforce attacks)
					$t = conf::getKey("core", "http_auth.interval", 600);
					$r = conf::getKey("core", "http_auth.requests", 10);
					$p = true;
				break;
				default:
					trigger_error("Unknown generic rule type '$type'", E_USER_ERROR);
				break;
			}

			/*
			This rule allows $r requests within $t seconds
			When $p is true, it means the time interval must be reached and
			this time is updated with every extra invalid request
			*/

			$this->setRule($rule = "$type-$id", $t, $r, $p);
			return $rule;
		}
	}
?>