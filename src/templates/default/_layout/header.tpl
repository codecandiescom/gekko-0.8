<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="{C_SITE.DESCRIPTION}" />
	<!--if($C_SITE.KEYWORDS)-->
	<meta name="keywords" content="{C_SITE.KEYWORDS}" />
	<!--endif-->
	<meta name="generator" content="Gekko (c) 2004-2006 by J. Carlos Nieto, http://www.gekkoware.org. Gekko is Free Software." />
	<link rel="shortcut icon" type="image/png" href="{C_SITE.URL}media/favicon.png" />
	<link rel="stylesheet" type="text/css" href="{C_SITE.URL}templates/{C_SITE.TEMPLATE}/_layout/main.css" />
	<link rel="stylesheet" type="text/css" href="{C_SITE.URL}templates/{C_SITE.TEMPLATE}/_themes/{C_SITE.STYLESHEET}/theme.css" />

	<!--if($V_DOC_STYLE)-->
	<style type="text/css">
	{V_DOC_STYLE}
	</style>
	<!--endif-->

	<title>{V_DOC_TITLE} {C_SITE.TITLE} {V_DOC_SUBTITLE}</title>

	<script type="text/javascript">
		//<!--
		var GEKKO_SITE_URL = '{C_SITE.URL}';
		document.root = '{C_SITE.URL}';
		document.relurl = '{C_SITE.REL_URL}';
		document.path = location.href.substr(document.root.length);
		var GEKKO_AUTH_HASH = {C_ANTIBOT};
		var GEKKO_ICON_CLOSE = unescape('{=rawurlencode(fetchicon("close.png", 16))=}');
		var GEKKO_MESSAGE_ERROR = unescape('{=rawurlencode(trim(createMessageBox("error", "%s")))=}');
		var GEKKO_WARNING_ERROR = unescape('{=rawurlencode(trim(createMessageBox("warning", "%s")))=}');
		//-->
	</script>

	<script type="text/javascript" src="{C_SITE.URL}js/scriptaculous/lib/prototype.js"></script>
	<script type="text/javascript" src="{C_SITE.URL}js/scriptaculous/src/scriptaculous.js"></script>
	<script type="text/javascript" src="{C_SITE.URL}js/scriptaculous/src/effects.js"></script>
	<script type="text/javascript" src="{C_SITE.URL}js/scriptaculous/src/dragdrop.js"></script>
	
	<script type="text/javascript" src="{C_SITE.URL}js/core.js"></script>
	<script type="text/javascript" src="{C_SITE.URL}js/captcha.js"></script>
	<script type="text/javascript" src="{C_SITE.URL}js/xmlhttp.js"></script>
	<script type="text/javascript" src="{C_SITE.URL}js/forms.js"></script>
	<script type="text/javascript" src="{C_SITE.URL}js/editor.js"></script>
	<script type="text/javascript" src="{C_SITE.URL}js/popup.js"></script>
	<script type="text/javascript" src="{C_SITE.URL}js/contextmenu.js"></script>
	
	{V_DOC_HEAD}
</head>
