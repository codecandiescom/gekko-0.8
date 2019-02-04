<script type="text/javascript">
	gekkoEditor.editorLevel[{V_ID}] = {V_LEVEL};
</script>
<div id="gekkoEditor_{V_ID}" class="gekkoEditorBox" style="width:340px;">
	<div id="gekkoEditorVisualFrame_{V_ID}" style="display: none">
		<div class="buttons">
			<table style="margin: 0px; padding: 0px; width: 100%">
				<tr><td>
				<!--if($V_LEVEL > 1 && !$V_OPTIONS_MINI)-->
					<div>
						<select onchange="gekkoEditor.exec('{V_ID}', 'fontname', this.value); this.style.FontName=this.value">
							{V_FONTS.SEL}
						</select>
						<!--if($V_LEVEL==3)-->
						<select name="heading" onchange="gekkoEditor.exec('{V_ID}', 'heading', this.value)">
							{V_HEADINGS}
						</select>
						<!--endif-->
					</div>
				<!--endif-->
				</td></tr>
				<tr><td>
				{V_BUTTONS}
				</td></tr>
			</table>
		</div>

		<div class="photo" id="gekkoEditorColorPicker_{V_ID}" style="position:absolute;display:none;">{V_COLORS}</div>

		<iframe id="gekkoEditorVisual_{V_ID}" class="gekkoEditorVisual" src="{C_SITE.URL}tools.php?action=editorFrame&amp;editor_id={V_ID}" style="width:100%" style="width:{V_WIDTH};height:{V_HEIGHT};"></iframe>

		<div class="buttons" style="text-align:left">
			<button type="button" onclick="gekkoEditor.setMode('{V_ID}', 'source')">{=createIcon("source.png")=} {L_SOURCE_MODE}</button>
		</div>
	</div>
	<div id="gekkoEditorSourceFrame_{V_ID}" style="display: block; font-size: medium">
		<textarea id="gekkoEditorSource_{V_ID}" class="gekkoEditorSource" style="width:100%" rows="20" name="{V_FIELD}" style="width:{V_WIDTH};height:{V_HEIGHT};">{V_CONTENT}</textarea>
		<div class="buttons" style="text-align:left">
			<button type="button" onclick="gekkoEditor.setMode('{V_ID}', 'visual')" class="visual">{=createIcon("visual.png")=} {L_VISUAL_MODE}</button>
			<button type="button" onclick="previewContent('gekkoEditorSource_{V_ID}')" class="preview">{=createIcon("preview.png")=} {L_PREVIEW}</button>
		</div>
		<!--if($V_ALLOWED_TAGS)-->
		<small><b>{L_ALLOWED_TAGS}</b>: <i>{V_ALLOWED_TAGS}</i></small>
		<!--endif-->
	</div>
	<div style="font-size: x-small; vertical-align: middle" id="gekkoEditorStatus_{V_ID}"></div>
</div>
