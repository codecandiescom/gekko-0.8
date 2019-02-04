<?xml version="1.0" encoding="utf-8"?>
<feed version="0.3" xmlns="http://purl.org/atom/ns#">
	<title>{C_SITE.NAME}</title>
	<link title="{C_SITE.NAME}" rel="alternate" type="text/html" href="{C_SITE.URL}" />
	<modified>{=timeformat::iso8601("{V_LASTBUILDTIME}")=}</modified>
	<generator url="http://www.gekkoware.org">Gekko {C_GEKKO_VERSION}</generator>
	<!--if($BUFFER.ITEM)-->
	<!--{bgn: item}-->
	<entry>
		<id>{V_ID}</id>
		<title>{=htmlentities("{V_TITLE}")=}</title>
		<link rel="alternate" type="text/html" href="{V_LINK}" />
		<content type="text/html" mode="escaped">{=htmlspecialchars("{V_DESCRIPTION}")=}</content>
		<author><name>{=htmlentities("{V_AUTHOR_NAME}")=}</name></author>
		<modified>{=timeformat::iso8601("{V_CREATIONTIME}")=}</modified>
		<issued>{=timeformat::iso8601("{V_CREATIONTIME}")=}</issued>
		<created>{=timeformat::iso8601("{V_CREATIONTIME}")=}</created>
	</entry>
	<!--{end: item}-->
	<!--endif-->
</feed>