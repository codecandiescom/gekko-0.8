<?php


	if (!defined("IN-GEKKO")) die("Get a life!");

	Lang::Set("L_SERVER", "Server");
	Lang::Set("L_ACCOUNT", "User account");
	Lang::Set("L_PATH", "Path");
	Lang::Set("L_INSTALLATION", "Installation");
	Lang::Set("L_STMP_ACCESS_HINT", "If you have access to an SMTP server and you want to use it instead of using PHP's mail() function, enter your account information here. If you don't want to, just leave those fields blank.");

	Lang::Set("L_INSTALLATION_TYPE", "Instalation type");
	Lang::Set("L_CLEAN_INSTALL", "Clean install");
	Lang::Set("L_VERSION_UPGRADE", "Version Upgrade (Gekko &gt;= 0.5)");
	Lang::Set("L_CONFIGURATION", "Configuration");

	Lang::Set("L_UPGRADE", "Upgrade");
	Lang::Set("L_UPGRADE_HINT", "If you do not desire to install a new module please delete its directory found under <i>modules/</i>.");

	Lang::Set("L_INSTALL_NEW_MODULES", "Install the newly found modules");

	Lang::Set("L_MANUAL_WAY", "Manual way");

	Lang::Set("L_FTP_SERVICE", "FTP Service");

	Lang::Set("L_PERMISSIONS_CHANGE", "Change permissions");

	Lang::Set("L_PERMISSIONS_CHANGE_HINT", "Gekko needs writing-rights for certain directories. If you have FTP service the changes can be performed by Gekko after filling this form. If you want to perform those changes manually you must change the permissions of the directories <i>data</i>, <i>temp</i> and the file <i>dbconf.php</i> for being writable (chmod -R 777 data temp dbconf.php) then yo should reload this page. Don't worry, your FTP account information will not be saved without your authorization.");

	Lang::Set("L_FTP_PATH_HINT", "The path must be relative to the directory where the FTP session will start and it must be the same as Gekko's root (where index.php lies)");

	Lang::Set("L_DATABASE", "Database");

	Lang::Set("L_DATABASE_HINT", "In order to access to your database you must choose a compatible <b>controller</b>. If you're unsure about which options you should enable you can let them unchanged. Please backup your database before continuing this proccess.");

	Lang::Set("L_INSTALLATION_TYPE_HINT", "Choose the <b>task</b> you want to perform, <i>version upgrade</i> or <i>clean install</i>. The first one will update the database to work with this version of Gekko, while the <i>clean install</i> will drop any information found in the database leaving you a new Gekko powered website.");

	Lang::Set("L_DRIVER", "Driver");
	Lang::Set("L_DATABASE", "Database");
	Lang::Set("L_OPTIONS", "Options");
	Lang::Set("L_TABLE_PREFIX", "Table prefix");
	Lang::Set("L_DROP_PREFIXED_TABLES", "Clean the tables with the prefix");
	Lang::Set("L_DROP_DATABASE", "Clear the database before installing");

	Lang::Set("L_ADMIN_CREATION", "Administrator creation");
	Lang::Set("L_REALNAME", "Real name");
	Lang::Set("L_CONFIRM_PASSWORD", "Confirm password");

	Lang::Set("L_ADMIN_CREATION_HINT", "Enter information for creating the site's <b>administrator</b>. Be careful when choosing a <b>password</b>, we recommend you to use one longer than 8 characters, difficult to guess, mixing numbers and letter cases.");

	Lang::Set("L_SITE_CONFIGURATION", "Site Configuration");
	Lang::Set("L_SITE_LANG", "Site language");
	Lang::Set("L_SITE_TITLE", "Site title");
	Lang::Set("L_SITE_NAME", "Site name");
	Lang::Set("L_SITE_SLOGAN", "Site slogan");
	Lang::Set("L_SITE_DESCRIPTION", "Site description");
	Lang::Set("L_SITE_COPYRIGHT", "Copyright message");
	Lang::Set("L_SITE_CONTACT_MAIL", "E-Mail address");

	Lang::Set("L_PRIMARY_AUDITORY", "Main audience (target public)");
	Lang::Set("L_PRIMARY_LANG", "Primary language");
	Lang::Set("L_PRIMARY_COUNTRY", "Primary country");

	Lang::Set("L_RTB_EDITOR", "Visual Content Editor");
	Lang::Set("L_GBB_CODE", "GBB style tags");
	Lang::Set("L_USERS_REQUIRE_CONFIRMATION", "Registration of new users require confirmation (via e-mail)");
	Lang::Set("L_USERS_ENABLE_SELF_REGISTRATION", "Allow registration by anyone");
	Lang::Set("L_USERS_SEND_WELCOME_LETTER", "Send a welcome letter to new users");

	Lang::Set("L_ADVANCED", "Advanced");
	Lang::Set("L_COOKIE_PREFIX", "Cookie prefix names");
	Lang::Set("L_COOKIE_PATH", "Cookie paths");
	Lang::Set("L_COOKIE_LIFE", "Cookie life (seconds)");
	Lang::Set("L_ENABLE_GZIP_OUTPUT", "Enable GZIP compressed output");
	Lang::Set("L_FEATURES", "Features");

	Lang::Set("L_DISABLE_MSIE_PNGFIX", "Disable PNG transparency plug-in for Microsoft Internet Explorer (by doing this you can prevent many users for experiment a freeze on their ancient browser)");

	Lang::Set("L_POST_INSTALLATION_OPTIONS", "Post instalation");
	Lang::Set("L_CREATE_BASIC_MENU", "Create basic menu");
	Lang::Set("L_DELETE_INSTALLATION_FILES", "Delete installation files");

	Lang::Set("L_SITE_CONFIGURATION_HINT", "Here you can personalize your website, all those options can be changed later. If you don't want to configure those options now you can continue installing using the default values.");

	Lang::Set("L_SECURITY_CHECK", "Security check");

	Lang::Set("L_SECURITY_CHECK_HINT", "As a security check, you must provide the same passwords you set during previous steps.");

	Lang::Set("L_FTP_PASSWORD", "FTP password");
	Lang::Set("L_DATABASE_PASSWORD", "Database password");

	Lang::Set("L_THANKS", "Thank you!");
	Lang::Set("L_THANKS_MESSAGE", "Thank you for using Gekko. We hope that you also enjoy and support the <a href=\"http://www.gnu.org/philosophy/free-sw.es.html\">Free Software</a> development. As final step, delete <i>install.php</i> file and <i>install/</i> directory. You may download icons, styles, smileys and more from our project's website <a href=\"http://www.gekkoware.org\">http://www.gekkoware.org</a>");

	Lang::Set("E_ACCESS_DENIED", "%s: Access denied");
	Lang::Set("E_PASSWORD_MISMATCH", "Incorrect password");
	Lang::Set("E_WRONG_DATABASE_PASSWORD", "Incorrect database password");
	Lang::Set("E_WRONG_FTP_PASSWORD", "Incorrect FTP password");

	Lang::Set("E_WRONG_PATH", "Incorrect path");

	Lang::Set("L_SMTP_ACCESS", "SMTP access");
	Lang::Set("L_ENABLE_SMTP", "Enable SMTP mail delivery using the built-in fuctions instead of using PHP's mail()");
	Lang::Set("L_COOKIE_SETTINGS", "Cookie configuration");
?>