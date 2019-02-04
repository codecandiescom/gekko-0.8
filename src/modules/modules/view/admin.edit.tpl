<h2>{=createIcon("{V_MODULE}.png", 48)=} {V_MODULE}</h2>
{V_ACTIONS}
<form action="{C_SITE.URL}modules/modules/actions.php" method="post">

	<input type="hidden" name="action" value="modify" />
	<input type="hidden" name="id" value="{V_ID}" />
	<input type="hidden" name="return" value="{V_RETURN}" />

	<div class="union">
		<label>
			{L_AUTH_EXEC}:
			{=createAuthSelector("{V_AUTH_EXEC}", "auth_exec")=}
		</label>
		<label>
			{L_AUTH_MANAGE}:
			{=createAuthSelector("{V_AUTH_MANAGE}", "auth_manage")=}
		</label>
	</div>

	<div class="union">
		<label><div>{=createCheckBox("status", "", {V_STATUS})=} {L_ENABLED}</div></label>
		<label><div>{=createCheckBox("hidden", "", {V_HIDDEN})=} {L_HIDDEN}</div></label>
	</div>

	<div class="buttons">
		{B_RESET} {B_SUBMIT}
	</div>
</form>
