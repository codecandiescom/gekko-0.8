// A class for showing a bubble
// Written by xiam <xiam@users.sourceforge.net>
// Usage:
// var bubble = new bubbleBox();
// bubble.addText('This is just a test');
// bubble.addElement($('my_html_object_id'));
// bubble.show();

var bubbleBox = Class.create();
bubbleBox.prototype =
{
	initialize: function()
	{
		this.bubble = document.createElement('div');

		this.bubble.style.fontSize = 'xx-small';
		this.bubble.style.margin = '10px';
		this.bubble.style.padding = '0px';
		this.bubble.style.width = '188px';
		this.bubble.style.display = 'none';

		this.head = document.createElement('div');
		this.head.style.background = 'url(\''+document.root+'media/bubble-head.png\')';
		this.head.style.height = '35px';

		this.body = document.createElement('div');
		this.body.style.overflow = 'auto';
		this.body.style.padding = '5px 20px 5px 20px';
		this.body.style.background = 'url(\''+document.root+'media/bubble-body.png\') repeat-y';

		this.bottom = document.createElement('div');
		this.bottom.style.height = '10px';
		this.bottom.style.background = 'url(\''+document.root+'media/bubble-bottom.png\') left bottom no-repeat';

		this.bubble.appendChild(this.head);
		this.bubble.appendChild(this.body);
		this.bubble.appendChild(this.bottom);

		document.body.appendChild(this.bubble);
	},
	// appends a html object
	addElement: function(obj)
	{
		this.body.appendChild(obj);
	},
	// creates an object, inserts some text inside it and then appends it to the bubble
	addText: function(txt)
	{
		var div = document.createElement('div');
		div.innerHTML = txt;
		this.addElement(div);
	},
	// shows the bubble
	show: function()
	{
		this.bubble.style.display = 'block';
	},
	// hides the bubble
	hide: function ()
	{
		//new Effect.Fade(this.bubble);
		this.bubble.style.display = 'none';
	},
	// shows the element near the given object
	mimicPos: function(obj)
	{
		Position.prepare();
		var pos = Position.cumulativeOffset(obj);

		this.bubble.style.position = 'absolute';
		this.bubble.style.top = pos[1]+20+'px';
		this.bubble.style.left = pos[0]+20+'px';
	}
};
