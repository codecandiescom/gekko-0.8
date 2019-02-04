<?php


	if (!defined("IN-GEKKO")) die("Get a life!");

	Lang::Set("E_FTP_TEST_FAILED", "There was an error while connecting to
	the FTP service. The service may be down. Please check configuration settings 
	like  port settings.");
	
	Lang::Set("E_COULDNT_CONNECT", "Couldn't connect to server.");

	Lang::Set("L_INSTALL_PACKAGE", "Install package");
	Lang::Set("L_PACKAGE", "Package");
	Lang::Set("L_FTP_CONFIGURATION", "FTP Configuration");
	Lang::Set("L_SERVER", "FTP Server");
	Lang::Set("L_FTP_PATH", "Relative path (Gekko's root)");
	Lang::Set("L_USER", "User");
	Lang::Set("L_PASS", "Password");
	Lang::Set("L_PACKAGE_SOURCE", "Package location");
	Lang::Set("L_INSTALL", "Install");
	Lang::Set("L_FTP_SERVER_NAME", "FTP Server: <b>%s</b>");
	Lang::Set("L_TEST_FTP", "FTP Test");
	
	Lang::Set("L_FTP_TEST_PASSED", "FTP configuration is correct.");
	Lang::Set("L_WRONG_FTP_PATH", "Incorrect path. Try to use the path where Gekko's \"conf.php\"
	file can be found.");
	Lang::Set("L_LOGIN_FAILED", "Login failed. Please check that you have typed your username and password 
	correctly.");
	
	Lang::Set("L_PACKAGE_FORMAT_ERROR", "Package format error.");
	Lang::Set("L_COULDNT_GET_PACKAGE", "The package could not be fetched.");
	Lang::Set("L_PACKAGE_INSTALL_SUCCESS", "The package was installed successfully.");
	Lang::Set("L_PROBABLE_FTP_PATH", "I'm just guessing, but it seems that \"%s\"
	is the correct path. Try to use it or append it to your current path.")
?>