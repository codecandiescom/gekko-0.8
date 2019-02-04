	<form action="{C_SITE.URL}modules/menu-editor/actions.php" method="post">
		<input type="hidden" name="id" value="{V_ID}" />
		<input type="hidden" name="action" value="{V_ACTION}" />
		<input type="hidden" name="return" value="{V_RETURN}" />

		<label>
			{L_NAME}:
			<input class="text" type="text" name="title" maxlength="128" value="{V_TITLE}" />
		</label>

		<label><span>{=createCheckBox("hide_icons", "{L_NO_ICONS}", {V_HIDE_ICONS})=}</span></label>
		<label><span>{=createCheckBox("dropdown", "{L_DROPDOWN}", {V_DROPDOWN})=}</span></label>

		<div class="buttons">{B_RESET} {B_SUBMIT}</div>
	</form>