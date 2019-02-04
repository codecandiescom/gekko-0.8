	<form action="{C_SITE.URL}modules/menu-editor/actions.php" method="post">
		<input type="hidden" name="id" value="{V_ID}" />
		<input type="hidden" name="action" value="{V_ACTION}" />
		<input type="hidden" name="return" value="{V_RETURN}" />
		<div class="collapse">
			<label>
				{L_ICON}:
				{=createIconchooser(16, "{V_ICON}")=}
			</label>
			<label class="required">
				{L_MENU}:
				{=createDropDown(unserialize("{V_ARR_MENU}"), "{V_MENU_ID}", "menu")=}
			</label>
			<label>
				{L_ORDER}:
				{=createDropDown(menu_editor::getPossibleOrder("{V_MENU_ID}"), "{V_ORDER_ID}", "order_id")=}
			</label>
		</div>
		<div class="union">
			<label class="required">
				{L_TITLE}:
				<input class="text" type="text" name="title" size="40" maxlength="128" value="{V_TITLE}" />
			</label>
			<label>
				{L_TOOLTIP}:
				<input class="text" type="text" name="tooltip" maxlength="255" size="40" value="{V_TOOLTIP}" />
			</label>
			<label class="required">
				{L_LINK}:
				<input class="text" size="40" type="text" name="link" maxlength="255" value="{V_LINK}" />
			</label>
			<label><span>{=createCheckBox("is_absolute", "{L_ABSOLUTE_LINK}", "{V_IS_ABSOLUTE}")=}</span></label>
		</div>
		<div class="collapse">
			<label>
				{L_TYPE}:
				{=createDropDown(menu_editor::getLinkTypes(), "{V_TYPE}", "type")=}
			</label>
			<label>
				{L_LEVEL}:
				{=createDropDown(menu_editor::getLevels(), "{V_LEVEL}", "level")=}
			</label>
			<label>
				{L_AUTH_ACCESS}:
				{=createAuthSelector("{V_AUTH_ACCESS}")=}
			</label>
		</div>
		<div class="buttons">{B_RESET} {B_SUBMIT}</div>
	</form>
