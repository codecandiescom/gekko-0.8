<?php

	/*
	*	Gekko - Open Source Web Development Framework
	*	------------------------------------------------------------------------
	*	Copyright (C) 2004-2006, J. Carlos Nieto <xiam@users.sourceforge.net>
	*	This program is Free Software.
	*
	*	@package	Gekko
	*	@license	http://www.gnu.org/copyleft/gpl.html GNU/GPL License 2.0
	*	@author		J. Carlos Nieto <xiam@users.sourceforge.net>
	*	@link		http://www.gekkoware.org
	*/

	if (!defined("IN-GEKKO")) die("Get a life!");

	// translation table stealed from http://radekhulan.cz/item/php-script-to-convert-x-html-entities-to-decimal-unicode-representation/category/apache-php
	$GLOBALS["unicodeEntities"] = Array (
		'&nbsp;' => '&#xA0;',
		'&iexcl;' => '&#xA1;',
		'&cent;' => '&#xA2;',
		'&pound;' => '&#xA3;',
		'&curren;' => '&#xA4;',
		'&yen;' => '&#xA5;',
		'&brvbar;' => '&#xA6;',
		'&sect;' => '&#xA7;',
		'&uml;' => '&#xA8;',
		'&copy;' => '&#xA9;',
		'&ordf;' => '&#xAA;',
		'&laquo;' => '&#xAB;',
		'&not;' => '&#xAC;',
		'&shy;' => '&#xAD;',
		'&reg;' => '&#xAE;',
		'&macr;' => '&#xAF;',
		'&deg;' => '&#xB0;',
		'&plusmn;' => '&#xB1;',
		'&sup2;' => '&#xB2;',
		'&sup3;' => '&#xB3;',
		'&acute;' => '&#xB4;',
		'&micro;' => '&#xB5;',
		'&para;' => '&#xB6;',
		'&middot;' => '&#xB7;',
		'&cedil;' => '&#xB8;',
		'&sup1;' => '&#xB9;',
		'&ordm;' => '&#xBA;',
		'&raquo;' => '&#xBB;',
		'&frac14;' => '&#xBC;',
		'&frac12;' => '&#xBD;',
		'&frac34;' => '&#xBE;',
		'&iquest;' => '&#xBF;',
		'&Agrave;' => '&#xC0;',
		'&Aacute;' => '&#xC1;',
		'&Acirc;' => '&#xC2;',
		'&Atilde;' => '&#xC3;',
		'&Auml;' => '&#xC4;',
		'&Aring;' => '&#xC5;',
		'&AElig;' => '&#xC6;',
		'&Ccedil;' => '&#xC7;',
		'&Egrave;' => '&#xC8;',
		'&Eacute;' => '&#xC9;',
		'&Ecirc;' => '&#xCA;',
		'&Euml;' => '&#xCB;',
		'&Igrave;' => '&#xCC;',
		'&Iacute;' => '&#xCD;',
		'&Icirc;' => '&#xCE;',
		'&Iuml;' => '&#xCF;',
		'&ETH;' => '&#xD0;',
		'&Ntilde;' => '&#xD1;',
		'&Ograve;' => '&#xD2;',
		'&Oacute;' => '&#xD3;',
		'&Ocirc;' => '&#xD4;',
		'&Otilde;' => '&#xD5;',
		'&Ouml;' => '&#xD6;',
		'&times;' => '&#xD7;',
		'&Oslash;' => '&#xD8;',
		'&Ugrave;' => '&#xD9;',
		'&Uacute;' => '&#xDA;',
		'&Ucirc;' => '&#xDB;',
		'&Uuml;' => '&#xDC;',
		'&Yacute;' => '&#xDD;',
		'&THORN;' => '&#xDE;',
		'&szlig;' => '&#xDF;',
		'&agrave;' => '&#xE0;',
		'&aacute;' => '&#xE1;',
		'&acirc;' => '&#xE2;',
		'&atilde;' => '&#xE3;',
		'&auml;' => '&#xE4;',
		'&aring;' => '&#xE5;',
		'&aelig;' => '&#xE6;',
		'&ccedil;' => '&#xE7;',
		'&egrave;' => '&#xE8;',
		'&eacute;' => '&#xE9;',
		'&ecirc;' => '&#xEA;',
		'&euml;' => '&#xEB;',
		'&igrave;' => '&#xEC;',
		'&iacute;' => '&#xED;',
		'&icirc;' => '&#xEE;',
		'&iuml;' => '&#xEF;',
		'&eth;' => '&#xF0;',
		'&ntilde;' => '&#xF1;',
		'&ograve;' => '&#xF2;',
		'&oacute;' => '&#xF3;',
		'&ocirc;' => '&#xF4;',
		'&otilde;' => '&#xF5;',
		'&ouml;' => '&#xF6;',
		'&divide;' => '&#xF7;',
		'&oslash;' => '&#xF8;',
		'&ugrave;' => '&#xF9;',
		'&uacute;' => '&#xFA;',
		'&ucirc;' => '&#xFB;',
		'&uuml;' => '&#xFC;',
		'&yacute;' => '&#xFD;',
		'&thorn;' => '&#xFE;',
		'&yuml;' => '&#xFF;',
		'&fnof;' => '&#x192;',
		'&Alpha;' => '&#x391;',
		'&Beta;' => '&#x392;',
		'&Gamma;' => '&#x393;',
		'&Delta;' => '&#x394;',
		'&Epsilon;' => '&#x395;',
		'&Zeta;' => '&#x396;',
		'&Eta;' => '&#x397;',
		'&Theta;' => '&#x398;',
		'&Iota;' => '&#x399;',
		'&Kappa;' => '&#x39A;',
		'&Lambda;' => '&#x39B;',
		'&Mu;' => '&#x39C;',
		'&Nu;' => '&#x39D;',
		'&Xi;' => '&#x39E;',
		'&Omicron;' => '&#x39F;',
		'&Pi;' => '&#x3A0;',
		'&Rho;' => '&#x3A1;',
		'&Sigma;' => '&#x3A3;',
		'&Tau;' => '&#x3A4;',
		'&Upsilon;' => '&#x3A5;',
		'&Phi;' => '&#x3A6;',
		'&Chi;' => '&#x3A7;',
		'&Psi;' => '&#x3A8;',
		'&Omega;' => '&#x3A9;',
		'&alpha;' => '&#x3B1;',
		'&beta;' => '&#x3B2;',
		'&gamma;' => '&#x3B3;',
		'&delta;' => '&#x3B4;',
		'&epsilon;' => '&#x3B5;',
		'&zeta;' => '&#x3B6;',
		'&eta;' => '&#x3B7;',
		'&theta;' => '&#x3B8;',
		'&iota;' => '&#x3B9;',
		'&kappa;' => '&#x3BA;',
		'&lambda;' => '&#x3BB;',
		'&mu;' => '&#x3BC;',
		'&nu;' => '&#x3BD;',
		'&xi;' => '&#x3BE;',
		'&omicron;' => '&#x3BF;',
		'&pi;' => '&#x3C0;',
		'&rho;' => '&#x3C1;',
		'&sigmaf;' => '&#x3C2;',
		'&sigma;' => '&#x3C3;',
		'&tau;' => '&#x3C4;',
		'&upsilon;' => '&#x3C5;',
		'&phi;' => '&#x3C6;',
		'&chi;' => '&#x3C7;',
		'&psi;' => '&#x3C8;',
		'&omega;' => '&#x3C9;',
		'&thetasym;' => '&#x3D1;',
		'&upsih;' => '&#x3D2;',
		'&piv;' => '&#x3D6;',
		'&bull;' => '&#x2022;',
		'&hellip;' => '&#x2026;',
		'&prime;' => '&#x2032;',
		'&Prime;' => '&#x2033;',
		'&oline;' => '&#x203E;',
		'&frasl;' => '&#x2044;',
		'&weierp;' => '&#x2118;',
		'&image;' => '&#x2111;',
		'&real;' => '&#x211C;',
		'&trade;' => '&#x2122;',
		'&alefsym;' => '&#x2135;',
		'&larr;' => '&#x2190;',
		'&uarr;' => '&#x2191;',
		'&rarr;' => '&#x2192;',
		'&darr;' => '&#x2193;',
		'&harr;' => '&#x2194;',
		'&crarr;' => '&#x21B5;',
		'&lArr;' => '&#x21D0;',
		'&uArr;' => '&#x21D1;',
		'&rArr;' => '&#x21D2;',
		'&dArr;' => '&#x21D3;',
		'&hArr;' => '&#x21D4;',
		'&forall;' => '&#x2200;',
		'&part;' => '&#x2202;',
		'&exist;' => '&#x2203;',
		'&empty;' => '&#x2205;',
		'&nabla;' => '&#x2207;',
		'&isin;' => '&#x2208;',
		'&notin;' => '&#x2209;',
		'&ni;' => '&#x220B;',
		'&prod;' => '&#x220F;',
		'&sum;' => '&#x2211;',
		'&minus;' => '&#x2212;',
		'&lowast;' => '&#x2217;',
		'&radic;' => '&#x221A;',
		'&prop;' => '&#x221D;',
		'&infin;' => '&#x221E;',
		'&ang;' => '&#x2220;',
		'&and;' => '&#x2227;',
		'&or;' => '&#x2228;',
		'&cap;' => '&#x2229;',
		'&cup;' => '&#x222A;',
		'&int;' => '&#x222B;',
		'&there4;' => '&#x2234;',
		'&sim;' => '&#x223C;',
		'&cong;' => '&#x2245;',
		'&asymp;' => '&#x2248;',
		'&ne;' => '&#x2260;',
		'&equiv;' => '&#x2261;',
		'&le;' => '&#x2264;',
		'&ge;' => '&#x2265;',
		'&sub;' => '&#x2282;',
		'&sup;' => '&#x2283;',
		'&nsub;' => '&#x2284;',
		'&sube;' => '&#x2286;',
		'&supe;' => '&#x2287;',
		'&oplus;' => '&#x2295;',
		'&otimes;' => '&#x2297;',
		'&perp;' => '&#x22A5;',
		'&sdot;' => '&#x22C5;',
		'&lceil;' => '&#x2308;',
		'&rceil;' => '&#x2309;',
		'&lfloor;' => '&#x230A;',
		'&rfloor;' => '&#x230B;',
		'&lang;' => '&#x2329;',
		'&rang;' => '&#x232A;',
		'&loz;' => '&#x25CA;',
		'&spades;' => '&#x2660;',
		'&clubs;' => '&#x2663;',
		'&hearts;' => '&#x2665;',
		'&diams;' => '&#x2666;',
		'&quot;' => '&#x22;',
		'&amp;' => '&#x26;',
		'&lt;' => '&#x3C;',
		'&gt;' => '&#x3E;',
		'&OElig;' => '&#x152;',
		'&oelig;' => '&#x153;',
		'&Scaron;' => '&#x160;',
		'&scaron;' => '&#x161;',
		'&Yuml;' => '&#x178;',
		'&circ;' => '&#x2C6;',
		'&tilde;' => '&#x2DC;',
		'&ensp;' => '&#x2002;',
		'&emsp;' => '&#x2003;',
		'&thinsp;' => '&#x2009;',
		'&zwnj;' => '&#x200C;',
		'&zwj;' => '&#x200D;',
		'&lrm;' => '&#x200E;',
		'&rlm;' => '&#x200F;',
		'&ndash;' => '&#x2013;',
		'&mdash;' => '&#x2014;',
		'&lsquo;' => '&#x2018;',
		'&rsquo;' => '&#x2019;',
		'&sbquo;' => '&#x201A;',
		'&ldquo;' => '&#x201C;',
		'&rdquo;' => '&#x201D;',
		'&bdquo;' => '&#x201E;',
		'&dagger;' => '&#x2020;',
		'&Dagger;' => '&#x2021;',
		'&permil;' => '&#x2030;',
		'&lsaquo;' => '&#x2039;',
		'&rsaquo;' => '&#x203A;',
		'&euro;' => '&#x20AC;',
	);

	function mkFeederButtons($rel_uri) {
		if (!$rel_uri)
			$rel_uri ="%s";
		$b = "";
		// feeder buttons
		$b .= "<a href=\"".urlEvalPrototype(sprintf($rel_uri, "rss.xml"))."\"><img src=\""._L("C_SITE.REL_URL")."media/button-rss.gif\" width=\"94\" height=\"15\" alt=\"RSS Feed\" /></a> ";
		$b .= "<a href=\"".urlEvalPrototype(sprintf($rel_uri, "atom.xml"))."\"><img src=\""._L("C_SITE.REL_URL")."media/button-atom.gif\" width=\"94\" height=\"15\" alt=\"Atom Feed\" /></a> ";
		return $b;
	}

	function absoluteURLs($text, $base_url) {
		$base_url = rtrim($base_url, "/");
	
		$text = preg_replace_callback (
			"/(src|href)=\"([^:]*?)\"/i",
			create_function('$a', 'return "{$a[1]}=\"'.$base_url.'/".ltrim($a[2], "/")."\"";'),
			$text
		);

		return $text;
	}

	function htmlEntityEncode($string){
		$string = htmlspecialchars($string);
		return preg_replace("/&[A-Za-z]+;/", " ", strtr($string, $GLOBALS["unicodeEntities"]));
	}

	function feedContentEncode($string) {
		$string = htmlEntityEncode($string);
		if (conf::getkey('core', 'feeder.remove_dangerous_attributes', true, 'b')) {
			$string = preg_replace('/style="[^\"]*"/', '', $string);
		}
		return $string;
	}

	Class timeFormat {
		function iso8601($time = null) {
			if (!$time)
				$time = time();
			return date("Y-m-d\TH:i:s", $time+date("Z", $time)).substr($gwt = date("O", $time), 0, 3).":".substr($gwt, 3);
		}
	}

	Class XMLFeed {
		var $items = Array();
		var $tpl;
		var $supported;
		var $lastbuild = 0;
		function supported() {
			return $this->supported;
		}
		function XMLFeed($type) {
			$this->supported = false;
			
			if (file_exists(GEKKO_TEMPLATE_DIR."default/_layout/$type.feed.tpl")) {
				$this->tpl = new BlockWidget("_layout/$type.feed.tpl");
				$this->supported = true;
			}
			if (!$this->supported())
				appAbort("Unsupported feed type.");
		}
		function addItem($data) {
			
			$data["creationtime"] = fromDate($data["date_created"]);

			if ($this->lastbuild < $data["creationtime"])
				$this->lastbuild = $data["creationtime"];

			if (isset($data["author_id"])) {
				$uinfo = cacheFunc("users::fetchInfo", $data["author_id"], "email, realname");
				$data["AUTHOR_NAME"] = $uinfo["realname"] ? $uinfo["realname"] : "unknown";
				$data["AUTHOR_EMAIL"] = $uinfo["email"] ? $uinfo["email"] : "unknown@example.com";
			}

			if (isset($data["content"]))
				$data["DESCRIPTION"] = absoluteURLs($data["content"], _L("C_SITE.URL"));

			$this->tpl->setArray($data, "ITEM");
			$this->tpl->saveBlock("ITEM");
		}
		function make() {
			$this->tpl->set("LASTBUILDTIME", $this->lastbuild);
			return $this->tpl->make();
		}
		function serve() {
			header("Content-type: text/xml; charset=utf-8");
			$buff = $this->make();
			cacheSave(GEKKO_PAGE_CACHE_KEY, $buff);
			echo $buff;
			appShutdown();
		}
	}

?>
