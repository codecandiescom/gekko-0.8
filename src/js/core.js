	document.onTopIndex = 1000;

	document.onTop = function (obj) {
		obj.style.zIndex = document.onTopIndex++;
	}

	String.prototype.trim = function() {
		str = this.replace(/^\s+/, '');
		return str.replace(/\s+$/, '');
	};

	// OBJECT BUFFER
	document.buff = new Object();
	document.buff.objects = new Array();
	document.buff.set = function (obj) {
		// trying to find the object in the array
		for (var i = 0; i < document.buff.objects.length; i++) {
			if (obj == document.buff.objects[i]) return i;
		}
		// the object doesn't exists, adding it
		document.buff.objects.push(obj);
		return (document.buff.objects.length-1);
	}
	document.buff.get = function (id) {
		if (document.buff.objects[id])
			return document.buff.objects[id];
		else
			return false;
	}

	// DOCUMENT
	document.getEventTarget = function(e) {
		switch (navigator.ident) {
			case 'msie':
				return event ? event.srcElement : e.srcElement;
			break;
			default:
				return e.target;
			break;
		}
	}

	document.getElementPosition = function (object) {
		var top = new Number(0);
		var left = new Number(0);
		while (object.offsetParent) {
			top += object.offsetTop+(object.currentStyle ? parseInt(object.currentStyle.borderTopHeight).NaN0() : 0)
			left += object.offsetLeft+(object.currentStyle ? parseInt(object.currentStyle.borderTopWidth).NaN0() : 0);
			object = object.offsetParent;
		}
		top += object.offsetTop+(object.currentStyle ? parseInt(object.currentStyle.borderTopHeight).NaN0() : 0)
		left += object.offsetLeft+(object.currentStyle ? parseInt(object.currentStyle.borderTopWidth).NaN0() : 0);
		return {left:left, top:top};
	}

	document.getElementGeometry = function (object) {
		var geom = document.getElementPosition(object);
		geom.width = object.offsetWidth;
		geom.height = object.offsetHeight;
		return geom;
	}

	document.getEventPosition = function (e) {
		var top = new Number(0);
		var left = new Number(0);
		for (; e.offsetParent; e = e.offsetParent) {
			top += e.offsetTop;
			left += e.offsetLeft;
		}
		top += e.offsetTop;
		left += e.offsetLeft;
		return {x:left, y:top};
	}

	document.cancelEvent = function (e) {
		switch (navigator.ident) {
			case 'msie':
				e.cancelBubble = true;
				e.returnValue = false;
			break;
			default:
				e.preventDefault();
				e.stopPropagation();
			break;
		}
	}

	document.setEventListener = function(obj, event, callback) {
		switch (navigator.ident) {
			case 'msie':
				obj.attachEvent('on'+event, callback);
			break;
			default:
				obj.addEventListener(event, callback, false);
			break;
		}
		if (!obj.events)
			obj.events = new Array();
 		obj.events.push([event, callback]);
	}

	document.delEventListener = function(obj, event, callback) {
		switch (navigator.ident) {
			case 'msie':
				obj.detachEvent('on'+event, callback);
			break;
			default:
				obj.removeEventListener(event, callback, false);
			break;
		}
	}

	document.scrollY = function() {
		var scrolly = new Number(0);
		switch (navigator.ident) {
			case 'msie':
				scrolly = document.documentElement.scrollTop;
			break;
			case 'gecko':
				scrolly = self.pageYOffset;
			break;
			default:
				scrolly = document.body.scrollTop;
			break;
		}
		return scrolly;
	}

	document.scrollX = function() {
		var scrollx = new Number(0);
		switch (navigator.ident) {
			case 'msie':
				scrollx = document.documentElement.scrollLeft;
			break;
			case 'gecko':
				scrollx = self.pageXOffset;
			break;
			default:
				scrollx = document.body.scrollLeft;
			break;
		}
		return scrollx;
	}

	document.show = function(obj) {
		obj.style.display = 'block';
	}

	document.hide = function(obj) {
		obj.style.display = 'none';
	}

	// NAVIGATOR
	navigator.getEngine = function() {
		if (navigator.userAgent.indexOf('Gecko') > 0)
			return 'gecko';
		else if (navigator.userAgent.indexOf('MSIE') > 0) {
			// opera says it's MSIE 6.0 compatible? WTF?
			if (navigator.userAgent.indexOf('Opera') > 0) {
				return 'opera';
			} else {
				return 'msie';
			}
		}
	}
	navigator.ident = navigator.getEngine();

	function elementSetAlpha(obj, alpha) {
		try {
			obj.style.opacity = alpha;
		} catch(e) {}
	}

	function urlSetVariable(url, variable, value) {
		return url+((url.indexOf('?') > 0) ? '&' : '?')+variable+'='+encodeURIComponent(value);
	}

	function xmlGetCDATA(xml) {
		var u = new Number();
		for (u = 0; u < xml.childNodes.length; u++) {
			if (xml.childNodes[u].nodeName == '#cdata-section')
				return xml.childNodes[u].nodeValue;
		}
		return false;
	}

	function gekkoDocumentUpdate(xml, level) {

		var j = new Number();

		if (!level)
			var level = new Number(0);

		for (j = 0; j < xml.childNodes.length; j++)
			gekkoDocumentUpdate(xml.childNodes[j], level + 1);

		switch (level) {
			case 2:
				switch (xml.nodeName) {
					case "title":
						document.title = xml.childNodes[0].nodeValue;
					break;
					case "element":
						var element_id = xml.getAttribute('id');
						var obj = document.getElementById(element_id);

						if (obj) {
							var elementHTML = xmlGetCDATA(xml);

							if (obj.innerHTML.trim() != elementHTML.trim()) {
								obj.style.visibility = 'hidden';

								while (obj.firstChild)
									obj.removeChild(obj.lastChild);

								obj.innerHTML = elementHTML;
								obj.style.visibility = 'visible';
							}
						}
					break;
				}
			break;
		}
	}

	function gekkoAjaxLinkFollow(href, forget) {

		var req = xmlHttp.init();

		if (req) {
			req.onreadystatechange = function () {
				if (req.readyState == 4) {
					if (req.responseXML) {
						xml = req.responseXML.documentElement;

						if (!forget) {
							// trying to fix the back/forward buttons.
							var frame = document.getElementById('locationFrame');

							if (!frame) {
								frame = document.createElement('IFRAME');
								frame.id = 'locationFrame';
								frame.style.display = 'none';
								document.body.appendChild(frame);
							}

							frame.src = GEKKO_SITE_URL+'tools.php?action=ajaxhistory&url='+encodeURIComponent(href);
						}

						// updating Gekko
						document.ajaxhref = href;
						gekkoDocumentUpdate(xml);
						gekkoEditor.init();

						if (href.indexOf('#') > 0)
							document.location.href = href.substr(href.indexOf('#'));

						/*
						// focusless document hack for firefox
						button = document.createElement('BUTTON');
						button.innerHTML = 'focus';
						document.getElementById('container').insertBefore(button, document.getElementById('container').firstChild);
						button.focus();
						document.getElementById('container').removeChild(button);
*/

					} else {
						//alert(req.responseText);
					}
				}
			}
			req.open("POST", href, true);
			req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			req.setRequestHeader("Referer", document.ajaxhref);
			req.send("ajax=1");
		}
		return false;
	}

	function gekkoAjaxLink(obj) {

		// making sure that this is not a link to outside
		if (obj.href.substr(0, document.root.length) == document.root || obj.href.substr(0, 3) == "../")
			return gekkoAjaxLinkFollow(obj.href);

		return true;
	}

	document.gekkoAjaxHistory = function (href) {
		var frame = document.getElementById('locationFrame');
		var url = decodeURIComponent(href.substr(href.indexOf('url=')+4));
		if (document.ajaxhref != url)
			gekkoAjaxLinkFollow(url, true);
	}

	// Ok, AJAX is just a buzzword...
	function gekkoAjaxFormSubmit(obj) {
		try {
			var postVars = new Array();

			// this form doesn't want to use AJAX for some reason?
			if (obj.method.toLowerCase() == 'get' || (obj.disable_ajax && obj.disable_ajax.value == 1))
				return true;

			var submitButtons = new Number(0);
			// initial inspection
			for (i = 0; i < obj.elements.length; i++) {
				inputElement = obj.elements[i];

				var nodeName = inputElement.tagName.toLowerCase();
				var nodeType = inputElement.type.toLowerCase();

				switch (nodeName) {
					case 'input':
						// it is almost impossible (unless someone founds a security hole) to upload
						// files using javascript
						if (nodeType == 'file' || submitButtons > 1)
							return true;
						if (nodeType == 'submit')
							submitButtons++;
					break;
					case 'button':
						// do you know how to know what button did the user use to
						// submit his/her request?
						if (submitButtons > 1)
							return true;
						if (nodeType == 'submit')
							submitButtons++;
					break;
				}

				// is this a required field?
				parentNode = inputElement.parentNode;
				if (parentNode.tagName.toLowerCase() == "label" && parentNode.className == "required") {
					if (inputElement.value == "") {
						inputElement.focus();

						new Effect.Highlight(inputElement);

						return false;
					}
				}
			}

			// collecting data from form's input elements
			for (i = 0; i < obj.elements.length; i++) {
				inputElement = obj.elements[i];

				if (inputElement.name) {
					inputTag = inputElement.tagName.toLowerCase();

					// gekkoEditor must be updated before submission
					if (inputElement.className.toLowerCase() == 'gekkoeditorsource')
						gekkoEditor.prepareCode(inputElement.id.substr(inputElement.id.indexOf('_')+1));

					if (inputTag == "input" && (inputElement.type == "radio" || inputElement.type == "checkbox")) {
						if (inputElement.checked)
							postVars.push(inputElement.name+'='+encodeURIComponent(inputElement.value));
					} else {
						postVars.push(inputElement.name+'='+encodeURIComponent(inputElement.value));
					}
				}

				// disabling input elements (some of them may be already disabled)
				inputElement.disabled = new Number(inputElement.disabled) + 1;
			}

			// transparency effect
			elementSetAlpha(obj, 0.3);

			if (postVars.length > 0) {
				req = xmlHttp.init();
				if (req) {

					req.onreadystatechange = function () {

						if (req.readyState == 4) {

							if (obj.loading_img) {
								obj.parentNode.removeChild(obj.loading_img);
								obj.loading_img = 0;
							}
							
							// redirecting to another page
							if (req.responseText.substr(0, 9) == 'Location:') {

								var url = req.responseText.substr(10);

								if (url.indexOf('#') > 0)
									url = url.substr(0, url.indexOf('#'));

								location.href = url;
							} else {
								// response is an error message
								var errorMessage = req.responseText;
								var focusElement = "";

								// instruction "Focus:"
								if (errorMessage.substr(0, 6).toLowerCase() == 'focus:') {
									var lineBreak = errorMessage.indexOf('\n');
									var focusElement = errorMessage.substr(6, lineBreak - 6);
									focusElement = focusElement.replace(" ", "");
									errorMessage = errorMessage.substr(lineBreak+1);
								}

								// showing error
								if (obj.block_flag) {
									// small error messagebox for inside-block forms
									if (obj.error_block)
										obj.removeChild(obj.firstChild);

									var div = document.createElement('div');
									div.className = 'error';
									div.style.display = 'none';
									div.innerHTML = errorMessage;

									obj.insertBefore(div, obj.firstChild);

									Effect.Appear(div);

									obj.error_block = 1;
								} else {
									// real error messagebox
									mkMessageBox(obj, 'error', errorMessage);
								}

								// returning elements to its original status
								// and processing instructions
								elementSetAlpha(obj, 1);
								for (i = 0; i < obj.elements.length; i++) {
									var objElement = obj.elements[i];
									objElement.disabled -= 1;
									if (focusElement && objElement.name == focusElement) {
										objElement.focus();
										new Effect.Highlight(objElement);
									}
								}
							}
						}
					}

					/*
						For some stupid reason that i don't want to know,  those alerts are ALWAYS returning the same shit under that
						shitty browser (Microsoft Internet Explorer 6),  when i'm using element.getAttribute('foo') i want to _GET_ THE FUCKING 'foo' _ATTRIBUTE_
						of element NOT A STUPID CHILD named 'foo'. Thanks again lazy MSIE 6 developers!
						alert(obj.attributes['action']);
						alert(obj.getAttribute('action'));
						alert(obj.getAttributeNode('action'));
						alert(obj.action);
					*/

					// hack for MSIE, will work with all other browsers

					try {
						var ieSucks = obj.action.cloneNode(true);
						obj.removeChild(obj.action);
						var formAction = obj.getAttribute('action');
						obj.appendChild(ieSucks);
					} catch (e) {
						var formAction = obj.getAttribute('action');
					}


					// sending request
					req.open(obj.getAttribute('method'), (formAction.substr(0, 7) == 'http://') ? formAction : document.relurl+formAction.replace(/\.\.\//g, ''), true);
					req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					req.setRequestHeader("Referer", location.href);
					req.send(postVars.join('&')+'&ajax_flag=1');

					// removing error messagebox (if any)
					if (obj.error_block) {
						obj.removeChild(obj.firstChild)
						obj.error_block = 0;
					}

					if (obj.loading_img) {
						obj.parentNode.removeChild(obj.loading_img);
						obj.loading_img = 0;
					}

					var loadingDiv = document.createElement("DIV");
					loadingDiv.style.textAlign = 'center';
					loadingDiv.style.display = 'none';
					var loadingImg = document.createElement("IMG");
					loadingImg.src = document.root+'media/loading.gif';
					loadingDiv.appendChild(loadingImg);

					obj.parentNode.insertBefore(loadingDiv, obj);
					new Effect.Appear(loadingDiv);
					obj.loading_img = loadingDiv;
					
					return false;
				}
				return true;
			}
		} catch (e) {
			return true;
		}
	}
	function confirmAction(url, msg) {
		if (confirm(msg)) {
			location.href = url;
		}
	}
	function debug(str) {
		obj = document.getElementById('debug');
		if (!obj) {
			var obj = document.createElement('textarea');
			obj.id = 'debug';
			document.body.insertBefore(obj, document.body.firstChild)
		}
		obj.value += str+'\n';
	}

	function triggerDisplay(obj) {
		obj = document.getElementById(obj);
		obj.style.display = (obj.style.display == 'none') ? 'block' : 'none';
	}


	function _l(str) {
		return str;
	}

	function useStyleSheet(title) {
		var taglink;
		for (i = 0; (taglink = document.getElementsByTagName("link")[i]); i++) {
			if (taglink.getAttribute("rel") == "alternate stylesheet") {
				taglink.disabled = (taglink.getAttribute("title") == title) ? false : true;
			}
		}
	}

	function ddctrl(id, a) {
		//clearTimeout(ddtime);
		if (id) {
			var obj = document.getElementById(id);
			if (a) {
				obj.style.display='block';
			} else {
				ddtime = setTimeout("ddclear('"+id+"')", 200);
			}
		}
	}

	function ddisplay(obj, a) {
		var par = obj.parentNode;
		for (i = 0; i < obj.childNodes.length; i++) {
			var ul = obj.childNodes[i];
			if (ul.nodeName.toLowerCase() == 'ul') {
				ul.style.display = a ? 'block' : 'none';
			}
		}
	}

	function previewContent(obj_id) {

		var width = document.body.clientWidth;
		var popup = gekkoPopup.create();
		var container = gekkoPopup.getPopup(popup);

		container.style.margin = '10px';
		container.style.overflow = 'auto';
		container.style.left = (width/8)+'px';
		container.style.width = (6*width/8)+'px';
		container.style.height = '90%';

		gekkoPopup.loadURL(popup, document.root+'tools.php', 'action=preview&data='+encodeURIComponent(document.getElementById(obj_id).value));

		gekkoPopup.emerge(popup);
	}
	function mkMessageBox(target, messageType, messageText) {

		if (target.error_block)
			target.removeChild(target.firstChild);

		var div = document.createElement('DIV');
		div.style.display = 'none';
		
		switch (messageType) {
			case 'error':
				template = GEKKO_MESSAGE_ERROR;
			break;
		}

		div.innerHTML = template.replace('%s', messageText);
		
		target.insertBefore(div, target.firstChild);

		Effect.Appear(div);

		target.firstChild.focus();
		target.error_block = 1;
	}
	
	// tracking mouse position
	document.setEventListener(document, "mousemove", function(event) {
		switch (navigator.ident) {
			case 'msie': case 'opera':
				document.mouseX = window.event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft;
				document.mouseY = window.event.clientY + document.documentElement.scrollTop + document.body.scrollTop;
			break;
			default:
				document.mouseX = event.clientX + window.scrollX;
				document.mouseY = event.clientY + window.scrollY;
			break;
		}
	})
