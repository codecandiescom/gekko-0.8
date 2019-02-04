gekkoModuleConf = new Object();

gekkoModuleConf.inputKeylist = null;

gekkoModuleConf.listKeys = function(module, obj) {
	var parent = obj.parentNode;
	var list;
	var i;
	var newKeyList = new Array();

	if (!gekkoModuleConf.inputKeylist) {
		var form = obj.parentNode;

		while (form.nodeName.toLowerCase() != 'form')
			form = form.parentNode;

		for (i = 0; i < form.elements.length; i++) {
			if (form.elements[i].nodeName.toLowerCase() == 'input' && form.elements[i].name == 'keylist')
				gekkoModuleConf.inputKeylist = form.elements[i];
		}
	}

	for (i = 0; i < parent.childNodes.length; i++) {
		var brother = parent.childNodes[i];
		if (brother.className == 'union') {
			list = brother;
			break;
		}
	}

	if (list.style.display == 'block') {

		var newKeyList = new Array();

		var toDel = new Array();
		while (list.firstChild) {
			if (list.firstChild.nodeName.toLowerCase() == 'label') {
				while (list.firstChild.firstChild) {
					if (list.firstChild.firstChild.nodeName.toLowerCase() == 'input') {
						// ok, hate me because i don't know how to use regexp in javascript...
						// here i am trying to remove element's id from inputKeylist (it is a
						// comma separated list)
						var name = list.firstChild.firstChild.name;
						var id = name.replace(/^.*\[/, '').replace(/\]$/, '');
						var keylist = ','+gekkoModuleConf.inputKeylist.value+',';
						keylist = keylist.replace(','+id+',', ',');
						keylist = keylist.replace(/^,*/, '').replace(/,*$/, '');
						gekkoModuleConf.inputKeylist.value = keylist;
					}
					list.firstChild.removeChild(list.firstChild.firstChild);
				}
			}
			list.removeChild(list.firstChild);
		}

		list.style.display = 'none';
	} else {
		list.style.display = 'block';

		var req = xmlHttp.init();

		if (req) {
			req.onreadystatechange = function() {
				if (req.readyState == 4) {

					newKeyList = gekkoModuleConf.inputKeylist.value.split(',');

					var keys = req.responseText.split('\n');
					keys.pop(); // ghost key
					for (var i = 0; i < keys.length; i++) {
						keys[i] = keys[i].split('|');
						var key = new Object();
						key.id = keys[i][0];
						key.name = keys[i][1];
						key.value = keys[i][2];
						key.type = keys[i][3];

						var entry = document.createElement('LABEL');
						var text = document.createElement('A');
						var input = document.createElement('INPUT');
						text.innerHTML = unescape(key.name);
						text.className = 'small';
						text.href = document.root+'index.php?module=admin&base=conf&action=edit_key&id='+key.id;
						input.className = 'text';
						input.value = unescape(key.value);

						input.name = 'key['+key.id+']';

						newKeyList.push(key.id);

						switch (key.type) {
							case '1':
								input.style.textAlign = 'right';
							break;
							case '2':
								input.type = 'checkbox';
								input.value = '1';
								if (key.value == 1)
									input.checked = 'checked';
							break;
						}

						entry.appendChild(text);
						entry.appendChild(input);

						list.appendChild(entry);
					}

					gekkoModuleConf.inputKeylist.value = newKeyList.join(',');
				}
			}
			req.open('GET', document.root+'tools.php?module=conf&action=list_keys&from='+module);
			req.send(0);
		}
	}
}