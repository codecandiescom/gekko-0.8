<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">
	<channel>
		<title>{=feedContentEncode("{C_SITE.NAME}")=}</title>
		<link>{C_SITE.URL}</link>
		<description>{=feedContentEncode("{C_SITE.DESCRIPTION}")=}</description>
		<language>{C_SITE.LANG}</language>
		<copyright>{=feedContentEncode("{C_SITE.COPYRIGHT}")=}</copyright>
		<pubDate>{=date("r")=}</pubDate>
		<lastBuildDate>{=date("r", "{V_LASTBUILDTIME}")=}</lastBuildDate>
		<generator>Gekko {C_GEKKO_VERSION}</generator>
		<image>
			<title>{=feedContentEncode("{C_SITE.NAME}")=}</title>
			<url>{C_SITE.URL}media/banner-88x31.png</url>
			<link>{C_SITE.URL}</link>
		</image>
		<!--if($BUFFER.ITEM)-->
		<!--{bgn: ITEM}-->
		<item>
			<title>{=feedContentEncode("{V_TITLE}")=}</title>
			<!--if($V_CATEGORY)--><category>{=feedContentEncode("{V_CATEGORY}")=}</category><!--endif-->
			<guid isPermaLink="false">{V_LINK}</guid>
			<link>{V_LINK}</link>
			<description>{=feedContentEncode("{V_DESCRIPTION}")=}</description>
			<author>{=feedContentEncode("{V_AUTHOR_EMAIL}")=} ({=feedContentEncode("{V_AUTHOR_NAME}")=})</author>
			<!--if($V_COMMENTS)--><comments>{V_COMMENTS}</comments><!--endif-->
			<pubDate>{=date("r", "{V_CREATIONTIME}")=}</pubDate>
		</item>
		<!--{end: ITEM}-->
		<!--endif-->
	</channel>
</rss>
