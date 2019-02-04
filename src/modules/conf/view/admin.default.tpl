{V_OPTIONS}
<form id="confForm" action="{C_SITE.URL}modules/conf/actions.php" method="post">
	<input type="hidden" name="action" value="quickedit" />
	<input type="hidden" name="keylist" value="" />
	<input type="hidden" name="return" value="{V_RETURN}" />

	<!--if($BUFFER.MODULE)-->
	<!--{bgn: MODULE}-->
	<div class="{V_SW_CLASS}">
		{=createIcon("{V_MODULE}.png", 16)=} <a href="javascript:void(0)" onclick="gekkoModuleConf.listKeys('{V_MODULE}', this)">{V_MODULE}</a>
		<div class="union" style="display: none"></div>
	</div>
	<!--{end: MODULE}-->
	<!--endif-->

	<div class="buttons">{B_RESET} {B_SUBMIT}</div>
</form>

