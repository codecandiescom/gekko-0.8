<?php

	/*
	*	Gekko - Open Source Web Development Framework
	*	------------------------------------------------------------------------
	*	Copyright (C) 2004-2006, J. Carlos Nieto <xiam@users.sourceforge.net>
	*	This program is Free Software.
	*
	*	@package	Gekko
	*	@subpackage /lib
	*	@license	http://www.gnu.org/copyleft/gpl.html GNU/GPL License 2.0
	*	@author		J. Carlos Nieto <xiam@users.sourceforge.net>
	*	@link		http://www.gekkoware.org
	*/

	/*
		Initializes and outputs a gzipped stream to user's browser.
		This feature could save a lot of bandwidth!
	*/

	if (!defined("IN-GEKKO")) die("Get a life!");

	function gzinit() {
		if (!defined("GZ_OUTPUT")) {
			if (extension_loaded("zlib") && (PHP_VERSION > 4.0) && (isset($_SERVER["HTTP_ACCEPT_ENCODING"]) && ereg("(gzip)",$_SERVER["HTTP_ACCEPT_ENCODING"]))) {
				ob_start('ob_gzhandler');
				define("GZ_OUTPUT", true);
				return true;
			}
		}
		return false;
	}

	function gzoutput() {
		# Borrowed from phpBB, Originally by php.net
		if (defined("GZ_OUTPUT") && !headers_sent()) {

			$gzip_contents = ob_get_contents();
			$gzip_size = strlen($gzip_contents);

			header("Content-Encoding: gzip");
			header("Vary: Accept-Encoding");

			ob_end_clean();

			$gzip_crc = crc32($gzip_contents);

			$gzip_contents = gzcompress($gzip_contents, 9);
			$gzip_contents = substr($gzip_contents, 0, strlen($gzip_contents) - 4);

			$buff = "";
			$buff .= "\x1f\x8b\x08\x00\x00\x00\x00\x00";
			$buff .= $gzip_contents;
			$buff .= pack('V', $gzip_crc);
			$buff .= pack('V', $gzip_size);

			header("Content-Length: ".strlen($buff)."");

			echo $buff;

			return true;
		} else {
			return false;
		}
	}

?>
