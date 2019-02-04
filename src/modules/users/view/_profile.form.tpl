<form action="{C_SITE.URL}modules/users/actions.php" method="post" name="profile">
	<input type="hidden" name="id" value="{V_ID}" />
	<input type="hidden" name="action" value="{V_ACTION}" />
	<input type="hidden" name="return" value="{V_RETURN}" />

	<table>
	<tr>
		<td>
			<h3>{V_NICKNAME}</h3>
			{V_PUBLIC_EMAIL}
			<div class="union">
				<label for="avatar">
					{L_AVATAR}:
					{=users::createAvatarchooser("{V_AVATAR}")=}
				</label>
			</div>
		</td>
		<td>
			<div class="union">
				<label class="required">
					{L_NICKNAME}:
					<input class="text" type="text" name="nickname" value="{V_NICKNAME}" maxlenght="32" />
				</label>
				<label>
					{L_PUBLIC_EMAIL}:
					<input class="text" type="text" name="public_email" value="{V_PUBLIC_EMAIL}" maxlenght="64" />
				</label>
				<label>
					{L_BIRTHDATE}:
					<div>{=createDateSelector("birthdate", "{V_BIRTHDATE}")=}</div>
				</label>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div class="union">
				<label>
					{L_WEBSITE}:
					<input class="text" type="text" name="website" value="{V_WEBSITE}" maxlenght="64" />
				</label>
				<label>
					{L_LOCATION}:
					<input class="text" type="text" name="location" value="{V_LOCATION}" maxlenght="255" />
				</label>
				<label>
					{L_GENDER}:
					{=createDropDown(users::listGenders(-1), "{V_GENDER}", "gender")=}
				</label>
			</div>
			<div class="union">
				<label>
					{L_MSN}:
					<input class="text" type="text" name="immsn" value="{V_IMMSN}" maxlenght="64" />
				</label>
				<label>
					{L_YIM}:
					<input class="text" type="text" name="imyim" value="{V_IMYIM}" maxlenght="64" />
				</label>
				<label>
					{L_ICQ}:
					<input class="text" type="text" name="imicq" value="{V_IMICQ}" maxlenght="64" />
				</label>
			</div>

			<div class="union">
				<label>
					{L_ABOUT_ME}:
					<textarea cols="50" rows="8" name="about_me">{V_ABOUT_ME}</textarea>
				</label>
				<label>
					{L_SIGNATURE}:
					<textarea cols="50" rows="3" name="signature">{V_SIGNATURE}</textarea>
				</label>
			</div>

			<div class="buttons">
				{B_RESET} {B_SUBMIT}
			</div>
		</td>
	</tr>
	</table>
</form>