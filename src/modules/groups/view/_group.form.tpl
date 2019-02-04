	<form action="{C_SITE.URL}modules/groups/actions.php" method="post">
		<input type="hidden" name="id" value="{V_ID}" />
		<input type="hidden" name="action" value="{V_ACTION}" />
		<table class="skeleton">
		<tr>
			<td>
				<label class="required">
					{L_GROUP_NAME}:<br />
					<input class="text" type="text" name="groupname" maxlength="32" value="{V_GROUPNAME}" /><br />
				</label>
			</td>
			<td>
				<label>
					{L_STATUS}:<br />
					{=createDropDown(array("{L_DISABLED}", "{L_ENABLED}"), {V_STATUS}, "status")=}
				</label>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<label>
					{L_DESCRIPTION}:<br/>
					<textarea name="description" cols="35" rows="4">{V_DESCRIPTION}</textarea><br />
				</label>
				<label>
					{L_INCLUDED_GROUPS}:<br/>
					<textarea readonly="readonly" onclick="groupChooser(this)" name="includegroups" cols="35" rows="4">{V_INCLUDEGROUPS}</textarea><br />
				</label>
				<div class="buttons">{B_RESET} {B_SUBMIT}</div>
			</td>
		</tr>
		</table>
		<input type="hidden" name="return" value="{V_RETURN}" />
	</form>