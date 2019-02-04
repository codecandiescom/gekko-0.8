	<form action="{C_SITE.URL}modules/conf/actions.php" method="post">
		<input type="hidden" name="id" value="{V_ID}" />
		<input type="hidden" name="action" value="{V_ACTION}" />
		<input type="hidden" name="return" value="{V_RETURN}" />

		<table class="skeleton">
			<tr>
				<td>
					<div class="union">
						<label class="required">
							{L_MODULE}:
							<input class="text" type="text" size="12" name="module" maxlength="64" value="{V_MODULE}" />
						</label>
						<label>
							{L_TYPE}:
							{=createDropDown(array("{L_STRING}", "{L_INTEGER}", "{L_BOOLEAN}"), {V_KEYTYPE}, "keytype")=}
						</label>
					</div>
				</td>
				<td>
					<div class="union">
						<label class="required">
							{L_KEY}:
							<input class="text" type="text" name="keyname" maxlength="128" value="{V_KEYNAME}" size="12"  />
						</label>
						<label>
							{L_VALUE}:
							<input class="text" type="text" name="keyvalue" maxlength="255" value="{V_KEYVALUE}" size="12" />
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
				<div class="union">
				<label>
					<div>{=createCheckBox("locked", "{L_LOCKED}", {V_LOCKED})=}</div>
				</label>
				</div>
				</td>
			</tr>
		</table>

		<div class="buttons">
			{B_RESET} {B_SUBMIT}
		</div>
	</form>