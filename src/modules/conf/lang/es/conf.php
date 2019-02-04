<?php


	if (!defined("IN-GEKKO")) die("Get a life!");

	Lang::Set("K_SITE.TITLE", "T&iacute;tulo del sitio");
	Lang::Set("K_SITE.NAME", "Nombre del sitio");
	Lang::Set("K_SITE.DESCRIPTION", "Descripci&oacute;n del sitio");
	Lang::Set("K_SITE.SLOGAN", "Lema del sitio");
	Lang::Set("K_SITE.COPYRIGHT", "Nota de copyright");
	Lang::Set("K_SITE.SHOW_TEXT_TITLE", "Mostrar t&iacute;tulo y lema en la cabecera");
	Lang::Set("K_SITE.TEMPLATE", "Plantilla");
	Lang::Set("K_SITE.STYLESHEET", "Hoja de estilo para plantilla");
	Lang::Set("K_SITE.SMILEYSTHEME", "Tema de smileys (&iacute;conos gestuales)");
	Lang::Set("K_SITE.ICONTHEME", "Tema de &Iacute;conos");
	Lang::Set("K_SITE.LANG", "Idioma de interfaz");
	Lang::Set("K_SITE.HOUR_DIFFERENCE", "Direfencia horaria (puedes usar horas positivas o negativas). Hora del sistema: {=date(\"h:i\")=}");
	Lang::Set("K_SITE.GZIP_OUTPUT", "Activar salida comprimida GZIP");
	Lang::Set("K_SITE.FOOTER", "Nota al Pie de la P&aacute;gina");
	Lang::Set("K_SITE.CONTACT_MAIL", "Correo electr&oacute;nico de contacto");
	Lang::Set("K_HTML_FILTER", "Filtro inteligente de HTML (basado en privilegios)");
	Lang::Set("K_PLUGINS.MSIEPNGFIX.DISABLE", "Desactivar plugin para imagenes PNG bajo Internet Explorer. Puede evitar
	<i>crashes</i> usando este navegador pero las imagenes PNG como &iacute;conos y dem&aacute;s perder&aacute;n
	la transparencia. Se recomienda NO usar Microsoft Internet Explorer hasta que sea un programa decente.");
	Lang::Set("K_MAGIC_BLACKLIST", "Activar bloqueo temporal contra usuarios que envian demasiadas peticiones en
	periodos cortos de tiempo (puede prevenir ataques comunes de pseudocrackers)");
	Lang::Set("K_GBBCODE", "Evaluacion de etiquetas GBBCode");
	Lang::Set("K_GBBCODE.SMILEYS", "Reemplazar cadenas como: ;) :) :O :-P por &iacute;conos.");
	Lang::Set("K_SMTP.ENABLE", "Correo por SMTP en vez de mail()");
	Lang::Set("K_RTBEDITOR", "Editor Visual de HTML, compatible con <a href=\"http://mozilla.org/products/firefox\">Firefox</a>");
?>