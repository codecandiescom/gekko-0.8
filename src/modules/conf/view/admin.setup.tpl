<!--if($BUFFER.MODULE)-->
<form id="confForm" action="{C_SITE.URL}modules/conf/actions.php" method="post">
	<input type="hidden" name="action" value="quickedit" />

	<input type="hidden" name="keylist" value="{V_KEYLIST}" />
<!--{bgn: MODULE}-->
	<h2>{=createIcon("{V_MODULE}.png")=} {V_MODULE_NAME}</h2>
	<br />
	<!--{bgn: KEY}-->
	<!--if(conf::getkey("conf", "setup.show_key_titles", 0, "b"))-->
	<i><b>{V_KEYNAME}</b></i><br />
	<!--endif-->
	{V_KEYDESC}<br />

	<!--if($V_KEYVALUE.INPUT)-->
		{V_KEYVALUE.INPUT}
	<!--endif-->

	<!--if(!$V_KEYVALUE.INPUT)-->
		<!--if($V_KEYTYPE == 2)-->
		{=createCheckBox("key[{V_ID}]", "", "{V_KEYVALUE}")=}
		<!--endif-->
		<!--if($V_KEYTYPE != 2)-->
		<input class="text" type="text" name="key[{V_ID}]" value="{V_KEYVALUE}" maxlenght="255" size="40" />
		<!--endif-->
	<!--endif-->
	<hr />
	<!--{end: KEY}-->
<!--{end: MODULE}-->
	<input type="hidden" name="return" value="{V_RETURN}" />
	<div class="buttons">
		{B_RESET} {B_SUBMIT}
	</div>
</form>
<!--endif-->