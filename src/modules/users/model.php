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

	Class gekkoModule_Users {
		function createMenu() {
			Menu_Editor::insertLink(1, "{L_LOGIN}", "index.php/module=users/action=login", "login.png", "?");
			Menu_Editor::insertLink(1, "{L_REGISTER}", "index.php/module=users/action=register", "users.png", "?");
			Menu_Editor::insertLink(2, "{L_MY_ACCOUNT}", "index.php/module=users", "account.png", "$");
			Menu_Editor::insertLink(2, "{L_LOGOUT}", "index.php/module=users/action=logout", "logout.png", "$");
			return true;
		}
		function configure() {
			conf::getKey("users", "account.require_confirmation", "1", "b");
			conf::getKey("users", "account.enable_self_registration", "1", "b");
			conf::getKey("users", "account.send_welcome_letter", "1", "b");

			conf::getKey("users", "account.enable_password_reset", "1", "b");
			conf::getKey("users", "account.register.interval", 60*60*24, "i");
			conf::getKey("users", "account.register.limit", "5", "i");
			conf::getKey("users", "password.min_length", "4", "i");

			conf::getKey("users", "login.multi_session", 0, "b");

			conf::getkey("users", "login_redirection", "index.php", "s");
		}
		function install() {
			$GLOBALS["db"]->query("
			CREATE TABLE ".dbTable("user")." (
				id MEDIUMINT (8) UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY (id),
				username VARCHAR (32),
				realname VARCHAR (64),
				password VARCHAR (32),
				email VARCHAR (64),
				date_registered DATETIME NOT NULL default '0000-00-00 00:00:00',
				date_login DATETIME NOT NULL default '0000-00-00 00:00:00',
				auth_key VARCHAR (32),
				status TINYINT (3) UNSIGNED NOT NULL default '0',
				reset_key VARCHAR (64),
				groups TEXT,
				preferences TEXT
			)
			",__FILE__, __LINE__, true);

			$GLOBALS["db"]->query("
			CREATE TABLE ".dbTable("user_profile")." (
				id MEDIUMINT (8) UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY (id),
				nickname VARCHAR (32),
				public_email VARCHAR (64),
				birthdate DATETIME NOT NULL default '0000-00-00 00:00:00',
				website VARCHAR (64),
				avatar VARCHAR (128),
				about_me TEXT,
				signature VARCHAR (255),
				location VARCHAR (255),
				gender TINYINT (1) UNSIGNED NOT NULL default '0',
				immsn VARCHAR (64),
				imyim VARCHAR (64),
				imicq VARCHAR (64)
			)
			",__FILE__, __LINE__, true);

			gekkoModule::register($GLOBALS["cgdb"]["users"], "users", "*", $GLOBALS["cgdb"]["users"]);
		}
		function uninstall() {
			$GLOBALS["db"]->query("DROP TABLE IF EXISTS ".dbTable("user"),__FILE__, __LINE__, true);
			$GLOBALS["db"]->query("DROP TABLE IF EXISTS ".dbTable("user_profile"),__FILE__, __LINE__, true);
		}
		function upgrade($provided_v) {
			$db =& $GLOBALS["db"];
			if (Packages::checkVersion("0.5.10", $provided_v) < 0) {
				// seslen usigned not null
				$db->query("ALTER TABLE ".dbTable("user")."
				CHANGE COLUMN seslen seslen SMALLINT (5) UNSIGNED NOT NULL
				",
				__FILE__, __LINE__);
			}
			if (packages::checkVersion("0.6.4", $provided_v) < 0) {
				$db->query("ALTER TABLE ".dbTable("user_profile")."
				CHANGE birthdate birthdate DATETIME NOT NULL default '0000-00-00 00:00:00',
				CHANGE COLUMN publicemail public_email VARCHAR (64),
				CHANGE COLUMN aboutme about_me TEXT
				", __FILE__, __LINE__);

				$db->query("ALTER TABLE ".dbTable("user")."
				ADD COLUMN date_registered DATETIME NOT NULL AFTER regtime,
				ADD COLUMN date_login DATETIME NOT NULL AFTER date_registered,
				CHANGE COLUMN resetcookie reset_key VARCHAR(32),
				CHANGE COLUMN authkey auth_key VARCHAR(32)",
				__FILE__, __LINE__);

				$db->query("UPDATE ".dbTable("user")." SET
				date_registered = FROM_UNIXTIME(regtime),
				date_login = FROM_UNIXTIME(lastlogin)", __FILE__, __LINE__);

				$db->query("ALTER TABLE ".dbTable("user")."
				DROP COLUMN regtime,
				DROP COLUMN lastlogin", __FILE__, __LINE__);
			}
		}
		function externalModuleUninstall($module) {

		}
	}

?>