<script type="text/javascript">
	gekkoLinkPlacer = new Object();
	
  	gekkoLinkPlacer.startEffect = function(element) {
		element._opacity = Element.getOpacity(element);
		new Effect.Opacity(element, {duration:0.2, from:element._opacity, to:0.7});
		new Effect.Highlight(element, {});
	}
	
	gekkoLinkPlacer.positions = new Array();
	
	gekkoLinkPlacer.compile = function(form) {
		var buff = new Array();
		for (var i = 0; i < gekkoLinkPlacer.positions.length; i++) {
			var container = gekkoLinkPlacer.positions[i];
			container.items = new Array();
			for (var j = 0; j < container.childNodes.length; j++) {
				var li = container.childNodes[j];
				if (li.nodeName.toLowerCase() == 'li')
					container.items.push(li.id.substr(9)+'-'+gekkoLinkPlacer.linkGetLevel(li));
			}
			buff.push(container.id.substr(11)+':'+container.items.join(','));
		}
		form.positions.value = buff.join('|');
		return true;
	}
	
	gekkoLinkPlacer.getParent = function(button) {
		if (!button.parent) {
			var parent = button;
			
			while (!parent.id || parent.id.substr(0, 8) != 'menuLink')
				parent = parent.parentNode;
		
			parent.level = (parseInt(parent.style.paddingLeft)-10)/20;
			button.parent = parent;
		}
		return button.parent;
	}
	
	gekkoLinkPlacer.linkSetLevel = function(link, level) {
		link.style.paddingLeft = level*20+10+'px';
		link.level = level;
	}

	gekkoLinkPlacer.linkGetLevel = function (link) {
		return (parseInt(link.style.paddingLeft)-10)/20;
	}
	
	gekkoLinkPlacer.getPrevBrother = function (object) {
		var parent = object.parentNode;
		var brother = object.previousSibling;

		while (brother && brother.nodeName.toLowerCase() != 'li')
			brother = brother.previousSibling;

		if (brother) {
			brother.level = gekkoLinkPlacer.linkGetLevel(brother);
			return brother;
		}	
		return null;
	}
	
	gekkoLinkPlacer.getNextBrother = function (object) {
		var parent = object.parentNode;
		var brother = object.nextSibling;
		
		while (brother && brother.nodeName.toLowerCase() != 'li')
			brother = brother.nextSibling;
			
		if (brother) {
			brother.level = gekkoLinkPlacer.linkGetLevel(brother);
			return brother;
		}
		
		return null;
	}

	gekkoLinkPlacer.moveLeft = function(button, parent) {
		if (!parent)
			var parent = gekkoLinkPlacer.getParent(button);

		var brother = gekkoLinkPlacer.getNextBrother(parent);

		if (parent.level > 0)
			gekkoLinkPlacer.linkSetLevel(parent, parent.level-1);

		while (brother && (brother.level - parent.level) > 1)
			gekkoLinkPlacer.moveLeft(null, brother);

	}

	gekkoLinkPlacer.moveRight = function(button, parent) {
	
		if (!parent)
			var parent = gekkoLinkPlacer.getParent(button);

		var brother = gekkoLinkPlacer.getPrevBrother(parent);

		if (((parent.level+1) - brother.level) > 1)
			return false;

		gekkoLinkPlacer.linkSetLevel(parent, parent.level+1);

		var brother = gekkoLinkPlacer.getNextBrother(parent);

		while (brother && (parent.level == brother.level))
			gekkoLinkPlacer.moveRight(null, brother);

	}

	gekkoLinkPlacer.init = function() {
		var uls = document.getElementsByTagName('UL');
		var containers = new Array();
		var i;
		
		for (i = 0; i < uls.length; i++) {
			var container = uls[i];
			if (container.id.substr(0, 10) == 'menuPlacer') {
				containers.push(container);
				container.menuId = container.id.replace(/^[^_]*_/, '');
				gekkoLinkPlacer.positions.push(container);
			}
		}

		for (i = 0; i < containers.length; i++) {
			Sortable.create(containers[i].id, { startEffect: gekkoLinkPlacer.startEffect, containment: containers, dropOnEmpty:true });
			gekkoLinkPlacer.positions.push(containers[i]);
		}
	
	};

	document.setEventListener(window, "load", function() { gekkoLinkPlacer.init(); });
</script>
{V_ACTIONS}
<!--if($BUFFER.MENU)-->
<h2>{L_MANAGE}</h2>

<form action="{C_SITE.URL}modules/menu-editor/actions.php" method="post" onsubmit="return gekkoLinkPlacer.compile(this);">
	<input type="hidden" name="positions" value="" />
	<input type="hidden" name="action" value="update_position" />
	<input type="hidden" name="return" value="" />
	{=createMessageBox("info", "{L_MENU_DRAG_AND_DROP_MODE}")=}
	<div class="buttons">
	<button type="submit">{=createIcon("backup.png", 16)=} {L_SAVE_CHANGES}</button>
	</div>
</form>

<!--{bgn: MENU}-->
<div class="{V_SW_CLASS}">
	<div class="item">
		{V_ACTIONS}
		<h3>{V_TITLE}</h3>
		<ul id="menuPlacer_{V_ID}" style="list-style-type: none;" class="dropzone">
		<!--if($BUFFER.MENU.LINK)-->
			<!--{bgn: LINK}-->
				<li id="menuLink_{V_ID}" style="cursor: move; padding-left: {=intval({V_LEVEL}*20+10)=}px; list-style-type: none">
					<b>{=createIcon("{V_ICON}", 16)=} {V_TITLE}</b><br />
					<div><small>{V_LINK}</small></div>
					<div style="cursor: default">{V_ACTIONS}</div>
				</li>
			<!--{end: LINK}-->
		<!--endif-->
		</ul>
		{V_ACTIONS}
	</div>
</div>
<!--{end: MENU}-->
<!--endif-->