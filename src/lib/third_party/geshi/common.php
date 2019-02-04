<?php
	//anti hack code
	if (!defined("IN-GEKKO")) die("Get a Life!");

	function geshi_language($extension) {
		switch ($extension) {
			// transparent extensions
			case "c": case "asm": case "asp": case "cpp": case "css":
			case "d": case "diff": case "gml": case "ini": case "java":
			case "php": case "xml": case "sql": case "mysql":
				$filetype = $extension;
			break;
			case "pl": $filetype = "perl"; break;
			default: return false; break;
		}
		return (file_exists(GEKKO_SOURCE_DIR."lib/third_party/geshi/lang/$filetype.php")) ? $filetype : false;
	}
?>
