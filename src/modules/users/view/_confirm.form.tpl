<form action="{C_SITE.URL}modules/users/actions.php" method="post">

	<input type="hidden" name="action" value="confirm" />
	<input type="hidden" name="return" value="{V_RETURN}" />

	<div class="union">
		<label class="required">
			{L_USERNAME}:
			<input class="text" type="text" name="username" />
		</label>
	</div>

	<!--if($V_OPTION != "resend_confirmation")-->
	<div class="union">
		<label class="required">
			{L_PASSWORD}:<br />
			<input class="text" type="password" name="password" />
		</label>
		<label class="required">
			{L_CONFIRMATION_CODE}:
			<input class="text" type="text" name="confirmation" />
		</label>
	</div>
	<!--endif-->

	<div class="buttons">
		{B_RESET} {B_SUBMIT}
	</div>

</form>