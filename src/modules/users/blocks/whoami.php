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

	if (!defined("IN-BLOCK")) die();

	/*
	<%%>
	<?xml version="1.0" encoding="UTF-8"?>
	<blockscript>
		<info lang="en">
			<title>Who am I?</title>
			<description>Shows an identification if user is already logged in or a login form otherwise.</description>
			<author><![CDATA[xiam &lt;<a href="mailto:xiam@users.sourceforge.net">xiam@users.sourceforge.net</a>&gt;]]></author>
			<homepage><![CDATA[<a href="http://www.gekkoware.org">http://www.gekkoware.org</a>]]></homepage>
		</info>
	</blockscript>
	<%%>
	*/

	appLoadLibrary("users");

	if (isUser()) {

		$avatar = cacheFunc("users::createAvatar", $GLOBALS["USER"]["id"]);

		$return = "<div style=\"height: 80px;\"><div class=\"avatar\" style=\"float: left; margin: 4px;\">{$avatar}</div><b style=\"font-size: large;\">".cacheFunc("users::createUserLink", $GLOBALS["USER"]["id"])."</b><br /><small>{$GLOBALS["USER"]["realname"]}</small></div>";

	} else {

		$safe_mode = isset($_SERVER["HTTPS"]) ?  _L("L_SAFE") : '<a href="https://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"].'">'._L("L_SAFE").'</a>';
		$normal_mode = isset($_SERVER["HTTPS"]) ? '<a href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"].'">'._L("L_NORMAL").'</a>' : _L("L_NORMAL");

		$return = '
		<form action="'._L("C_SITE.REL_URL").'modules/users/actions.php" method="post">
		<input type="hidden" name="action" value="login" />
		<input type="hidden" name="block_flag" value="1" />
		<input type="hidden" name="return" value="" />
		'._L("L_USERNAME").':<br />
		<input class="text" type="text" name="username" value="" /><br />
		'._L("L_PASSWORD").':<br />
		<input class="text" type="password" name="password" /><br />
		<button type="submit">'.createIcon("login.png", 16).' '._L("L_LOGIN").'</button>
		<div class="buttons">
			'.createIcon("lock.png", 16).' [ '.$normal_mode.' | '.$safe_mode.' ]<br />
			'.(conf::getKey("users", "account.enable_self_registration") ? createLink('index.php/module=users/action=register', _L("L_REGISTER"), "users.png") : '').'
		</div>
		</form>';
	}
?>