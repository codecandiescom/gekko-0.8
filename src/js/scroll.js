
// Accepts a list whose items are HTML Objects.
function gekkoScrollDisplay(objID, objList, span) {

	objElement = document.getElementById(objID);
	objElement.innerHTML = "";

	objList.itemIndex = 0;

	for (i = 0; i < span; i++)
		objElement.appendChild(listGetElement(objList, i));

}

// Moves the visual objID element up or down.
function gekkoScrollMove(objID, objList, items, span) {
	objElement = document.getElementById(objID);

	if (items < 0) {
		for (i = 0; i > items; i--) {
			objElement.removeChild(objElement.firstChild);
			objElement.appendChild(listGetElement(objList, span));
			objList.itemIndex = listGetPlace(objList, 1);
		}
	} else if (items > 0) {
		for (i = 0; i < items; i++) {
			objElement.removeChild(objElement.lastChild);
			objElement.insertBefore(listGetElement(objList, -1), objElement.firstChild);
			objList.itemIndex = listGetPlace(objList, -1);
		}
	}
}
