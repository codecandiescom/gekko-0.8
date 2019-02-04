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

	define ("IN-GEKKO", true);

	require_once "../../conf.php";

	appLoadLibrary (
		"core.lib.php", "file.lib.php", "compression.lib.php", "packages"
	);

	dbInit();

	require_once GEKKO_SOURCE_DIR."lib/auth.inc.php";

	varImport("action", "id", "return");

	define("GEKKO_PKG_DIR", "temp/packages/");

	// setting writable packages directory
	$pkgdir = GEKKO_PKG_DIR;
	
	createWorkDirectory(GEKKO_USER_DIR.GEKKO_PKG_DIR);

	function package_cleanup() {
		removeDirectory($GLOBALS["abs_workdir"]);
		rmdir($GLOBALS["abs_workdir"]);
		@unlink($GLOBALS["file"]["abs_loc"]);
	}

	// administration tasks
	if (isAdmin() && GEKKO_SUBDOMAIN_MODE == false) {
		switch ($action) {
			case "ftp_test":
				// will attempt to login and check if everything is going to be ok when
				// using ftp

				varImport("ftp_host", "ftp_port", "ftp_path", "ftp_user", "ftp_pass");

				varRequire("ftp_host", "ftp_port", "ftp_user");

				// creating ftp session
				$ftp = new ftpSession($ftp_host, $ftp_port, $ftp_user, $ftp_pass);

				if ($ftp->connect()) {
					if ($ftp->login()) {
						if ($ftp->cd($ftp_path) && $ftp->mtime("conf.php")) {
							// conf.php exists in this path
							$_SESSION["success"] = _L("L_FTP_TEST_PASSED");
							// saving ftp configuration
							conf::setKey("core", "ftp.host", $ftp_host);
							conf::setKey("core", "ftp.user", $ftp_user);
							conf::setKey("core", "ftp.path", $ftp_path);
							conf::setKey("core", "ftp.port", $ftp_port);

							// saving ftp password (this is not done by default)
							if (conf::getKey("core", "ftp.save_pass")) {
								conf::setKey("core", "ftp.pass", $ftp_pass);
							}

							// this test is *required*
							conf::setKey("packages", "ftp_test_passed", true, "b");
						} else {
							// guessing correct path
							$pwd = $ftp->pwd();

							// expected string: "/foo/bar" is current directory.
							preg_match("/\"(.*?)\"/", $pwd, $mwd);

							if (isset($mwd[1])) {
								$pwd = $mwd[1];
								if (substr(GEKKO_SOURCE_DIR, 0, strlen($pwd)) == $pwd)
									$prob_path = substr(GEKKO_SOURCE_DIR, strlen($pwd));
							}

							if (isset($prob_path))
								formInputError(sprintf(_L("L_PROBABLE_FTP_PATH"), $prob_path), "focus: ftp_path");
							else
								formInputError("{L_WRONG_FTP_PATH}", "focus: ftp_path");
						}
						$ftp->bye();
					} else {
						formInputError("{L_LOGIN_FAILED}", "focus: ftp_user");
					}
				} else {
					formInputError("{E_COULDNT_CONNECT}", "focus: ftp_server");
				}

			break;
			case "install":

				varImport("ftp_host", "ftp_port", "ftp_path", "ftp_user", "ftp_pass");

				varRequire("ftp_host", "ftp_port", "ftp_user");

				// get by package name
				if ($_POST["remote_file"] && !preg_match("/([a-z]*):\/\//", $_POST["remote_file"])) {

					$pkgname = $_POST["remote_file"];

					// checking if this package exists
					$pkgurl = "http://prdownloads.sourceforge.net/gekkoware/$pkgname?download";
					$htdoc = @saveFile($pkgurl, "temp/cache/", $pkgname);

					if (isset($htdoc["abs_loc"])) {

						// fetching mirrors
						preg_match_all("/use_mirror=([a-z]*)/m", loadFile($htdoc["abs_loc"]), $mirrors);
						$mirrors = $mirrors[1];

						// using random mirror
						$mirror = $mirrors[rand(0, count($mirrors[1])-1)];

						// creating url where to download this package
						$_POST["remote_file"] = "http://$mirror.dl.sourceforge.net/sourceforge/gekkoware/{$pkgname}";

					} else {

						$_SESSION["error"] = _L("L_PACKAGE_NOT_FOUND");
						break;

					}
				}

				// getting specified file
				if ($_POST["remote_file"] || $_FILES["local_file"]["size"]) {

					$file = getUploadedFile("local_file", "remote_file", GEKKO_PKG_DIR);

					// work folder and relative path
					$rel_workdir = GEKKO_PKG_DIR."work/".$file["basename"];
					$abs_workdir = GEKKO_USER_DIR.$rel_workdir;

					createWorkDirectory($abs_workdir);
					if (file_exists($abs_workdir)) {
						// cleaning workdir
						removeDirectory($abs_workdir);
					} else {
						// creating wordir
						trigger_error("Coudn't create work directory!", E_USER_ERROR);
					}

					/*
						** Package Type **
						Package format: pkgname-version.pkgtype.tgz
						example:
							foo-1.2.module.tgz
							13375p33k-0.5.lang.tgz
					*/
					if (preg_match("/(.*?)-([\d*.]*)\.([^.]*?)\.(gpk|tgz)/", $file["original"], $match)) {
						// normal package
						if (isset($match[4]))
							list( , $pkgname, $pkgversion, $pkgtype, $pkgext) = $match;
					} elseif (preg_match("/gekko-(.*?)\.tar\.(gz|bz2)/", $file["original"])) {
						// gekko core
						$pkgtype = "core";
					} else {
						// old package name format (deprecated)
						$pkgtype = substr($file["basename"], 0, strpos($file["original"], "_"));
						switch ($pkgtype) {
							case "mod": $pkgtype = "module"; break;
							case "css": $pkgtype = "style"; break;
							case "sml": $pkgtype = "smileys"; break;
							case "ico": $pkgtype = "iconset"; break;
						}
					}

					if (!isset($pkgtype)) {
						$_SESSION["error"] = _L("L_PACKAGE_FORMAT_ERROR");
						package_cleanup();
						break;
					}

					// extracting file to working directory
					appPackageExtract($file["abs_loc"], $abs_workdir);

					// scanning package contents
					$pkgcontents = listDirectory($abs_workdir, true);

					// setting null flags
					$flag = Array();

					// test phase
					switch ($pkgtype) {
						case "core":
							$realworkdir = $abs_workdir;

							$release = substr($pkgcontents[0], 0, strpos($pkgcontents[0], "/"));

							// moving files
							foreach ($pkgcontents as $file) {
								if (is_file("$abs_workdir/$file")) {
									switch (basename($file)) {
										case "dbconf.php":
											// should not be overwritten
										break;
										default:
											// relative name
											$relname = preg_replace("/.*\/src\//", "", $file);

											// directory structure
											createWorkDirectory($abs_workdir."/".dirname($relname));

											// moving file
											rename("$abs_workdir/$file", "$abs_workdir/$relname");
										break;
									}
								}
							}

							// removing directory structure
							removeDirectory("$abs_workdir/$release", true);
							rmdir("$abs_workdir/$release");

							// updating pkgcontents
							$pkgcontents = listDirectory($abs_workdir, true);

						break;
						case "module":
							$pm = false;

							foreach ($pkgcontents as $f) {
								if (!preg_match("/^(modules|media).*?/", $f)) {
									package_cleanup();
									trigger_error("This seems not to be a package module.", E_USER_ERROR);
								}
							}

							// loading and parsing xml
							$xml_file = $abs_workdir."/modules/$pkgname/package.xml";

							$xml = "";
							Packages::parseXML($xml_file, $xml);

							if (($inform = Packages::checkInstall($xml)) == false) {

								// checking installating type
								$db->query("SELECT module
								FROM ".dbTable("module")." WHERE module = '$pkgname'",
								__FILE__, __LINE__);

								// setting flag for post-install procedure
								$flag["is_installed"] = ($db->numRows());

								// checking if cgdb.php needs to be regenerated
								if (!isset($cgdb[$xml["module"]["name"]])) {
									// adding to $cgdb, will regenerate ./cgdb.php
									$cgdb[$xml["module"]["name"]] = $xml["module"]["group"];
									$flag["cgdbregen"] = true;
								}

							} else {
								$_SESSION["error"] = $inform;
								package_cleanup();
								appAbort();
							}
						break;
						case "style":
							// css style
							foreach ($pkgcontents as $f) {
								if (preg_match("/^templates.*?/", $f)) {
									$path = "$abs_workdir/$f";
									if (is_file($path)) {
										switch($ext = getFileExtension($path)) {
											case "css": case "jpg": case "gif": case "png": case "ttf": case "txt": case "readme": case "license":
											case "copyright": case "authors":
											break;
											default:
												trigger_error("Style Packages must not contain '$ext' files. ", E_USER_ERROR);
											break;
										}
									}
								} else {
									trigger_error("This package seems not to be a style for Gekko.", E_USER_ERROR);
									break;
								}
							}
						break;
						case "smileys": case "smileyset":
							foreach ($pkgcontents as $f) {
								if (preg_match("/media(.*?)/", $f)) {
									$path = "$abs_workdir/$f";
									if (is_file($path)) {
										switch($ext = getFileExtension($path)) {
											case "jpg": case "gif": case "png": case "xml": case "txt": case "readme": case "license":
											case "copyright": case "authors":
											break;
											default:
												trigger_error("Smiley Packages must not contain '$ext' files. ", E_USER_ERROR);
											break;
										}
									}
								} else {
									trigger_error("This package seems not to contain smileys for Gekko.", E_USER_ERROR);
									break;
								}
							}
						break;
						case "template":
							foreach ($pkgcontents as $f) {
								if (preg_match("/templates(.*?)/", $f)) {
									$path = "$abs_workdir/$f";
									if (is_file($path)) {
										switch($ext = getFileExtension($path)) {
											case "jpg": case "gif": case "png": case "txt": case "readme": case "license": case "css": case "xcf": case "tpl": case "block": case "ttf": case "copyright": case "authors":
											break;
											default:
												trigger_error("Template packages must not contain '$ext' files. ", E_USER_ERROR);
											break;
										}
									}
								} else {
									trigger_error("This package seems not to be a Gekko template.", E_USER_ERROR);
									break;
								}
							}
						break;
						case "iconset":
							foreach ($pkgcontents as $f) {
								if (preg_match("/media(.*?)/", $f)) {
									$path = "$abs_workdir/$f";
									if (is_file($path)) {
										switch($ext = getFileExtension($path)) {
											case "jpg": case "gif": case "png": case "txt": case "readme": case "license": case "copyright": case "authors":
											break;
											default:
												trigger_error("Icon packages must not contain '$ext' files. ", E_USER_ERROR);
											break;
										}
									}
								} else {
									trigger_error("This package seems not to contain icons for Gekko.", E_USER_ERROR);
									break;
								}
							}
						break;
						case "lang": case "language":

						break;
						default:
							trigger_error("Unknown package type", E_USER_ERROR);
						break;
					}
					// test phase concluded

					// reversing package contents for listing folders before folder content
					$pkgcontents = array_reverse($pkgcontents);

					// ready to install?!
					$ftp = new ftpSession($ftp_host, $ftp_port, $ftp_user, $ftp_pass);

					$ftp->connect();

					if ($ftp->login()) {

						if ($ftp->cd($ftp_path) && $ftp->mtime("conf.php")) {

							// binary connection
							$ftp->type("I");

							// installation
							foreach ($pkgcontents as $rel_dest) {

								// renaming from temp_dir/* to ./*
								$abs_dest = GEKKO_SOURCE_DIR.$rel_dest;

								$rel_src = "$rel_workdir/$rel_dest"; // relative source
								$abs_src = "$abs_workdir/$rel_dest"; // absolute source

								if (is_dir($abs_src)) {
									if (!file_exists($abs_dest))
										$ftp->mkdir($rel_dest, "0755");
								} else {
									$ftp->put($abs_src, $rel_dest);
									if (md5_file($abs_src) != md5_file($abs_dest)) {
										trigger_error("Error moving file '{$abs_dest}'.", E_USER_ERROR);
									}
								}
								$ftp->chmod($rel_dest, "0755");
							}

							// saving actual cgdb if neccesary
							if (isset($flag["cgdbregen"])) {
								$ftp->chmod("cgdb.php", "0777");
								writeFile(GEKKO_USER_DIR."cgdb.php", Packages::cgdbRegen());
								$ftp->chmod("cgdb.php", "0775");
							}

							// post-installation
							switch ($pkgtype) {
								case "module":
									gekkoModule::install($xml["module"]["name"], $flag["is_installed"] ? "P" : "I");
									appWriteLog("packages: ".($flag["is_installed"] ? "upgraded" : "installed")." module '{$xml["module"]["name"]}'", "actions", 1);
									$return = "index.php/module=admin/base={$xml["module"]["name"]}";
								break;
								// CSS Styles, Smileys
								case "style":
									$return = "index.php/module=admin/base=conf/action=looknfeel";
								break;
								case "smileys": case "smileyset": case "iconset": case "lang": case "template":
									$return = "index.php/module=admin/base=conf";
								break;
								case "core":
									$return = "install.php?mode=upgrade";
								break;
							}

							$_SESSION["success"] = _L("L_PACKAGE_INSTALL_SUCCESS");

						} else {
							$_SESSION["error"] = _L("L_WRONG_FTP_PATH");
						}

						package_cleanup();

						$ftp->bye();

					} else {
						package_cleanup();
						formInputError("{L_LOGIN_FAILED}", "focus: ftp_user");
					}

				} else {
					formInputError("{E_COULDNT_DOWNLOAD_PACKAGE}", "focus: remote_file");
				}
			break;
			default:
				appAbort(_L("L_UNDEFINED_ACTION"));
			break;
		}
	}

	appDisableSSL($return);

	appRedirect($return);

	dbExit();
?>
