
	document.contextMenu = null;

	document.setEventListener(window, 'mousedown',
		function (e) {
			var obj = document.getEventTarget(e);
			if (document.contextMenu) {
				while (obj) {
					if (obj == document.contextMenu)
						return;
					obj = obj.parentNode;
				}
				document.contextMenu.style.display = 'none';
			}
			document.contextMenu = null;
		}
	);

	document.setEventListener(window, 'load', function() {
			var divs = document.getElementsByTagName('DIV');
			for (var i = 0; i < divs.length; i++) {
				var div = divs[i];
				if (div.className == 'gekkoContextMenu') {

					cont = div.parentNode;
					
					var contextMenu = div;
					contextMenu.style.position = 'absolute';
					cont.contextMenu = contextMenu;

					document.setEventListener (
						cont,
						'contextmenu',
						function (e) {
							var obj = document.getEventTarget(e);

							while (obj && !obj.contextMenu) {
								if (obj.nodeName.toLowerCase() == 'a')
									return;
									
								obj = obj.parentNode;
							}

							if (obj) {
								document.body.appendChild(obj.contextMenu);
	
								obj.contextMenu.style.top = document.mouseY+'px';
								obj.contextMenu.style.left = document.mouseX+'px';
								obj.contextMenu.style.zIndex = '1000';
								obj.contextMenu.style.display = 'block';
								obj.contextMenu.style.visibility = 'visible';
	
								document.contextMenu = obj.contextMenu;
								
								document.cancelEvent(e);
							}
							
							return false;
						}
					);
				}
			}
		}
	);

