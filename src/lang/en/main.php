<?php

	if (!defined("IN-GEKKO")) die("Get a life!");

	setlocale(LC_ALL, "en_US.utf8");

	// actions and buttons
	Lang::Set("L_CANCEL", "Cancel");
	Lang::Set("L_DELETE", "Delete");
	Lang::Set("L_DISABLE", "Disable");
	Lang::Set("L_EDIT", "Edit");
	Lang::Set("L_ENABLE", "Enable");
	Lang::Set("L_NEW", "New");
	Lang::Set("L_GO_BACK", "Back");
	Lang::Set("L_GO_FORWARD", "Forward");
	Lang::Set("L_MOVE_DOWN", "Move down");
	Lang::Set("L_MOVE_UP", "Move up");
	Lang::Set("L_OK", "OK");
	Lang::Set("L_PUBLISH", "Publish");
	Lang::Set("L_RESET", "Reset");
	Lang::Set("L_SUBMIT", "Submit");
	Lang::Set("L_VIEW_MORE", "View more");
	Lang::Set("L_SAVE_CHANGES", "Save changes");
	Lang::Set("L_NAME", "Name");
	Lang::Set("L_NEXT", "Next");
	Lang::Set("L_PREV", "Previous");
	Lang::Set("L_FORMAT_BOLD", "Bold");
	Lang::Set("L_FORMAT_UNDERLINE", "Underlife");
	Lang::Set("L_FORMAT_ITALIC", "Italic");
	Lang::Set("L_FORMAT_STRIKETHROUGH", "Strikethrough");
	Lang::Set("L_FORMAT_FORECOLOR", "Font color");
	Lang::Set("L_FORMAT_HILITECOLOR", "Background color");
	Lang::Set("L_FORMAT_INDENT", "Increase Indentation");
	Lang::Set("L_FORMAT_OUTDENT", "Decrease indentation");
	Lang::Set("L_FORMAT_JUSTIFYLEFT", "Justify left");
	Lang::Set("L_FORMAT_JUSTIFYRIGHT", "Justify right");
	Lang::Set("L_FORMAT_JUSTIFYCENTER", "Justify center");
	Lang::Set("L_FORMAT_JUSTIFYFULL", "Justify text");
	Lang::Set("L_FORMAT_CODE", "Source Code");
	Lang::Set("L_FORMAT_CREATELINK", "Create link");
	Lang::Set("L_FORMAT_INSERTORDEREDLIST", "Ordered list");
	Lang::Set("L_FORMAT_INSERTUNORDEREDLIST", "Unordered list");
	Lang::Set("L_FORMAT_QUOTE", "Quote");
	Lang::Set("L_FORMAT_INSERTFILE", "Insert file");
	Lang::Set("L_FORMAT_INSERTHORIZONTALRULE", "Horizontal rule");
	Lang::Set("L_FORMAT_TABLE", "Table");
	Lang::Set("L_FORMAT_SUBSCRIPT", "Subscript");
	Lang::Set("L_FORMAT_SUPERSCRIPT", "Superscript");
	Lang::Set("L_FORMAT_SMILEY", "Smiley");
	Lang::Set("L_FORMAT_REDO", "Redo");
	Lang::Set("L_FORMAT_UNDO", "Undo");
	Lang::Set("L_FORMAT_REMOVEFORMAT", "Remove format");
	Lang::Set("L_FORMAT_FULLSCREEN", "Fullscreen editor");
	Lang::Set("L_AUTH_ACCESS", "Access authorization");
	Lang::Set("L_THUMBNAIL_URL", "Thumbnail URL");
	Lang::Set("L_KEEP_SIZE", "Keep original image dimensions");
	Lang::Set("L_ABSOLUTE_LINK", "Absolute link");
	Lang::Set("L_STYLESHEET_CHANGE", "You're currently seeing template <b>%s</b> using style <b>%s</b>");


	// skeleton and sections
	Lang::Set("L_ADD_ITEM", "Add Item");
	Lang::Set("L_CREATE", "Create");
	Lang::Set("L_ENTRIES", "Entries");
	Lang::Set("L_ITEMS", "Items");
	Lang::Set("L_LIST", "List");
	Lang::Set("L_MANAGE", "Manage");
	Lang::Set("L_MODIFY", "Modify");

	// forms
	Lang::Set("L_ADVANCED", "Advanced options");
	Lang::Set("L_ADVANCED_MODE", "Advance Mode (Enable/Disable)");
	Lang::Set("L_AUTO", "(Automatic)");
	Lang::Set("L_CLASS", "Class");
	Lang::Set("L_COMMA_SEPARATED_LIST", "Comma (,) separated list");
	Lang::Set("L_CONTENT", "Content");
	Lang::Set("L_CUSTOMIZATION", "Customization");
	Lang::Set("L_CUSTOM_ICON", "Custom icon");
	Lang::Set("L_DESCRIPTION", "Description");
	Lang::Set("L_DISABLED", "Disabled");
	Lang::Set("L_DONT_ALIGN", "Do not align");
	Lang::Set("L_ENABLED", "Enabled");
	Lang::Set("L_FILE", "File");
	Lang::Set("L_FOOTER", "Article footer");
	Lang::Set("L_GET_FILE", "Get file");
	Lang::Set("L_ICON", "Icon");
	Lang::Set("L_ID", "ID");
	Lang::Set("L_INSERT_FILE", "Insert file");
	Lang::Set("L_ITEM", "Item");
	Lang::Set("L_LARGER_SIDE", "Longer side");
	Lang::Set("L_LINK_TO_ORIGINAL", "Link to original (only images)");
	Lang::Set("L_LINK_TO_SELECTED", "Link to selected item");
	Lang::Set("L_LOCAL_FILE", "Local file");
	Lang::Set("L_MODULE", "Module");
	Lang::Set("L_MODULES", "Modules");
	Lang::Set("L_OPTIONAL", "Optional");
	Lang::Set("L_ORDER", "Order");
	Lang::Set("L_PHOTO_STYLE", "Photo-style");
	Lang::Set("L_POSITION", "Position");
	Lang::Set("L_PREVIEW", "Preview");
	Lang::Set("L_PROPERTIES", "Properties");
	Lang::Set("L_REQUIRED", "<b>*</b>");
	Lang::Set("L_REQUIRED_FIELDS", "NOTE:<br /><b>*</b> marked fields can't be left blank.");
	Lang::Set("L_SORT_BY", "Sort by");
	Lang::Set("L_STATUS", "Status");
	Lang::Set("L_TITLE", "Title");
	Lang::Set("L_URL", "Web address (URL)");
	Lang::Set("L_UPLOAD_SIZE_LIMIT", "Upload size limit {INI_UPLOAD_MAX_FILESIZE}");

	// items status
	Lang::Set("L_DRAFT", "Draft");
	Lang::Set("L_PUBLISHED", "Published");

	// editor specific
	Lang::Set("L_ALLOWED_TAGS", "Allowed tags");
	Lang::Set("L_DEFAULT", "Default");
	Lang::Set("L_FONT_COLOR", "Font color");
	Lang::Set("L_FONT_NAME", "Typography");
	Lang::Set("L_SOURCE_MODE", "Source mode");
	Lang::Set("L_HEADINGS", "Heading");
	Lang::Set("L_HEADING_1", "Title");
	Lang::Set("L_HEADING_2", "Subtitle");
	Lang::Set("L_HEADING_3", "Subtitle 1");
	Lang::Set("L_HEADING_4", "Subtitle 2");
	Lang::Set("L_HEADING_5", "Subtitle 3");
	Lang::Set("L_VISUAL_MODE", "Visual mode");
	Lang::Set("L_UPDATED", "Updated");

	// errors
	Lang::Set("E_ACCESS_DENIED", "Access denied. You do not have enough privileges to view this page.");
	Lang::Set("E_DEMO_MODE", "This website is currently in <b>demo mode</b>. You can't execute this kind of action.");
	Lang::Set("E_INSUFFICIENT_DATA", "Submitted fields are insufficient to proccess your request.");
	Lang::Set("E_UPLOAD_FAILED", "There was a problem while uploading '%s'. Please verify that the file is lower than {INI_UPLOAD_MAX_FILESIZE}");
	Lang::Set("E_INVALID_EMAIL", "E-mail address may be incorrect.");
	Lang::Set("L_JAVASCRIPT_REQUIRED", "Please enable javascript");

	// generic words
	Lang::Set("L_ANONYMOUS", "Anonymous");
	Lang::Set("L_ANY", "(any)");
	Lang::Set("L_AUTHOR", "Author");
	Lang::Set("L_BOTTOM", "Bottom");
	Lang::Set("L_CENTER", "Centered");
	Lang::Set("L_CREATED", "Created");
	Lang::Set("L_DATABASE_QUERIES", "Queries");
	Lang::Set("L_GEKKO_IS_FREE_SOFTWARE", "<a href=\"http://www.gekkoware.org\" target=\"blank\">Gekko</a> is <a href=\"http://www.gnu.org/philosophy/free-sw.html\" target=\"_blank\">Free Software</a> released under the terms of the <a href=\"http://www.gnu.org/licenses/gpl.html\" target=\"_blank\">GNU/GPL</a>.");
	Lang::Set("L_GUESTS", "Guests");
	Lang::Set("L_INDEX", "Index");
	Lang::Set("L_LEFT", "Left");
	Lang::Set("L_LOADING", "Loading...");
	Lang::Set("L_MAIN", "Main");
	Lang::Set("L_MEMORY_USAGE", "Memory usage");
	Lang::Set("L_MODE", "Mode");
	Lang::Set("L_MODIFIED", "Modified");
	Lang::Set("L_NONE", "None");
	Lang::Set("L_NORMAL", "Normal");
	Lang::Set("L_PAGES", "Pages");
	Lang::Set("L_POSTED_BY", "Written by");
	Lang::Set("L_POWERED_BY", "Powered by <a href=\"http://www.gekkoware.org\" target=\"blank\">Gekko</a>");
	Lang::Set("L_RENDER_TIME", "Generation time");
	Lang::Set("L_RIGHT", "Right");
	Lang::Set("L_SAFE", "Safe");
	Lang::Set("L_TOP", "Top");
	Lang::Set("L_TOTAL", "Total");
	Lang::Set("L_TYPE", "Type");
	Lang::Set("L_UNKNOWN", "Unknown");
	Lang::Set("L_VIEW", "View");

	// time and date
	Lang::Set("L_FULL_DATE", "%A %B %e, %Y, %H:%M");
	Lang::Set("L_MINUTES_AGO", "%M minutes ago");
	Lang::Set("L_NEXT_MINUTES", "Within the next %M minutes");
	Lang::Set("L_NEXT_SECONDS", "Within the next %S seconds");
	Lang::Set("L_SECONDS_AGO", "%S seconds ago");
	Lang::Set("L_TODAY_AT", "Today at %H:%M");
	Lang::Set("L_TOMORROW_AT", "Tomorrow at %H:%M");
	Lang::Set("L_YESTERDAY_AT", "Yesterday at %H:%M");
	Lang::Set("L_DATE_MODIFIED", "Modified");
	Lang::Set("L_DATE_CREATED", "Created");

	// notes
	Lang::Set("L_PLEASE_DELETE_INSTALLER", "It is recommended, for security reasons, to delete or rename the installation script <b>install.php</b> as well as the <b>./install/</b> directory.");
	Lang::Set("L_CHECK_WRITABLE_DIRECTORIES", "It is necessary that directories <b>data</b> and <b>temp</b> could be writable (CHMOD 777)");

	Lang::Set("L_SSL_ENABLED", "Secure mode (<a href=\"http://es.wikipedia.org/wiki/SSL\">SSL</a>) is currently enabled, the connection between you and this server is being encryted. If you are not performing important activities that require encryption such as dealing with passwords or private information we suggest you to <a href=\"http://{SERVER_HTTP_HOST}{SERVER_REQUEST_URI}\">leave the secure mode</a>.");

	Lang::Set("L_EMPTY_PAGE", 'Esta P&aacute;gina a&uacute;n no contiene informaci&oacute;n o
	Usted no est&aacute; autorizado a verla. Si usted es el Administrador del sitio puede
	{=createLink("index.php/module=users/action=login", "ingresar")=} al Panel de Administraci&oacute;n para agregar contenido.');
	
	Lang::Set("L_ADMIN_EMPTY_PAGE", "You can go to the {=createLink(\"index.php/module=admin\", \"Administration Panel\")=} and add some content to {=createLink(\"index.php/module=admin/base=\".constant(\"GEKKO_REQUEST_MODULE\"), \"this module\")=}.");

	// obtienes spanglish a bajo costo ;)
	Lang::Set("L_GEKKO_WELCOME", "Welcome to your new installation of <a href=\"http://www.gekkoware.org\">Gekko</a>'s Web Application Framework.<br /> Gekko is Free Software released under the terms of the <a href=\"http://www.gnu.org/licenses/gpl.txt\">GNU/GPL</a>.<br /> <hr /> Thank you for using <a href=\"http://www.gekkoware.org\">Gekko</a>!");
?>
