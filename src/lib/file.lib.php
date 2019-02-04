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

	/* cannot upload|download files with those extensions */
	define("FILE_BANNED_EXTENSIONS", "");
	/* */
	define("DEFAULT_FILE_PERMISSION", 0775);

	require_once "network.lib.php";

	function guessFileType($file) {
		$extension = getFileExtension($file);

		switch ($extension) {
			case "jpg": case "jpeg": case "png": case "gif": case "bmp": case "tiff":
				$type = "image";
			break;
			case "swf":
				$type = "flash";
			break;
			case "mpg": case "mpeg": case "avi": case "mpg":
				$type = "video";
			break;
			case "mp3": case "ogg": case "wav":
				$type = "audio";
			break;
			case "mid": case "midi":
				$type = "midi";
			break;
			case "txt": case "asc":
				$type = "text";
			break;
			case "o": case "f": case "h": case "sql": case "c": case "perl":
				$type = "source";
			break;
			default:
				if (is_dir($file))
					$type = "folder";
				else
					$type = $extension;
			break;
		}

		return $type;
	}
	/*
		Splits an URI into username, password, host, port and file.
		-> protocol://user:pass@host:port/file
	*/
	function parseURI($uri) {
		$r = array();
		preg_match("/^([a-zA-Z0-9]*)?\:\/\/([^\/]+)(.*)/i", $uri, $m);

		if (!count($m))
			return array("proto" => "");

		$r["proto"] = $m[1];
		$r["host"] = $m[2];
		$r["file"] = $m[3];
		$r["user"] = "";
		$r["pass"] = "";
		$r["port"] = "";

		if (($d = strpos($r["host"], "@")) !== false) {
			$r["user"] = substr($r["host"], 0, $d);
			$r["host"] = substr($r["host"], $d+1);
		}
		if (($d = strpos($r["user"], ":")) !== false) {
			$r["pass"] = substr($r["user"], $d+1);
			$r["user"] = substr($r["user"], 0, $d);
		}
		if (($d = strpos($r["host"], ":")) !== false) {
			$r["port"] = substr($r["host"], $d+1);
			$r["host"] = substr($r["host"], 0, $d);
		}
		return $r;
	}

	/* Gets a remote or local file. */
	function getFile($location, $try = 0) {

		$uri = parseURI($location);
		$try++;
	

		switch ($uri["proto"]) {
			// http get
			case "http":
				$rc = new httpSession($uri["host"], $uri["port"], $uri["user"], $uri["pass"]);

				if (($buff = $rc->get($uri["file"])) !== false) {
					$rc->close();
				} elseif ($rc->status()) {

					// checking headers
					$headers = explode("\n", $rc->header);

					foreach ($headers as $h) {
						$del = strpos($h, ":");
						$hdr = strtolower(substr($h, 0, $del));
						$val = trim(substr($h, $del+1));
						switch ($hdr) {
							case "location":
								// preventing loop
								if ($try > 2)
									return false;
								$rc->close();

								// making new url

								if (strtolower(substr($val, 0, 7)) == "http://") {
									$new_url = $val;
								} else {

									$new_url = "http://";

									$uri2 = parseURI($val);
									if ($uri["user"]) {
										$new_url .= "".$uri["user"].":".$uri["pass"]."@";
									}
									$new_url .= $uri["host"].($uri["port"] ? ":".$uri["port"]: "");
									$new_url .= $uri2["file"];
								}

								$buff = getfile($new_url, $try);
							break;
						}
					}
				}
			break;
			case "ftp":
				$rc = new ftpSession($uri["host"], $uri["port"], $uri["user"], $uri["pass"]);
				if ($rc->status()) {
					$buff = $rc->get(substr($uri["file"], 1));
					$rc->bye();
				}
			break;
			default:
				if (!file_exists($location))
					$location = GEKKO_USER_DIR.$location;
				if (($buff = @loadFile($location)) == false) {
					trigger_error("Couldn't get file \"$location\"!", E_WARNING);
				}
			break;
		}
		return $buff ? $buff : false;
	}

	function uploadError($file) {
		return sprintf(_L("E_UPLOAD_FAILED"), $file, ini_get("upload_max_filesize"));
	}

	/* checks for an indexed free filename to avoid overwriting */
	function allocateBasename($parent_directory, $basename, $full_path = false) {
		$n = substr($basename, 0, strpos($basename, "."));
		$e = substr($basename, strpos($basename, ".") + 1);
		$i = 1;
		while (file_exists("$parent_directory/$basename")) {
			$basename = $n."[".$i."]".".".$e;
			$i++;
		}
		return (($full_path) ? $parent_directory."/": "").$basename;
	}

	/* a work directory must exist and be writable */
	function createWorkDirectory($dir) {
		$dirs = explode("/", $dir);
		$tmp = "";
		foreach ($dirs as $dir) {
			$tmp .= "/$dir";
			if (!@is_dir($tmp)) {
				@mkdir($tmp, 0775);
				if (!is_writable($tmp)) @chmod($tmp, 0775);
			}
		}
	}

	function writeFile($file, $content, $mode = "w") {
		if (($fp = fopen($file, $mode)) != false) {
			fwrite($fp, $content);
			fclose($fp);
		} else {
			trigger_error("Couldn't open $file for writing.", E_USER_ERROR);
		}
	}

	function saveFile($location, $destination, $basename = null, $overwrite = false, $delete_source_after_save = false) {

		$file = Array();

		if (substr($destination, -1) != "/")
			$destination .= "/";

		$abs_destination = GEKKO_USER_DIR.$destination;

		createWorkDirectory($abs_destination);

		$file["original"] = $basename;

		if (file_exists($abs_destination) && is_dir($abs_destination) && is_writable($abs_destination)) {
			if (($buff = getFile($location)) !== false) {

				$file["basename"] = $basename ? $basename : basename($location);

				if (!$overwrite)
					$file["basename"] = allocateBasename($abs_destination, $file["basename"]);

				$file["rel_loc"] = $destination.$file["basename"];
				$file["abs_loc"] = $abs_destination.$file["basename"];

				writeFile($file["abs_loc"], $buff);

				chmod($file["abs_loc"], DEFAULT_FILE_PERMISSION);

				if ($delete_source_after_save)
					unlink($location);

				return $file;

			} else {
				appSetMessage("error", "The specified file couldn't be downloaded");
			}

		} else {
			appSetMessage("error", "The directory '$destination' is not writable.");
		}
	}

	function saveUploadedFile($file, $module) {
		$file =& $_FILES[$file];

		if (isset($file) && !$file['error']) {
			$f = saveFile (
				$file['tmp_name'],
				"data/$module",
				basename($file['name'])
			);
			return $f;
		}

		return false;
	}

	function getUploadedFile($upload = null, $url = null, $save = "temp/") {
		if (($u = isset($_FILES[$upload]["tmp_name"])) || $_POST[$url]) {

			$u = (!$_FILES[$upload]["error"]);

			$f = saveFile (
				$u ? $_FILES[$upload]["tmp_name"] : $_POST[$url],
				$save,
				$u ? basename($_FILES[$upload]["name"]) : urldecode(basename($_POST[$url])),
				false,
				($u ? true : false)
			);

			return $f;
		} else {
			return false;
		}
	}

	// human readable byte size scale
	function humanFileSize($bytes, $round = 1) {
		$postfix = Array("bytes", "Kb", "Mb", "Gb", "Tb");

		$buff = $bytes;

		for ($i = 0; ($buff = round($buff/1024)) > 0; $i++);

		return round(($bytes/pow(1024, $i)), $round)." ".$postfix[$i];
	}



	/**
	* remoteFileSize(URL)
	* @returns: file size for the specified URL
	*/
	function remoteFileSize($file) {
		if (file_exists($file)) {
			return filesize($file);
		} else {
			$uri = parseURI($file);
			switch ($uri["proto"]) {
				case "http":
					$conn = new httpSession($uri["host"], $uri["port"] ? $uri["port"] : 80);
    				$conn->conn->write("GET {$uri["file"]} HTTP/1.0\r\n");
					$conn->sendCommonHeaders();
					$conn->conn->write("\r\n");
					$headers = $conn->getHeaders();
					$conn->close();
					preg_match("/content-length:\s*(\d*)/si", $headers, $size);
					return isset($size[1]) ? $size[1] : 0;
				break;
			}
		}
		return 0;
	}

	/**
	* removeDirectory(directory name);
	* Deletes directory contents (recursive mode)
	*/
	function removeDirectory($dirname) {
		$remove = listDirectory($dirname, true);
		foreach ($remove as $path) {
			$path = $dirname."/".$path;
			if (is_dir($path)) {
				rmdir($path);
			} else {
				unlink($path);
			}
		}
		return true;
	}
?>
