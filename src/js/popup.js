
gekkoPopup = new Object();

gekkoPopup.counter = new Number(0);

gekkoPopup.getPopup = function (id) {
	return document.getElementById('gekkoPopup_'+id);
}
gekkoPopup.getBody = function (id) {
	return document.getElementById('gekkoPopupBody_'+id);
}
gekkoPopup.getStatus = function (id) {
	return document.getElementById('gekkoPopupStatus_'+id);
}
gekkoPopup.getTitle = function (id) {
	return document.getElementById('gekkoPopupTitle_'+id);
}
gekkoPopup.getHead = function (id) {
	return document.getElementById('gekkoPopupHead_'+id);
}
gekkoPopup.emerge = function (id) {
	obj = this.getPopup(id);

	obj.style.visibility = 'hidden';
	obj.style.display = 'block';

	var left = (document.mouseX - obj.offsetWidth/2)+'px';
	var top = (document.scrollY()+80)+'px';

	if (!obj.style.width) {
		left = (document.mouseX-10)+'px'
		top = (document.mouseY-10)+'px';

		var body = this.getBody(id);
	}

	obj.style.left = left;
	obj.style.top = top;
	
	obj.style.visibility = 'visible';
	
	Effect.Appear(obj);

	gekkoPopup.getBody(id).style.height = (obj.offsetHeight-gekkoPopup.getHead(id).offsetHeight-30)+'px';

	document.onTop(obj);
}
gekkoPopup.setTitle = function (id, title) {
	obj = this.getTitle(id);
	obj.innerHTML = title;
}
gekkoPopup.create = function (width, height) {
	var container = document.createElement('div');
	var head = document.createElement('div');
	var body = document.createElement('div');
	var button = document.createElement('img');
	var title = document.createElement('span');
	var id = this.counter++;

	container.id = 'gekkoPopup_'+id;
	container.popupId = id;
	container.className = 'popup';
	container.style.left = '20px';
	container.style.top = (document.scrollY()+22)+'px';
	container.style.display = 'none';

	head.id = 'gekkoPopupHead_'+id;
	head.className = 'head';

	button.src = GEKKO_ICON_CLOSE;
	button.className = 'button';
	button.onclick = function () { gekkoPopup.close(id) };
	head.appendChild(button);

	title.id = 'gekkoPopupTitle_'+id;
	title.innerHTML = 'popup';
	head.appendChild(title);

	body.id = 'gekkoPopupBody_'+id;
	body.className = 'body';

	if (width)
		container.style.width = width;

	if (height)
		container.style.height = height;

	container.appendChild(head);
	container.appendChild(body);

	if (width) {
		var status = document.createElement('div');
		status.className = 'status';
		status.id = 'gekkoPopupStatus_'+id;

		var resize = document.createElement('div');
		resize.className = 'button';
		resize.style.width = resize.style.height = '16px';
		resize.onmousedown = function (e) { gekkoPopup.startResize(e) };
		status.appendChild(resize);

		container.appendChild(status);
	}

	document.body.appendChild(container);

	container.onmousedown = function (e) {
		var container = document.getEventTarget(e);
		while (container.className != 'popup')
			container = container.parentNode;
		document.onTop(container);
	}
	
	new Draggable(container, {scroll: window, handle: head});

	return id;
}

gekkoPopup.close = function (id) {
	obj = this.getPopup(id);
	Effect.Fade(obj, {afterFinish: function() { document.body.removeChild(obj); } });
}

gekkoPopup.startResize = function (e) {

	var button = document.getEventTarget(e);

	var container = button;
	while (container.className != 'popup')
		container = container.parentNode;

	var body = gekkoPopup.getBody(container.popupId);

	container.origW = container.offsetWidth-document.mouseX;
	container.origH = container.offsetHeight-document.mouseY;

	body.origW = body.offsetWidth-document.mouseX;
	body.origH = body.offsetHeight-document.mouseY;

	container.resize = function () {
		var width = container.origW+document.mouseX;
		var height = container.origH+document.mouseY;
		if (width > 200 && height > 200) {
			container.style.width = width+'px';
			container.style.height = height+'px';

			body.style.height = (body.origH+document.mouseY)+'px';
			body.style.height = (body.origH+document.mouseY)+'px';
		}
	}

	document.setEventListener(document, "mousemove", container.resize);
	document.setEventListener(document, "mouseup",
		function () {
			document.delEventListener(document, "mousemove", container.resize)
		}
	);
}

gekkoPopup.test = function() {
	var pop = gekkoPopup.create('200px', '200px');
	gekkoPopup.setTitle(pop, 'Xiam Skywalker');
	gekkoPopup.loadURL(pop, 'http://localhost?fake=xiam_skywalker');
	gekkoPopup.emerge(pop);
}

gekkoPopup.loadURL = function (id, url, postvars) {
	xmlHttp.load('gekkoPopup.getBody('+id+').innerHTML', url, postvars);
}