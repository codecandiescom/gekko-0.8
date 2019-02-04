	<form action="{C_SITE.URL}modules/users/actions.php" method="post">
		<input type="hidden" name="id" value="{V_ID}" />
		<input type="hidden" name="action" value="{V_ACTION}" />

		<h3>{L_ACCOUNT_INFO}</h3>
		<div class="union">
			<label>
				{L_REALNAME}:
				<input class="text" type="text" name="realname" maxlength="64" value="{V_USERNAME}" />
			</label>
			<label class="required">
				{L_EMAIL}:
				<input class="text" type="text" name="email" maxlength="64" value="{V_EMAIL}" />
			</label>
		</div>
		<div class="union">
			<label class="required">
				{L_USERNAME}:
				<input <!--if($V_ACTION == "edit" && !conf::getkey("users", "allow_username_change", 0, "b"))-->readonly="readonly" <!--endif-->class="text" type="text" name="username" maxlength="32" value="{V_USERNAME}" />
			</label>
			<label<!--if($V_ACTION != "edit")--> class="required"<!--endif-->>
				{L_PASSWORD}:
				<input class="text" type="text" name="password" maxlength="32" value="" />
			</label>
		</div>
		<label>
			{L_STATUS}:
			{=createBooleanStatus("{V_STATUS}", "status")=}
		</label>
		<!--if($V_DATE_LOGIN)-->
		<label>
			{L_LAST_LOGIN}:
			{=dateformat({V_DATE_LOGIN})=}
		</label>
		<!--endif-->

		<div id="userGroups" style="display:none">
			<h3>{L_GROUPS}</h3>
			{=Groups::mkList("groups", "{V_GROUPS}")=}
		</div>
		<button type="button" onclick="triggerDisplay('userGroups')">{=createIcon("groups.png")=} {L_TRIGGER_GROUPS}</button>

		<div class="buttons">{B_RESET} {B_SUBMIT}</div>

		<input type="hidden" name="return" value="{V_RETURN}" />
	</form>