<?php


	if (!defined("IN-GEKKO")) die("Get a life!");

	Lang::Set("E_FTP_TEST_FAILED", "Hubo un error al intentar conectar al servicio FTP
	en este mismo servidor. Probablemente el servicio est&eacute; apagado o corriendo
	en un puerto diferente.");
	Lang::Set("E_COULDNT_CONNECT", "No se pudo conectar al servidor especificado.");

	Lang::Set("L_INSTALL_PACKAGE", "Instalar paquete");
	Lang::Set("L_PACKAGE", "Paquete");
	Lang::Set("L_FTP_CONFIGURATION", "Configuraci&oacute;n FTP");
	Lang::Set("L_SERVER", "Servidor FTP");
	Lang::Set("L_FTP_PATH", "Ruta relativa (raiz de Gekko)");
	Lang::Set("L_USER", "Usuario");
	Lang::Set("L_PASS", "Contrase&ntilde;a");
	Lang::Set("L_PACKAGE_SOURCE", "Localizaci&oacute;n del Paquete");
	Lang::Set("L_INSTALL", "Instalar");
	Lang::Set("L_FTP_SERVER_NAME", "Servidor FTP: <b>%s</b>");
	Lang::Set("L_TEST_FTP", "Probar FTP");
	
	Lang::Set("L_FTP_TEST_PASSED", "La configuraci&oacute;n de acceso FTP es correcta.");
	Lang::Set("L_WRONG_FTP_PATH", "Ruta incorrecta. Intenta especificar la misma que usas
	para ingresar a FTP desde tu programa favorito. Debe ser la misma donde est&aacute; el
	archivo \"conf.php\" de Gekko.");
	Lang::Set("L_LOGIN_FAILED", "Contrase&ntilde;a o nombre de usuario
	incorrectos.");
	Lang::Set("L_PACKAGE_FORMAT_ERROR", "Error en el formato del paquete.");
	Lang::Set("L_COULDNT_GET_PACKAGE", "No se pudo obtener el paquete.");
	Lang::Set("L_PACKAGE_INSTALL_SUCCESS", "El paquete fue instalado correctamente.");
	Lang::Set("L_PROBABLE_FTP_PATH", "Solo estoy adivinando, pero es probable que la ruta correcta
	sea \"%s\" o sea esa parte la que falta a la ruta que ingresaste.")
?>