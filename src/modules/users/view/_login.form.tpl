<div class="avatar" style="float: right">{=createIcon("account.png", 48)=}</div>
<form action="{C_SITE.URL}modules/users/actions.php" method="post">
	<input type="hidden" name="action" value="login" />
	<input type="hidden" name="return" value="{V_RETURN}" />
	<div class="union">
		<label class="required">
			{L_USERNAME}:
			<input class="text" type="text" name="username" value="{V_USERNAME}" />
		</label>
		<label class="required">
			{L_PASSWORD}:
			<input class="text" type="password" name="password" />
		</label>
	</div>
	<div class="buttons">
		{B_RESET} {B_SUBMIT}
	</div>
</form>