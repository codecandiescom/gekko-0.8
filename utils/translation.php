<?php

	/*
		This script is VERY INSECURE IF RUNNING IN A REMOTE SERVER. Those issues will
		not be solved for me because this script intends to be executed ONLY from
		the translator's machine and to the translator's machine.
		Please don't try to run this script in a remote server unless you know
		what are you doing!
	*/

	if ($_SERVER["REMOTE_ADDR"] != "127.0.0.1")
		trigger_error("You can execute this script ONLY in local mode (from 127.0.0.1).
		If you don't want this setting to be applied just open this file and edit or remove the first
		condition.", E_USER_ERROR);

	define("IN-GEKKO", true);

	define("GEKKO_BASE_LANGUAGE", isset($_GET["base_lang"]) ? $_GET["base_lang"] : (isset($_POST["lang"]) ? $_POST["lang"][0] : "es"));

	define("GEKKO_SOURCE_DIR", "../src/");
	define("GEKKO_LANG_DIR", GEKKO_SOURCE_DIR."lang/");

	define("LIST_ALL", 0);
	define("LIST_DIRECTORIES", 1);
	define("LIST_FILES", 2);

	Class Lang {
		var $default;
		var $loaded;
		var $capture;
		var $lang;
		var $missing;
		var $test;
		function Lang($default) {
			$this->test = false;
			$this->default_lang = $default;
		}
		function startCapture($lang, $file) {
			$this->file = $file;
			$this->capture = $lang;
		}
		function Set($code, $meaning) {
			$self =& $GLOBALS["scan"];

			if ($self->test) {
				return true;
			} else {
				$self->lang[$self->capture][$code] = $meaning;
				if (!isset($self->lang[GEKKO_BASE_LANGUAGE][$code])) {
					$self->missing[$self->capture][$code];
					trigger_error("'$code' language code is defined on '".$self->file."' but undefined in the default one.", E_USER_WARNING);
				}
			}
		}
		function editFile($lang, $file, $trans = false) {
			echo '<div class="column">';
			echo "<h1>Language: '$lang'</h1>";
			$this->mapFile($lang, $file, $trans);
			echo '</div>';
		}
		function loadFile($lang, $file) {
			ob_start();
			$file = str_replace("LANG", $lang, $file);
			if (file_exists($file)) {
				$this->startCapture($lang, $file);
				require $file;
			}
			$f = ob_get_contents();
			ob_end_clean();
			return $f;
		}
		function mapFile($lang, $file, $trans = false) {

			if ($lang != GEKKO_BASE_LANGUAGE)
				$letter = $this->loadFile(GEKKO_BASE_LANGUAGE, $file);

			if ($trans && is_array($trans) && (count($trans) > 0)) {
				$this->lang[$lang] = $trans;
			} else {
				$letter = $this->loadFile($lang, $file);
			}

			if (is_array($this->lang[GEKKO_BASE_LANGUAGE])) {

				reset($this->lang[GEKKO_BASE_LANGUAGE]);

				while (list($code) = each($this->lang[GEKKO_BASE_LANGUAGE])) {

					$class = null;

					if ($lang != GEKKO_BASE_LANGUAGE) {
						if (!isset($this->lang[$lang][$code]) || !$this->lang[$lang][$code]) {
							$class = "missing";
							$this->lang[$lang][$code] = '';
						} else if ($this->lang[$lang][$code] == $this->lang[GEKKO_BASE_LANGUAGE][$code]) {
							$class = "duplicated";
						}
					}

					$string = trim(preg_replace("/[\t\n]/", " ", $this->lang[$lang][$code]));
					$string = preg_replace("/\s+/", " ", $string);

					echo "<label".($class ? " class=\"$class\"" : "").">";
					echo "{$code}";
					if (strlen($this->lang[GEKKO_BASE_LANGUAGE][$code]) > 60)
						echo '<textarea name="'.$lang."[".$code.']" cols="70" rows="5">'.htmlspecialchars($string).'</textarea>';
					else
						echo '<input name="'.$lang."[".$code.']" size="70" value="'.htmlspecialchars($string).'" />';
					echo "</label>\n";
				}
			} else {
				echo "<label>Letter<textarea name=\"{$lang}[LETTER]\" rows=\"15\">".$letter."</textarea></label>";
			}
		}
		function cleanCapture() {
			$this->lang = array();
		}
	}

	function listDirectory($path, $mode = LIST_ALL) {
		$files = array();
		$dir = opendir(GEKKO_SOURCE_DIR."{$path}");
		while (($file = readdir($dir)) !== false) {
			if ($file[0] != '.') {
				if ($mode == LIST_ALL || ($mode == LIST_DIRECTORIES && is_dir(GEKKO_SOURCE_DIR."{$path}/{$file}")) || ($mode == LIST_FILES && is_file(GEKKO_SOURCE_DIR."{$path}/{$file}")))
					$files[] = $file;
			}
		}
		return $files;
	}
	
	function addLangFile($last) {
		$last = preg_replace("/\b".GEKKO_BASE_LANGUAGE."\b/", "LANG", $last);
		return substr($last, strlen(GEKKO_SOURCE_DIR));
	}

	function createLanguageFile($file, $lang) {
		$buff = null;

		$fp = fopen($file, "r");
		$buff = fread($fp, filesize($file));
		fclose($fp);

		foreach ($lang as $code => $value) {
			$value = str_replace("\"", "\\\"", stripslashes($value));
			$buff = preg_replace("/lang::set\(\"$code\",\s*\".*?\"\);/si", $value ? "Lang::Set(\"$code\", \"$value\");" : "", $buff);
		}

		return preg_replace("/(\t\n)+/", "\n", $buff);
	}

	require GEKKO_SOURCE_DIR."lang/codes.php";

	unset($buff);
	$trans = false;
	if (isset($_POST) && $_POST) {
		$file = $_POST["file"];
		switch ($_POST["action"]) {
			case "translate":
				if (isset($_POST["auto_translate"])) {

					include GEKKO_SOURCE_DIR."lib/network.lib.php";

					$buff = Array();
					foreach ($_POST[$lang[0]] as $key => $value) {
						$buff[] = "{".$key."} $value";
					}
					$buff = implode("\n\n\n\n", $buff);

					$http = new httpSession("babelfish.altavista.com", "80");
					$vars = Array (
						"lp" => "{$lang[0]}_{$lang[1]}",
						"trtext" => $buff,
						"intl" => 1,
						"tt" => "urltext",
						"doit" => "done"
					);
					$buff = $http->post("/tr", $vars);
					preg_match("/.*<td bgcolor=white class=s><div style=padding:10px;>(.*?)<\/div>.*/s", $buff, $match);

					$http->close();

					if (isset($match[1])) {
						$trans = Array();

						$buff = explode("\n\n\n\n", $match[1]);
						foreach ($buff as $string) {
							preg_match("/^{(.*?)}\s(.*)$/", $string, $match);
							if (isset($match[2]))
								$trans[$match[1]] = ucfirst($match[2]);

							$_GET["lang"] = $_POST["lang"][1];
							$_GET["file"] = substr($_POST["file"], strlen(GEKKO_SOURCE_DIR));

						}
						unset($buff);
					} else {
						die("Babelfish couldn't translate between those languages.");
					}

				} else {
					$dump = array();

					$base_file = str_replace("LANG", $_POST["lang"][0], $file);

					foreach ($_POST["lang"] as $lang) {
						$file = str_replace("LANG", $lang, $file);
						$file = dirname($_SERVER["SCRIPT_FILENAME"]).'/'.$file;
						$buff[$lang] = isset($_POST[$lang]["LETTER"]) ? $_POST[$lang]["LETTER"] : createLanguageFile($base_file, $_POST[$lang]);
					}
				}
			break;
			case "download":
				$scan = new Lang(GEKKO_BASE_LANGUAGE);

				$lang = $_POST["lang"];
				$buff = stripslashes($_POST["buff"]);

				$tmp = tempnam("/tmp", "gekko_language_syntax_check_");

				$fp = fopen($tmp, "w");
				fwrite($fp, $buff);
				fclose($fp);

				ob_start();
				highlight_string($buff);
				include $tmp;
				ob_end_clean();

				unlink($tmp);

				header("Content-disposition: filename=".basename(str_replace("LANG", $lang, $file)));
				header("Content-type: application/octect-stream");

				header("Content-Length: ".strlen($buff)."");
				header("Pragma: no_cache");
				header("Expires: 0");

				die($buff);
			break;
		}
	}

	if (!isset($buff)) {

		$languages = Array();
		$languages["lang"] = listDirectory("lang", LIST_DIRECTORIES);
		$languages["install/lang"] = listDirectory("install/lang", LIST_DIRECTORIES);
		$modules = listDirectory("modules", LIST_DIRECTORIES);

		foreach ($modules as $module) {
			$languages["modules/$module/lang"] = listDirectory("modules/$module/lang", LIST_DIRECTORIES);
			if (file_exists(GEKKO_SOURCE_DIR."modules/$module/$module")) {
				$dirs = listDirectory("modules/$module/$module", LIST_DIRECTORIES);
				foreach ($dirs as $dir) {
					if (file_exists(GEKKO_SOURCE_DIR."modules/$module/$module/$dir/lang"))
						$languages["modules/$module/$module/$dir/lang"] = listDirectory("modules/$module/$module/$dir/lang", LIST_FILES);
				}
			}
		}

		$scan = new Lang(GEKKO_BASE_LANGUAGE);

		$select = array();
		foreach ($languages as $path => $dirs) {
			$path = GEKKO_SOURCE_DIR."$path/";

			$file = false;
			if (file_exists("$path".GEKKO_BASE_LANGUAGE)) {
				$file = "$path".GEKKO_BASE_LANGUAGE;
			} elseif (file_exists("$path".GEKKO_BASE_LANGUAGE.".php")) {
				$file = "$path".GEKKO_BASE_LANGUAGE.".php";
			} else {
				trigger_error("Default language file (".GEKKO_BASE_LANGUAGE.") is missing on '{$path}'.");
			}
			if ($file) {
				if (is_dir($file)) {
					$files = listDirectory($file, LIST_FILES);
					foreach ($files as $f)
						$select[] = addLangFile("$file/$f");
				} else {
					$select[] = addLangFile($file);
				}
			}
		}
	}
?>
<html>
<head>
	<title>Gekko translation tool</style>
	<style type="text/css">
	BODY {
		font-family: "lucida grande", "arial", "verdana";
	}
	LABEL {
		color: #888;
		display: block;
		margin-bottom: 20px;
		padding: 10px;
	}
	LABEL INPUT, LABEL TEXTAREA, LABEL SELECT {
		display: block;
		margin: 10px;
		margin-left: 30px;
		width: 90%;
		padding: 5px;
		border: 1px solid #eee;
	}
	LABEL INPUT:focus, LABEL TEXTAREA:focus {
		background: #F7FFB0;
		color: #000;
		border: 1px solid #bbb;
	}
	.missing {
		color: #f00;
	}
	.duplicated {
		color: #f0f;
	}
	LABEL:hover {
		background: #f6f6f6;
	}
	BUTTON {
		cursor: pointer;
		font-weight: bold;
		background: #eee;
		border: 1px solid #bbb;
		padding: 5px;
		margin: 3px;
	}
	BUTTON:hover {
		background: #fff;
		border: 1px solid #999;
	}
	SELECT {
		font-size: medium;
		margin: 5px;
		padding: 5px;
	}
	.column {
		float: left;
		width: 40%;
		margin-left: auto;
		margin-right: auto;
	}
	.instructions {
		padding: 30px;
		margin: 10px;
		background: #fafafa;
		border: 1px solid #eee;
	}
	</style>
</head>
<body>
<h1>Gekko translation tool</h1>
<?php if (isset($buff)) { ?>
	<div class="instructions">
	Here are the modified files, please be sure to browse the correct path and drop
	the generated file. A PHP parsing test will be performed before giving
	you the language file, an error means that you have to go back to this screen
	and correct the file.
	</div>
	<?php
		foreach ($buff as $lang => $content) {
			echo '
			<form action="translation.php" method="post">
			<input type="hidden" name="action" value="download" />
			<input type="hidden" name="lang" value="'.$lang.'" />
			<input type="hidden" name="file" value="'.$_POST["file"].'" />
			';

			echo "<label><b>$lang</b> : ".str_replace("LANG", $lang, $_POST["file"])."<textarea name=\"buff\" rows=\"10\" name=\"$lang\">".htmlspecialchars($content)."</textarea></label>\n";

			echo '<button>Download</button>
			</form>';
		}
	?>
<?php } else { ?>
	<div class="instructions">
	Choose your base language (it will appear at the left side), then choose
	the language you want to write (it will appear at the right side) and the file you
	want to load for being modified. The unknown or missing phrases will appear highlighted
	<span class="missing">this way</span> while the phrases that are probably wrong will appear
	<span class="duplicated">like this</span>.
	</div>
	<form id="file" action="translation.php" method="get">
	<label>
	File:
	<select name="file" onchange="document.getElementById('file').submit()">
	<?php

		$file = isset($_GET["file"]) ? $_GET['file'] : null;
		$lang = isset($_GET['lang']) ? $_GET['lang'] : null;

		if (!$lang)
			$lang = GEKKO_BASE_LANGUAGE;
		if (!$file)
			$file = "lang/LANG/main.php";

		foreach ($select as $i => $opt) {
			if (isset($select[$i+1]) && ($select[$i+1] == $file)) {
				$prev_file = $opt;
			} else if (isset($select[$i-1]) && ($select[$i-1] == $file)) {
				$next_file = $opt;
			}
			echo "<option value=\"$opt\"".(($file == $opt) ? " selected=\"selected\"" : "").">".$opt."</option>\n";
		}

		if (!isset($prev_file) || !$prev_file)
			$prev_file = $select[count($select)-1];

		if (!isset($next_file) || !$next_file)
			$next_file = $select[0];

		$file = GEKKO_SOURCE_DIR.$file;
	?>
	</select>
	</label>
	<input type="hidden" name="next_file" value="<?=$next_file?>" />
	<input type="hidden" name="prev_file" value="<?=$prev_file?>" />
	<table style="width: 100%"><tr><td>
	<div style="height: 100px; display: block; width: 95%">
		<label class="column">
			Base language:
			<select name="base_lang">
			<?php
			foreach ($languages["lang"] as $code)
				echo "<option value=\"$code\"".((GEKKO_BASE_LANGUAGE == $code) ? " selected=\"selected\"": "").">".$ISO_639[$code]."</option>";
			?>
			</select>
		</label>
		<label class="column">
			Translation language:
			<select name="lang">
			<?php
			foreach ($ISO_639 as $code => $name)
				echo "<option value=\"$code\"".(($lang == $code) ? " selected=\"selected\"": "").">".$name."</option>";
			?>
			</select>
		</label>
	</div>
	</td></tr></table>
	<button type="button" onclick="document.getElementById('file').file.value = document.getElementById('file').prev_file.value; document.getElementById('file').submit()">Prev File</button>
	<button type="button" onclick="document.getElementById('file').file.value = document.getElementById('file').next_file.value; document.getElementById('file').submit()">Next File</button>
	<button type="submit">Update</button>
	</form>
	<form action="translation.php" method="post">
	<table style="width: 100%"><tr><td>
	<input type="hidden" name="action" value="translate" />
	<input type="hidden" name="lang[]" value="<?= GEKKO_BASE_LANGUAGE ?>" />
	<input type="hidden" name="lang[]" value="<?=$lang?>" />
	<input type="hidden" name="file" value="<?=$file?>" />

	<?php
		if ($lang != GEKKO_BASE_LANGUAGE)
			$scan->editFile(GEKKO_BASE_LANGUAGE, $file);

		$scan->editFile($lang, $file, $trans);
	?>
	</td></tr></table>
	<div>
		<button type="submit">Create language file</button>
		<button type="submit" name="auto_translate">Autotranslate using Babelfish</button>
	</div>
	</form>
<?php } ?>
</body>
</html>
