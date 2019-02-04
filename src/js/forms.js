gekkoForms = new Object();

gekkoForms.icon = function (input, size) {
	var parent = input.parentNode;

	// trying to find the icon object
	for (var i = 0; i < parent.childNodes.length; i++) {
		var brother = parent.childNodes[i];
		if (brother.nodeName.toLowerCase() == 'img')
			var icon = brother;
	}
	var buff_icon = document.buff.set(icon);
	var buff_input = document.buff.set(input);

	var popup = gekkoPopup.create('440px', '360px');
	gekkoPopup.setTitle(popup, _l('L_ICON_CHOOSER'));
	gekkoPopup.loadURL(popup, document.root+'tools.php?action=formIcon&size='+size+'&selected='+input.value);
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

gekkoForms.checkboxUpdate = function(form, buff_output) {

	while (form.nodeName.toLowerCase() != 'form')
		form = form.parentNode;

	var values = new Array();
	for (var i = 0; i < form.elements.length; i++)
		if (form.elements[i].checked)
			values.push(form.elements[i].value)

	values = values.join(', ');

	document.buff.get(buff_output).value = values;
}