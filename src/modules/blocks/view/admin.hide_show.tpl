{V_ACTIONS}
<script type="text/javascript">
function toggleHideShow(module) {
	var obj = document.getElementById('hide_show_'+module);
	
	if (obj.style.display == 'block') {
		obj.style.display = 'none';
	} else {
		obj.style.display = 'block';
	}
	
}
</script>

<h2>{L_HIDE_AND_SHOW}</h2>
{=createMessageBox("info", "{L_HIDE_AND_SHOW_HINT}")=}
<form action="{C_SITE.URL}modules/blocks/actions.php" method="post">
	<input type="hidden" name="action" value="hide_show" />
	<!--{bgn: MODULE}-->
	<div class="{V_SW_CLASS}">
		<h3><a href="javascript:void(0)" onclick="toggleHideShow('{V_MODULE}')">{=createIcon("{V_MODULE}.png")=} {V_MODULE}</a></h3>
		<div id="hide_show_{V_MODULE}" style="display: none">
			{V_OPTIONS}
		</div>
	</div>
	<!--{end: MODULE}-->
	<div class="buttons">{B_RESET} {B_SUBMIT}</div>
	<input type="hidden" name="return" value="{V_RETURN}" />
</form>