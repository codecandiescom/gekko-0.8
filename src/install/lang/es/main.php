<?php


	if (!defined("IN-GEKKO")) die("Get a life!");

	Lang::Set("L_SERVER", "Servidor");

	Lang::Set("L_ACCOUNT", "Cuenta de Acceso");
	Lang::Set("L_PATH", "Ruta");
	Lang::Set("L_INSTALLATION", "Instalaci&oacute;n");
	Lang::Set("L_STMP_ACCESS_HINT", "Si posee acceso a una cuenta de Entrega de Correo via SMTP y desea hacer uso de ella en vez de utilizar mail() de PHP ingrese sus datos aqu&iacute;. Si usted no lo desea, o prefiere configurarlo mas tarde, deje los campos en blanco.");

	Lang::Set("L_INSTALLATION_TYPE", "Tipo de instalaci&oacute;n");
	Lang::Set("L_CLEAN_INSTALL", "Instalaci&oacute;n limpia");
	Lang::Set("L_VERSION_UPGRADE", "Actualizaci&oacute;n (Gekko &gt;= 0.5)");
	Lang::Set("L_CONFIGURATION", "Configuraci&oacute;n");

	Lang::Set("L_UPGRADE", "Actualizaci&oacute;n");
	Lang::Set("L_UPGRADE_HINT", "Si usted no desea que un modulo nuevo sea instalado en su distribuci&oacute;n de Gekko por favor borre su respectivo directorio dentro de <i>modules</i>");

	Lang::Set("L_INSTALL_NEW_MODULES", "Instalar los modulos nuevos encontrados");

	Lang::Set("L_MANUAL_WAY", "Forma manual");

	Lang::Set("L_FTP_SERVICE", "Servicio FTP");

	Lang::Set("L_PERMISSIONS_CHANGE", "Cambio de permisos");

	Lang::Set("L_PERMISSIONS_CHANGE_HINT", "Gekko necesita poder escribir bajo ciertos directorios, si tiene servicio FTP los cambios pueden realizarse de forma autom&aacute;tica llenando &eacute;ste formulario. Si desea llevar a cabo los cambios de forma manual deber&aacute; cambiar los permisos de los directorios <i>data</i>, <i>temp</i> y el archivo <i>dbconf.php</i> a escribibles (CHMOD 777) y actualizar &eacute;sta p&aacute;gina. No se preocupe, la informaci&oacute;n de acceso FTP no ser&aacute; salvada sin su autorizaci&oacute;n.");

	Lang::Set("L_FTP_PATH_HINT", "La ruta debe ser relativa al directorio donde la sesi&oacute;n FTP iniciar&aacute; y debe ser el mismo raiz de Gekko (donde se encuentra index.php)");

	Lang::Set("L_DATABASE", "Base de datos");

	Lang::Set("L_DATABASE_HINT", "Escoja un <b>controlador</b> que sea compatible con su base de datos y proporcione los datos de acceso para almacenar informaci&oacute;n. Si no est&aacute; seguro de que opciones marcar, puede dejarlas sin cambio. Por favor haga un respaldo de sus datos antes de continuar.");

	Lang::Set("L_INSTALLATION_TYPE_HINT", "Escoja la <b>tarea</b> requerida, <i>actualizaci&oacute;n</i> o <i>instalaci&oacute;n</i>, la <i>actualizaci&oacute;n</i> adaptar&aacute; la informaci&oacute;n de alguna versi&oacute;n anterior de Gekko a la m&aacute;s reciente, mientras que la <i>instalaci&oacute;n</i> crear&aacute; un sitio nuevo de Gekko.");

	Lang::Set("L_DRIVER", "Controlador");
	Lang::Set("L_DATABASE", "Base de datos");
	Lang::Set("L_OPTIONS", "Opciones");
	Lang::Set("L_TABLE_PREFIX", "Prefijo de tablas");
	Lang::Set("L_DROP_PREFIXED_TABLES", "Vaciar tablas que utilizen el prefijo");
	Lang::Set("L_DROP_DATABASE", "Vaciar la base de datos entera antes de instalar");

	Lang::Set("L_ADMIN_CREATION", "Creaci&oacute;n del Administrador");
	Lang::Set("L_REALNAME", "Nombre Real");
	Lang::Set("L_CONFIRM_PASSWORD", "Confirmar Contrase&ntilde;a");

	Lang::Set("L_ADMIN_CREATION_HINT", "Especifique los datos para la creaci&oacute;n del usuario <b>administrador</b> del sistema. Sea cuidadoso al elegir su <b>contrase&ntilde;a</b>, se recomienda utilizar una de longitud mayor a 8 caracteres, dif&iacute;cil de adivinar, que contenga letras min&uacute;sculas, may&uacute;sculas y n&uacute;meros.");

	Lang::Set("L_SITE_CONFIGURATION", "Configuraci&oacute;n del Sitio");
	Lang::Set("L_SITE_LANG", "Idioma de interfaz");
	Lang::Set("L_SITE_TITLE", "T&iacute;tulo del sitio");
	Lang::Set("L_SITE_NAME", "Nombre del sitio");
	Lang::Set("L_SITE_SLOGAN", "Lema del sitio");
	Lang::Set("L_SITE_DESCRIPTION", "Descripci&oacute;n del sitio");
	Lang::Set("L_SITE_COPYRIGHT", "Mensaje de Copyright");
	Lang::Set("L_SITE_CONTACT_MAIL", "E-Mail de Contacto");

	Lang::Set("L_PRIMARY_AUDITORY", "Auditorio Principal (p&uacute;blico objetivo)");
	Lang::Set("L_PRIMARY_LANG", "Idioma primario");
	Lang::Set("L_PRIMARY_COUNTRY", "Pa&iacute;s primario");

	Lang::Set("L_RTB_EDITOR", "Editor de Contenido Visual");
	Lang::Set("L_GBB_CODE", "Etiquetas de estilo GBB");
	Lang::Set("L_USERS_REQUIRE_CONFIRMATION", "El registro de nuevas cuentas require confirmaci&oacute;n (v&iacute;a e-mail)");
	Lang::Set("L_USERS_ENABLE_SELF_REGISTRATION", "Permitir el registro de nuevos usuarios a los visitantes");
	Lang::Set("L_USERS_SEND_WELCOME_LETTER", "Enviar mensaje de bienvenida a nuevos usuarios");

	Lang::Set("L_ADVANCED", "Avanzado");
	Lang::Set("L_COOKIE_PREFIX", "Nombre de Cookie");
	Lang::Set("L_COOKIE_PATH", "Ruta de Cookie");
	Lang::Set("L_COOKIE_LIFE", "Vida de Cookie (segundos)");
	Lang::Set("L_ENABLE_GZIP_OUTPUT", "Activar salida comprimida GZIP");
	Lang::Set("L_FEATURES", "Caracter&iacute;sticas");

	Lang::Set("L_DISABLE_MSIE_PNGFIX", "Desactivar plug-in para transparencia de im&aacute;genes PNG bajo Microsoft Internet Explorer (puede solucionar \"cuelgues\" del navegador).");

	Lang::Set("L_POST_INSTALLATION_OPTIONS", "Opciones Post-Instalaci&oacute;n");
	Lang::Set("L_CREATE_BASIC_MENU", "Crear men&uacute; b&aacute;sico");
	Lang::Set("L_DELETE_INSTALLATION_FILES", "Borrar archivos de instalaci&oacute;n");

	Lang::Set("L_SITE_CONFIGURATION_HINT", "Aqu&iacute; puede personalizar su sitio web, todas estas opciones podr&aacute;n ser modificadas posteriormente. Si en este momento no desea configurar Gekko, puede seguir con la instalaci&oacute;n sin problemas, los valores por defecto funcionar&aacute;n bien.");

	Lang::Set("L_SECURITY_CHECK", "Verificaci&oacute;n de seguridad");

	Lang::Set("L_SECURITY_CHECK_HINT", "Como medida de seguridad, especifique las contrase&ntilde;as que utiliz&oacute; durante los pasos anteriores.");

	Lang::Set("L_FTP_PASSWORD", "Contrase&ntilde;a FTP");
	Lang::Set("L_DATABASE_PASSWORD", "Contrase&ntilde;a de base de datos");

	Lang::Set("L_THANKS", "&iexcl;Gracias!");
	Lang::Set("L_THANKS_MESSAGE", "Gracias por usar Gekko, esperamos que usted tambi&eacute;n disfrute y apoye el desarrollo de <a href=\"http://www.gnu.org/philosophy/free-sw.es.html\">Software Libre</a>. Como paso final por favor borre el archivo <i>install.php</i> y el directorio <i>install</i>. Puede descargar paquetes de &iacute;conos, estilos, smileys y dem&aacute;s desde el sitio web de Gekko <a href=\"http://www.gekkoware.org\">http://www.gekkoware.org</a>");

	Lang::Set("E_ACCESS_DENIED", "%s: Acceso denegado");
	Lang::Set("E_PASSWORD_MISMATCH", "La contrase&ntilde;a no coincide");
	Lang::Set("E_WRONG_DATABASE_PASSWORD", "Contrase&ntilde;a de base de datos equivocada");
	Lang::Set("E_WRONG_FTP_PASSWORD", "Contrase&ntilde;a FTP equivocada");

	Lang::Set("E_WRONG_PATH", "Ruta incorrecta");

	Lang::Set("L_SMTP_ACCESS", "Acceso SMTP");
	Lang::Set("L_ENABLE_SMTP", "Activar envio de correo usando el cliente SMTP inclu&iacute;do en vez de mail()");
	Lang::Set("L_COOKIE_SETTINGS", "Configuraci&oacute;n de Cookies");
?>