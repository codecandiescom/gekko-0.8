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

	appLoadLibrary (
		"users"
	);

	if (defined("IN-TEMPLATE"))
		appLoadJavascript("modules/users/main.js");

	Extract (
		urlImportMap("null/action=alpha/user=alpha?id=int&edit=int&sent=int&code=alpha&email=string&step=int&option=alpha")
	);

	switch ($action) {
		case "login":

			appEnableSSL();

			$tpl = new contentBlock();

			$tpl->insert ("BLOCK_CONTENT", "users/main.login.tpl");
			$options = new optionBox($tpl, $option);
			$options->add ("register", "index.php/module=users/action=register", "{L_REGISTER}", "new.png");
			$options->add ("confirm", "index.php/module=users/action=confirm", "{L_CONFIRM_ACCOUNT}", "login.png");
			$options->make();

			$tpl->setArray (
				Array (
					"BLOCK_TITLE"	=> _L("L_LOGIN"),
					"BLOCK_ICON"	=> createIcon("login.png", 16),
					"USERNAME"		=> htmlspecialchars($user),
					"RETURN"		=> conf::getkey("users", "login_redirection")
				)
			);

			$mcBuff = $tpl->make();

			pageSetSubtitle(_L("L_LOGIN"));
		break;
		case "logout":
			if (isUser()) {

				if (!conf::getKey("users", "login.multi_session")) {

					// invalidating auth key
					$db->query("UPDATE ".dbTable("user")." SET auth_key = '".createRandomKey(32)."'
					WHERE id = '{$USER["id"]}'", __FILE__, __LINE__);

					// deleting from online users lists
					$db->query("DELETE FROM ".dbTable("control")."
					WHERE accesstype='$-{$USER["id"]}'", __FILE__, __LINE__);
				}

				appWriteLog("users: session closed for user '{$USER["id"]}'");

				// dropping cookie
				setUserConf("auth", "");
			}
			appRedirect("index.php");
			appShutdown();
		break;
		case "confirm":

			// redirecting to use ssl is available
    		appEnableSSL();

			if (conf::getKey("users", "account.require_confirmation")) {

				$tpl = new contentBlock();
				$tpl->insert ("BLOCK_CONTENT", "users/main.confirm.tpl");

				$options = new optionBox($tpl, $option);
				$options->add ("resend_confirmation", "index.php/module=users/action=confirm?option=%s", "{L_RESEND_CONFIRMATION}", "account.png");
				$options->make();

				$tpl->setArray (
					Array (
						"BLOCK_TITLE"	=> _L("L_ACCOUNT_CONFIRMATION"),
						"BLOCK_ICON"	=> createIcon("users.png", 16),
						"RETURN"		=> "index.php"
					)
				);

				$mcBuff = $tpl->make();

				pageSetSubtitle(_L("L_ACCOUNT_CONFIRMATION"));

			} else {
				$mcBuff = _L("L_DISABLED_FEATURE");
			}
		break;
		case "password_reset":

			// using ssl if available
    		appEnableSSL();

			if (conf::getKey("users", "account.enable_password_reset")) {

				$tpl = new contentBlock();
				$tpl->insert ("BLOCK_CONTENT", "users/_password_reset.form.tpl");

				$tpl->setArray (
					Array (
						"BLOCK_TITLE"		=> _L("L_RESET_PASSWORD"),
						"BLOCK_ICON"		=> createIcon("password.png", 16),
						"RETURN"			=> "index.php",
						"SENT"				=> htmlspecialchars($sent),
						"USERNAME"			=> htmlspecialchars($user),
						"CONFIRMATION"		=> htmlspecialchars($code),
						"EMAIL"				=> htmlspecialchars($email),
						"STEP"				=> htmlspecialchars($step),
					)
				);

				$mcBuff = $tpl->make();

				pageSetSubtitle(_L("L_RESET_PASSWORD"));
			} else {
				$mcBuff = _L("L_DISABLED_FEATURE");
			}
		break;
		case "register":

   			appEnableSSL();

			if (conf::getKey("users", "account.enable_self_registration")) {

				$tpl = new contentBlock();

				$tpl->insert("BLOCK_CONTENT", "users/_registration.form.tpl");

				$tpl->setArray (
					Array (
						"BLOCK_TITLE"	=> _L("L_NEW_ACCOUNT"),
						"BLOCK_ICON"	=> createIcon("users.png", 16),
						"RETURN"		=> "index.php/module=users/action=login",
					)
				);

				$mcBuff = $tpl->make();

				pageSetSubtitle(_L("L_USER_REGISTRATION"));

			} else {
				$mcBuff = accessDenied();
			}
		break;
		case "profile" :

			// a user can edit only his own profile, superuser is better than god :)
			$edit = ($edit && isUser() && (appAuthorize("users") || ($id == $USER["id"]) || ($user == $USER["user"])));

			// view profile
			if (($user || $id) && !$edit) {

				// viewing user's profile

				$q = $db->query ("
					SELECT id, username, realname FROM ".dbTable("user")."
					WHERE ".($id ? "id = '{$id}'" : "username = '{$user}'")."
				", __FILE__, __LINE__);

				if ($db->numRows($q)) {

					// saving first query info into buffer
					$profile = $db->fetchRow($q);

					saneHTML($profile);

					$user_id = $profile["id"];

					$q = $db->query("
						SELECT
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
						FROM ".dbTable("user_profile")."
						WHERE id = '{$user_id}'
					", __FILE__, __LINE__);

					if ($db->numRows($q)) {

						$buff = $db->fetchRow($q);

						saneHTML($buff, "about_me,signature");

						$profile = array_merge($profile, $buff);

						format::style($profile["about_me"], $user_id);
						format::style($profile["signature"], $user_id);

						$actions = new tplButtonBox();

						if (gekkoModule::auth("messages"))
							$actions->add(createLink("index.php/module=messages/action=compose/to={$profile["username"]}", _L("L_SEND_MESSAGE"), "messages.png"));

						$actions = $actions->make();

						$tpl = new contentBlock();
						$tpl->insert("BLOCK_CONTENT", "users/main.profile.tpl");

						$profile["public_email"] = createAntispam($profile["public_email"]);
						$profile["immsn"] = createAntispam($profile["immsn"]);

						$tpl->setArray($profile);
						$tpl->setArray(
							Array (
								"BLOCK_TITLE"	=> $profile["nickname"],
								"BLOCK_ICON"	=> createIcon("profile.png", 16),
								"ACTIONS"		=> $actions
							)
						);

						$mcBuff = $tpl->make();

						pageSetSubtitle(_L("L_USER_PROFILE").": {$profile["username"]}");

					} else {
						// this user has not created a public profile
						$mcBuff = createMessageBox("info", _L("L_NO_SUCH_PROFILE"));
						break;
					}
				}
			} else {

				if ($user && $edit) {
					$q = $db->query("SELECT id, username, email
					FROM ".dbTable("user")." WHERE username = '{$user}'", __FILE__, __LINE__);
					$u = $db->fetchRow($q);
				} else {
					$u = $USER;
				}

				// scanning avatars directory
				$avatars = users::listAvatars();

				$q = $db->query ("
					SELECT
						nickname, public_email, birthdate, website, avatar, about_me,
						signature, location, gender, immsn, imyim, imicq
					FROM ".dbTable("user_profile")."
					WHERE id = '{$u["id"]}'
				", __FILE__, __LINE__);

				if (!$db->numRows($q)) {
					// this user has not created a profile, using defaults
					$row = Array (
						"nickname"		=> $u["username"],
						"public_email"	=> $u["email"],
						"website"		=> "",
						"avatar"		=> "default.png",
						"about_me"		=> "",
						"signature"		=> "",
						"location"		=> "",
						"gender"		=> "0",
						"immsn"			=> "",
						"imicq"			=> "",
						"imyim"			=> "",
						"birthdate"		=> date("Y")."-01-01 00:00:00"
					);
				} else {
					$row = $db->fetchRow($q);
					saneHTML($row);
				}

				$tpl = new contentBlock();
				$tpl->insert("BLOCK_CONTENT", "users/_profile.form.tpl");

				$tpl->setArray($row);
				$tpl->setArray(
					Array (
						"BLOCK_TITLE"		=> _L("L_MY_PROFILE"),
						"BLOCK_ICON"		=> createIcon("profile.png", 16),
						"ID"				=> $u["id"],
						"ACTION"			=> "profile",
						"RETURN"			=> "index.php/module=users/action=profile/user={$u["username"]}"
					)
				);

				$mcBuff = $tpl->make();

				pageSetSubtitle(_L("L_MY_PROFILE"));
			}
		break;
		case "account":
			if (isUser()) {

   				appEnableSSL();

				$tpl = new contentBlock();
				$tpl->insert("BLOCK_CONTENT", "users/main.account.tpl");

				$q = $db->query("
					SELECT id, username, realname, email, preferences
						FROM ".dbTable("user")."
					WHERE id = '{$USER["id"]}'
					",__FILE__, __LINE__);

				$row = $db->fetchRow($q);
				saneHTML($row);

				$options = new optionBox($tpl, $option);
					$options->add (null, "index.php/module=users", "{L_USER_PANEL}", "login.png");
					$options->add (null, "index.php/module=users/action=account", "{L_MY_ACCOUNT}", "account.png");
					$options->add (null, "index.php/module=users/action=profile", "{L_MY_PROFILE}", "profile.png");
					$options->add ("modify_password", "index.php/module=users/action=account?option=%s", "{L_MODIFY_PASSWORD}", "lock.png");
				$options->make();

				$tpl->setArray($row);
				$tpl->setArray(
					Array (
						"BLOCK_TITLE"		=> _L("L_MY_ACCOUNT"),
						"BLOCK_ICON"		=> createIcon("account.png", 16),
						"ID"				=> $USER["id"],
						"ACTION"			=> "account",
						"RETURN"			=> "index.php/module=users"
					)
				);

				$mcBuff = $tpl->make();

				pageSetSubtitle(_L("L_MY_ACCOUNT"));
			} else {
				$mcBuff = accessDenied();
			}

		break;
		default:
			if (isUser()) {

				$Panel = new Panel(_L("L_USER_PANEL"), "account.png");

				$modules = gekkoModule::getList();

				foreach ($modules as $module) {
					if (cacheFunc("gekkoModule::auth", $module) && file_exists($icon = GEKKO_MODULE_DIR."$module/user_panel.php"))
						include $icon;
				}

				$mcBuff .= $Panel->make();

				pageSetSubtitle(_L("L_USER_PANEL"));

			} else {
				appAbort(header("Location: ".urlEvalPrototype("index.php/module=users/action=register", true, false)));
			}

		break;
	}
?>
