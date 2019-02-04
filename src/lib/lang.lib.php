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

	class Lang {
		var $langCode = "en";
		var $langPath = "lang/en";

		// return the current Gekko's language ID
		function getLang($main = false) {
			$self =& $GLOBALS["Lang"];
			return $main ? substr($self->langCode, 0, 2) : $self->langCode;
		}

		// Initializes class and loads main language file
		function Lang($lang = null) {
			
			$lang = preg_replace("/[^a-zA-Z\-]/", '', substr($lang, 0, 5));

			$this->langCode = ($lang) ? $lang : GEKKO_DEFAULT_LANG;
			$this->langPath = GEKKO_SOURCE_DIR."lang/{$this->langCode}/";

			// falling back to default language
			if (!file_exists($this->langPath)) {
				$this->langCode = GEKKO_DEFAULT_LANG;
				$this->langPath = GEKKO_SOURCE_DIR."lang/{$this->langCode}/";
			}

			if (!defined("IN-INSTALL")) {
				$db =& $GLOBALS["db"];
	
				$q = $db->query("SELECT * FROM `{$GLOBALS["dbinfo"]["pref"]}".$this->langCode."_control` LIMIT 1", __FILE__, __LINE__, true);

				define("GEKKO_LANGUAGE_ID", $q ? $this->langCode : '');
			}
		}

		function loadCoreFiles() {
			// loading main language file
			include_once $this->langPath."main.php";

			// loading each module's basic language definitions
			$modules = gekkoModule::getList();
			foreach ($modules as $module) {
				$path = GEKKO_SOURCE_DIR."modules/$module/lang/{$this->langCode}/core.php";
				if (file_exists($path))
					include_once ($path);
			}
		}

		// loads language files for the given module
		function loadFromModule($module, $load = "main") {
			$path = GEKKO_SOURCE_DIR."modules/{$module}/lang/{$this->langCode}/{$load}.php";
			if (file_exists($path))
				include $path;
		}

		// sets a language variable
		function set($varname, $value) {

			$value = preg_replace_callback("/{SERVER_([^}]*)}/", create_function('$a', 'return $_SERVER[$a[1]];'), $value);
			$value = preg_replace_callback("/{INI_([^}]*)}/", create_function('$a', 'return ini_get("{$a[1]}");'), $value);

			preg_match_all("/{=(.*?)=}/", $value, $match);

			if ($match[1]) {
				foreach ($match[1] as $i => $func) {
					$lambda = create_function (
						'$a',
						'return '.$func.';'
					) or die(gdebug("FUNCTION: ".$func));
					$value = str_replace($match[0][$i], $lambda(null), $value);
				}
			}

			$GLOBALS["L"][$varname] = $value;

		}

		// returns a language variable given its key
		function get($varname) {
			return isset($GLOBALS["L"][$varname]) ? $GLOBALS["L"][$varname] : $varname;
		}

		// parses text against language variables
		function parse($string) {
			return preg_replace_callback("/{([^}]*)}/", create_function('$a', 'return _L($a[1]);'), $string);
		}
	}

	function _L($id, $value = false) {
		return ($value !== false) ? Lang::Set($id, $value) : Lang::Get($id);
	}

	function getLang () {
		return $GLOBALS["Lang"]->getLang();
	}
?>
