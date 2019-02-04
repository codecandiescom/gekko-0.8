<!DOCTYPE html PUBLIC "-//W3C//DTD XH
TML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="Generator" content="Gekko - http://www.gekkoware.org" />
	<link rel="stylesheet" type="text/css" href="{C_SITE.REL_URL}templates/default/_layout/main.css" />
	<link rel="stylesheet" type="text/css" href="{C_SITE.REL_URL}templates/default/_themes/default/theme.css" />
	<title>Gekko {C_GEKKO_VERSION} - {L_INSTALLATION}</title>

	<script type="text/javascript" src="{C_SITE.URL}js/core.js"></script>
	<script type="text/javascript" src="{C_SITE.URL}js/xmlhttp.js"></script>

	<script type="text/javascript">
		function createMessageBox(target, messageType, messageText) {

			if (target.error_block)
				target.removeChild(target.firstChild);

			div = document.createElement('DIV');

			switch (messageType) {
				case 'error':
					template = '{=trim(createMessageBox("error", "%s"))=}';
				break;
			}

			div.innerHTML = template.replace('%s', messageText);

			target.insertBefore(div, target.firstChild);

			target.firstChild.focus();
			target.error_block = 1;
		}
	</script>
</head>
<body>
	<div id="container">
		<table id="framework">
		<tr>
			<td class="td_blockT" colspan="2">
			<div id="pageHeader">
				<h1>Gekko v{C_GEKKO_VERSION}</h1>
				<h2>Open Source Web Development Framework</h2>
			</div>
			</td>
		</tr>
		<tr>
			<td style="width: 250px; vertical-align: top;">
				<div class="info" style="width: 240px">
					<h1>Gekko {C_GEKKO_VERSION}</h1>
					<div style="text-align:center;margin: 10px;">
					<a href="" href="http://www.gekkoware.org">
						<img src="media/gekko-logo.png" width="126" height="126" alt="Gekkoware.org" />
					</a>
					</div>
					<b><a href="http://www.gekkoware.org">Gekko</a> is Copyright (C) 2004-2006 by Jos&eacute;
					Carlos Nieto Jarqu&iacute;n
					<a href="mailto:xiam@users.sourceforge.net">&lt;xiam@users.sourceforge.net&gt;</a>
					</b>
					<br /><br />

					This program is free software; you can redistribute it and/or
					modify it under the terms of the <a href="http://www.gnu.org/licenses/gpl.html">GNU General Public License</a>
					as published by the <a href="http://www.fsf.org/">Free Software Foundation</a>; either version 2
					of the License, or (at your option) any later version.<br /><br />

					This program is distributed in the hope that it will be useful,
					but WITHOUT ANY WARRANTY; without even the implied warranty of
					MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
					<a href="http://www.gnu.org/licenses/gpl.html">GNU General Public License</a> for more details.<br /><br />

					You should have received a copy of the <a href="http://www.gnu.org/licenses/gpl.html">GNU General Public License</a>
					along with this program; if not, write to the <a href="http://www.fsf.org/">Free Software
					Foundation</a>, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
					<hr />
					
					<div style="text-align: center">
					<a href="http://www.php.net">
						<img src="{C_SITE.REL_URL}media/powered-by-php.gif" width="88" height="31" alt="Powered by PHP" />
					</a>
					<a href="http://www.mysql.com">
						<img src="{C_SITE.REL_URL}media/powered-by-mysql.gif" width="88" height="31" alt="Powered by MySQL" />
					</a>
					<a href="http://www.gekkoware.org">
						<img src="{C_SITE.REL_URL}media/powered-by-gekko.png" width="88" height="31" alt="Powered by Gekko" />
					</a>
					<a href="http://validator.w3.org/check?uri=referer">
						<img src="media/valid-xhtml10.png" width="88" height="31" alt="Valid XHTML 1.0 Transitional" />
					</a>
					</div>
				</div>
			</td>
			<td id="td_blockC">
				<!--if($V_MESSAGES)-->{V_MESSAGES}<!--endif-->
				
				<!--if($V_USING_IE)-->
				<div class="contentBlock">
					<h1>Warning!</h1>
					<img src="media/no-ie.gif" width="90" height="90" style="float: left; margin: 8px" />
					<div style="margin: 8px; text-align: left">
						The program you're using for browsing is too old, slow, unstable and is becoming obsolet.
						<br />
						There are many new web features you're missing because your browser lacks support like
						PNG image transparency, a full featured on-line visual editor, popup protection,
						security improvements and much more.
						<br />
						We recommend you to switch to <a href="http://firefox.mozilla.com">Firefox</a> now, is
						easy and free of charge!
						<br />
						If you wish to continue using Microsoft Internet Explorer please
						download and install any version greater than or equal to 7.0.
					</div>
				</div>
				<!--endif-->
					
				<div class="contentBlock">
				<form id="frmInstall" action="install.php" method="post">
				<input type="hidden" name="step" value="{V_STEP}" />

				<!--if($V_STEP == "lang")-->
				<h1>Welcome to Gekko!</h1>
				Please choose your <b>language</b> to continue.<br />
				<label>
				{=createDropDown(unserialize({V_ARR_LANGUAGES}), SITE_DEFAULT_LANGUAGE, "lang")=}
				</label>
				<!--endif-->

				<!--if($V_STEP == "ftp")-->
				<h1>{L_PERMISSIONS_CHANGE}</h1>
				{=createMessageBox("info", "{L_PERMISSIONS_CHANGE_HINT}")=}
				<h2>{L_FTP_SERVICE}</h2>
				<h3>{L_CONFIGURATION}</h3>

				<div class="collapse">
					<label>
						{L_SERVER}:
						<input class="text" type="text" name="host" value="localhost" />
					</label>
					<label style="display: none">
						{L_PORT}:
						<input class="text" type="text" name="port" value="21" size="5" />
					</label>
				</div>


				<h3>{L_ACCOUNT}</h3>

				<div class="union">
					<label>
						{L_PATH}:
						<input class="text" size="40" type="text" name="path" value="{=dirname($_SERVER["SCRIPT_FILENAME"])=}" />
						<div class="whisper">{L_FTP_PATH_HINT}</div>
					</label>
					<label>
						{L_USERNAME}:
						<input class="text" type="text" name="user" />
					</label>
					<label>
						{L_PASSWORD}:
						<input class="text" type="password" name="pass" />
					</label>
				</div>

				<h2>{L_MANUAL_WAY}</h2>
				<code>me@unix $ chmod -R 777 data temp dbconf.php</code>

				<!--endif-->

				<!--if($V_STEP == "dbconf")-->
				<h1>{L_INSTALLATION}</h1>

				<h2>{L_DATABASE}</h2>
				{=createMessageBox("info", "{L_DATABASE_HINT}")=}

				<h3>{L_CONFIGURATION}</h3>
				<div class="collapse">
					<label>
						{L_SERVER}:
						<input class="text" type="text" name="host" value="localhost" />
					</label>
					<label style="display: none">
						{L_PORT}:
						<input class="text" type="text" name="port" value="3306" size="5" />
					</label>
					<label>
						{L_DRIVER}:
						{=createDropDown(unserialize({V_ARR_DB_DRIVERS}), DB_DEFAULT_DRIVER, "driver")=}
					</label>
				</div>

				<h3>{L_ACCOUNT}</h3>

				<div class="union">
					<label>
						{L_DATABASE}:
						<input class="text" type="text" name="database" value="gekko" />
					</label>
					<label>
						{L_TABLE_PREFIX}:
						<input class="text" type="text" name="prefix" value="gekkocms_" />
					</label>
					<label>
						{L_USERNAME}:
						<input class="text" type="text" name="user" />
					</label>
					<label>
						{L_PASSWORD}:
						<input class="text" type="password" name="pass" />
					</label>
				</div>

				<!--endif-->

				<!--if($V_STEP == "install_type")-->
				<h2>{L_INSTALLATION_TYPE}</h2>
				{=createMessageBox("info", "{L_INSTALLATION_TYPE_HINT}")=}
				<div class="buttons" style="text-align: center">
					<input type="hidden" name="install" value="" />
					<button type="button" onclick="var form = document.getElementById('frmInstall'); form.install.value = 'upgrade'; form.submit();" style="margin-right: 50px" />{=createIcon("up.png")=} {L_VERSION_UPGRADE}</button>
					<button type="button" onclick="var form = document.getElementById('frmInstall'); form.install.value = 'clean'; form.submit();" style="margin-left: 50px" />{=createIcon("delete.png")=} {L_CLEAN_INSTALL}</button>
				</div>
				<h3>{L_OPTIONS} ({L_CLEAN_INSTALL})</h3>
				<div class="union">
					<label><span><input type="checkbox" name="clean_prefix" value="1" checked="checked" /> {L_DROP_PREFIXED_TABLES}</span></label>
					<label><span><input type="checkbox" name="clean_database" value="1" /> {L_DROP_DATABASE}</span></label>
				</div>
				<!--endif-->

				<!--if($V_STEP == "upgrade")-->
				<h1>{L_UPGRADE}</h1>
				{=createMessageBox("info", "{L_UPGRADE_HINT}")=}

				<h2>{L_OPTIONS}</h2>
				<label>
					<span><input type="checkbox" name="new_modules" value="1" checked="checked" /> {L_INSTALL_NEW_MODULES}</span>
				</label>
				<!--endif-->

				<!--if($V_STEP == "createuser")-->
				<h1>{L_ADMIN_CREATION}</h1>
				{=createMessageBox("info", "{L_ADMIN_CREATION_HINT}")=}
				<div class="collapse">
					<label>
						{L_USERNAME}:
						<input class="text" type="text" name="user" value="admin" />
					</label>
					<label>
						{L_REALNAME}:
						<input class="text" type="text" name="realname" />
					</label>
					<label>
						{L_EMAIL}:
						<input class="text" type="text" name="email" />
					</label>
				</div>
				<div class="collapse">
					<label>
						{L_PASSWORD}:
						<input class="text" type="password" name="pass" />
					</label>
					<label>
						{L_CONFIRM_PASSWORD}:
						<input class="text" type="password" name="pass2" />
					<label>
				</div>
				<!--endif-->

				<!--if($V_STEP == "siteconf")-->
				<h1>{L_SITE_CONFIGURATION}</h1>
				{=createMessageBox("info", "{L_SITE_CONFIGURATION_HINT}")=}
				<h2>{L_CUSTOMIZATION}</h2>
				<div class="union">
					<label>
						{L_SITE_LANG}:
						{=createDropDown(unserialize("{V_ARR_LANG}"), "{V_SELECTED_LANG}", "conf[core:site.lang]")=}
					</label>
					<label>
						{L_SITE_TITLE}:
						<input size="40" class="text" type="text" name="conf[core:site.title]" value="{V_CORE_SITE.TITLE}" />
					</label>
					<label>
						{L_SITE_NAME}:
						<input size="40" class="text" type="text" name="conf[core:site.name]" value="{V_CORE_SITE.NAME}" /><br />
					</label>
					<label>
						{L_SITE_SLOGAN}:
						<input size="40" class="text" type="text" name="conf[core:site.slogan]" value="{V_CORE_SITE.SLOGAN}" />
					</label>
					<label>
						{L_SITE_DESCRIPTION}:
						<input size="40" class="text" type="text" name="conf[core:site.description]" value="{V_CORE_SITE.DESCRIPTION}" />
					</label>
					<label>
						{L_SITE_CONTACT_MAIL}:
						<input size="40" class="text" type="text" name="conf[core:site.contact_mail]" value="{V_CORE_SITE.CONTACT_MAIL}" />
					</label>
					<label>
						{L_SITE_COPYRIGHT}:
						<input size="40" class="text" type="text" name="conf[core:site.copyright]" value="{V_CORE_SITE.COPYRIGHT}" />
					</label>
				</div>

				<h2>{L_PRIMARY_AUDITORY}</h2>
				<div class="union">
					<label>
						{L_PRIMARY_LANG}:
						{=createDropDown(unserialize("{V_ARR_LANG}"), "{V_SELECTED_LANG}", "conf[core:site.primary_lang]")=}
					</label>
					<label>
						{L_PRIMARY_COUNTRY}:
						{=createDropDown(unserialize("{V_ARR_COUNTRY_CODE}"), SITE_DEFAULT_COUNTRY_CODE, "conf[core:site.primary_country]")=}
					</label>
				</div>

				<h2>{L_OPTIONS}</h2>
				<div class="union">
					<label><span><input type="checkbox" name="conf[users:account.require_confirmation]" value="1" checked="checked" /> {L_USERS_REQUIRE_CONFIRMATION}</span></label>
					<label><span><input type="checkbox" name="conf[users:account.enable_self_registration]" value="1" checked="checked" /> {L_USERS_ENABLE_SELF_REGISTRATION}</span></label>
					<label><span><input type="checkbox" name="conf[users:account.send_welcome_letter]" value="1" checked="checked" /> {L_USERS_SEND_WELCOME_LETTER}</span></label>
					<label><span><input type="checkbox" name="conf[core:site.gzip_output]" value="1" checked="checked" /> {L_ENABLE_GZIP_OUTPUT}</span></label>
					<label><span><input type="checkbox" name="conf[core:plugins.msiepngfix.disable]" value="1" checked="checked" /> {L_DISABLE_MSIE_PNGFIX}</span></label>
				</div>

				<h2>{L_POST_INSTALLATION_OPTIONS}</h2>
				<div class="union">
					<label><span><input type="checkbox" name="option[create_menu]" value="1" checked="checked" /> {L_CREATE_BASIC_MENU}</span></label>
				</div>
				<!--endif-->

				<!--if($V_STEP == "install")-->
				<h1>{L_SECURITY_CHECK}</h1>
				{=createMessageBox("info", "{L_SECURITY_CHECK_HINT}")=}

				<div class="union">
					<!--if($V_FTP)-->
					<label>
						{L_FTP_PASSWORD}:
						<input class="text" type="password" name="ftp_pass" />
					</label>
					<!--endif-->
					<label>
						{L_DATABASE_PASSWORD}:
						<input class="text" type="password" name="db_pass" />
					</label>
				</div>
				<!--endif-->

				<!--if($V_STEP == "manual_config")-->
				<h1>{L_THANKS}</h1>
				{=createMessageBox("success", "{L_THANKS_MESSAGE}")=}
				<div class="buttons">
					<button type="button" onclick="location.href='{=urlevalprototype("index.php/module=users/action=login")=}'" />{=createIcon("login.png")=} {L_LOGIN}</button>
				</div>
				<!--endif-->
				<!--if(!$V_HIDE_BUTTONS)-->
					<div class="buttons">{B_RESET} {B_SUBMIT}</div>
				<!--endif-->
				</form>
				</div>
			</td>
		</tr>
		</table>
		<div id="pageNote">
			<div>Gekko &copy; 2004-2006 <a href="mailto:xiam@users.sourceforge.net">Jos&eacute; Carlos Nieto Jarqu&iacute;n</a></div>
			{L_GEKKO_IS_FREE_SOFTWARE}
		</div>
	</div>
	</body>
</html>
