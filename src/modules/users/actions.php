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

	define ("IN-GEKKO", true);

	require_once "../../conf.php";

	appLoadLibrary (
		"users", "groups", "network.lib.php", "template.lib.php"
	);

	require_once GEKKO_SOURCE_DIR."lib/auth.inc.php";

	varImport("id", "action", "return");

	// access rules
	$loginAccess = $AccessTable->setGenericRule("user", "auth");

	switch ($action) {
		// account confirmation
		case "confirm":

			varImport("username", "password", "confirmation");

			varRequire("username", "password", "confirmation");

			$username = strtolower($username);

			$hash = md5($username.$password);

			if (conf::getKey("users", "account.require_confirmation")) {

				$q = $db->query ("
				UPDATE ".dbTable("user")." SET
					auth_key = '',
					status = '1'
				WHERE username = '{$username}' AND password='{$hash}' AND auth_key = '{$confirmation}'",
				__FILE__, __LINE__);

				$changed = $db->affectedRows($q);

				$return = "index.php/module=users/action=".($changed ? "login": "confirm");

				if ($changed)
					appWriteLog("users: account confirmed for user '$username'");
				else
					formInputError("{E_DATA_MISMATCH}", "focus: username");
			}
		break;
		// password reset requested by user
		case "resetpasswd":

			if (conf::getKey("users", "account.enable_password_reset")) {

				varImport("username", "email", "confirmation", "password1", "password2");

				varRequire("username", "email");

				$username = strtolower($username);

				if ($confirmation) {
					// this user has submitted a request to reset his password
					if ($password1 == $password2) {
						$q = $db->query ("
							UPDATE ".dbTable("user")." SET
								password = '".md5($username.$password1)."', reset_key=''
							WHERE
							username = '{$username}' AND reset_key = '{$confirmation}' AND email = '{$email}'
						");
						if ($db->affectedRows($q)) {
							appWriteLog("users: password reassigned for user '$username'");
							$return = "index.php/module=users/action=login?user=$username";
						} else {
							formInputError("{E_WRONG_DATA}", "focus: username");
						}
					} else {
						formInputError("{E_DATA_MISMATCH}", "focus: password");
					}
				} else {

					// creating "access rule" for avoiding "reset password" attacks
					$AccessTable->setRule (
						"user_resetpw_request",
						conf::getKey("users", "resetpw.interval", 600, "i"),
						conf::getKey("users", "resetpw.requests", 2, "i"),
						true
					);

					// Using rule for preventing bruteforce attacks
					$AccessTable->loadRule ("user_resetpw_request", "L_RESETPW_DELAY");

					// keys for password reset are 64 chars long
					$confirmation = createRandomKey(64);

					$q = $db->query("
					UPDATE ".dbTable("user")." SET
						reset_key = '{$confirmation}'
					WHERE username = '{$username}' AND email = '{$email}'
					", __FILE__, __LINE__);

					if ($db->affectedRows($q)) {
						sendMail($email, _L("L_RESET_PASSWORD"),
							createLetter("users/resetpw",
								Array (
									"USERNAME"			=> $username,
									"EMAIL"				=> $email,
									"CONFIRMATION_CODE"	=> $confirmation
								)
							)
						);
						$return = "index.php/module=users/action=password_reset?sent=1&step=2";
					} else {
						formInputError("{L_DATA_MISMATCH}", "focus: username");
					}
				}
			} else {
				formInputError("{E_DISABLED_FEATURE}");
			}
		break;
		case "login":

			// Creating "login" access rule (persistent mode allowing 10 requests within 800 seconds)
			$AccessTable->setRule (
				"account_login_attempt",
				conf::getKey("users", "login.interval", 800, "i"),
				conf::getKey("users", "login.requests", 10, "i"),
				true
			);

			// Loading rule for preventing bruteforce attacks
			$AccessTable->loadRule ("account_login_attempt", "E_TOO_MANY_FAILED_LOGIN_ATTEMPTS");

			varImport("username", "password");

			varRequire("username", "password");

			$username = strtolower($username);

			// autentication hash
			$hash = md5($username.$password);

			$q = $db->query ("
				SELECT
					id, status, auth_key
				FROM ".dbTable("user")."
				WHERE username = '{$username}' AND password = '{$hash}'
			", __FILE__, __LINE__);

			if ($db->numRows($q)) {

				$row = $db->fetchRow($q);

				if ($row["status"] || (!conf::getKey("users", "account.require_confirmation"))) {

					// auth key is a 32 chars random string for preventing username to being showed in a cookie
					$auth_key = "";
					if (!conf::getKey("users", "login.multi_session") || (conf::getKey("users", "login.multi_session") && !$row["auth_key"]))
						$auth_key = createRandomKey(32);

					$db->query("
						UPDATE ".dbTable("user")." SET
							".(($auth_key) ? "auth_key = '{$auth_key}'," : "")."
							date_login = CURRENT_TIMESTAMP()
						WHERE id = '{$row["id"]}'
					", __FILE__, __LINE__);

					// deleting "guest" from online users list
					$db->query("DELETE FROM ".dbTable("control")."
					WHERE (accesstype='?-' ".(isset($USER["id"]) ? " OR accesstype='$-{$USER["id"]}'": "").") AND ip='".IP_ADDR."'");

					// setting authentication cookie
					setUserConf("auth", createAuthCookie($hash, $auth_key ? $auth_key : $row["auth_key"]), (conf::getkey("users", "session_length", 84600*30, 'i')*60));

					appWriteLog("users: session opened for user '$username'");

				} elseif (strlen($row["auth_key"]) == 16) {
					// this user hasn't yet confirmed his account, and that is required for him to be able to login.
					$return = "index.php/module=users/action=confirm";
				} else {
					// inactive account
					formInputError("{E_INACTIVE_ACCOUNT}");
				}
			} else {

				appWriteLog("users: login failed for '$username'");

				sleep(conf::getkey("users", "failed_login_delay", 5, "i"));

				formInputError("{E_LOGIN_FAILED}");
			}

		break;
		case "register":

			// access rule for preventing flood
			$AccessTable->setRule (
				"user_registration",
				conf::getKey("users", "register.interval", 86400, "i"),
				conf::getKey("users", "register.requests", 3, "i"),
				false
			);

			if (isAdmin()) {

				varImport("username", "password", "email", "realname",
				"status", "groups");

			} else {
				// verifing visitor's permission to register new users
				if (!conf::getKey("users", "account.enable_self_registration"))
					appAbort(accessDenied());

				varImport("username", "password", "email", "realname", "confirm");
			}

			varRequire("username", "password", "email");

			$username = strtolower($username);

			// password length
			if (strlen($password) < conf::getKey("users", "password.min_length"))
				formInputError("{E_INSUFFICIENT_LENGTH}", "focus: password");

			// e-mail charset
			if (preg_match("/[^a-zA-Z0-9\_\-.@]/", $email) || !preg_match("/.*?@.*?/", $email))
				formInputError("{E_INVALID_CHARSET}", "focus: email");

			// password confirmation
			if ((!isAdmin() || isset($confirm)) && ($confirm != $password))
				formInputError("{E_DATA_MISMATCH}", "focus: password");

			// username charset
			if (preg_match("/[^a-zA-Z0-9\-_.]/", $username))
				formInputError("{E_INVALID_CHARSET}", "focus: username");

			// duplicated username?
			$db->query("SELECT id FROM ".dbTable("user")." WHERE username = '{$username}'");
			if ($db->numRows())
				formInputError("{E_DUPLICATED_ENTRY}", "focus: username");

			// duplicated e-mail address?
			$db->query("SELECT id FROM ".dbTable("user")." WHERE email = '{$email}'");
			if ($db->numRows())
				formInputError("{E_DUPLICATED_ENTRY}", "focus: email");

			// groups to be part of
			$groups = (isAdmin() && isset($groups) && is_array($groups)) ? implode(",", $groups) : "";

			// loading access rule
			if (!isAdmin())
				$AccessTable->loadRule("user_registration", "E_TOO_MANY_REGISTRATIONS");

			$db->query ("
			INSERT INTO ".dbTable("user")."
				(
					id,
					username,
					realname,
					password,
					email,
					date_registered,
					status,
					groups
				)
					VALUES
				(
					'".($id = $db->findFreeId("user"))."',
					'{$username}',
					'{$realname}',
					'".md5($username.$password)."',
					'{$email}',
					CURRENT_TIMESTAMP(),
					'".(isAdmin() ? $status : 1)."',
					'".($groups ? $groups : "500")."'
				)
			", __FILE__, __LINE__);

			$confirm = conf::getkey("users", "account.require_confirmation");
			$welcome = conf::getKey("users", "account.send_welcome_letter");

			if (!isAdmin() && ($confirm || $welcome)) {
				$confirmation = "";
				if ($confirm) {
					// disabling account and setting auth_key as a 16 chars long random key
					$confirmation = createRandomKey(16);
					$db->query("
						UPDATE ".dbTable("user")." SET
							status = '0',
							auth_key = '{$confirmation}'
						WHERE id = '{$id}'
					", __FILE__, __LINE__);
				}

				sendMail($email, _L("L_WELCOME"),
					createLetter("users/welcome",
						Array (
							"REALNAME" => $realname,
							"USERNAME" => $username,
							"PASSWORD" => $password,
							"CONFIRMATION_CODE" => $confirmation
						)
					)
				);
			}

			appWriteLog("users: new account registered ('$username')");
		break;
		case "account":
			# allow only authenticated users can change his account settings
			if (appAuthorize("$")) {

				varImport("realname", "email", "password", "new_password",
				"confirm_password", "option");

				switch ($option) {
					case "modify_password":
						varRequire("confirm_password", "new_password", "password");
					break;
					default:
						varRequire("realname", "email", "password");
					break;
				}

				// checking given password
				$q = $db->query("SELECT username, password FROM ".dbTable("user")."
				WHERE id = '{$USER["id"]}'");

				$row = $db->fetchRow($q);

				$username = strtolower($row["username"]);
				$hash = md5($username.$password);

				if ($hash == $row["password"]) {

					// want to change his password?
					if ($new_password || $confirm_password) {
						if ($new_password == $confirm_password) {
							$new_password = md5($username.$new_password);
						} else {
							formInputError("{E_DATA_MISMATCH}", "focus: new_password");
						}
					}

					// duplicated email
					$q = $db->query("SELECT id FROM ".dbTable("user")."
					WHERE email = '{$email}' AND id != '{$USER["id"]}'");
					if ($db->numRows($q))
						formInputError("{E_DUPLICATED_ENTRY}", "focus: email");

					$db->query("
						UPDATE ".dbTable("user")." SET
							".($realname ? "realname = '{$realname}'," : '')."
							".($new_password ? "password = '{$new_password}'" : "")."
							".($email ? "email = '{$email}'" : '')."
						WHERE id = '{$USER["id"]}' AND password = '$hash'
					", __FILE__, __LINE__);

				} else {
					formInputError("{E_INCORRECT_PASSWORD}", "focus: password");
				}
			} else {
				$return = "index.php/module=users/action=login";
			}
		break;
		case "profile":

			// allowing a user to edit his own profile. superuser is God, he can do anything he want
			if ((appAuthorize("$") && $USER["id"] == $id) || isAdmin()) {

				varImport("nickname", "public_email", "website", "location", "gender",
				"birthdate", "avatar", "about_me", "signature", "immsn", "imyim", "imicq");

				varRequire("id", "nickname");

				// is this nickname/email owned by someone else?
				$q = $db->query("SELECT u.id, p.id FROM ".dbTable("user")." u, ".dbTable("user_profile")." p
				WHERE (u.username = '{$nickname}' OR p.nickname = '{$nickname}')
				AND u.id != '{$id}' AND p.id != '{$id}'", __FILE__, __LINE__);
				if ($db->numRows($q))
					formInputError("{L_DUPLICATED_ENTRY}", "focus: nickname");

				$q = $db->query("SELECT id FROM ".dbTable("user")."
				WHERE email = '{$public_email}' AND id != '{$id}'", __FILE__, __LINE__);
				if ($db->numRows($q))
					formInputError("{L_DUPLICATED_ENTRY}", "focus: public_email");

				// making sure this profile exists
				$q = $db->query("SELECT id FROM ".dbTable("user_profile")." WHERE id = '$id'");

				$birthdate = $birthdate['y'].'-'.$birthdate['m'].'-'.$birthdate['d'];

				if ($db->numRows($q)) {
					// this profile exists, updating it
					$db->query("
						UPDATE ".dbTable("user_profile")." SET
							nickname = '{$nickname}',
							public_email = '{$public_email}',
							birthdate = '{$birthdate}',
							website = '{$website}',
							avatar = '{$avatar}',
							about_me = '{$about_me}',
							signature = '{$signature}',
							location = '{$location}',
							gender = '{$gender}',
							immsn = '{$immsn}',
							imyim = '{$imyim}',
							imicq = '{$imicq}'
						WHERE id = '{$id}'
					", __FILE__, __LINE__);

						appWriteLog("users: profile created for user '".users::getName($id)."'");

				} else {
					// this user has not created a profile
					$db->query("
						INSERT INTO ".dbTable("user_profile")." (
							id,
							nickname,
							public_email,
							birthdate,
							website,
							avatar,
							about_me,
							signature,
							location,
							gender,
							immsn,
							imyim,
							imicq
						) VALUES (
							'{$id}',
							'{$nickname}',
							'{$public_email}',
							'{$birthdate}',
							'{$website}',
							'{$avatar}',
							'{$about_me}',
							'{$signature}',
							'{$location}',
							'{$gender}',
							'{$immsn}',
							'{$imyim}',
							'{$imicq}'
						)
					", __FILE__, __LINE__);

					appWriteLog("users: profile updated for user '".users::getName($id)."'");

				}
			} else {
				$return = "index.php/module=users/action=login";
			}

		break;
	}

	// administrator actions
	if (isAdmin()) {
		switch ($action) {
			case "delete":
				varRequire("id");
				execWithEach($id, "users::deleteRegistry");
			break;
			case "enable":
				varRequire("id");
				users::setRegistryStatus($id, 1);
			break;
			case "edit":

				varImport("username", "password", "email", "realname",
				"status", "groups");

				varRequire("id", "username", "email");

				$allow_change = conf::getkey("users", "allow_username_change", 0, "b");

				if ($allow_change) {
					// checking for duplicated username
					$q = $db->query("SELECT id FROM ".dbTable("user")." WHERE username = '{$username}' AND id != '{$id}'");
					if ($db->numRows($q))
						formInputError("{L_TAKEN_USERNAME}", "focus: username");
					if (!$password) {
						formInputError("{L_MUST_CHANGE_PASSWORD_TOO}", "focus: password");
					}
				}

				if ($password) {
					if (!$allow_change) {

						$q = $db->query("SELECT username FROM ".dbTable("user")."
						WHERE id='{$id}'", __FILE__, __LINE__);

						$row = $db->fetchRow($q);

						$username = $row["username"];
					}

					$password = md5(strtolower($username).$password);
				}

				$groups = is_array($groups) ? implode(",", $groups) : "";

				// username could be allowed for administrators only
				$db->query("
					UPDATE ".dbTable("user")." SET
						".($allow_change ? "username = '{$username}'," : "")."
						realname = '{$realname}',
						".($password ? "password = '{$password}'," : "")."
						email = '{$email}',
						status = '{$status}',
						groups = '{$groups}'
					WHERE id = '{$id}'
				", __FILE__, __LINE__);

				appWriteLog("users: updated account information for user '$username'");

				if ($password)
					appWriteLog("users: password updated for user '$username'");

			break;
		}
	}

	appDisableSSL($return);

	appRedirect($return);

	dbExit();
?>