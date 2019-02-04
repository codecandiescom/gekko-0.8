<?php

	/*
	*	Gekko - Open Source Web Development Framework
	*	------------------------------------------------------------------------
	*	Copyright (C) 2004-2006, J. Carlos Nieto <xiam@users.sourceforge.net>
	*	This program is Free Software.
	*
	*	@package	Gekko
	*	@subpackage	/lib
	*	@license	http://www.gnu.org/copyleft/gpl.html GNU/GPL License 2.0
	*	@author		J. Carlos Nieto <xiam@users.sourceforge.net>
	*	@link		http://www.gekkoware.org
	*/

	if (!defined("IN-GEKKO")) die("Get a life!");

	define ("GEKKO_PLUGINS_DIR", GEKKO_SOURCE_DIR."lib/plugins/");

	Class gekkoPluginHandler {

		var $plugins = Array();
		var $events = Array();

		function gekkoPluginHandler() {

			$plugins =& $this->plugins;
			
			// general plugins
			$fh = opendir(GEKKO_PLUGINS_DIR);
			while (($file = readdir($fh)) !== false) {
				if ($file[0] != '.' && file_exists(GEKKO_PLUGINS_DIR."{$file}/main.php")) {
					$plugins[] = Array(
						"name" => $file,
						"path" => GEKKO_PLUGINS_DIR."{$file}/main.php"
					);
				}
			}
			fclose($fh);

			// module plugins
			$modules = gekkoModule::getList();
			foreach ($modules as $module) {
				$path = GEKKO_MODULE_DIR."{$module}/plugin.php";
				if (file_exists($path)) {
					$plugins[] = Array(
						"name" => "{$module}Module",
						"path" => $path
					);
				}
			}

			foreach ($plugins as $i =>$plugin) {
				include $plugin["path"];
				$class = "{$plugin["name"]}Plugin";
				$plugins[$i]["class"] = new $class;
				$object =& $plugins[$i]["class"];

				foreach ($object->events as $event) {
					if (!isset($this->events[$event]))
						$this->events[$event] = Array();
					$this->events[$event][] =& $this->plugins[$i];
				}
			}

		}
		function triggerEvent($event, &$arg1) {
			if (!defined("IN-INSTALL")) {
				if (isset($this->events[$event])) {
					foreach ($this->events[$event] as $plugin) {
						if (!conf::getkey("core", "plugins.".strtolower($plugin["name"]).".disable")) {
							$arg1 = $plugin["class"]->Invoke($event, $arg1);
						}
					}
				}
			}
		}
	}

	$GLOBALS["appPluginHandler"] = new gekkoPluginHandler();
?>
