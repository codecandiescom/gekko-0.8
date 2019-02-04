<!--if($BUFFER.USER)-->
{V_OPTIONS}
<h2>{L_LIST}</h2>
<!--{bgn: USER}-->
<div class="{V_SW_CLASS}">
	<div class="item">
		<div class="avatar" style="displat: block; float: right">{V_AVATAR}</div>
		<h3>({V_ID}) {V_USERNAME}</h3>
		<div style="margin-left: 30px">
			{V_REALNAME}<br />
			{V_EMAIL}
			<hr />
			{V_ACTIONS}
		</div>
	</div>
</div>
<!--{end: USER}-->
{V_PAGER}
<br />
<div class="tbox1">
	<b>{L_SORT_BY}</b>:<br />
	{=createDropDown(array("id"=>"{L_ID}","username"=>"{L_USERNAME}","email"=>"{L_EMAIL}"), "{V_SORT}", "sort", "id=\"sort\"")=}
	{=createDropDown(array("asc"=>"{L_ASCENDENT}","desc"=>"{L_DESCENDENT}"), "{V_MODE}", "mode", "id=\"mode\"")=}
	<button onclick="location.href='{=urlEvalPrototype('index.php/module=admin/base=users?sort=\'+document.getElementById(\'sort\').value+\'&mode=\'+document.getElementById(\'mode\').value+\'')=}'">{L_SORT}</button>
</div>
<!--endif-->