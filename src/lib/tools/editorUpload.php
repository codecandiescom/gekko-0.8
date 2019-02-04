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

	require_once GEKKO_SOURCE_DIR."lib/auth.inc.php";

	varImport("editor_id", "auth", "longest_side",
	"createlink", "position", "photo_style", "link_to_selected", "keep_size");

	if (isset($auth)) {

		if (appAuthorize("admin") && checkAuthHash($auth)) {

			appLoadLibrary ("file.lib.php", "image.lib.php");

			$dest = "data/files/uploaded";

			createWorkDirectory(GEKKO_USER_DIR.$dest);

			$file = getUploadedFile("local_file", "remote_file", $dest);

			if ($file) {

				$url = _L("C_SITE.URL").$file["rel_loc"];

				$type = guessFileType($file["rel_loc"]);

				if ($link_to_selected) {

					$script = "window.opener.gekkoEditor.exec('{$editor_id}', 'createlink', unescape('".rawurlencode($url)."'))";

				} else {

					$style = "";
					$style .= "margin:3px;";
					$style .= $position;

					switch ($type) {
						case "image":

							// creating thumbnail
							if (!$keep_size) {
								if ($longest_side > 0) {
									appLoadLibrary ("image.lib.php");

									$thumb = createThumbnail($file["abs_loc"], $longest_side, false, false, "data/thumbs");

									$url = _L("C_SITE.URL").$thumb["url"];
								}
							}

							if ($createlink) {

								// linking thumbnail to original image
								$script = "window.opener.gekkoEditor.exec('{$editor_id}', 'inserthtml', '".sprintf('<a href="%s"><img '.($photo_style ? 'class="photo" ' : '').'src="%s" style="%s" /></a>', _L("C_SITE.URL").addslashes($file["rel_loc"]), addslashes($url), $style)."');";

							} else {

								// inserting thumbnail and deleting base image
								$script = "window.opener.gekkoEditor.exec('{$editor_id}', 'inserthtml', '".sprintf('<img '.($photo_style ? 'class="photo" ' : '').'src="%s" style="%s" />', addslashes($url), $style)."');";

								if (isset($thumb))
									@unlink($file["abs_loc"]);

							}
						break;
						case "flash":
							$size = getimagesize($file["abs_loc"]);

							list($width, $height) = $size;

							$html = createFlashObject($url, $width, $height);

							$script = "window.opener.gekkoEditor.exec('{$editor_id}', 'insertflash', unescape('".rawurlencode($html)."'));";
						break;
						default:
							if (GEKKO_DEMO_MODE) {

								appAbort(_L("E_DEMO_MODE"));

							} else {

								// another kind of file

								$html = createIcon("files/$type.png", 48);

								$html = '<div '.($photo_style ? 'class="photo "' : '').'style="display:block;text-align: center;width:70px;'.$style.'"><a href="'.$url.'">'.$html.'</a><br />'.prettyFileName($url).'</div>';

								$script = "window.opener.gekkoEditor.exec('{$editor_id}', 'inserthtml', unescape('".rawurlencode($html)."'))";

							}
						break;
					}
				}

				// saving preferences
				conf::setKey("misc", "thumb.longest_side", $longest_side);

				echo "
				<script type=\"text/javascript\">
					$script
					window.close();
				</script>
				";
			} else {
				appAbort(uploadError());
			}
		} else {
			appAbort(accessDenied(true));
		}
		die;
	} else {

		$tpl = new blockWidget("_layout/editorUpload.tpl");

		$tpl->set("EDITOR_ID", $editor_id);

		echo $tpl->make();
	}
?>