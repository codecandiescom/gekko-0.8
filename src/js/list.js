
function listCreate(items) {
	var newList = new Object();
	newList.items = items;
	newList.itemIndex = 0;
	return newList;
}

function listGetElement(listObject, elementIndex) {
	return listObject.items[listGetPlace(listObject, elementIndex)];
}

function listGetPlace(listObject, increment) {
	var elementPlace = listObject.itemIndex + increment;

	if (elementPlace >= listObject.items.length)
		elementPlace = elementPlace % listObject.items.length;

	while (elementPlace < 0)
		elementPlace = listObject.items.length + elementPlace;

	return elementPlace;
}