<?php

	if (!defined("IN-GEKKO")) die("Get a life!");

	setlocale(LC_ALL, "es_MX.utf8");

	// actions and buttons
	Lang::Set("L_CANCEL", "Cancelar");
	Lang::Set("L_DELETE", "Borrar");
	Lang::Set("L_DISABLE", "Desactivar");
	Lang::Set("L_EDIT", "Modificar");
	Lang::Set("L_ENABLE", "Activar");
	Lang::Set("L_NEW", "Nuevo");
	Lang::Set("L_GO_BACK", "Regresar");
	Lang::Set("L_GO_FORWARD", "Avanzar");
	Lang::Set("L_MOVE_DOWN", "Mover abajo");
	Lang::Set("L_MOVE_UP", "Mover arriba");
	Lang::Set("L_OK", "Aceptar");
	Lang::Set("L_PUBLISH", "Publicar");
	Lang::Set("L_RESET", "Reestablecer");
	Lang::Set("L_SUBMIT", "Enviar");
	Lang::Set("L_VIEW_MORE", "Ver m&aacute;s");
	Lang::Set("L_SAVE_CHANGES", "Guardar cambios");
	Lang::Set("L_NAME", "Nombre");
	Lang::Set("L_NEXT", "Siguiente");
	Lang::Set("L_PREV", "Anterior");
	Lang::Set("L_FORMAT_BOLD", "Negrita");
	Lang::Set("L_FORMAT_UNDERLINE", "Subrayado");
	Lang::Set("L_FORMAT_ITALIC", "It&aacute;lica");
	Lang::Set("L_FORMAT_STRIKETHROUGH", "Tachada");
	Lang::Set("L_FORMAT_FORECOLOR", "Color de fuente");
	Lang::Set("L_FORMAT_HILITECOLOR", "Color de fondo");
	Lang::Set("L_FORMAT_INDENT", "Incrementar Indentaci&oacute;n");
	Lang::Set("L_FORMAT_OUTDENT", "Decrementar indentaci&iacute;n");
	Lang::Set("L_FORMAT_JUSTIFYLEFT", "Alinear a la izquierda");
	Lang::Set("L_FORMAT_JUSTIFYRIGHT", "Alinear a la derecha");
	Lang::Set("L_FORMAT_JUSTIFYCENTER", "Alinear al centro");
	Lang::Set("L_FORMAT_JUSTIFYFULL", "Justificar bloque");
	Lang::Set("L_FORMAT_CODE", "C&oacute;digo");
	Lang::Set("L_FORMAT_CREATELINK", "Crear enlace");
	Lang::Set("L_FORMAT_INSERTORDEREDLIST", "Lista ordenada");
	Lang::Set("L_FORMAT_INSERTUNORDEREDLIST", "Lista");
	Lang::Set("L_FORMAT_QUOTE", "Cita");
	Lang::Set("L_FORMAT_INSERTFILE", "Insertar archivo");
	Lang::Set("L_FORMAT_INSERTHORIZONTALRULE", "L&iacute;nea horizontal");
	Lang::Set("L_FORMAT_TABLE", "Tabla");
	Lang::Set("L_FORMAT_SUBSCRIPT", "Sub&iacute;ndice");
	Lang::Set("L_FORMAT_SUPERSCRIPT", "Super&iacute;ndice");
	Lang::Set("L_FORMAT_SMILEY", "Emoticono");
	Lang::Set("L_FORMAT_REDO", "Rehacer");
	Lang::Set("L_FORMAT_UNDO", "Deshacer");
	Lang::Set("L_FORMAT_REMOVEFORMAT", "Quitar formato");
	Lang::Set("L_FORMAT_FULLSCREEN", "Editor a pantalla completa/normal");
	Lang::Set("L_AUTH_ACCESS", "Autorizaci&oacute;n de acceso");
	Lang::Set("L_THUMBNAIL_URL", "URL de vista previa");
	Lang::Set("L_KEEP_SIZE", "Mantener dimensiones originales de imagen");
	Lang::Set("L_ABSOLUTE_LINK", "Enlace absoluto");
	Lang::Set("L_STYLESHEET_CHANGE", "Est&aacute;s viendo la plantilla <b>%s</b> con el estilo <b>%s</b>");


	// skeleton and sections
	Lang::Set("L_ADD_ITEM", "Agregar &iacute;tem");
	Lang::Set("L_CREATE", "Crear");
	Lang::Set("L_ENTRIES", "Registros");
	Lang::Set("L_ITEMS", "&Iacute;tems");
	Lang::Set("L_LIST", "Lista");
	Lang::Set("L_MANAGE", "Administrar");
	Lang::Set("L_MODIFY", "Modificar");

	// forms
	Lang::Set("L_ADVANCED", "Opciones Avanzadas");
	Lang::Set("L_ADVANCED_MODE", "Activar/Desactivar Modo Avanzado");
	Lang::Set("L_AUTO", "(automático)");
	Lang::Set("L_CLASS", "Clase");
	Lang::Set("L_COMMA_SEPARATED_LIST", "Lista separada por comas (,)");
	Lang::Set("L_CONTENT", "Contenido");
	Lang::Set("L_CUSTOMIZATION", "Personalizaci&oacute;n");
	Lang::Set("L_CUSTOM_ICON", "&Iacute;cono personalizado");
	Lang::Set("L_DESCRIPTION", "Descripci&oacute;n");
	Lang::Set("L_DISABLED", "Inactivo");
	Lang::Set("L_DONT_ALIGN", "No alinear");
	Lang::Set("L_ENABLED", "Activo");
	Lang::Set("L_FILE", "Archivo");
	Lang::Set("L_FOOTER", "Pie de Art&iacute;culo");
	Lang::Set("L_GET_FILE", "Obtener archivo");
	Lang::Set("L_ICON", "&Iacute;cono");
	Lang::Set("L_ID", "ID");
	Lang::Set("L_INSERT_FILE", "Insertar archivo");
	Lang::Set("L_ITEM", "&Iacute;tem");
	Lang::Set("L_LARGER_SIDE", "Longitud mayor");
	Lang::Set("L_LINK_TO_ORIGINAL", "Enlazar al original (s&oacute;lo im&aacute;genes)");
	Lang::Set("L_LINK_TO_SELECTED", "Enlazar al elemento seleccionado");
	Lang::Set("L_LOCAL_FILE", "Archivo local");
	Lang::Set("L_MODULE", "M&oacute;dulo");
	Lang::Set("L_MODULES", "M&oacute;dulos");
	Lang::Set("L_OPTIONAL", "Opcional");
	Lang::Set("L_ORDER", "&Oacute;rden");
	Lang::Set("L_PHOTO_STYLE", "Estilo fotograf&iacute;a");
	Lang::Set("L_POSITION", "Posici&oacute;n");
	Lang::Set("L_PREVIEW", "Vista Previa");
	Lang::Set("L_PROPERTIES", "Propiedades");
	Lang::Set("L_REQUIRED", "<b>*</b>");
	Lang::Set("L_REQUIRED_FIELDS", "NOTA:<br />Los campos marcados con <b>*</b> no pueden quedar vac&iacute;os.");
	Lang::Set("L_SORT_BY", "Ordenar por");
	Lang::Set("L_STATUS", "Estado");
	Lang::Set("L_TITLE", "T&iacute;tulo");
	Lang::Set("L_URL", "Direcci&oacute;n Web (URL)");
	Lang::Set("L_UPLOAD_SIZE_LIMIT", "L&iacute;mite {INI_UPLOAD_MAX_FILESIZE}");

	// items status
	Lang::Set("L_DRAFT", "Borrador");
	Lang::Set("L_PUBLISHED", "Publicado");

	// editor specific
	Lang::Set("L_ALLOWED_TAGS", "Etiquetas permitidas");
	Lang::Set("L_DEFAULT", "Predeterminado");
	Lang::Set("L_FONT_COLOR", "Color de Fuente");
	Lang::Set("L_FONT_NAME", "Tipograf&iacute;a");
	Lang::Set("L_SOURCE_MODE", "Modo C&oacute;digo");
	Lang::Set("L_HEADINGS", "Encabezado");
	Lang::Set("L_HEADING_1", "T&iacute;tulo");
	Lang::Set("L_HEADING_2", "Subt&iacute;tulo");
	Lang::Set("L_HEADING_3", "Subt&iacute;tulo 1");
	Lang::Set("L_HEADING_4", "Subt&iacute;tulo 2");
	Lang::Set("L_HEADING_5", "Resaltado");
	Lang::Set("L_VISUAL_MODE", "Modo Visual");
	Lang::Set("L_UPDATED", "Actualizado");

	// errors
	Lang::Set("E_ACCESS_DENIED", "Acceso denegado. Tus privilegios son insuficientes.");
	Lang::Set("E_DEMO_MODE", "Este sitio est&aacute; en <b>modo demo</b>. No puedes ejecutar este tipo de acciones.");
	Lang::Set("E_INSUFFICIENT_DATA", "Los datos ingresados son insuficientes para procesar su petici&oacute;n.");
	Lang::Set("E_UPLOAD_FAILED", "Hubo problemas al tratar de cargar '%s' verifica que el archivo sea menor a {INI_UPLOAD_MAX_FILESIZE}");
	Lang::Set("E_INVALID_EMAIL", "La direcci&oacute;n de correo parece incorrecta.");
	Lang::Set("L_JAVASCRIPT_REQUIRED", "Por favor activa Javascript.");

	// generic words
	Lang::Set("L_ANONYMOUS", "An&oacute;nimo");
	Lang::Set("L_ANY", "(todos)");
	Lang::Set("L_AUTHOR", "Autor");
	Lang::Set("L_BOTTOM", "Fondo");
	Lang::Set("L_CENTER", "Centrado");
	Lang::Set("L_CREATED", "Creado");
	Lang::Set("L_DATABASE_QUERIES", "Consultas");
	Lang::Set("L_GEKKO_IS_FREE_SOFTWARE", "<a href=\"http://www.gekkoware.org\" target=\"blank\">Gekko</a> es <a href=\"http://www.gnu.org/philosophy/free-sw.es.html\" target=\"_blank\">Software Libre</a> distribu&iacute;do bajo la <a href=\"http://www.gnu.org/licenses/gpl.html\" target=\"_blank\">GNU/GPL</a>.");
	Lang::Set("L_GUESTS", "Invitados");
	Lang::Set("L_INDEX", "Inicio");
	Lang::Set("L_LEFT", "Izquierda");
	Lang::Set("L_LOADING", "Cargando...");
	Lang::Set("L_MAIN", "Principal");
	Lang::Set("L_MEMORY_USAGE", "Uso de memoria");
	Lang::Set("L_MODE", "Modo");
	Lang::Set("L_MODIFIED", "Modificado");
	Lang::Set("L_NONE", "Ninguno");
	Lang::Set("L_NORMAL", "Normal");
	Lang::Set("L_PAGES", "P&aacute;ginas");
	Lang::Set("L_POSTED_BY", "Escrito por");
	Lang::Set("L_POWERED_BY", "Potencia <a href=\"http://www.gekkoware.org\" target=\"blank\">Gekko</a>");
	Lang::Set("L_RENDER_TIME", "Tiempo de Generaci&oacute;n");
	Lang::Set("L_RIGHT", "Derecha");
	Lang::Set("L_SAFE", "Seguro");
	Lang::Set("L_TOP", "Arriba");
	Lang::Set("L_TOTAL", "Total");
	Lang::Set("L_TYPE", "Tipo");
	Lang::Set("L_UNKNOWN", "Desconocido");
	Lang::Set("L_VIEW", "Ver");

	// time and date
	Lang::Set("L_FULL_DATE", "%A %e de %B de %Y, %H:%M");
	Lang::Set("L_MINUTES_AGO", "Hace %M minutos");
	Lang::Set("L_NEXT_MINUTES", "Dentro de %M minutos");
	Lang::Set("L_NEXT_SECONDS", "Dentro de %S segundos");
	Lang::Set("L_SECONDS_AGO", "Hace %S segundos");
	Lang::Set("L_TODAY_AT", "Hoy a las %H:%M");
	Lang::Set("L_TOMORROW_AT", "Ma&ntilde;ana a las %H:%M");
	Lang::Set("L_YESTERDAY_AT", "Ayer a las %H:%M");
	Lang::Set("L_DATE_MODIFIED", "Modificado");
	Lang::Set("L_DATE_CREATED", "Creado");

	// notes
	Lang::Set("L_PLEASE_DELETE_INSTALLER", "Se recomienda, por motivos de seguridad, borrar o cambiar el nombre de los archivos de instalaci&oacute;n <b>install.php</b> y el directorio <b>install/</b>");
	Lang::Set("L_CHECK_WRITABLE_DIRECTORIES", "Es necesario que los directorios <b>data</b>
	y <b>temp</b> tengan permisos de escritura (CHMOD 777).");

	Lang::Set("L_SSL_ENABLED", "El
	{=ini_get(\"upload_max_filesize\")=}
	Modo Seguro (<a href=\"http://es.wikipedia.org/wiki/SSL\">SSL</a>) est&aacute; activo, la conexi&oacute;n entre el servidor y usted est&aacute; siendo encriptada. Si no est&aacute; realizando tareas de suma importancia que requieran la encripci&oacute;n de datos, tales como ingresar contrase&ntilde;as o datos privados, le sugerimos <a href=\"http://{SERVER_HTTP_HOST}{SERVER_REQUEST_URI}\">salir del modo seguro</a>.");

	Lang::Set("L_EMPTY_PAGE", 'Esta P&aacute;gina a&uacute;n no contiene informaci&oacute;n o
	Usted no est&aacute; autorizado a verla. Si usted es el Administrador del sitio puede
	{=createLink("index.php/module=users/action=login", "ingresar")=} al Panel de Administraci&oacute;n para agregar contenido.');
	
	Lang::Set("L_ADMIN_EMPTY_PAGE", "Ingresa al
	{=createLink(\"index.php/module=admin\", \"Panel de administraci&oacute;n\")=}
	para agregar contenido a
	{=createLink(\"index.php/module=admin/base=\".constant(\"GEKKO_REQUEST_MODULE\"), \"&eacute;ste m&oacute;dulo\")=}.");

	// obtienes spanglish a bajo costo ;)
	Lang::Set("L_GEKKO_WELCOME", "Bienvenido a su nueva instalaci&oacute;n del Entorno de Desarrollo de Aplicaciones Web <a href=\"http://www.gekkoware.org\">Gekko</a>.<br /> Gekko es Software Libre protegido por la <a href=\"http://www.gnu.org/licenses/gpl.txt\">GNU/GPL</a>.<br /> <hr /> &iexcl;Gracias por usar <a href=\"http://www.gekkoware.org\">Gekko</a>!");
?>
