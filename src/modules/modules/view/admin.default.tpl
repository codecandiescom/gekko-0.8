<script type="text/javascript">
	function doModShowHide(id) {
		obj = document.getElementById('modshow_'+id);
		obj.style.display = (obj.style.display == 'block') ? 'none' : 'block';
	}
</script>

<!--if($BUFFER.MODULE)-->
<form action="{C_SITE.URL}modules/modules/actions.php" method="post">
<input type="hidden" name="action" value="set_default" />

<table class="data">
<tr class="head">
	<td style="width: 40px;">{L_MAIN}</td>
	<td>{L_MODULE}</td>
</tr>
<!--{bgn: module}-->
<tr class="{V_SW_CLASS}">
	<td class="id" style="text-align:center;">{V_DEFAULT.RADIO}</td>
	<td class="small">
		<!--if($V_MESSAGE)-->{V_MESSAGE}<!--endif-->
		<h2>{=createIcon("{V_MODULE}.png", 16)=} {V_MODULE}</h2>
		<div style="margin-left: 30px">
			<b>{L_ID}:</b> {V_ID}<br />
			<b>{L_AUTH_EXEC}:</b> {V_AUTH_EXEC}<br />
			<b>{L_AUTH_MANAGE}:</b> {V_AUTH_MANAGE}<br />
		</div>
		<div id="modshow_{V_ID}" class="union" style="display:none">
			<h3>{L_MODULE_INFO}</h3>
			<div style="margin-left: 30px">
				<b>{L_AUTHOR}:</b> {V_AUTHOR}<br />
				<b>{L_HOMEPAGE}:</b> {V_HOMEPAGE}<br />
				<b>{L_VERSION}:</b> {V_VERSION}<br />
				<b>{L_RELEASE_AUTHOR}:</b> {V_RELEASE_AUTHOR}<br />
				<b>{L_NOTES}:</b><br />
				{V_NOTES}<br />
				<b>{L_CHANGELOG}:</b><br />
				{V_CHANGELOG}
			</div>
		</div>
		<a href="javascript:doModShowHide('{V_ID}')">[{L_TOGGLE_EXPAND}]</a>
		{V_ACTIONS}
	</td>
</tr>
<!--{end: module}-->
</table>
<div class="buttons">
	{B_RESET} <button type="submit">{=createIcon("home.png", 16)=} {L_SET_AS_DEFAULT}</button>
</div>
<input type="hidden" name="return" value="{V_RETURN}" />
</form>
<!--endif-->
<br /><br />
<!--if($BUFFER.PACKAGE)-->
<form action="{C_SITE.URL}modules/modules/actions.php" method="post">
<input type="hidden" name="action" value="install" />

<h2>{=createIcon("packages.png", 48)=} {L_NEW_PACKAGES}</h2>
<table class="data">
	<tr class="head">
		<td>{L_MODULE}</td>
		<td>{L_TIMESTAMP}</td>
	</tr>
	<!--{bgn: package}-->
	<tr class="{V_SW_CLASS}">
		<td>
		<input type="checkbox" name="module[]" value="{V_MODULE}" />
		{V_MODULE}
		</td>
		<td>{V_TIMESTAMP}</td>
	</tr>
	<!--{end: package}-->
</table>
<div class="buttons">
	{B_RESET} <button type="submit">{=createIcon("install.png", 16)=} {L_INSTALL}</button>
</div>
<input type="hidden" name="return" value="{V_RETURN}" />
</form>
<!--endif-->
