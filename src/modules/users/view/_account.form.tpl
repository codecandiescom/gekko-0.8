	{V_OPTIONS}
	<!--if($V_OPTION == "modify_password")-->
		<h2>{L_MODIFY_PASSWORD}</h2>
	<!--endif-->
	<!--if(!$V_OPTION)-->
		<h2>{L_ACCOUNT_INFO}</h2>
	<!--endif-->
	<form action="{C_SITE.URL}modules/users/actions.php" method="post">
		<input type="hidden" name="id" value="{V_ID}" />
		<input type="hidden" name="action" value="{V_ACTION}" />
		<input type="hidden" name="return" value="{V_RETURN}" />
		<input type="hidden" name="option" value="{V_OPTION}" />

		<div class="union">
		<label class="required">
			{L_CURRENT_PASSWORD}:
			<input class="text" type="password" name="password" maxlength="32" value="" />
		</label>
		</div>

		<!--if(!$V_OPTION)-->
		<div class="union">
			<label class="required">
				{L_REALNAME}:
				<input class="text" type="text" name="realname" maxlength="64" value="{V_REALNAME}" />
			</label>
			<label class="required">
				{L_EMAIL}:
				<input class="text" type="text" name="email" maxlength="64" value="{V_EMAIL}" />
			</label>
		</div>
		<!--endif-->

		<!--if($V_OPTION == "modify_password")-->
		<div class="union">
			<label class="required">
				{L_NEW_PASSWORD}:
				<input class="text" type="password" name="new_password" maxlength="32" value="" />
			</label>
			<label class="required">
				{L_CONFIRM_PASSWORD}:
				<input class="text" type="password" name="confirm_password" maxlength="32" value="" />
			</label>
		</div>
		<!--endif-->

		<div class="buttons">{B_RESET} {B_SUBMIT}</div>
	</form>

	{V_OPTIONS}