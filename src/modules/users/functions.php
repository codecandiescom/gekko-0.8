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

	define("GEKKO_AVATAR_DIR", GEKKO_SOURCE_DIR."media/avatars/");

	Class Users {

		function proccessSearch($query_id, $limit = -1) {
			$db =& $GLOBALS["db"];

			$data = array();

			for ($i = 0; ($limit < 0 || $i < $limit) && $row = $db->fetchRow($query_id); $i++) {
				saneHTML($row);

				$row["show_full_description"] = true;
				$row["icon"] = "users.png";
				$row["title"] = users::createUserLink($row["id"]);
				$row["description"] = "<div style=\"float: left\" class=\"avatar\">".users::createAvatar($row["id"])."</div>".($row["realname"] ? "{$row["realname"]}<br />" : "")."<b>"._L("L_LAST_LOGIN").":</b> ".dateFormat($row["date_login"]);

				if (appAuthorize("users")) {
					$row["actions"] = new tplButtonBox();
						$row["actions"]->add(createLink("index.php/module=admin/base=users/action=edit?id={$row["id"]}", _L("L_EDIT"), "edit.png"));
						$row["actions"]->add(createLink("modules/users/actions.php?action=delete&id={$row["id"]}&auth={C_AUTH_STR}", _L("L_DELETE"), "delete.png", true));
					$row["actions"] = $row["actions"]->make();
				}

				$data[] = $row;
			}

			return $data;
		}

		function createAvatarChooser($selected = "default.png") {
			return '<div><img class="avatar" src="'.users::getAvatarURL($selected).'" width="50" height="50" /><input readonly="readonly" onclick="avatarChooser(this)" class="text" type="text" name="avatar" size="14" value="'.$selected.'" /></div>';
		}
		/**
		* listGenders(gender_id);
		* @returns: human readable gender given its gender ID
		*/
		function listGenders($id) {
			$gender = Array (
				_L("L_UNKNOWN"),
				_L("L_MALE"),
				_L("L_FEMALE")
			);
			return ($id < 0) ? $gender : $gender[$id];
		}

		function listSessionTimes($id = 0) {
			$session = Array(
				_L("L_DEFAULT"),
				_L("L_A_HOUR"),
				_L("L_A_DAY"),
				_L("L_A_WEEK"),
				_L("L_A_MONTH")
			);

			return $id ? $session[$id] : $session;
		}

		/**
		* fetchInfo(user_id, [field list]);
		* @returns: an array containing requested user fields of false if the user doesn't exists
		*/
		function fetchInfo($id, $field = "*") {

			$q = $GLOBALS["db"]->query("SELECT $field FROM
			".dbTable("user")." WHERE id = '{$id}'", __FILE__, __LINE__, true);

			if ($GLOBALS["db"]->numRows($q)) {
				$row = $GLOBALS["db"]->fetchRow($q);
				return ($field == "*" || preg_match("/,/", $field)) ? $row : $row[$field];
			} else {
				return false;
			}

		}

		/**
		* createAvatar(user_id, [avatar size]);
		* @returns: HTML formatted avatar for the given user_id
		*/
		function createAvatar($id, $email = false, $size = 50, $avatar = false) {
			if ($id) {
				$avatar = cacheFunc("dbGetField", "user_profile", $id, "avatar");
				$email = cacheFunc("dbGetField", "user_profile", $id, "public_email");
			}
			return "<img src=\"".($url = users::getAvatarURL($avatar, $email))."\" width=\"{$size}\" height=\"{$size}\" alt=\"\" />";
		}

		/**
		* listAvatars();
		* @returns: a list of avatars found in GEKKO_AVATAR_DIR
		*/
		function listAvatars() {
			$avdir = listDirectory(GEKKO_AVATAR_DIR);
			$info = @parse_ini_file(GEKKO_AVATAR_DIR."avatars.ini");
			$avatars = Array();
			natsort($avdir);
			foreach ($avdir as $avatar) {
				if (ereg("(png|gif|jpg)", getFileExtension($avatar))) {
					$avatars[$avatar] = isset($info[$avatar]) ? $info[$avatar] : $avatar;
				}
			}
			return $avatars;
		}


		/**
		* getAvatarURL(avatar, [user.email]);
		* @returns: URL for specified avatar or, if avatar is null, its gravatar URL (this is a nice
		* feature for guest's comments that I saw in jaws cms)
		*/
		function getAvatarURL($avatar = null, $email = false) {

			// avatar is null, checking for gravatar
			if (!$avatar)
				$avatar = (conf::getKey("users", "gravatar", 0, "b") && $email) ? "http://gravatar.com/avatar.php?gravatar_id=".md5(strtolower($email))."&size=50&default=".urlencode(_L("C_SITE.URL")."media/avatars/default.png") : "default.png";

			if (strtolower(substr($avatar, 0, 7)) != "http://")
				$avatar = _L("C_SITE.URL")."media/avatars/".(($avatar && file_exists(GEKKO_AVATAR_DIR."$avatar")) ? $avatar : "default.png");

			return $avatar;
		}

		/**
		* createUserLink(user_id);
		* @returns: HTML formatted link to user's profile.
		*/
		function createUserLink($id) {

			$db =& $GLOBALS["db"];

			$q = $db->query("
			SELECT u.username, p.nickname
				FROM ".dbTable("user")." u, ".dbTable("user_profile")." p
				WHERE u.id = '{$id}' AND u.id = p.id
			UNION
				SELECT username, username
				FROM ".dbTable("user")."
				WHERE id='{$id}'",
			__FILE__, __LINE__, true);

			if (($aff = $db->numRows($q)) > 0) {
				$row = $db->fetchRow($q);
				saneHTML($row);
				return createLink("index.php/module=users/action=profile/user={$row["username"]}", $row["nickname"]);
			}

			return false;
		}

		function getName($id) {
			$db =& $GLOBALS["db"];

			$q = $db->query("SELECT username FROM ".dbTable("user")." WHERE id = '$id'", __FILE__, __LINE__, true);

			if ($db->numRows($q)) {
				$row = $db->fetchRow($q);
				return $row["username"];
			} else
				return false;
		}

		function getID($username) {
			$db =& $GLOBALS["db"];

			$q = $db->query("SELECT id FROM ".dbTable("user")." WHERE username = '$username'", __FILE__, __LINE__, true);

			if ($db->numRows($q)) {
				$row = $db->fetchRow($q);
				return $row["id"];
			} else
				return false;
		}

		function deleteRegistry($id) {
			$GLOBALS["db"]->query ("DELETE FROM ".dbTable("user")." WHERE id='{$id}'",
			__FILE__, __LINE__, true);
			$GLOBALS["db"]->query ("DELETE FROM ".dbTable("user_profile")." WHERE id='{$id}'",
			__FILE__, __LINE__, true);

			appWriteLog("users: deleted user with id=$id", "actions", 1);
		}

		function setRegistryStatus($id, $status) {
			return $GLOBALS["db"]->query ("UPDATE ".dbTable("user")." SET status = '{$status}'
			WHERE id='{$id}'", __FILE__, __LINE__, true);

			appWriteLog("users: changed status for user with id=$id", "actions", 5);
		}
	}
?>
