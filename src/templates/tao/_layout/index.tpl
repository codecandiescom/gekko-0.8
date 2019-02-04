{include("../../default/_layout/header.tpl")}
<body>
	<div id="container">
		<div class="chead"></div>
		<div class="cbody">
			<table id="framework" cellpadding="0" cellspacing="0">
			<!--if($M_BLOCK_U)-->
			<tr>
				<td class="blockContainer" id="td_blockU" colspan="{V_FW_COLSPAN}">
				<div id="blockU">
				{M_BLOCK_U}
				</div>
				</td>
			</tr>
			<!--endif-->
			<tr>
				<td class="td_blockT" colspan="{V_FW_COLSPAN}">
				<div id="pageHeader">
					<!--if(!$C_SITE.SHOW_TEXT_TITLE)-->
					<div style="display: none;">
					<!--endif-->
					<h2 id="site_slogan">{C_SITE.SLOGAN}</h2>
					<h1 id="site_name">{C_SITE.NAME}</h1>
					<!--if(!$C_SITE.SHOW_TEXT_TITLE)-->
					</div>
					<!--endif-->
					<!--if($M_BLOCK_H)-->
					<div id="blockH">
						{M_BLOCK_H}
					</div>
					<!--endif-->
				</div>
				</td>
			</tr>
			<tr>
				<!--if($M_BLOCK_L)-->
				<td id="td_blockL">
					<div id="blockL">
					{M_BLOCK_L}
					</div>
				</td>
				<!--endif-->
				<td id="td_blockC">
					<a name="main"></a>
					<!--if ($M_BLOCK_TC)-->
					<div id="blockTC">
					{M_BLOCK_TC}
					</div>
					<!--endif-->
					<!--if ($M_BLOCK_C)-->
					<div id="blockC">
					{M_BLOCK_C}
					</div>
					<!--endif-->
					<!--if ($M_BLOCK_DC)-->
					<div id="blockDC">
					{M_BLOCK_DC}
					</div>
					<!--endif-->
				</td>
				<!--if($M_BLOCK_R)-->
				<td id="td_blockR">
					<div id="blockR">
					{M_BLOCK_R}
					</div>
				</td>
				<!--endif-->
			</tr>
			<!--if($M_BLOCK_D)-->
			<tr>
				<td class="blockContainer" id="td_blockD" colspan="{V_FW_COLSPAN}">
					<div id="blockD">
						{M_BLOCK_D}
					</div>
				</td>
			</tr>
			<!--endif-->
			<tr>
				<td colspan="{V_FW_COLSPAN}">
					<div id="pageFooter">
						<div>{C_SITE.COPYRIGHT}</div>
						<div>{C_SITE.FOOTER}</div>
						{V_DOC_FOOTER}
					</div>
				</td>
			</tr>
			</table>
		</div>
		<div class="cbottom"></div>
	</div>
	<div id="pageNote">
		<div>
		<!--if($V_WEBSITE_CREDITS)-->
		<div id="author">{V_WEBSITE_CREDITS}</div>
		<!--endif-->
		<div>
			{L_RENDER_TIME}: <span id="exec_time">{V_SCRIPT_EXEC_TIME}</span>s,
			<!--if($V_MEMORY_USAGE)-->
			{L_MEMORY_USAGE}: <span id="memory_usage">{V_MEMORY_USAGE}</span>,
			<!--endif-->
			{L_DATABASE_QUERIES}: <span id="database_queries">{V_DATABASE_QUERIES}</span>
		</div>
		</div>
		{L_GEKKO_IS_FREE_SOFTWARE}
	</div>
</body>
{include("../../default/_layout/footer.tpl")}
