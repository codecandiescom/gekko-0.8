<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="templates/{C_SITE.TEMPLATE}/_layout/main.css" />
	<link rel="stylesheet" type="text/css" href="templates/{C_SITE.TEMPLATE}/_themes/{C_SITE.STYLESHEET}/theme.css" />
	<title>{L_GET_FILE}</title>

	<script type="text/javascript">
		var GEKKO_AUTH_HASH = {C_ANTIBOT};
		document.root = '{C_SITE.URL}';
	</script>

	<script type="text/javascript" src="{C_SITE.URL}js/core.js"></script>
	<script type="text/javascript" src="{C_SITE.URL}js/tab.js"></script>
	<script type="text/javascript" src="{C_SITE.URL}js/editor.js"></script>
	<script type="text/javascript" src="{C_SITE.URL}js/xmlhttp.js"></script>
	<script type="text/javascript" src="{C_SITE.URL}js/browser.js"></script>
	<script type="text/javascript">
		function formResult() {
			switch (document.choosenTool) {
				case 'filebrowser':
					var browser = document.getElementById('gekkoFileBrowser_0');
					if (browser.selected) {
						window.opener.document.getElementById('gekkoEditorVisual_'+'{V_EDITOR_ID}').contentWindow.focus();
						try {
						window.opener.gekkoEditor.exec('{V_EDITOR_ID}', 'insertobject', document.root.replace(/\/+$/, '')+'/data/'+browser.selected.replace(/^\/+/, ''));
						} catch(e) {};
						window.close();
					}
				break;
				case 'gallery':
					var browser = document.getElementById('gekkoGalleryBrowser_0');
					if (browser.selected) {

						window.opener.document.getElementById('gekkoEditorVisual_'+'{V_EDITOR_ID}').contentWindow.focus();
						try {
							window.opener.gekkoEditor.exec('{V_EDITOR_ID}', 'inserthtml', '<a href="'+browser.selected.media.src+'"><img class="photo" src="'+browser.selected.photo.src+'" width="'+browser.selected.photo.width+'" height="'+browser.selected.photo.height+'" /></a>');
						} catch(e) {};
						window.close();
					}
				break;
				default:
					var form = document.getElementById('formUpload');
					if (form.remote_file.value || form.local_file.value)
						return true;
				break;
			}
			return false;
		}

		document.choosenTool = new String();

		document.setEventListener(window, 'load', function () { gekkoBrowser.init('gekkoFileBrowser_0') });

		document.setEventListener(window, 'load', function () { gekkoGallery.init('gekkoGalleryBrowser_0') });
	</script>
</head>
<body style="margin: 0px; padding: 0px;">
	<div id="td_blockC">
	<div class="toolbox">
		<form id="formUpload" action="{C_SITE.URL}tools.php?action=editorUpload" onsubmit="return formResult();" method="post" enctype="multipart/form-data">
			<input type="hidden" name="editor_id" value="{V_EDITOR_ID}" />
			<input type="hidden" name="auth" value="{C_AUTH_STR}" />
			<div class="tab">
				<div class="head">
					<button class="selected" type="button" onclick="gekkoTab.show(0, this)">{L_FILE}</button>
					<button type="button" onclick="gekkoTab.show(1, this)">{L_FILEBROWSER}</button>
					<button type="button" onclick="gekkoTab.show(2, this)">{L_GALLERY}</button>
				</div>
				<div class="body" id="gekkoTabBody_0" style="display: block">
				<table>
					<tr>
						<td>
							<label>
							{L_URL}:
							<input class="text" type="text" name="remote_file" />
							</label>

							<label>
							{L_FILE}:
							<input type="file" name="local_file" />
							</label>
						</td>
						<td>
							<label>
							{L_LARGER_SIDE}: (px)
							<input class="text" type="text" name="longest_side" value="{=conf::getKey("misc", "thumb.longest_side", "250", "i")=}" size="5" />
							</label>
							<label>
							{L_POSITION}:
								<select name="position">
									<option value="">{L_DONT_ALIGN}</option>
									<option value="float: left;">{L_LEFT}</option>
									<option value="float: right;">{L_RIGHT}</option>
									<option value="vertical-align: middle;">{L_CENTER}</option>
								</select>
							</label>
							<input type="checkbox" name="createlink" value="1" checked="checked"> {L_LINK_TO_ORIGINAL}<br />
							<input type="checkbox" name="photo_style" value="1" checked="checked"> {L_PHOTO_STYLE}<br />
							<input type="checkbox" name="link_to_selected" value="1"> {L_LINK_TO_SELECTED}<br />
							<input type="checkbox" name="keep_size" value="1"> {L_KEEP_SIZE}<br />
						</td>
					</tr>
				</table>
			</div>
			<div class="body" id="gekkoTabBody_1" style="display: none">
				<div class="fileBrowser" id="gekkoFileBrowser_0">
					<div style="height: 60px; vertical-align: middle; padding: 6px; border: 1px solid #eee;">
						<div class="address">
							<input class="text" type="text" name="location" style="width: 400px;" />
						</div>
						<div class="navigator">
							<img src="{=fetchIcon('back.png', 16)=}" width="16" height="16" alt="{L_GO_BACK}" onclick="gekkoBrowser.back(this)" />
							<img src="{=fetchIcon('forward.png', 16)=}" width="16" height="16" alt="{L_GO_FORWARD}" onclick="gekkoBrowser.forward(this)" />
							<img src="{=fetchIcon('up.png', 16)=}" width="16" height="16" alt="{L_GO_UP}" onclick="gekkoBrowser.up(this)" />
							<img src="{=fetchIcon('reload.png', 16)=}" width="16" height="16" alt="{L_RELOAD}" onclick="gekkoBrowser.reload(this)" />
							<img src="{=fetchIcon('new_folder.png', 16)=}" width="16" height="16" alt="{L_NEW_FOLDER}" onclick="gekkoBrowser.newFolder(this)" />
							<img src="{=fetchIcon('new.png', 16)=}" width="16" height="16" alt="{L_NEW_FILE}" onclick="gekkoBrowser.newFile(this)" />

							<img src="{=fetchIcon('cut.png', 16)=}" width="16" height="16" alt="{L_CUT}" onclick="gekkoBrowser.cut(this)" />
							<img src="{=fetchIcon('copy.png', 16)=}" width="16" height="16" alt="{L_COPY}" onclick="gekkoBrowser.copy(this)" />
							<img src="{=fetchIcon('paste.png', 16)=}" width="16" height="16" alt="{L_PASTE}" onclick="gekkoBrowser.paste(this)" />
							<img src="{=fetchIcon('delete.png', 16)=}" width="16" height="16" alt="{L_DELETE}" onclick="gekkoBrowser.unlink(this)" />

							<img src="{=fetchIcon('download.png', 16)=}" width="16" height="16" alt="{L_DOWNLOAD}" onclick="gekkoBrowser.download(this)" />
							<img src="{=fetchIcon('upload.png', 16)=}" width="16" height="16" alt="{L_UPLOAD}" onclick="gekkoBrowser.upload(this)" />
						</div>
					</div>
					<div style="height: 210px;">
						<div class="browser" style="width:400px;height:200px;overflow:auto;float:left"></div>
						<div class="preview" style="width:150px;height:200px;float:left"></div>
					</div>
					<div>
						<div class="clipboard">
						</div>
					</div>
				</div>
			</div>
			<div class="body" id="gekkoTabBody_2" style="display: none; text-align:center">
				<div class="fileBrowser" id="gekkoGalleryBrowser_0">
					<div style="height: 20px; vertical-align: middle; padding: 6px; border: 1px solid #eee;">
						<div class="navigator" style="text-align: left">
							<img src="{=fetchIcon('back.png', 16)=}" width="16" height="16" alt="{L_GO_BACK}" onclick="gekkoGallery.back(this)" />
							<img src="{=fetchIcon('forward.png', 16)=}" width="16" height="16" alt="{L_GO_FORWARD}" onclick="gekkoGallery.forward(this)" />
							<img src="{=fetchIcon('reload.png', 16)=}" width="16" height="16" alt="{L_RELOAD}" onclick="gekkoGallery.reload(this)" />
						</div>
					</div>
					<div style="height: 200px" class="frame">
						<div class="browser" style="width:200px;height:200px;overflow:auto;float:left;"></div>
						<div class="browser" style="width:380px;height:200px;overflow:auto;float:left"></div>
					</div>
				</div>
			</div>
			<div class="buttons">
				<button type="submit">{=createIcon("submit.png")=} {L_SUBMIT}</button>
				<button type="button" onclick="window.close()">{=createIcon("cancel.png")=} {L_CANCEL}</button>
			</div>
		</form>
	</div>
	</div>
</body>
</html>
