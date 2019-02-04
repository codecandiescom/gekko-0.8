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
	* untar(tar_file, [destination], [overwrite])
	*/
	function untar($file, $destination = ".", $overwrite = true) {
		/*
			*********************************************************
				This code portion is based on:
												Simple Machines Forum
			*********************************************************
				URL: http://www.simplemachines.org
				License: GNU/GPL
				Copyright: (c) Lewis Media (http://www.lewismedia.com)
				Software Version: SMF 1.0.3
			**********************************************************
		*/
		// fixing destination
		if (substr($destination, -1) != "/")
			$destination = $destination."/";

		// checking destination
		if (!is_writable($destination))
			trigger_error("'$destination' must be writable.", E_USER_ERROR);

		$fp = fopen($file, "r");

		$octdec = array('mode', 'uid', 'gid', 'size', 'mtime', 'checksum', 'type');
		$blocks = filesize($file)/512-1;

		$offset = 0;

		while ($offset < $blocks) {

			fseek($fp, $offset << 9);

			$header = fread($fp, 512);

			if (!($current = @unpack("a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/a8checksum/a1type/a100linkname/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor/a155path", $header))) {
				trigger_error("unpack.", E_USER_ERROR);
				return false;
			}

			foreach ($current as $name => $value) {
				$current[$name]= trim($current[$name]);
				if (in_array($name, $octdec))
					$current[$name] = octdec($current[$name]);
			}

			$checksum = 256;
			for ($i = 0; $i < 148; $i++) {
				$checksum += ord($header{$i});
			}
			for ($i = 156; $i < 512; $i++) {
				$checksum += ord($header{$i});
			}

			if ($current["checksum"] != $checksum) {
				if ($current["filename"])
					trigger_error("Checksum error!", E_USER_ERROR);
				 else {
				 	// finished
					return true;
				}
			}

			// protecting from parent directory writing
			$current["filename"] = preg_replace("/(\.\.\/|\/\.\.)/", "", $current["filename"]);

			$output = $destination.$current["filename"];

			$offset++;

			switch ($current["type"]) {
				case 5:
					// directory
					@mkdir($output, 0755);
				break;
				default:
					if (!file_exists($output) || $overwrite) {
						fseek($fp, $offset << 9);

						$fh = fopen($output, "wb");

						if ($current["size"])
							fwrite($fh, fread($fp, $current["size"]));

						fclose($fh);
					}
				break;
			}
			$offset += ceil($current["size"]/512);
			//chmod($output, $current["mode"]);
		}
		fclose($fp);
		return true;
	}

	/**
	* appPackageExtract(compressed_file, [destination]);
	*/
	function appPackageExtract($file, $destination = '.', $overwrite = 0, $type = null) {
		
		if (substr($destination, -1) != "/")
			$destination .= '/';
		
		if (!is_writable($destination))
			trigger_error("Directory '$destination' should be writable.", E_USER_ERROR);

		if (($fp = fopen($file, "r")) != false) {

			// reading file header to guess filetype
			$header = fread($fp, 4);
			fclose($fp);

			// guessing file type
			switch ($header) {
				case "\x1f\x8b\x08\x00":
					// gzip
					$fopen = "gzopen";
					$feof = "gzeof";
					$fread = "gzread";
					$fclose = "gzclose";
					
				break;
				case "\x42\x5a\x68\x39":
					// bzip2
					$fopen = "bzopen";
					$feof = "feof";
					$fread = "bzread";
					$fclose = "bzclose";

				break;
				case "\x50\x4b\x03\x04":
					// zip
					$zh = zip_open($file);

					while ($zfp = zip_read($zh)) {
						if (zip_entry_open($zh, $zfp, "w")) {
							$zipname = trim(zip_entry_name($zfp));
							// preventing hacks
							if (!preg_match("/\.\./", $zipname)) {
								$path = "{$destination}{$zipname}";
								if (substr($zipname, -1) == '/') {
									mkdir ($path);
								} else {
									$fh = fopen ($path, "w");
									fwrite($fh, zip_entry_read($zfp, zip_entry_filesize($zfp)));
									fclose($fh);
									zip_entry_close($zfp);
								}
								chmod ($path, 0775);
							}
						}
					}
					zip_close($zh);

					return true;
				break;
				default:
					if (getFileExtension($file) == "tar") {
						// let's suppose this is as a .tar archive
						$fopen = "fopen";
						$feof = "feof";
						$fread = "fread";
						$fclose = "fclose";
					} else {
						trigger_error("Unknown file type. You may specify .tar.gz, .tar.bz2, .zip or even .tar files only.", E_USER_ERROR);
					}
				break;
			}

			// temporary file name
			$temp = tempnam($destination, "tar");

			if (isset($fopen)) {
				// creating temporary file
				$tempf = fopen($temp, "w");

				// uncompressing
				$fi = $fopen($file, "r");
				while (!$feof($fi))
					fwrite($tempf, $fread($fi, 1024*1024));
				$fclose($fi);

				// closing temp file
				fclose($tempf);
			}

			untar($temp, $destination, $overwrite);

			// removing temporary file
			unlink($temp);

			return true;
		} else {
			return false;
		}
	}

?>
