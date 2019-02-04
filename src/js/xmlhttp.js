	var xmlHttp = new Object();

	xmlHttp.init = function() {
		var req = false;

		try {
			// gecko, opera
			req = new XMLHttpRequest();
		} catch (e) {
			// msie
			try {
				req = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				try {
					req = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {
					alert("Can't initialize AJAX request within this browser.");
				}
			}
		}

		return req;
	}

	xmlHttp.load = function(dest, doc, postvars) {
		var req = xmlHttp.init();

		if (req) {

			req.onreadystatechange = function() {
				if (req.readyState == 4)
					eval(dest+'=req.responseText;');
			}

			req.open(postvars ? 'POST' : 'GET', doc, true);

			if (postvars)
				req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

			req.send(postvars);

		} else {
			alert("I don't know how to handle AJAX request within this browser.");
		}
	}
