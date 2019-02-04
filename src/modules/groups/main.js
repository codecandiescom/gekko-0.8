
	function fetchGroupsIds(list) {
		var groups = new Array();
		list = list.split(',');
		for (var i = 0; i < list.length; i++)
			groups.push(list[i].replace(/^[^(]*\(/, '').replace(/\)$/, ''));
		return groups.join(',');
	}

	function groupChooser(dest_input, auth_wizard) {
		var popup = gekkoPopup.create('620px', '350px');
		gekkoPopup.setTitle(popup, _l('L_GROUP_CHOOSER'));
		gekkoPopup.loadURL(popup, document.root+'tools.php?module=groups&action=chooser&selected='+escape(fetchGroupsIds(dest_input.value))+'&object_id='+document.buff.set(dest_input)+'&popup_id='+popup+(auth_wizard ? '&authwizard=1' : ''));
		gekkoPopup.emerge(popup);
	}