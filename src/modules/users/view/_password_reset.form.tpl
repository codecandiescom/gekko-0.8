<!--if($V_SENT)-->
<div class="info">
{L_REGISTRATION_EMAIL_SENT}
</div>
<!--endif-->
<form action="{C_SITE.URL}modules/users/actions.php" method="post">
	<input type="hidden" name="action" value="resetpasswd" />
	<input type="hidden" name="return" value="{V_RETURN}" />

	<div class="union">
		<label class="required">
			{L_USERNAME}:
			<input class="text" type="text" name="username" value="{V_USERNAME}" />
		</label>
		<label class="required">
			{L_EMAIL}:
			<input class="text" type="text" name="email" value="{V_EMAIL}" />
		</label>
	</div>

	<!--if($V_STEP==2)-->
	<div class="union">
		<label class="required">
			{L_CONFIRMATION_CODE}:
			<input class="text" type="text" name="confirmation" value="{V_CONFIRMATION}" />
		</label>
	</div>
	<div class="union">
		<label class="required">
			{L_NEW_PASSWORD}:
			<input class="text" type="text" name="password1" />
		</label>
		<label class="required">
		{L_CONFIRM_PASSWORD}:
		<input class="text" type="text" name="password2" />
		</label>
	</dib>
	<!--endif-->

	<div class="buttons">
		{B_RESET} {B_SUBMIT}
	</div>
</form>