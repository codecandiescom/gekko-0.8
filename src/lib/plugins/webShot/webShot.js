/*
*	Gekko - Open Source Web Development Framework
*	------------------------------------------------------------------------
*	Copyright (C) 2004-2006, J. Carlos Nieto <xiam@users.sourceforge.net>
*	This program is Free Software.
*
*	@package	Plugins
*	@license	http://www.gnu.org/copyleft/gpl.html GNU/GPL License 2.0
*	@author		David Valdez <gnuget@lidsol.org>
*	@author		J. Carlos Nieto <xiam@users.sourceforge.net>
*	@link		http://www.gekkoware.org
*/

Event.observe(window, 'load',
	function () {
		var anchors = document.getElementsByTagName('a');
		for (var i = 0; i < anchors.length; i++) {
			var anchor = anchors[i];
			Event.observe(anchor, 'mousemove',
				function (e) {

					// preventing the default popup for being showed
					Event.stop(e);

					var obj = Event.element(e);
				
					while (obj.nodeName.toLowerCase() != 'a')
						obj = obj.parentNode;
				
					if (((obj.title && typeof(obj.title) == 'string') || obj.href) && !obj._bubble) {
						
						obj._bubble = new bubbleBox();
						
						if (obj.href) {
							if (obj.href.match(/^http:\/\//) && obj.href.search('http://'+document.domain) == -1) {
								
								obj._bubble.body.style.textAlign = 'center';
								
								var img = document.createElement('img');
								img.src = 'http://mozshot.nemui.org/shot/shot?'+obj.href;
								img.width = '128';
								img.height = '128';

								obj._bubble.addElement(img);
							} else if (!obj.title) {
								return;
							}
						}
						
						obj._bubble.addText(obj.title);

						obj._bubble.mimicPos(obj);
						obj._bubble.show();
						
						// adding an event for mouseout
						Event.observe(obj, 'mouseout',
							function(e) {
								if (obj._bubble) {
									obj._bubble.hide();
									obj._bubble = null;
								}
							}
						,false);
					}

				}
			, false);
		}
	}
, false);
