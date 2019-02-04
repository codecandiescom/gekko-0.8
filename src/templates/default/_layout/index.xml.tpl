<?xml version="1.0" encoding="utf-8"?>
<gekko>
	<head>
		<title><![CDATA[{V_DOC_TITLE} {C_SITE.TITLE} {V_DOC_SUBTITLE}]]></title>
		<template><![CDATA[{C_SITE.TEMPLATE}]]></template>
		<stylesheet><![CDATA[{C_SITE.STYLESHEET}]]></stylesheet>
		
		<element id="site_name"><![CDATA[{C_SITE.NAME}]]></element>
		<element id="site_slogan"><![CDATA[{C_SITE.SLOGAN}]]></element>
		<element id="exec_time"><![CDATA[{V_SCRIPT_EXEC_TIME}]]></element>
		<element id="memory_usage"><![CDATA[{V_MEMORY_USAGE}]]></element>
		<element id="database_queries"><![CDATA[{V_DATABASE_QUERIES}]]></element>
	</head>
	<body>
		<!--if($BUFFER.BLOCK)-->
		<!--{bgn: BLOCK}-->
		<element id="{V_POSITION}">
		<![CDATA[{={V_CONTENT}=}]]>
		</element>
		<!--{end: BLOCK}-->
		<!--endif-->
	</body>
</gekko>