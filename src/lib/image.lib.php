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

	function imageSize($file) {
		$size = @getimageSize($file);
		if (isset($size[0]) && isset($size[1])) {
			return "{$size[0]}x{$size[1]}";
		}
		return false;
	}
	
	function thumbName($file) {
		if (file_exists($file))
			return md5_file($file).'-'.strlen(basename($file)).'.'.getFileExtension($file);
		return false;
	}

	function mkGenericThumbnail($file, $size = 48, $dest = "data/thumbs") {
		$type = guessFileType($file);

		if (!file_exists($src = GEKKO_SOURCE_DIR."media/icons/default/48/files/$type.png"))
			$src = GEKKO_SOURCE_DIR."media/icons/default/48/files/default.png";

		if (file_exists($src))
			$thumb = createThumbnail($src, $size, false, false, $dest);
		else
			return false;

		return $thumb;
	}

	Class Image {
		var $buff;
		var $size = Array("w" => 0, "h" => 0);
		var $info = Array();
		var $type;
		var $status;
		function Image($path) {
			if (file_exists($path)) {

				switch (getFileExtension($path)) {
					case "jpg": case "jpeg":
						$this->buff = imagecreatefromjpeg($path);
						$this->type = "jpg";
					break;
					case "png":
						$this->buff = imagecreatefrompng($path);
						$this->type = "png";
					break;
					case "bmp":
						$this->buff = imagecreatefromwbmp($path);
						$this->type = "wbmp";
					break;
					case "gif":
						$this->buff = imagecreatefromgif($path);
						$this->type = "gif";
					break;
					default:
						$this->status = 0;
						return;
						//trigger_error("Unsupported/unknown image format! (".basename($path).")", E_USER_ERROR);
					break;
				}
				if ($this->buff) {

					$this->status = 1;

					$tmp = getimageSize($path);

					list($this->size["w"], $this->size["h"]) = $tmp;

					$this->info["mime"] = $tmp["mime"];

					$this->path = $path;

				} else {
					$this->status = 0;
				}

			} else {
				trigger_error("Couldn't find image path!", E_USER_ERROR);
			}
		}
		function dimensions() {
			return $this->size;
		}
		function mimeType() {
			if (isset($this->info["mime"])) {
				return $this->info["mime"];
			} else {
				switch ($this->type) {
					case "jpg": return "image/jpeg"; break;
					case "png": return "image/png"; break;
					case "bmp": return "image/bmp"; break;
					case "gif": return "image/gif"; break;
				}
			}
		}
		function resize($nw, $nh = null) {

			if (!$nh) {
				// calculating relative width and height, larger side
				if ($this->size["w"] > $nw || $this->size["h"] > $nw) {
					$scale = $nw/(($this->size["w"] > $this->size["h"]) ? $this->size["w"] : $this->size["h"]);
					$nw = round($this->size["w"]*$scale);
					$nh = round($this->size["h"]*$scale);
				} else {
					$nh = $nw;
				}
			}

			// checking for the better available function
			$gd_resize = (function_exists("imagecopyresampled") ? "imagecopyresampled" : "imagecopyresized");
			$gd_create = (function_exists("imagecreatetruecolor") ? "imagecreatetruecolor" : "imagecreate");

			// creating destination image buffer
			$nbuff = $gd_create($nw, $nh);

			if ($this->type == 'png') {
				if (function_exists('imageantialias') && function_exists('imagealphablending') && function_exists('imagesavealpha')) {
					imageantialias($nbuff, true);
					imagealphablending($nbuff, false);
					imagesavealpha($nbuff, true);
					$transparent = imagecolorallocatealpha($nbuff, 255, 255, 255, 0);
					for ($x = 0; $x < $this->size["w"]; $x++)
						for($y = 0; $y < $this->size["h"]; $y++)
							imagesetpixel($nbuff, $x, $y, $transparent);
				}
			}

			// copying a resized image
			$gd_resize($nbuff, $this->buff, 0, 0, 0, 0, $nw, $nh, $this->size["w"], $this->size["h"]);

			// updating info
			$this->size["w"] = $nw;
			$this->size["h"] = $nh;

			// using the new buffer
			$this->buff = $nbuff;
			/*
			header("content-type: image/png");
			imagepng($nbuff);
			die;
			*/

		}
		function flush($destroy = true) {
			switch ($this->type) {
				case "jpg": imagejpeg($this->buff); break;
				case "png": imagepng($this->buff); break;
				case "wbmp": imagewbmp($this->buff); break;
				case "gif": imagegif($this->buff); break;
			}
			if ($destroy) imagedestroy($this->buff);
		}
		function save($file, $destroy = true) {
			switch ($this->type) {
				case "jpg": imagejpeg($this->buff, $file); break;
				case "png": imagepng($this->buff, $file); break;
				case "wbmp": imagewbmp($this->buff, $file); break;
				case "gif": imagegif($this->buff, $file); break;
			}
			if ($destroy) imagedestroy($this->buff);
		}
	}

	/*
		$thumb = createThumbnail("/tmp/image.png", 150);
	*/
	function createThumbnail($file, $size, $samefile = false, $unlink = true, $dest_dir = "data/thumbs/", $abs_des = false) {

		if (!$abs_des) {
			if (substr($dest_dir, -1) != "/")
				$dest_dir .= "/";
			$save_to = $samefile ? $dest_dir.basename($file) : $dest_dir.allocateBasename(GEKKO_USER_DIR.$dest_dir, "_".basename($file));
		} else {
			$save_to = $dest_dir;
			$dest_dir = dirname($dest_dir);
		}

		@mkdir(GEKKO_USER_DIR.$dest_dir, 0777);
		@chmod(GEKKO_USER_DIR.$dest_dir, 0777);

		$abs_des = GEKKO_USER_DIR.$save_to;

		$img = new Image($file);

		// fixing size
		$img_size = $img->dimensions();

		if ($img_size['w'] > $size || $img_size['h'] > $size) {
			$size = explode("x", $size);

			if (isset($size[1])) {
				$img->resize($size[0], $size[1]);
			} else {
				$img->resize($size[0]);
			}
		}

		$img->save($abs_des);

		@chmod($abs_des, 0777);
		@chmod($file, 0777);

		if ($unlink)
			@unlink($file);

		$thumb = Array(
			"width" => $img->size["w"],
			"height" => $img->size["h"],
			"url" => $save_to,
			"path" => $abs_des,
			"origin" => $file
		);

		return $thumb;

	}
?>