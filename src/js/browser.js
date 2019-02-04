gekkoGallery = new Object();
gekkoGallery.init = function (id) {
	var browser = document.getElementById(id);

	browser.history = new Object();
	browser.history.back = new Array();
	browser.history.forward = new Array();

	for (var i = 0; i < browser.childNodes.length; i++) {
		if (browser.childNodes[i].nodeName.toLowerCase() == 'div' && browser.childNodes[i].className.toLowerCase() == 'frame') {
			for (var j = 0; j < browser.childNodes[i].childNodes.length; j++) {
				var node = browser.childNodes[i].childNodes[j];
				if (node.className && node.className.toLowerCase() == 'browser') {
					if (browser.galleries) {
						browser.items = node;
						break;
					} else {
						browser.galleries = node;
					}
				}
			}
		}
	}

	gekkoGallery.list(0, browser);
}

gekkoGallery.getBrowser = function (element) {
	var browser = element;
	while (browser.className != 'fileBrowser')
		browser = browser.parentNode;
	return browser;
}

gekkoGallery.back = function(obj) {
	var browser = gekkoGallery.getBrowser(obj);
	if (browser.history.back.length) {
		browser.history.forward.push(browser.path);
		gekkoGallery.list(browser.history.back.pop(), browser);
	}
}

gekkoGallery.forward = function(obj) {
	var browser = gekkoGallery.getBrowser(obj);
	if (browser.history.forward.length) {
		browser.history.back.push(browser.path);
		gekkoGallery.list(browser.history.forward.pop(), browser);
	}
}

gekkoGallery.reload = function(obj) {
	var browser = gekkoGallery.getBrowser(obj);
	gekkoGallery.list(browser.gallery, browser);
}

gekkoGallery.list = function (gallery, browser) {

	var req = new xmlHttp.init();

	browser.gallery = gallery;

	gekkoBrowser.loading(browser.galleries);
	gekkoBrowser.loading(browser.items);

	if (req) {
		req.onreadystatechange = function () {
			if (req.readyState == 4) {
				if (req.responseXML) {
					gekkoGallery.populate(req.responseXML, browser);
				} else {
					browser.contents.innerHTML = req.responseText;
				}
			}
		}
		req.open('GET', document.root+'tools.php?module=gallery&action=list&gallery='+parseInt(gallery), true);
		req.send(null);
	} else {
		alert('Request failed.');
	}
}

gekkoGallery.populate = function (xml, browser) {

	var selected = new String();

	// removing contents
	while (browser.galleries.firstChild)
		browser.galleries.removeChild(browser.galleries.firstChild);

	while (browser.items.firstChild)
		browser.items.removeChild(browser.items.firstChild);

	for (; xml.firstChild; xml.removeChild(node)) {
		var node = xml.firstChild;
		if (node.nodeName == 'content') {
			for (; node.firstChild; node.removeChild(node1)) {
				node1 = node.firstChild;
				var node2;
				switch (node1.nodeName.toLowerCase()) {
					case 'item':

						var photo = document.createElement('IMG');
						photo.className = 'photo';

						for (; node1.firstChild; node1.removeChild(node2)) {
							node2 = node1.firstChild;
							switch (node2.nodeName.toLowerCase()) {
								case 'title':
									var title = document.createElement('H4');
									title.innerHTML = xmlGetCDATA(node2);
								break;
								case 'description':
									photo.alt = xmlGetCDATA(node2);
								break;
								case 'thumbnail':
									photo.src = node2.getAttribute('url');
									photo.width = node2.getAttribute('width');
									photo.height = node2.getAttribute('height');
								break;
								case 'media':
									media = new Object();
									media.src = node2.getAttribute('url');
									media.width = node2.getAttribute('width');
									media.height = node2.getAttribute('height');
								break;
							}
						}

						var container = document.createElement('DIV');
						container.itemId = node1.getAttribute('id');

						container.appendChild(photo);
						container.appendChild(title);
						container.className = 'file';
						container.photo = photo;
						container.media = media;
						container.title = title;
						container.style.width = (parseInt(photo.width)+10)+'px';
						container.style.height = (parseInt(photo.height)+50)+'px';
						container.style.padding = '4px';
						container.style.margin = '4px';
						browser.items.appendChild(container);
					break;
					case 'gallery':

						var photo = document.createElement('IMG');
						photo.className = 'photo';

						for (; node1.firstChild; node1.removeChild(node2)) {
							node2 = node1.firstChild;
							switch (node2.nodeName.toLowerCase()) {
								case 'title':
									var title = document.createElement('H3');
									title.innerHTML = xmlGetCDATA(node2);
								break;
								case 'description':
									photo.alt = xmlGetCDATA(node2);
								break;
								case 'thumbnail':
									photo.src = node2.getAttribute('url');
									photo.width = node2.getAttribute('width');
									photo.height = node2.getAttribute('height');
								break;
							}
						}

						var container = document.createElement('DIV');
						container.galleryId = node1.getAttribute('id');
						container.appendChild(photo);
						container.appendChild(title);
						container.className = 'file';
						container.style.width = (parseInt(photo.width)+10)+'px';
						container.style.height = (parseInt(photo.height)+50)+'px';
						container.style.padding = '4px';
						container.style.margin = '4px';
						browser.galleries.appendChild(container);
					break;
				}
			}
		}
	}

	browser.galleries.onmousedown = function(e) {
		var target = document.getEventTarget(e);
		var item = target;

		while (item.className != 'file')
			item = item.parentNode;

		if (item) {
			if (typeof(item.galleryId) != 'undefined') {
				browser.history.back.push(browser.gallery);
				gekkoGallery.list(item.galleryId, browser);
			}
		}
	}

	browser.items.onmousedown = function(e) {
		var target = document.getEventTarget(e);
		var item = target;

		while (item.className != 'file')
			item = item.parentNode;

		var browser = gekkoBrowser.getBrowser(item);
		if (item) {
			if (typeof(item.itemId) != 'undefined') {
				document.choosenTool = 'gallery';
				browser.selected = item;
				formResult();
			}
		}
	}
}

gekkoBrowser = new Object();

gekkoBrowser.init = function (id) {

	var browser = document.getElementById(id);

	browser.history = new Object();
	browser.history.back = new Array();
	browser.history.forward = new Array();

	// mapping components
	for (var i = 0; i < browser.childNodes.length; i++) {
		var sect = browser.childNodes[i];
		if (sect.nodeName.toLowerCase() == 'div') {
			for (var j = 0; j < sect.childNodes.length; j++) {
				var comp = sect.childNodes[j];
				if (comp.nodeName.toLowerCase() == 'div') {
					switch (comp.className) {
						case 'address':
							for (var k = 0; k < comp.childNodes.length; k++) {
								var input = comp.childNodes[k];
								if (input.name == 'location')
									browser.location = input;
							}
						break;
						case 'navigator':
						break;
						case 'browser':
							browser.contents = comp;
						break;
						case 'preview':
							browser.preview = comp;
						break;
						case 'clipboard':
							browser.clipboard = comp;
						break;
					}
				}
			}
		}
	}

	gekkoBrowser.list('/', browser);
}

gekkoBrowser.details = function (xml, browser) {

	while (browser.preview.firstChild)
		browser.preview.removeChild(browser.preview.firstChild);

	for (; xml.firstChild; xml.removeChild(node)) {
		var node = xml.firstChild;
		if (node.nodeName == 'filelist') {
			for (; node.firstChild; node.removeChild(node1)) {
				node1 = node.firstChild;
				if (node1.nodeName == 'file') {
					var file = new Object();
					for (; node1.firstChild; node1.removeChild(node2)) {
						node2 = node1.firstChild;
						switch (node2.nodeName) {
							case 'thumbnail':
								file.thumbnail = xmlGetCDATA(node2);
							break;
							case 'name':
								file.name = xmlGetCDATA(node2);
							break;
							case 'size':
								file.size = xmlGetCDATA(node2);
							break;
						}
					}

					if (file.thumbnail) {
						var img = document.createElement('img');
						img.src = file.thumbnail;
						browser.preview.appendChild(img);
					}

					var info = document.createElement('div');

					var name = document.createElement('b');
					name.innerHTML = file.name;
					info.appendChild(name);

					// TODO: file.size & stuff

					browser.preview.appendChild(info);
				}
			}
		}
	}
}

gekkoBrowser.select = function (file) {
	var browser = gekkoBrowser.getBrowser(file);

	for (var i = 0; i < browser.contents.childNodes.length; i++)
		browser.contents.childNodes[i].style.backgroundColor='';

	file.style.backgroundColor = '#eee';

	browser.selectedFile = file;

	document.choosenTool = 'filebrowser';

	browser.selected = browser.path.replace(/\/+$/, '')+'/'+file.path;

	if (!file.isDirectory)
		gekkoBrowser.preview(browser.selected, browser);
}

gekkoBrowser.populate = function (xml, browser) {

	var selected = new String();

	if (browser.target) {
		selected = browser.target.path;
		browser.target = '';
		browser.selected = '';
	}

	// removing contents
	while (browser.contents.firstChild)
		browser.contents.removeChild(browser.contents.firstChild);

	for (; xml.firstChild; xml.removeChild(node)) {
		var node = xml.firstChild;
		if (node.nodeName == 'filelist') {
			for (; node.firstChild; node.removeChild(node1)) {
				node1 = node.firstChild;

				switch (node1.nodeName) {
					case 'parent':
						browser.path = xmlGetCDATA(node1);
						browser.location.value = browser.path;
					break;
					case 'files':

						for (; node1.firstChild; node1.removeChild(node2)) {
							var node2 = node1.firstChild;
							if (node2.nodeName == 'file') {

								var file = document.createElement('div');
								var name = document.createElement('span');

								file.className = 'file';
								file.path = xmlGetCDATA(node2);

								name.innerHTML = file.path;

								if (name.innerHTML.length > 18)
									name.innerHTML = name.innerHTML.substr(0, 15)+'...';

								for (var i = 0; i < node2.attributes.length; i++) {
									var attr = node2.attributes[i];
									switch (attr.nodeName) {
										case 'isDirectory':
											file.isDirectory = parseInt(attr.nodeValue);
										break;
										case 'icon':
											file.icon = attr.nodeValue;
										break;
										case 'size':
											file.title = parseInt(attr.nodeValue);
										break;
										case 'thumbnail':
											if (attr.nodeValue) {
												file.thumbnail = new Object();
												var thumb = attr.nodeValue.split(':');
												file.thumbnail.src = thumb[0];
												file.thumbnail.width = thumb[1];
												file.thumbnail.height = thumb[2];
											}
										break;
									}
								}


								if (file.thumbnail) {
									/*
									file.style.backgroundImage = 'url('+document.root+file.thumbnail+')';
									file.style.backgroundRepeat = 'no-repeat';
									name.style.backgroundColor = '#fff';
									*/
									var img = document.createElement('img');
									img.src = file.thumbnail.src;
									img.width = file.thumbnail.width/2;
									img.height = file.thumbnail.height/2;
									img.style.marginLeft = 'auto';
									img.style.marginRight = 'auto';
									img.style.display = 'block';
									file.appendChild(img);
								}

								var icon = document.createElement('img');
								icon.src = file.icon;
								file.appendChild(icon);

								file.appendChild(name);

								browser.contents.appendChild(file);


								if (selected && file.path == selected)
									gekkoBrowser.select(file);

							}
						}
					break;
				}

			}
		}
	}

	browser.location.onkeydown = function (e) {
		var target = document.getEventTarget(e);
		if (e.keyCode == 13) {
			var browser = gekkoBrowser.getBrowser(target);
			gekkoBrowser.list(browser.location.value, browser);
			document.cancelEvent(e);
		}
	}

	// secondary click
	browser.contents.oncontextmenu = function(e) {
		var target = document.getEventTarget(e);

		if (target.nodeName.toLowerCase() != 'div') {
			var file = target.parentNode;
		} else {
			var file = target;
		}

		gekkoBrowser.select(file);

		document.cancelEvent(e);
	};

	// mousedown
	browser.contents.onmousedown = function (e) {

		var target = document.getEventTarget(e);

		document.cancelEvent(e);

		if (target.nodeName.toLowerCase() != 'div') {
			var file = target.parentNode;
		} else {
			var file = target;
		}

		switch (e.which) {
			case 1:
				// normal click
				if (file.className == 'file') {
					var browser = gekkoBrowser.getBrowser(file);
					browser.target = file;

					if (file.isDirectory) {

						browser.history.back.push(browser.path);
						gekkoBrowser.list(browser.path.replace(/\/+$/, '')+'/'+file.path, browser);

					} else {

						gekkoBrowser.select(file);

						var coords = document.getEventPosition(target);
						coords.x = document.mouseX - coords.x;
						coords.y = document.mouseY - coords.y;

						// dragging
						var onmousemove = function (e) {
							e = document.getEventTarget(e);
							p = document.getEventPosition(e);
							file.style.position = 'absolute';
							file.style.opacity = 0.5;
							file.style.left = (document.mouseX - coords.x)+'px';
							file.style.top = (document.mouseY - coords.y)+'px';
							return false;
						}

						// end of dragging
						var onmouseup = function (e) {
							var target = document.getEventTarget(e);

							if (target.nodeName.toLowerCase() != 'div')
								var target = target.parentNode;

							if (target.className == 'file') {
								if (target.isDirectory) {
									// move contents
									gekkoBrowser.move(target, file.path, target.path);
								} else {
									// swap positions
									target.parentNode.insertBefore(file, target);
								}
							}

							file.style.position = 'static';
							file.style.opacity = 1;

							document.delEventListener(document, 'mousemove', onmousemove);
							document.delEventListener(document, 'mouseup', onmouseup)
						}

						document.setEventListener(document, 'mouseup', onmouseup);
						document.setEventListener(document, 'mousemove', onmousemove);
					}
				}
			break;
			case 2:
				// copy
				gekkoBrowser.copy(file);
			break;
			case 3:
				// context menu
			break;
		}
	}
}

gekkoBrowser.getBrowser = function (element) {
	var browser = element;
	while (browser.className != 'fileBrowser')
		browser = browser.parentNode;
	return browser;
}

gekkoBrowser.move = function(obj, source, dest) {
	var browser = this.getBrowser(obj);
	gekkoBrowser.list(browser.path, browser, '&command=move&argv[]='+encodeURIComponent(source)+'&argv[]='+encodeURIComponent(dest));
}

gekkoBrowser.back = function(obj) {
	var browser = this.getBrowser(obj);
	if (browser.history.back.length) {
		browser.history.forward.push(browser.path);
		gekkoBrowser.list(browser.history.back.pop(), browser);
	}
}

gekkoBrowser.forward = function(obj) {
	var browser = this.getBrowser(obj);
	if (browser.history.forward.length) {
		browser.history.back.push(browser.path);
		gekkoBrowser.list(browser.history.forward.pop(), browser);
	}
}

gekkoBrowser.reload = function(obj) {
	var browser = this.getBrowser(obj);
	gekkoBrowser.list(browser.path, browser);
}

gekkoBrowser.download = function (obj) {
	var browser = this.getBrowser(obj);
	var url = prompt(_l('L_URL'));
	if (url)
		gekkoBrowser.list(browser.path, browser, '&command=download&argv[]='+encodeURIComponent(url));
}

gekkoBrowser.upload = function (obj) {

	var browser = this.getBrowser(obj);

	var iframe = document.createElement('iframe'); // sorry again w3c validator
	iframe.id = 'frameUpload';
	var buff = document.buff.set(iframe);

	iframe.style.position = 'absolute';
	iframe.style.overflow = 'hidden';
	iframe.style.top = iframe.style.left = '0px';
	iframe.style.width = iframe.style.height = '100%';
	iframe.style.border = '0';
	iframe.style.backgroundColor = '#fff';

	iframe.src = document.root+'tools.php?action=simpleUpload&path='+browser.path+'&buff='+buff+'&browser='+document.buff.set(browser);

	document.body.appendChild(iframe);
}

gekkoBrowser.up = function (obj) {
	var browser = this.getBrowser(obj);
	var path = browser.path.replace(/\/+$/, '');
	if (path) {
		path = path.substr(0, path.lastIndexOf('/'));
		gekkoBrowser.list(path ? path : '/', browser);
	}
}

gekkoBrowser.copy = function (obj) {
	var browser = this.getBrowser(obj);

	var newNode = browser.selectedFile.cloneNode(true);

	while (browser.clipboard.firstChild)
		browser.clipboard.removeChild(browser.clipboard.firstChild);

	browser.clipboard.appendChild(newNode);
	browser.clipboard.path = browser.selected;
	browser.clipboard.action = 'copy';
}

gekkoBrowser.cut = function (obj) {
	var browser = this.getBrowser(obj);

	while (browser.clipboard.firstChild)
		browser.clipboard.removeChild(browser.clipboard.firstChild);

	browser.clipboard.appendChild(browser.selectedFile);
	browser.clipboard.path = browser.selected;
	browser.clipboard.action = 'cut';
}

gekkoBrowser.paste = function (obj) {
	var browser = this.getBrowser(obj);

	if (browser.clipboard.action)
		gekkoBrowser.list(browser.path, browser, '&command='+browser.clipboard.action+'&argv[]='+encodeURIComponent(browser.clipboard.path)+'&argv[]='+encodeURIComponent(browser.path));

	if (browser.clipboard.action == 'cut')
		while (browser.clipboard.firstChild)
			browser.clipboard.removeChild(browser.clipboard.firstChild);

}

gekkoBrowser.newFolder = function (obj) {
	var browser = this.getBrowser(obj);
	var directory = prompt(_l('L_NEW_DIRECTORY'));
	if (directory)
		gekkoBrowser.list(browser.path, browser, '&command=mkdir&argv[]='+encodeURIComponent(directory));
}

gekkoBrowser.newFile = function (obj) {
	var browser = this.getBrowser(obj);
	var filename = prompt(_l('L_NEW_FILE'));
	if (filename)
		gekkoBrowser.list(browser.path, browser, '&command=mkfile&argv[]='+encodeURIComponent(filename));
}

gekkoBrowser.unlink = function (obj) {
	var browser = this.getBrowser(obj);
	gekkoBrowser.list(browser.path, browser, '&command=delete&argv[]='+encodeURIComponent(browser.selected));
}

gekkoBrowser.loading = function(element) {

	while (element.firstChild)
		element.removeChild(element.firstChild);

	var loading = document.createElement('img');
	loading.src = document.root+'media/loading.gif';
	element.appendChild(loading);
}

gekkoBrowser.preview = function (file, browser) {
	var req = new xmlHttp.init();

	this.loading(browser.preview);

	if (req) {
		req.onreadystatechange = function () {
			if (req.readyState == 4) {
				if (req.responseXML) {

					gekkoBrowser.details(req.responseXML, browser);

				} else {
					browser.contents.innerHTML = req.responseText;
				}
			}
		}
		req.open('GET', document.root+'tools.php?module=files&action=list&path='+encodeURIComponent(browser.selected), true);
		req.send(null);
	} else {
		alert('request failed.');
	}

}

gekkoBrowser.list = function (path, browser, command) {

	var req = new xmlHttp.init();

	if (!command)
		command = new String('');

	this.loading(browser.contents);

	if (!path)
		path = '/';

	if (req) {
		req.onreadystatechange = function () {
			if (req.readyState == 4) {
				if (req.responseXML) {
					gekkoBrowser.populate(req.responseXML, browser);

					var iframe = document.getElementById('frameUpload');
					if (iframe)
						document.body.removeChild(iframe);
				} else {
					browser.contents.innerHTML = req.responseText;
				}
			}
		}
		req.open('GET', document.root+'tools.php?module=files&action=list&path='+encodeURIComponent(path)+command, true);
		req.send(null);
	} else {
		alert('Request failed.');
	}
}