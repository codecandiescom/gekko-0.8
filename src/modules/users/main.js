function avatarChooser(input) {
	var parent = input.parentNode;

	// trying to find the icon object
	for (var i = 0; i < parent.childNodes.length; i++) {
		var brother = parent.childNodes[i];
		if (brother.nodeName.toLowerCase() == 'img')
			var icon = brother;
	}
	var buff_icon = document.buff.set(icon);
	var buff_input = document.buff.set(input);

	var popup = gekkoPopup.create('480px', '360px');
	gekkoPopup.setTitle(popup, _l('L_AVATAR_CHOOSER'));
	gekkoPopup.loadURL(popup, document.root+'tools.php?module=users&action=avatar&selected='+input.value+'&input_id='+buff_input+'&icon_id='+buff_icon+'&popup_id='+popup);
	gekkoPopup.emerge(popup);

	gekkoPopup.getBody(popup).onclick = function (e) {
		var target = document.getEventTarget(e);
		if (target.nodeName.toLowerCase() == 'img') {
			document.buff.get(buff_icon).src = target.src;
			document.buff.get(buff_input).value = target.alt;
			gekkoPopup.close(popup);
		}
	};
}
