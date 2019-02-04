	<script type="text/javascript">
		function updateBlockVars(obj) {
			if (!document.getElementById('scriptvars').value) {
				if (obj.value.substr(-4) == '.php')
					xmlHttp.load("document.getElementById('scriptvars').innerHTML", '{C_SITE.URL}tools.php?module=blocks&action=getvars&block='+escape(obj.value)+'&html=1');
				else
					document.getElementById('scriptvars').innerHTML = '';
			}
		}
	</script>
	<form action="{C_SITE.URL}modules/blocks/actions.php" method="post">
		<input type="hidden" name="id" value="{V_ID}" />
		<input type="hidden" name="action" value="{V_ACTION}" />
		<input type="hidden" name="return" value="{V_RETURN}" />
		<table class="skeleton">
			<tr>
				<td>
					<div class="union">
						<label>
							{L_CLASS}:
							{=createDropDown(blocks::getClasses(), "{V_BLOCKCLASS}", "blockclass")=}
						</label>
						<label>
							{L_STATUS}:
							{=createDropDown(blocks::getStatus(), "{V_STATUS}", "status")=}
						</label>
					</div>
				</td>
				<td>
					<div class="union">
						<label>
							{L_POSITION}:
							{=createDropDown(blocks::getPositions(), "{V_POSITION}", "position")=}
						</label>
						<label>
							{L_ORDER}:
							{=createDropDown(unserialize("{V_ORDERS}"), "{V_ORDER_ID}", "order_id")=}
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="union">
						<label class="required">
							{L_TITLE}:
							<input class="text" type="text" name="title" maxlength="64" value="{V_TITLE}" />
						</label>
					</div>
				</td>
				<td>
					<div class="union">
						<label>
							{L_ICON}:
							{=createIconchooser(16, "{V_ICON}")=}
						</label>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<label>
						{L_CONTENT}:<br/>
						{=textEditor("{V_CONTENT}")=}
					</label>
				</td>
			</tr>
			<tr>
				<td>
					<div class="union">
						{L_MODULES}:<br />
						{=Modules::mkChooser("{V_MODULES}", (strpos("{V_MODULES}", "*") !== false))=}
						<label>
							{L_AUTH_ACCESS}:
							{=createAuthSelector("{V_AUTH_ACCESS}")=}
						</label>
					</div>
				</td>
				<td>
					<div class="union">
						<label>
							{L_CSS_TEXT}:<br/>
							<textarea name="csstext" cols="40" rows="4">{V_CSSTEXT}</textarea>
						</label>
					</div>

					<div class="union">
						<label>
							{L_SCRIPTPATH}:
							<select name="scriptpath" onchange="updateBlockVars(this)">{=createDropDown(blocks::getScripts(), "{V_SCRIPTPATH}")=}</select>
						</label>
						{L_SCRIPTVARS}:
						<div class="tbox1">
							<small><div id="scriptvars">{V_SCRIPTVARS}</div></small>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="buttons">{B_RESET} {B_SUBMIT}</div>
				</td>
			</tr>
		</table>
	</form>