<form method="get" action="{=urlEvalPrototype("index.php")=}">
<input type="hidden" name="module" value="search">

<label class="required">
	{L_SEARCH_TERMS}:
	<input type="text" class="text" name="q" size="16" value="{V_SEARCH_TERMS}" />
</label>

<label class="required">
	{L_SEARCH_IN}:
	{=createDropDown(search::getModules(), "{V_BASE}", "base")=}
</label>

<button type="submit">{=createIcon("search.png")=} {L_SEARCH}</button>

</form>
