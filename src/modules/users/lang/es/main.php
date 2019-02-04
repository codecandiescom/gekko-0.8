<?php


	if (!defined("IN-GEKKO")) die("Get a life!");

	Lang::Set("E_LOGIN_FAILED", "Ingreso fallido, el nombre de usuario no existe
	o su contrase&ntilde;a es incorrecta. Por favor verifique sus datos.");

	Lang::Set("E_TOO_MANY_FAILED_LOGIN_ATTEMPTS", "Demasiado ingresos fallidos,
	el acceso a la cuenta se le ha bloqueado temporalmente.");

	Lang::Set("L_MODIFY_PASSWORD", "Modificar contrase&ntilde;a");
	Lang::Set("L_NEW_ACCOUNT", "Registro de nuevo usuario");
	Lang::Set("L_ALPHA_CHARS", "Puede usar letras, numeros y gui&oacute;n bajo (_), sin espacios.");
	Lang::Set("L_CONFIRM_ACCOUNT", "Confirmar cuenta");
	Lang::Set("L_RESEND_CONFIRMATION", "Reenviar confirmaci&oacute;n");

	Lang::Set("L_PROFILE", "Perfil");

	Lang::Set("L_A_HOUR", "Una hora");
	Lang::Set("L_A_DAY", "Un d&iacute;a");
	Lang::Set("L_A_WEEK", "Una semana");
	Lang::Set("L_A_MONTH", "Una mes");

	Lang::Set("E_SHORT_PASSWORD", "La contrase&ntilde;a es demasiado corta");
	Lang::Set("E_PASSWORD_MISMATCH", "Las contrase&ntilde;as no coinciden");
	Lang::Set("E_INVALID_USERNAME", "El nombre de usuario contiene caracteres no v&aacute;lidos. Puede usar s&oacute;lo letras, numeros y gui&oacute;n bajo (_), sin espacios.");
	Lang::Set("E_INVALID_EMAIL", "La direcci&oacute;n e-mail parece incorrecta");
	Lang::Set("E_DUPLICATED_USERNAME", "El nombre de usuario ya fue tomado por algui&eacute;n m&aacute;s");
	Lang::Set("E_DUPLICATED_EMAIL", "El e-mail ya fue asignado a otra cuenta");

	Lang::Set("L_CREATE_ACCOUNT", "Crear cuenta");
	Lang::Set("L_ACCOUNT_INFO", "Informaci&oacute;n de Cuenta");
	Lang::Set("L_USER_REGISTRATION", "Registro de Usuario");
	Lang::Set("L_SESSION_LENGTH", "Duraci&oacute;n de la sesi&oacute;n (minutos)");
	Lang::Set("L_USER_GROUPS", "Grupos de Usuario");

	Lang::Set("L_NO_SUCH_PROFILE", "Este usuario no ha creado un perfil p&uacute;blico.");

	Lang::Set("L_ASCENDENT", "Ascendente");
	Lang::Set("L_SORT", "Ordenar");
	Lang::Set("L_DESCENDENT", "Descendente");

	Lang::Set("L_ACCOUNT_CONFIRMATION", "Confirmar registro");

	Lang::Set("L_ACCOUNT_CONFIRMATION_SENT", "Un email se le ha enviado, por favor, despu&eacute;s de recibirlo
	complete la informaci&oacute;n en este formulario para que su cuenta sea activada.");

	Lang::Set("L_USER_PANEL", "Panel de Usuario");
	Lang::Set("L_USER_SETTINGS", "Preferencias de Usuario");
	Lang::Set("L_MY_PROFILE", "Mi Perfil");

	Lang::Set("L_MY_ACCOUNT_HINT", "Aqu&iacute; puede cambiar las preferencias de su cuenta, tales como nombre
	de usuario, correo electr&oacute;nico, etc. Para que los cambios se lleven a cabo es necesario indicar la
	contrase&ntilde;a actual. Usted puede cambiar tambi&eacute;n su
	<a href=\"{=URLEVALPROTOTYPE(\"index.php/module=users/action=profile\")=}\">Perfil</a>");

	Lang::Set("L_CURRENT_PASSWORD", "Contrase&ntilde;a Actual");
	Lang::Set("L_NEW_PASSWORD", "Contrase&ntilde;a Nueva");
	Lang::Set("L_CHANGE_PASSWORD", "Modificar la Contrase&ntilde;a");

	Lang::Set("L_MY_PROFILE_HINT", "El perfil es la informaci&oacute;n
	que se hace p&uacute;blica para los visitantes del sitio, Usted puede escojer que informaci&oacute;n
	desea revelar.<br />
	<b>Enlaces:</b>
	<a href=\"{=URLEVALPROTOTYPE(\"index.php/module=users/action=account\")=}\">Preferencias de Usuario</a>");

	Lang::Set("L_AVATAR", "Avatar");
	Lang::Set("L_BIRTHDATE", "Fecha de nacimiento (d&iacute;a/mes/a&ntilde;o)");
	Lang::Set("L_ABOUT_ME", "Un poco acerca de m&iacute;");
	Lang::Set("L_LOCATION", "Ubicaci&oacute;n geogr&aacute;fica");
	Lang::Set("L_GENDER", "G&eacute;nero");
	Lang::Set("L_MSN", "MSN Messenger ID");
	Lang::Set("L_YIM", "Yahoo Messenger ID");
	Lang::Set("L_ICQ", "ICQ");
	Lang::Set("L_SIGNATURE", "Firma personal");
	Lang::Set("L_PUBLIC_EMAIL", "Correo Electr&oacute;nico (P&uacute;blico)");
	Lang::Set("L_WEBSITE", "Sitio web");
	Lang::Set("L_AGE", "Edad");
	Lang::Set("L_YEARS", "A&ntilde;os");
	Lang::Set("L_USER_PROFILE", "Perfil del usuario");
	Lang::Set("L_EDIT_PROFILE", "Editar Perfil");
	Lang::Set("L_MALE", "Hombre");
	Lang::Set("L_FEMALE", "Mujer");
	Lang::Set("L_LOGIN", "Ingresar");
	Lang::Set("L_REALNAME", "Nombre Real");
	Lang::Set("L_WELCOME", "Bienvenid@");
	Lang::Set("L_LAST_LOGIN", "&Uacute;ltimo login");
	Lang::Set("L_CREATE_USER", "Crear usuario");

	Lang::Set("L_MUST_CHANGE_PASSWORD_TOO", "La contrase&ntilde;a debe ser cambiada tambi&eaucte;n");

	Lang::Set("L_TRIGGER_GROUPS", "Ver/Ocultar Grupos &raquo;");
	Lang::Set("L_CONFIRM_PASSWORD", "Confirmar contrase&ntilde;a");

	Lang::Set("L_CONTACT_INFO", "Informaci&oacute;n de Contacto");

	Lang::Set("L_CONFIRMATION_CODE", "C&oacute;digo de confirmaci&oacute;n");
	Lang::Set("L_RESET_PASSWORD", "Reestablecer Contrase&ntilde;a");
	Lang::Set("L_GROUP_USERS", "Usuarios: Mantiene derechos sobre la creaci&oacute;n, modificaci&oacute;n y
	borrado de usuarios.");
	Lang::Set("L_GROUP_NORMAL_USER", "Usuario normal: El es Grupo por defecto al que se agregan los nuevos
	miembros.");
	Lang::Set("L_GROUP_ANONYMOUS", "An&oacute;nimo: Grupo autom&aacute;tico para los usuarios sin acceso a
	una cuenta.");
?>