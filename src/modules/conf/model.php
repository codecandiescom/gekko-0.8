<?php

	/*
	*	Gekko - Open Source Web Development Framework
	*	------------------------------------------------------------------------
	*	Copyright (C) 2004-2006, J. Carlos Nieto <xiam@users.sourceforge.net>
	*	This program is Free Software.
	*
	*	@package	Gekko CMS
	*	@license	http://www.gnu.org/copyleft/gpl.html GNU/GPL License 2.0
	*	@author		J. Carlos Nieto <xiam@users.sourceforge.net>
	*	@link		http://www.gekkoware.org
	*/

	if (!defined("IN-GEKKO")) die("Get a life!");

	Class gekkoModule_Conf {
		function createMenu() {
			// we don't want a menu entry to be created
			return false;
		}
		function configure() {

			// this is the only key that must be overwrited when updating
			conf::setKey("core", "site.gekko_version", GEKKO_VERSION, 's');

			// below keys are created with default values if doesn't exists
			conf::getKey("core", "site.title", "Site title", 's');
			conf::getKey("core", "site.name", "Site name", 's');
			conf::getKey("core", "site.slogan", "Site slogan", 's');
			conf::getKey("core", "site.slogan", "Site description", 's');
			conf::getKey("core", "site.show_text_title", 1, 'b');
			conf::getKey("core", "site.icontheme", "default", 's');
			conf::getKey("core", "site.smileystheme", "default", 's');
			conf::getKey("core", "site.gzip_output", 1, 'b');
			conf::getKey("core", "site.copyright", "&amp;copy; ".date("Y")." example.org", "default", 's');
			conf::getKey("core", "site.footer", "Powered by <a href=\"http://www.gekkoware.org\">Gekko</a>", 's');
			conf::getKey("core", "site.lang", "es", 's');
			conf::getKey("core", "site.primary_lang", "es", 's');
			conf::getKey("core", "site.primary_country", "MX", 's');
			conf::getKey("core", "site.contact_mail", "webmaster@example.org", 's');
			conf::getKey("core", "site.cookie.prefix", "gekkocms_", 's');
			conf::getKey("core", "site.cookie.path", "/", 's');
			conf::getKey("core", "site.cookie.life", 60*60*24, 'i');
			conf::getKey("core", "ftp.user", "", 's');
			conf::getKey("core", "ftp.pass", "", 's');
			conf::getKey("core", "ftp.port", "21", 's');
			conf::getKey("core", "ftp.save_pass", 0, 'b');
			conf::getKey("core", "ftp.host", "localhost", 's');
			conf::getKey("core", "ftp.path", "", 's');
			conf::getKey("core", "smtp.host", "localhost", 's');
			conf::getKey("core", "smtp.port", "25", 's');
			conf::getKey("core", "smtp.user", "", 's');
			conf::getKey("core", "smtp.pass", "", 's');
			conf::getKey("core", "smtp.enable", 0, 'b');
			conf::getKey("core", "html_filter", 1, 'b');
			conf::getKey("core", "rtbeditor", 1, 'b');
			conf::getKey("core", "rtbeditor.default", 1, 'b');
			conf::getKey("core", "gbbcode", 1, 'b');
			conf::getKey("core", "gbbcode.smileys", 1, 'b');
			conf::getKey("core", "magic_blacklist", 1, 'b');

			conf::getKey("modules", "default", "blog", 's');
		}
		function install() {
			$GLOBALS["db"]->query("
			CREATE TABLE ".dbTable("conf")." (
				id SMALLINT (8) UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY (ID),
				module VARCHAR (64) NOT NULL default '',
				keyname VARCHAR (128) NOT NULL default '',
				keyvalue VARCHAR (255),
				keytype TINYINT (1),
				date_modified TIMESTAMP(19),
				date_created DATETIME default '0000-00-00 00:00:00',
				locked ENUM('0', '1') default '0'
			)
			", __FILE__, __LINE__, true);

			if (!defined("IN-INSTALL")) {
				gekkoModule::register($GLOBALS["cgdb"]["conf"], "conf", "#", $GLOBALS["cgdb"]["conf"]);
			}
		}
		function uninstall() {
			$GLOBALS["db"]->query("DROP TABLE IF EXISTS ".dbTable("conf"), __FILE__, __LINE__, true);
		}
		function upgrade($provided_v) {
			$db =& $GLOBALS["db"];

			if (packages::checkVersion("0.5.20", $provided_v) < 0) {
				$db->query ("ALTER TABLE ".dbTable("conf")."
				ADD COLUMN locked ENUM('0', '1') default '0' AFTER modified",
				__FILE__, __LINE__, true);
			}
			if (packages::checkVersion("0.6.9", $provided_v) < 0) {
				// replacing 'modified' field
				$db->query("ALTER TABLE ".dbTable("conf")."
				ADD COLUMN date_modified TIMESTAMP(19) AFTER modified,
				ADD COLUMN date_created DATETIME default '0000-00-00 00:00:00' AFTER date_modified",
				__FILE__, __LINE__, true);


				$db->query("UPDATE ".dbTable("conf")." SET date_modified = FROM_UNIXTIME(modified)", __FILE__, __LINE__, true);

				$db->query("ALTER TABLE ".dbTable("conf")."
				DROP COLUMN modified", __FILE__, __LINE__, true);

				// replacing blockscript named 'style-chooser' whith 'style_chooser'
				appLoadLibrary("blocks");

				blocks::renameScript("conf/style-chooser.php", "conf/style_chooser.php");
			}
		}
		function externalModuleUninstall($module) {

		}
	}

?>
