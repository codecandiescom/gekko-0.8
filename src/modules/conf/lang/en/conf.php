<?php


	if (!defined("IN-GEKKO")) die("Get a life!");

	Lang::Set("K_SITE.TITLE", "Website's title");
	Lang::Set("K_SITE.NAME", "Website's name");
	Lang::Set("K_SITE.DESCRIPTION", "Website's description");
	Lang::Set("K_SITE.SLOGAN", "Website's slogan");
	Lang::Set("K_SITE.COPYRIGHT", "Copyright notice");
	Lang::Set("K_SITE.SHOW_TEXT_TITLE", "Show website's title and slogan at header");
	Lang::Set("K_SITE.TEMPLATE", "Template");
	Lang::Set("K_SITE.STYLESHEET", "Template's stylesheet");
	Lang::Set("K_SITE.SMILEYSTHEME", "Smileys theme (emoticons)");
	Lang::Set("K_SITE.ICONTHEME", "Icon theme");
	Lang::Set("K_SITE.LANG", "Interface language");
	Lang::Set("K_SITE.HOUR_DIFFERENCE", "Hour difference (you may use positive or nevative integers). System's hour: {=date(\"h:i\")=}");
	Lang::Set("K_SITE.GZIP_OUTPUT", "GZIP Compressed output");
	Lang::Set("K_SITE.FOOTER", "Pagefooter");
	Lang::Set("K_SITE.CONTACT_MAIL", "Contact e-mail");
	Lang::Set("K_HTML_FILTER", "Intelligent HTML filter (privileges based)");
	Lang::Set("K_PLUGINS.MSIEPNGFIX.DISABLE", "Disable PNG plugin for transparency under Internet Explorer. It may avoid some <i>crashes</i> while using that browser, but PNG images, like Icons and some photos, will lost their transparency showing a horrible gray background instead. We recommend to avoid using Microsoft Internet Explorer until it reaches a decent status.");
	Lang::Set("K_MAGIC_BLACKLIST", "Set a temporal ban for users that sends page request too quickly within a very short time. It may prevent common pseudocracker attacks.");
	Lang::Set("K_GBBCODE", "GBBCode evaluation");
	Lang::Set("K_GBBCODE.SMILEYS", "Replace strings like: ;) :) :O :-P with icons.");
	Lang::Set("K_SMTP.ENABLE", "Use SMTP e-mail implementation instead of PHP's mail()");
	Lang::Set("K_RTBEDITOR", "<a href=\"http://mozilla.org/products/firefox\">Firefox</a> compatible Visual HTML Editor");
?>