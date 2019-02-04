	<script type="text/javascript">
	function clonePuff(obj) {
		try {
			var clone = obj.cloneNode(true);
			obj.style.display = 'none';
			obj.parentNode.insertBefore(clone, obj);
			Effect.Puff(clone);
			obj.style.display = 'block';
		} catch (e) {};
	}
	</script>

	<!--if($BUFFER.BLOCK)-->
	<div class="adminBlock" style="float: left">
	<!--{bgn: block}-->
		<div>
			<h3>{V_TITLE}</h3>
			{V_CONTENT}
		</div>
	<!--{end: block}-->
	</div>
	<!--endif-->

	<table class="panel" style="width: 100%; <!--if($BUFFER.BLOCK)-->width: 70%; float: left<!--endif-->">
	<!--{bgn: category}-->
	<tr>
		<td>
			<h2>{V_CATEGORY}</h2>
			<!--{bgn: icon}-->
			<div class="icon" onclick="clonePuff(this)">
			<!--if($V_EMBLEM)-->
				<div style="float: right; display:block; bottom: 0px; right: 0px">{V_EMBLEM}</div>
			<!--endif-->
				{V_ICON}
			</div>
			<!--{end: icon}-->
		</td>
	</tr>
	<!--{end: category}-->
	</table>
