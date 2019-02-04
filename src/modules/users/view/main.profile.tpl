{V_ACTIONS}
<table class="skeleton">
<tr>
	<td style="vertical-align:center">
		<div class="union">
			<table>
			<tr>
				<td><img class="avatar" src="{=users::getAvatarURL("{V_AVATAR}")=}" width="50" height="50" alt="{V_AVATAR}" /></td>
				<td>
					<h2>{V_REALNAME}</h2>
					&lt;{=createAntispam("{V_PUBLIC_EMAIL}")=}&gt;
				</td>
			</tr>
			</table>
			<!--if($V_SIGNATURE)--><blockquote>{V_SIGNATURE}</blockquote><!--endif-->
		</div>
	</td>
	<td>
		<div class="union">
			<b>{L_AGE}:</b>
			<div style="margin-left: 20px">{=getAge("{V_BIRTHDATE}")=} {L_YEARS}</div>
			<!--if($V_LOCATION)-->
			<b>{L_LOCATION}:</b>
			<div style="margin-left: 20px">{V_LOCATION}</div>
			<!--endif-->
			<b>{L_GENDER}:</b>
			<div style="margin-left: 20px">{=users::listGenders("{V_GENDER}")=}</div>
		</div>
	</td>
</tr>
<tr>
	<td colspan="2">
		<!--if($V_ABOUT_ME)-->
		<div class="union">
			<h3>{L_ABOUT_ME}</h3>
			{V_ABOUT_ME}
		</div>
		<!--endif-->
		<div class="union">
			<!--if($V_WEBSITE)-->
			<b>{L_WEBSITE}:</b>
			<div style="margin-left: 20px">{=safeLink("{V_WEBSITE}")=}</div>
			<!--endif-->
			<!--if($V_EMAIL)-->
			<b>{L_EMAIL}:</b>
			<div style="margin-left: 20px">{=safeLink("{V_PUBLIC_EMAIL}")=}</div>
			<!--endif-->
			<!--if($V_IMMSN)-->
			<b>{L_MSN}:</b>
			<div style="margin-left: 20px">{V_IMMSN}</div>
			<!--endif-->
			<!--if($V_IMYIM)-->
			<b>{L_YIM}:</b>
			<div style="margin-left: 20px">{V_IMYIM}</div>
			<!--endif-->
			<!--if($V_IMICQ)-->
			<b>{L_ICQ}:</b>
			<div style="margin-left: 20px">{V_IMICQ}</div>
			<!--endif-->
		</div>
	</td>
</tr>
</table>
{V_ACTIONS}