<textarea id="simpleEditor_{V_ID}" cols="20" rows="5" name="{V_FIELD}" style="width:{V_WIDTH};height:{V_HEIGHT};">{V_CONTENT}</textarea>
<!--if($V_ALLOWED_TAGS)--><div class="whisper"><b>{L_ALLOWED_TAGS}</b>: {V_ALLOWED_TAGS}</div><!--endif-->
<br />
<button type="button" onclick="previewContent('simpleEditor_{V_ID}'); return false;">{=createIcon("preview.png")=} {L_PREVIEW}</button><br />