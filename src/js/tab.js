
	gekkoTab = new Object;
	gekkoTab.show = function (id, button) {
		for (var i = 0; document.getElementById('gekkoTabBody_'+i); i++)
			document.getElementById('gekkoTabBody_'+i).style.display = 'none';
		document.getElementById('gekkoTabBody_'+id).style.display = 'block';

		var parent = button.parentNode;
		for (i = 0; i < parent.childNodes.length; i++) {
			var brother = parent.childNodes[i];
			if (brother.nodeName.toLowerCase() == 'button')
				brother.className = '';
		}
		button.className = 'selected';
	}