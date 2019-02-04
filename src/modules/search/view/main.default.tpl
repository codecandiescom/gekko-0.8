<form method="get" action="{=urlevalprototype("index.php")=}">
<input type="hidden" name="module" value="search">

<fieldset class="horizontal">
	<label class="required">
		{L_SEARCH_TERMS}:
		<input type="text" class="text" name="q" value="{V_SEARCH_TERMS}" />
	</label>
	<label>
		{L_SEARCH_IN}:
		{=createDropDown(search::getModules(), "{V_BASE}", "base")=}
	</label>
</fieldset>
<div class="buttons">
	<button type="submit">{=createIcon("search.png")=} {L_SEARCH}</button>
</div>
</form>

<!--if($BUFFER.RESULT)-->
<!--{bgn: RESULT}-->
<div class="{V_SW_CLASS}">
	<div class="item">
		<h3>{=createIcon("{V_ICON}")=} {V_TITLE}</h3>
		<!--if($V_IMAGE)-->
		<div style="margin:5px; text-align:center">{V_IMAGE}</div>
		<!--endif-->
		{V_DESCRIPTION}
		<!--if($V_ACTIONS)-->
		{V_ACTIONS}
		<!--endif-->
	</div>
</div>
<!--{end: RESULT}-->
{V_PAGER}
<!--endif-->

<!--if(!$BUFFER.RESULT && $V_SEARCH_TERMS)-->
{=createMessageBox("error", "{L_NO_RESULTS_WERE_FOUND}")=}
<!--endif-->