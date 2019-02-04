<script type="text/javascript">
	gekkoBlockPlacer = new Object();
	gekkoBlockPlacer.positions = new Array();
	
	gekkoBlockPlacer.compile = function(form) {
		var buff = new Array();
		for (var i = 0; i < gekkoBlockPlacer.positions.length; i++) {
			var container = gekkoBlockPlacer.positions[i];
			container.items = new Array();

			for (var j = 0; j < container.parent.childNodes.length; j++) {
				var block = container.parent.childNodes[j];
				if (block.nodeName.toLowerCase() == 'ul') {
					for (var k = 0; k < block.childNodes.length; k++) {
						var li = block.childNodes[k];
						for (var m = 0; m < li.childNodes.length; m++) {
							var div = li.childNodes[m];
							if (div.nodeName.toLowerCase() == 'div') {
								container.items.push(div.blockId);
							}
						}
					}
				}
			}
			buff.push(container.parent.id.substr(5)+':'+container.items.join(','));
		}
		form.positions.value = buff.join('|');
		return true;
	}

	gekkoBlockPlacer.createShield = function (object) {
		
		var shield = document.createElement('DIV');

		shield.style.position = 'absolute';
		shield.style.top = '0px';
		shield.style.left = '0px';
		shield.style.height = '100%';
		shield.style.width = '100%';
		shield.style.display = 'block';
		
		object.appendChild(shield);

		return shield;
	}

	gekkoBlockPlacer.init = function () {
		var divs = document.getElementsByTagName('DIV');
		var blockArray = new Array();
		var containers = new Array();

		for (var i = 0; i < divs.length; i++) {
			if (divs[i].id.substr(0, 5) == 'block' && divs[i].id != 'blockC') {
				blockNest = new Object();
				blockNest.parent = divs[i];
				blockNest.children = new Array();
				for (var j = 0; j < blockNest.parent.childNodes.length; j++) {
					if (blockNest.parent.childNodes[j].nodeName.toLowerCase() == 'div') {
						blockNest.children.push(blockNest.parent.childNodes[j]);
					}
				}
				blockArray.push(blockNest);
			}
		}

		for (i = 0; i < blockArray.length; i++) {
			blockNest = blockArray[i];
			var dropZone = document.createElement('UL');
			dropZone.className = 'dropZone';
			dropZone.id = 'blockPost_'+i;
			
			for (j = 0; j < blockNest.children.length; j++) {
				var block = blockNest.children[j];
				block.blockId = block.id.substr(11);

				var shield = this.createShield(block);

				var dragZone = document.createElement('LI');
				dragZone.className = 'dragZone';
				
				if (block.style.cssText) {
					dragZone.style.cssText = block.style.cssText;
					block.style.cssText = '';
				}

				dragZone.appendChild(block);
				dropZone.appendChild(dragZone);

			}
			containers.push(dropZone);
			
			blockNest.parent.appendChild(dropZone);
		}

		gekkoBlockPlacer.positions = blockArray;
		
		for (i = 0; i < containers.length; i++)
			Sortable.create(containers[i].id, { containment: containers, dropOnEmpty:true, scroll:window });
		
	}

	document.setEventListener(window, "load", function() { gekkoBlockPlacer.init(); });
</script>

{=createMessageBox("info", "{L_BLOCKS_POSITIONING_MODE}")=}
<form action="{C_SITE.URL}modules/blocks/actions.php" onsubmit="return gekkoBlockPlacer.compile(this);" method="post">
	<input type="hidden" name="action" value="block_placer" />
	<input type="hidden" name="positions" value="" />
	<input type="hidden" name="return" value="{V_RETURN}" />
	<label>
		{L_ACT_AS_MODULE}:
		<select onchange="location.href='{=urlevalprototype("index.php/module=admin/base=blocks/action=block_placer?test_module=")=}'+escape(this.value)">
		{=createDropDown(unserialize("{V_TEST_MODULES}"), "{V_TEST_MODULE}")=}
		</select>
	</label>
	<div class="buttons">
		<button type="button" onclick="history.back(1)">{=createIcon("back.png", 16)=} {L_GO_BACK}</button>
		<button type="submit">{=createIcon("backup.png", 16)=} {L_SAVE_POSITIONS}</button>
	</div>
</form>