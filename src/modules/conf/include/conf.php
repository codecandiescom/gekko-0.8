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

	/**
	* This script is loaded by ./modules/conf/admin.php->setup
	*
	* The main pourpose of this file is to display the most important
	* configuration keys with a description and some posible values (A.K.A
	* to make configuration easy for human beings)
	*/
	if (is_object($modConf)) {

		// first of all, we're going to recolect data for a posterior use.

		// fetching languages
		include_once GEKKO_SOURCE_DIR."lang/codes.php"; // (available languages are stored in $lang_db variable)

		// fetching stylesheets
		$stylesheets = Array();
		
		$data = listDirectory(GEKKO_TEMPLATE_DIR._L("C_SITE.TEMPLATE")."/_themes");

		foreach ($data as $style) {
			if ($file[0] != '.') {
				$stylesheets[$style] = ucwords($style);
			}
		}

		foreach ($stylesheets as $style => $name) {
			// this is part of a nice script to change Gekko's current stylesheet on-the-fly
			pageSetMarkContent("HEAD", "<link rel=\"alternate stylesheet\" type=\"text/css\" href=\""._L("C_SITE.REL_URL")."templates/"._L("C_SITE.TEMPLATE")."/_themes/{$style}/theme.css\" title=\"{$style}\" />");
		}

		// fetching templates (templates are not equal to stylesheets)
		$templates = Array();
		$data = listDirectory(GEKKO_TEMPLATE_DIR);
		foreach ($data as $file) {
			if ($file[0] != '.' && is_dir(GEKKO_TEMPLATE_DIR.$file)) {
				// beautifying this name too
				$templates[$file] = ucwords($file);
			}
		}

		// fetching smiley themes
		$smileys = Array();
		$data = listDirectory(GEKKO_SOURCE_DIR."media/smileys/");
		foreach ($data as $file) {
			if ($file[0] != '.' && is_dir(GEKKO_SOURCE_DIR."media/smileys/".$file)) {
				$smileys[$file] = ucwords($file);
			}
		}

		// fetching icon themes
		$icons = Array();
		$data = listDirectory(GEKKO_SOURCE_DIR."media/icons/");
		foreach ($data as $file) {
			if ($file[0] != '.' && is_dir(GEKKO_SOURCE_DIR."media/icons/".$file)) {
				$icons[$file] = ucwords($file);
			}
		}

		// now is time to use all the information we've recolected

		/**
			$modConf->set(
				Array (
					"module:keyname1" => false,
					"module:keyname2" => "some alternate html"
				)
			);

			As you can see in the above example $modConf->set() function requires
			an array as parameter, this array must have at least one element and
			with key "your_module:your_key_name_here" (You should only list the most
			important keys). If your array element value is setted to _false_ you
			don't need to specify posible values, it will be showed as a normal
			inputbox or checkbox (according to its type). In the other hand, if you
			specify other value than _false_ for that element it will be showed
			"as is". this is very useful if you want to specify a limited range
			of posible values for a key.
		*/
		$modConf->set (
			Array (
				"core:site.title"				=> false,
				"core:site.name"				=> false,
				"core:site.description"			=> false,
				"core:site.slogan"				=> false,
				"core:site.copyright"			=> false,
				"core:site.show_text_title"		=> false,
				"core:site.template"			=> createDropDown($templates, _L("C_SITE.TEMPLATE"), "key[%id]", "onchange=\"document.getElementById('confForm').submit()\""),
				"core:site.smileystheme"		=> createDropDown($smileys, _L("C_SITE.SMILEYSTHEME"), "key[%id]"),
				"core:site.icontheme"			=> createDropDown($icons, _L("C_SITE.ICONTHEME"), "key[%id]"),
				"core:site.stylesheet"			=> createDropDown($stylesheets, _L("C_SITE.STYLESHEET"), "key[%id]", "onchange=\"useStyleSheet(this.value)\""),
				"core:site.lang"				=> createDropDown($lang_db, _L("C_SITE.LANG"), "key[%id]"),
				"core:site.footer"				=> false,
				"core:site.contact_mail"		=> false,
				"core:site.gzip_output"			=> false,
				"core:site.hour_difference"		=> false,
				"core:plugins.msiepngfix.disable" => false,
				"core:html_filter"				=> false,
				"core:magic_blacklist"			=> false,
				"core:gbbcode"					=> false,
				"core:gbbcode.smileys"			=> false,
				"core:smtp.enable"				=> false,
				"core:rtbeditor"				=> false
			)
		);

	} else {
		trigger_error("\$modConf", E_USER_ERROR);
	}
?>
