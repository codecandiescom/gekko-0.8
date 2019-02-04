{V_OPTIONS}

<div style="overflow: auto">
	<!--if($BUFFER.THEME)-->
	<!--{bgn: THEME}-->
	<div style="display: block; float: left; padding: 10px; width: 40%">
		<h3><a name="{V_TEMPLATE}_{V_NAME}">{V_TEMPLATE}/{V_NAME}</a></h3>
		<div style="text-align: center">
			<a href="{=urlEvalPrototype("modules/conf/actions.php?action=looknfeel&request=set_theme&theme={V_TEMPLATE}:{V_NAME}&auth={C_AUTH_STR}")=}">
				<img class="photo" src="{V_SCREENSHOT}" width="140" height="103" alt="{V_NAME}" />
			</a>
		</div>
		<small>
			<!--if($V_INFO_AUTHOR)--><b>{L_AUTHOR}:</b><br />&nbsp;&nbsp;{V_INFO_AUTHOR}<br /><!--endif-->
			<!--if($V_INFO_ACKNOWLEDGEMENTS)--><b>{L_ACKNOWLEDGEMENT}:</b><br />&nbsp;&nbsp;{V_INFO_ACKNOWLEDGEMENTS}<br /><!--endif-->
			<!--if($V_INFO_LICENSE)--><b>{L_LICENSE}:</b><br />&nbsp;&nbsp;{V_INFO_LICENSE}<br /><!--endif-->
			<b>{L_FILENAME}:</b><br />&nbsp;&nbsp;<a href="{C_SITE.URL}{V_FILENAME}">{V_FILENAME}</a><br />
		</small>
		{V_ACTIONS}
	</div>
	<!--{end: THEME}-->
	<!--endif-->
</div>
