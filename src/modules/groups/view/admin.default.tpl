<!--if($BUFFER.GROUP)-->
<h2>{L_MANAGE}</h2>
<!--{bgn: GROUP}-->
<div class="{V_SW_CLASS}">
	<h4>{=createIcon("{V_GROUPNAME}.png", 16)=} ({V_ID}) {V_GROUPNAME}</h4>
	<div style="margin-left: 30px;">
		{V_DESCRIPTION}
		<!--if($V_INCLUDEGROUPS)-->
		<div class="whisper">
			<b>{L_INCLUDED_GROUPS}:</b><br />
			{V_INCLUDEGROUPS}
		</div>
		<!--endif-->
		{V_ACTIONS}
	</div>
</div>
<!--{end: GROUP}-->
<!--endif-->

<h2>{L_CREATE}</h2>
{include("_group.form.tpl")}