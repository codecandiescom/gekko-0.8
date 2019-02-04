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

	define ("GEKKO_CACHEDIR", GEKKO_USER_DIR."temp/cache/");
	define ("GEKKO_CACHELIFE", 60*15);

	$GLOBALS["_CACHE"] = Array();

	function cacheDecode($data) {
		return function_exists('gzuncompress') ? gzuncompress($data) : $data;
	}
	function cacheEncode($data) {
		return function_exists('gzcompress') ? gzcompress($data): $data;
	}

	/**
	* cacheInit();
	* Cleans cache's working directory.
	*/
	function cacheInit() {
	
		if (!defined("GEKKO_CACHEDUMP"))
			define ("GEKKO_CACHEDUMP", GEKKO_CACHEDIR.(GEKKO_LANGUAGE_ID ? GEKKO_LANGUAGE_ID.'_' : '')."vardump.cache");

		if (!defined("GEKKO_PAGE_CACHE_KEY")) {
			if (defined("GEKKO_PAGE_CACHE_KEY") || defined("GEKKO_REQUEST_MODULE"))
				define ("GEKKO_PAGE_CACHE_PREFIX", (defined("GEKKO_ACTION_MODULE") ? GEKKO_ACTION_MODULE : GEKKO_REQUEST_MODULE).'@');
			else
				define ("GEKKO_PAGE_CACHE_PREFIX", '');
	
			define ("GEKKO_PAGE_CACHE_KEY", (GEKKO_LANGUAGE_ID ? GEKKO_LANGUAGE_ID.'_' : '').GEKKO_PAGE_CACHE_PREFIX.md5($_SERVER["SERVER_NAME"]).md5($_SERVER["REQUEST_URI"]).strlen($_SERVER["REQUEST_URI"]).'.cache');
		}
		
		if (!file_exists(GEKKO_CACHEDIR)) {
			@mkdir(GEKKO_CACHEDIR);
			@chmod(GEKKO_CACHEDIR, 0775);
		}
		
		if (GEKKO_VORAZ_CACHE && file_exists(GEKKO_CACHEDUMP))
			$GLOBALS["_CACHE"] = array_merge($GLOBALS["_CACHE"], unserialize(cacheDecode(loadFile(GEKKO_CACHEDUMP))));

		cacheClean(GEKKO_CACHEDIR, GEKKO_CACHELIFE);
	}

	/**
	* cacheClean();
	* Gets rid of outdated cache files.
	*/
	function cacheClean($directory, $life) {
		$dp = opendir($directory);
		while (($file = readdir($dp)) !== false) {
			if ($file[0] != '.') {
				$abs_file = $directory.$file;
				if (filectime($abs_file)+$life < time()) {
					unlink($abs_file);
				}
			}
		}
		closedir($dp);
	}

	/**
	* cacheAssignKey(&content, str_append, preserve_extension);
	* @returns: a unique key for storing a buffer in cache
	*/
	function cacheAssignKey(&$content, $append = null, $preserve_extension = false) {
		return (GEKKO_LANGUAGE_ID ? GEKKO_LANGUAGE_ID.'_' : '').md5($_SERVER["HTTP_HOST"]).md5($content).strlen($content).($append ? "_$append" : "").($preserve_extension ? "" : ".cache");
	}

	/**
	* cacheSave($key, &$content);
	* Saves the given buffer to disk.
	*/
	function cacheSave($key, &$content) {
		if (!defined("IN-INSTALL"))
			if (($fh = @fopen(GEKKO_CACHEDIR.$key, "w")) !== false) {
				fwrite($fh, cacheEncode(serialize($content)));
				fclose($fh);
			} else {
				if (!is_writable(GEKKO_CACHEDIR))
					trigger_error("Please verify that ".GEKKO_CACHEDIR." is writable.", E_USER_WARNING);
			}
	}
	/**
	* cacheCheckLifetime($key);
	* @returns: The last modified time of a cached buffer (UNIX time)
	*/
	function cacheCheckLifetime($key) {
		return file_exists(GEKKO_CACHEDIR.$key) ? filemtime(GEKKO_CACHEDIR.$key) : false;
	}

	/**
	* cacheRead($key, &$buffer);
	* Stores the cached data, identified by $key into &$buffer
	*/
	function cacheRead($key, &$buff) {
		$file = GEKKO_CACHEDIR.$key;
		if (file_exists($file)) {
			if (($fs = filesize($file)) > 0) {
				$fh = fopen($file, "r");
				$buff = unserialize(cacheDecode(fread($fh, $fs)));
				fclose($fh);
			}
			return true;
		}
		return false;
	}

	/**
	* cacheFunc(function_name, argument_1, argument_2, ...);
	* @returns: cached function return value
	*/
	function cacheFunc() {

		$args = func_get_args();

		$args[0] = strtolower($args[0]);

		$key = md5('function.'.implode($args));

		if (!isset($GLOBALS["_CACHE"][$key])) {

			$function = explode("::", array_shift($args));

			array_walk($args, create_function('$a', 'return addslashes($a);'));
			$args = "'".implode("' , '", $args)."'";

			$func = create_function (
				'$function',
				'return call_user_func('.(isset($function[1]) ? "array(\$function[0], \$function[1])" : "\$function[0]").($args ? ', '.$args : '').');'
			) or die("Function does not exists: ".implode("::", $function));

			$GLOBALS["_CACHE"][$key] = cacheEncode(serialize($func($function)));
		}

		return unserialize(cacheDecode($GLOBALS["_CACHE"][$key]));
	}

	/**
	* cacheVar(variable_name, value);
	* @returns: Memorized value of a variable (just like keeping it as global...)
	*/
	function cacheVar($key, $value = null) {
		$key = md5("variable.".$key);
		if (!isset($GLOBALS["_CACHE"][$key])) {
			if (!$value) {
				return false;
			} else {
				$GLOBALS["_CACHE"][$key] = cacheEncode(serialize($value));
			}
		}
		return unserialize(cacheDecode($GLOBALS["_CACHE"][$key]));
	}

	function cacheCleanAfterPost() {
		if (!defined("GEKKO_CLEAN_CACHE")) {
			define("GEKKO_CLEAN_CACHE", true);
			$dh = opendir(GEKKO_CACHEDIR);
			while (($file = readdir($dh)) != false) {
				if ($file != '.' && $file != "..") {
					@chmod(GEKKO_CACHEDIR.$file, 0755);
					@unlink(GEKKO_CACHEDIR.$file);
				}
			}
			fclose($dh);
		}
	}
	
	function cacheDestroy($key) {
		@unlink(GEKKO_CACHEDIR.$key);
	}
	function cacheVarDestroy($key) {
		$key = md5("variable.".$key);
		unset($GLOBALS["_CACHE"][$key]);
	}

	function cacheDump() {
		if (GEKKO_VORAZ_CACHE && !defined("GEKKO_CLEAN_CACHE")) {
			$fp = @fopen(GEKKO_CACHEDUMP, "w");
			if ($fp) {
				fwrite($fp, cacheEncode(serialize($GLOBALS["_CACHE"])));
				fclose($fp);
			}
		}
	}
?>
