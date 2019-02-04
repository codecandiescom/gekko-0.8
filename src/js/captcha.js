	document.jsfilter = new Object();
	document.jsfilter.passed = false;

	document.jsfilter.test = function() {
		if (document.jsfilter.passed == false && document.forms && document.forms.length) {
			for (i = 0; i < document.forms.length; i++) {
				var currForm = document.forms[i];
				if (!currForm.auth || !currForm.auth.value) {
					var newInput = document.createElement('input');
					newInput.name = 'auth';
					newInput.style.display = 'none';
					newInput.value = GEKKO_AUTH_HASH;
					currForm.appendChild(newInput);
				}
			}
			document.jsfilter.passed = true;
		}
	}

	document.setEventListener(document, "mousedown", document.jsfilter.test);
	document.setEventListener(document, "keydown", document.jsfilter.test);
	document.setEventListener(document, "focus", document.jsfilter.test);
