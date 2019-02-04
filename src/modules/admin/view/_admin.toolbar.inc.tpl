<div class="panel" style="display: block; overflow: auto; white-space: no-wrap">
	<a href="{=URLEVALPROTOTYPE("index.php/module=users")=}">
		<div class="icon">
			{=createIcon("account.png", 48)=}
			{L_MY_ACCOUNT}
		</div>
	</a>
	<a href="{=URLEVALPROTOTYPE("index.php/module=admin")=}">
		<div class="icon">
			{=createIcon("admin.png", 48)=}
			{L_ADMIN}
		</div>
	</a>
	<a href="{=URLEVALPROTOTYPE("index.php")=}">
		<div class="icon">
			{=createIcon("website.png", 48)=}
			{L_MY_WEBSITE}
		</div>
	</a>
	<a href="http://www.gekkoware.org">
		<div class="icon">
			{=createIcon("gekko.png", 48)=}
			Gekkoware.org
		</div>
	</a>
	<a href="http://www.gekkoware.org/doc/{=lang::getLang(true)=}/html/index.html">
		<div class="icon">
			{=createIcon("help.png", 48)=}
			{L_DOCUMENTATION}
		</div>
	</a>
</div>