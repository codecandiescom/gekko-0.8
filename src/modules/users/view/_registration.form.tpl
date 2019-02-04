<form action="{C_SITE.URL}modules/users/actions.php" method="post">
	<input type="hidden" name="action" value="register" />
	<input type="hidden" name="return" value="{V_RETURN}" />
	<div class="union">
		<label title="{L_ALPHA_CHARS}" class="required">
			{L_USERNAME}:
			<input class="text" type="text" name="username" maxlenght="32" />
		</label>

		<label>
			{L_REALNAME}:
			<input class="text" type="text" name="realname" maxlenght="32" />
		</label>

		<label class="required">
			{L_EMAIL}:
			<input class="text" type="text" name="email" maxlenght="64" />
		</label>
	</div>
	<div class="union">
		<label class="required">
			{L_PASSWORD}:
			<input class="text" type="password" name="password" maxlenght="64" />
		</label>
		<label class="required">
			{L_CONFIRM_PASSWORD}:
			<input class="text" type="password" name="confirm" maxlenght="64" />
		</label>
	</div>
	<div class="buttons">
		{B_RESET} {B_SUBMIT}
	</div>
</form>