{V_ACTIONS}

<h2>{L_INSTALL_PACKAGE}</h2>
<!--if(!$V_CONN_TEST_STATUS)-->
{=createMessageBox("error", "{E_FTP_TEST_FAILED}")=}
<!--endif-->
<!--if($V_SERVER_BANNER && !conf::getkey("packages", "ftp_test_passed"))-->
{=createMessageBox("success", sprintf("{L_FTP_SERVER_NAME}", "{V_SERVER_BANNER}"))=}
<!--endif-->
<form action="{C_SITE.URL}modules/packages/actions.php" method="post" enctype="multipart/form-data">

	<input type="hidden" name="action" value="{V_ACTION}" />

	<h3>{L_FTP_CONFIGURATION}</h3>

	<div class="union">
		<label>
			{L_SERVER}:
			<span>
			<input class="text" type="text" size="18" name="ftp_host" value="{C_FTP.HOST}" />:<input class="text" type="text" size="3" name="ftp_port" value="{C_FTP.PORT}" />
			</span>
		</label>
		<label>
			{L_FTP_PATH}:
			<input class="text" type="text" size="32" name="ftp_path" value="{C_FTP.PATH}" />
		</label>
		<label>
			{L_USER}:
			<input class="text" type="text" size="16" name="ftp_user" value="{C_FTP.USER}" />
		</label>
		<label>
			{L_PASS}:
			<input class="text" type="password" size="16" name="ftp_pass" value="{C_FTP.PASS}" />
		</label>
	</div>

	<!--if(conf::getkey("packages", "ftp_test_passed"))-->
	<div class="union">
		{L_PACKAGE_SOURCE}:
		<div class="small">
			<label>
				{L_URL}:
				<input class="text" type="text" name="remote_file" value="" />
			</label>
			<label>
				{L_LOCAL_FILE}:
				<input type="file" name="local_file" />
			</label>
		</div>
	</div>
	<!--endif-->

	<div class="buttons">
		{B_CANCEL}
		<!--if(conf::getkey("packages", "ftp_test_passed"))-->
		<button type="submit">{=createIcon("packages.png")=} {L_INSTALL}</button>
		<!--endif-->
		<button type="submit" name="action" value="ftp_test">{=createIcon("info.png")=} {L_TEST_FTP}</button>
	</div>

	<input type="hidden" name="return" value="{V_RETURN}" />
</form>
