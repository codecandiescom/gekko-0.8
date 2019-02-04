
gekkoEditor = new Object();

gekkoEditor.editorLevel = new Array();

gekkoEditor.init = function () {

	var i = new Number();
	var j = new Number();

	for (i = 0; i < document.forms.length; i++) {
		var boxes = new Array();
		for (j = 0; j < document.forms[i].elements.length; j++) {
			if (document.forms[i].elements[j].className.toLowerCase() == 'gekkoeditorsource') {
				var id = document.forms[i].elements[j].id;
				boxes.push(id.substr(id.indexOf('_')+1));
			}
		}
		if (boxes.length)
			eval("document.setEventListener(document.forms["+i+"], 'submit', function () { gekkoEditor.prepareCode('"+boxes.join('.')+"') })");
	}
}

//

gekkoEditor.handleKeys = function (e) {
	var obj = document.getEventTarget(e);

	while (obj.parentNode)
		obj = obj.parentNode;

	if (e.ctrlKey) {
		var charCode = String.fromCharCode(e.keyCode).toUpperCase();
		var cancel = true;
		switch (charCode) {
			case 'B': this.exec(obj.editorId, 'bold'); break;
			case 'U': this.exec(obj.editorId, 'underline'); break;
			case 'I': this.exec(obj.editorId, 'italic'); break;
			case 'S': this.exec(obj.editorId, 'strikethrough'); break;
			case 'W': this.exec(obj.editorId, 'code'); break;
			case 'X': this.action(obj.editorId); cancel = false; break;
			case 'Z': this.undo(obj.editorId); break;
			case 'Y': this.redo(obj.editorId); break;
			case 'K': this.exec(obj.editorId, 'gcreatelink', prompt(_l('L_URL'), 'http://')); break;
			default: cancel = false; break;
		}
		if (cancel)
			document.cancelEvent(e);
	} else {
		if ((obj.keyCount++)%10 == 0)
			this.action(obj.editorId);
	}
	return true;
}

gekkoEditor.designMode = function(obj) {
	try {
		gekkoEditor.setMode(obj.editorId, 'visual');

		switch (navigator.ident) {
			case 'msie':
				obj.body.contentEditable = true;
			break;
			default:
				obj.designMode = 'On';
			break;
		}

		obj.keyCount = new Number(0);
		
		this.setMode(obj.editorId, 'visual');

		window.setTimeout("gekkoEditor.autoSave('"+obj.editorId+"');", 20000);

		document.setEventListener(obj, 'keydown', function (e) { return gekkoEditor.handleKeys(e) });

	} catch (e) {
		gekkoEditor.setMode(obj.editorId, 'source');

		var source = document.getElementById('gekkoEditorSourceFrame_'+obj.editorId);

		document.getElementById('gekkoEditorSource_'+obj.editorId).value = '';

		for (i = 0; i < source.childNodes.length; i++) {
			var node = source.childNodes[i];
			if (node.nodeName.toLowerCase() == 'div' && node.className.toLowerCase() == 'buttons') {
				for (var j = 0; j < node.childNodes.length; j++) {
					var node1 = node.childNodes[j];
					if (node1.nodeName.toLowerCase() == 'button' && node1.className == 'visual') {
						node.removeChild(node1);
						break;
					}
				}
			}
		}
	}
}

gekkoEditor.setStatus = function (id, message) {
	var statusBar = document.getElementById('gekkoEditorStatus_'+id);
	statusBar.innerHTML = message;
}
gekkoEditor.autoSave = function (id) {
	
	gekkoEditor.setStatus(id, '<img src="'+document.root+'media/draft.gif" width="20" height="20" /> Savig emergency draft...');
	
	var req = new xmlHttp.init();

	if (req) {

		req.open('POST', document.root+'tools.php?action=editorFrame&auth='+GEKKO_AUTH_HASH+'&path='+encodeURIComponent(location.href.replace(/[a-z]*:\/\/[^\/]*\//, '/'))+'&editor_id='+id);
		req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

		req.setRequestHeader("Referer", document.root);


		switch (gekkoEditor.getMode(id)) {
			case 'visual':
				var data = document.getElementById('gekkoEditorVisual_'+id).contentWindow.document.body.innerHTML;
			break;
			case 'source':
				var data = document.getElementById('gekkoEditorSource_'+id).value;
			break;
		}

		req.send('data='+encodeURIComponent(data));

		window.setTimeout("gekkoEditor.setStatus("+id+", 'Emergency draft saved.');", 2000);
		window.setTimeout("gekkoEditor.setStatus("+id+", '');", 4000);
	}

	window.setTimeout("gekkoEditor.autoSave("+id+");", 20000);
}

gekkoEditor.setMode = function(id, mode) {

	var sourceFrame = document.getElementById('gekkoEditorSourceFrame_'+id);
	var visualFrame = document.getElementById('gekkoEditorVisualFrame_'+id);

	switch (mode) {
		case 'visual':
			this.update(id, 'visual');

			document.hide(sourceFrame);
			document.show(visualFrame);
		break;
		case 'source':
			if (this.getMode(id) == 'visual')
				this.update(id, 'source');

			document.hide(visualFrame);
			document.show(sourceFrame);
		break;
	}
}

gekkoEditor.getMode = function(id) {
	return (document.getElementById('gekkoEditorVisualFrame_'+id).style.display == 'block') ? 'visual' : 'source';
}

gekkoEditor.update = function(id, p) {
	var v = document.getElementById('gekkoEditorVisual_'+id).contentWindow;
	var s = document.getElementById('gekkoEditorSource_'+id);
	switch (p) {
		case 'visual': v.document.body.innerHTML = s.value; break;
		case 'source': s.value = v.document.body.innerHTML; break;
	}
}

gekkoEditor.prepareCode = function(ids) {
	ids = ids.split('.');
	while (ids.length) {
		var id = ids.pop();
		if (this.getMode(id) == 'visual')
			this.update(id, 'source');
	}
}
gekkoEditor.colors = new Array (
	'#000', '#333', '#666', '#888', '#999', '#aaa',
	'#ddd', '#008', '#00f', '#080', '#088', '#08f',
	'#0f0', '#0f8', '#0ff', '#f00', '#f08', '#f0f',
	'#f80', '#f88', '#f8f', '#fff'
);
gekkoEditor.exec = function(id, command, arg, silent) {

	if (this.getMode(id) != 'visual')
		return false;

	var obj = document.getElementById('gekkoEditorVisual_'+id).contentWindow;

	obj.focus();

	if (!silent)
		gekkoEditor.action(id);

	try {
		if (command == 'insertimage') {
			arg = prompt(_l('L_URL'), 'http://');
		} else if (command == 'createlink') {
			var url = prompt(_l('L_URL'), 'http://');
			if (url) {
				var sel = this.getSelectedHtml(id);
				gekkoEditor.exec(id, 'inserthtml', '<a href="'+url+'">'+(sel ? sel : url)+'</a>');
			}
			return;
		} else if (command == 'forecolor' && !arg) {
			this.colorPicker(id, 'forecolor');
			return;
		} else if (command == 'hilitecolor' && !arg) {
			this.colorPicker(id, 'hilitecolor');
			return;
		}
		obj.document.execCommand(command, 0, arg);
	} catch (e) {
		//try {
			switch (command) {
				case 'fullscreen':
					var editor = document.getElementById('gekkoEditor_'+id);
					var source = document.getElementById('gekkoEditorSource_'+id);
					var visual = document.getElementById('gekkoEditorVisual_'+id);

					if (typeof(editor.fullscreen) == 'undefined') {
						editor.style.zIndex = 1000;
						editor.fullscreen = 0;
					}

					editor.fullscreen = !editor.fullscreen;
					gekkoEditor.prepareCode(id);

					if (editor.fullscreen == true) {

						var fs = document.createElement('DIV');
						fs.style.display = 'none';

						fs.className = 'sw_class_0';
						fs.style.display = 'none';
						fs.style.position = 'absolute';
						fs.style.top = '0px';
						fs.style.left = '0px';
						fs.style.width = '100%';
						fs.style.height = '100%';

						editor.origWidth = parseInt(editor.style.width);
						editor.origHeight = parseInt(editor.offsetHeight);
						editor.origBrother = editor.nextSibling;
						editor.origScrollY = document.scrollY();
						editor.origScrollX = document.scrollX();

						source.origHeight = parseInt(source.offsetHeight);
						visual.origHeight = parseInt(visual.offsetHeight);

						fs.appendChild(editor);

						source.style.height = visual.style.height = '300px';

						editor.style.width = '95%';

						fs.style.display = 'block';

						document.body.appendChild(fs);

						window.scrollTo(0,0);

						document.body.style.overflow = 'hidden';
					} else {
						var fs = editor.parentNode;

						editor.style.width = editor.origWidth+'px';
						editor.style.height = editor.origHeight+'px';
						visual.style.height = visual.origHeight+'px';
						source.style.height = source.origHeight+'px';

						editor.origBrother.parentNode.insertBefore(editor, editor.origBrother);

						document.body.removeChild(fs);

						window.scrollTo(editor.origScrollX, editor.origScrollY);

						document.body.style.overflow = null;

					}
				break;
				case 'code':
					this.exec(id, 'removeformat', false, true);
					frag = this.createFragment(id, 'code', this.getSelectedText(id).replace('\n', '<br />'));
					this.exec(id, 'replacehtml', this.domToHtml(frag), true);
				break;
				case 'inserthtml':
					var range = obj.document.selection.createRange();
					range.pasteHTML(arg);
					range.collapse(false);
					range.select();
				break;
				case 'replacehtml':
					var sel = this.getSelection(id);
					var range = this.createRange(id, sel);
					range.deleteContents();
					this.exec(id, 'inserthtml', arg, true)
				break;
				case 'table':
					this.tableCreator(id, 5, 5);
				break;
				case 'smiley':
					var popup = gekkoPopup.create('230px', '250px');
					gekkoPopup.setTitle(popup, _l('L_SMILEY'));
					gekkoPopup.loadURL(popup, document.root+'tools.php?action=editorInsertSmiley&editor_id='+id);
					gekkoPopup.getBody(popup).onclick = function (e) {
						var target = document.getEventTarget(e);
						if (target.nodeName.toLowerCase() == 'img')
							gekkoEditor.exec(id, 'inserthtml', target.alt+' ');
					}
					gekkoPopup.emerge(popup);
				break;
				case 'quote':
					var sel = this.getSelectedHtml(id);
					gekkoEditor.exec(id, 'inserthtml', '<blockquote>'+(sel ? sel : '')+'</blockquote>');
				break;
				case 'insertobject':

					var sel = this.getSelectedHtml(id);

					var ext = arg.substr(arg.lastIndexOf('.')+1).toLowerCase();
					if (sel == "") {
						switch (ext) {
							case 'jpg': case 'jpeg': case 'png': case 'gif':
								gekkoEditor.exec(id, 'inserthtml', '<img src="'+arg+'" class="photo" />');
							break;
							case 'swf':
								gekkoEditor.exec(id, 'inserthtml', '<object width="425" height="350"><param name="movie" value="'+arg+'"></param><embed src="'+arg+'" type="application/x-shockwave-flash" width="425" height="350"></embed></object><br />');
							break;
							default:
								gekkoEditor.exec(id, 'inserthtml', '<a href="'+arg+'">'+arg.substr(arg.lastIndexOf('/')+1)+'</a>');
							break;
						}
					} else {
						gekkoEditor.exec(id, 'inserthtml', '<a href="'+arg+'">'+sel+'</a>');
					}
				break;
				case 'insertfile':
					var level = this.editorLevel[id];
					switch (level) {
						case 3:
							// owner
							window.open(document.root+'tools.php?action=editorUpload&editor_id='+id, '', 'width=680,height=400');
						break;
						default:
							// mortal

						break;
					}
				break;
			}
		//} catch (e) {
		//	alert('"'+command+'" command is not supported in this browser.');
		//}
	}

	return false;
}
gekkoEditor.tableCreator = function(id, cols, rows) {
	var container = document.createElement('div');
	container.className = 'gekkoEditorBox';

	var table = document.createElement('table');
	table.className = 'tableCreator';
	table.objs = Array();
	for (var i = 0; i < rows; i++) {
		var tr = document.createElement('tr');
		for (var j = 0; j < cols; j++) {
			var td = document.createElement('td');
			td.i = i;
			td.j = j;
			tr.appendChild(td);
		}
		table.appendChild(tr);
	}

	container.appendChild(table);
	var popup = gekkoPopup.create();
	gekkoPopup.getBody(popup).appendChild(container);
	gekkoPopup.getHead(popup).style.display = 'none';
	gekkoPopup.emerge(popup);

	table.onmousemove = function (e) {
		var target = document.getEventTarget(e);
		if (target.nodeName.toLowerCase() == 'td') {
			for (var i = 0; i < rows; i++) {
				for (var j = 0; j < cols; j++) {
					this.childNodes[i].childNodes[j].style.backgroundColor = (i <= target.i && j <= target.j) ? '#eee' : '#fff';
				}
			}
		}
	}

	table.onmousedown = function (e) {
		var target = document.getEventTarget(e);
		if (target.nodeName.toLowerCase() == 'td') {
			var table = document.createElement('table');
			table.className = 'data';
			for (i = 0; i <= target.i; i++) {
				var tr = document.createElement('tr');
				if (i == 0)
					tr.className = 'head';
				for (j = 0; j <= target.j; j++) {
					tr.appendChild(document.createElement('td'));
				}
				table.appendChild(tr);
			}
			gekkoEditor.exec(id, 'inserthtml', gekkoEditor.domToHtml(table));
			table.onmousemove = "";
			gekkoPopup.close(popup);
		}
	}
}
gekkoEditor.colorPicker = function(id, command) {
	var colorpicker = document.createElement('div');

	colorpicker.className = 'gekkoEditorBox';
	colorpicker.style.margin = '0px';
	colorpicker.style.padding = '0px';

	for (var i = 0; i < gekkoEditor.colors.length; i++) {
		var color = document.createElement('div');
		color.style.background = gekkoEditor.colors[i];
		color.className = 'color';
		colorpicker.appendChild(color);
		if (i%5 == 4)
			colorpicker.appendChild(document.createElement('br'));
	}

	colorpicker.onclick = function(e) {
		var target = document.getEventTarget(e);
		if (target.className == 'color') {
			gekkoEditor.exec(id, command, target.style.backgroundColor);
			gekkoPopup.close(popup);
		}
	}

	var popup = gekkoPopup.create();
	gekkoPopup.getBody(popup).appendChild(colorpicker);
	gekkoPopup.getHead(popup).style.display = 'none';
	gekkoPopup.emerge(popup);
}

gekkoEditor.colorFill = function (id) {
	var picker = document.getElementById('gekkoEditorColorPicker_'+id);
	picker.style.display = 'block';
	picker.onmouseup = function () { document.hide(this) };
}

gekkoEditor.getIframe = function (id) {
	return document.getElementById('gekkoEditorVisual_'+id);
}

gekkoEditor.getTextbox = function (id) {
	return document.getElementById('gekkoEditorSource_'+id);
}

gekkoEditor.createRange = function (id, s) {
	if (navigator.ident == 'msie') {
		return s.createRange();
	} else {
		var obj = this.getIframe(id);
		var range;
		obj.contentWindow.focus();
		try {
			range = s.getRangeAt(0);
		} catch (e) {
			range = obj.contentWindow.document.createRange();
		}
		return range;
	}
}

gekkoEditor.getSelection = function (id) {
	var obj = this.getIframe(id);
	if (navigator.ident == 'msie') {
		return obj.selection;
	} else {
		return obj.contentWindow.getSelection();
	}
}

gekkoEditor.selectNode = function (id, node) {
	var obj = this.getIframe(id);
	var sel = this.getSelection(id);
	var range = this.createRange(id, sel);;

	obj = obj.contentWindow.document;

	sel.removeAllRanges();

	range.selectNodeContents(node);

	sel.addRange(range);

	this.redraw(id);
}

gekkoEditor.undo = function (id) {
	var obj = this.getIframe(id);
	if (obj.undoHistory.length) {
		obj.redoHistory.push(obj.contentWindow.document.body.innerHTML);
		obj.contentWindow.document.body.innerHTML = obj.undoHistory.pop();
	}
}

gekkoEditor.redo = function (id) {
	var obj = this.getIframe(id);
	if (obj.redoHistory.length) {
		obj.undoHistory.push(obj.contentWindow.document.body.innerHTML);
		obj.contentWindow.document.body.innerHTML = obj.redoHistory.pop();
	}
}

gekkoEditor.action = function (id) {
	var obj = this.getIframe(id);

	if (!obj.undoHistory) obj.undoHistory = new Array();
	obj.redoHistory = new Array();

	obj.undoHistory.push(obj.contentWindow.document.body.innerHTML);
}

gekkoEditor.redraw = function (id) {
	var obj = this.getIframe(id);
	document.hide(obj);
	document.show(obj);
}

gekkoEditor.createFragment = function (id, name, html) {

	var obj = this.getIframe(id);
	obj = obj.contentWindow;

	var frag = obj.document.createDocumentFragment(name);
	var newDiv = obj.document.createElement('div');
	var newNode = obj.document.createElement(name);

	newNode.innerHTML = html;
	newDiv.appendChild(newNode);

	while (newDiv.firstChild)
		frag.appendChild(newDiv.firstChild);

	return frag;
}

gekkoEditor.isSingleTag = function (tag) {
	tag = tag.toLowerCase();
	if (tag == 'br' || tag == 'img' || tag == 'input' || tag == 'hr')
		return true;
	return false;
}
gekkoEditor.domToHtml = function(content) {
	var buff = new String("");

	var isTag = (content.nodeName.substr(0, 1) != '#');

	if (!isTag)
		if (content.nodeName == '#text')
			buff += content.nodeValue;

	if (isTag) {
		buff += '<'+content.nodeName.toLowerCase();

		if (content.attributes.length) {
			var attr = new Array();
			for (var i = 0; i < content.attributes.length; i++) {
				var name = content.attributes[i].nodeName;
				var value = content.attributes[i].nodeValue;
				value = value.replace(/-moz-.*;/, '');

				if (name.substr(0, 1) != '-')
					attr.push(name+'="'+value+'"');
			}
			buff += ' '+attr.join(' ');
		}

		if (this.isSingleTag(content.nodeName))
			buff += ' /';

		buff += '>';
	}

	for (var i = 0; i < content.childNodes.length; i++)
		buff += this.domToHtml(content.childNodes[i]);

	if (isTag && !this.isSingleTag(content.nodeName))
		buff += '</'+content.nodeName.toLowerCase()+'>';

	return buff;
}

gekkoEditor.getSelectedText = function (id) {
	var div = document.createElement('div');
	div.innerHTML = this.getSelectedHtml(id);
	return this.htmlToText(div);
}

gekkoEditor.htmlToText = function (node) {
	var buff = new String();
	switch (node.nodeName.toLowerCase()) {
		case '#text': buff += node.nodeValue+'\n'; break;
		case 'br': buff += '\n'; break;
	}
	if (node.childNodes)
		for (var j = 0; j < node.childNodes.length; j++)
			buff += this.htmlToText(node.childNodes[j]);
	return buff;
}

gekkoEditor.getSelectedHtml = function (id) {
	var sel = this.getSelection(id);
	var range = this.createRange(id, sel);
	if (navigator.ident == 'msie') {
		return range.htmlText;
	} else {
		var content = range.cloneContents();
		return this.domToHtml(content);
	}
	return false;
}

document.setEventListener(window, "load", function() { gekkoEditor.init(); });
