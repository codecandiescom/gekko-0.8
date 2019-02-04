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

	appLoadLibrary("search");

	Extract (
		urlImportMap ("null?q=data&base=alpha&page=int")
	);

	if (!$base)
		$base = "*";

	if ($page <= 0)
		$page = "1";

	$total_results = 0;

	$tpl = new contentBlock();
	$tpl->insert("BLOCK_CONTENT", "search/main.default.tpl");

	$items_per_page = conf::getkey("search", "items_per_page", 10, "i");

	$q = trim($q);

	if ($q && strlen($q) >= 3) {

		$keywords_array = explode(" ", $q);

		// keywords to use with MySQL LIKE
		$keywords = "%".str_replace(" ", " % ", $q)."%";

		// counters
		$search_begin = ($page-1)*$items_per_page;
		$search_end = $items_per_page;

		if ($base == "*") {
			$modules = search::getModules();
			// this is not a module
			unset($modules["*"]);
		} else
			$modules = array($base => _L("L_MODULE_".strtoupper($base)));

		while (list($module) = each($modules)) {

			$module = preg_replace("/[^a-z\-]/", '', $module);

			if (!file_exists(GEKKO_SOURCE_DIR."modules/$module/include/search.php"))
				continue;

			// variable $p is our query result
			unset ($p);

			// will perform a very simple query to know how many items do wee need to fetch
			include GEKKO_SOURCE_DIR."modules/$module/include/search.php";

			$module_results = $GLOBALS["db"]->numRows($p);

			if ($module_results) {
				if ($total_results+$module_results > $search_begin && $search_end > 0) {

					// dropping results until $seach_begin
					for ($i = $total_results; ($i < $search_begin) && $db->fetchRow($p); $i++);

					// loading module library
					if (file_exists(GEKKO_SOURCE_DIR."modules/$module/functions.php"))
						appLoadLibrary($module);

					// mapping proccessing option
					$proccess = create_function('$query, $limit', 'return '.$module.'::proccessSearch($query, $limit);');

					$items = $proccess($p, $search_end);

					foreach ($items as $row) {

						if (!isset($row["show_full_description"])) {
							search::hightlight($row["description"], $keywords_array);
							if (strlen($row["description"]) > 250)
								$row["description"] = mkintro($row["description"], 250);
						}

						$tpl->setArray($row, "RESULT");
						$tpl->saveBlock("RESULT");
					}

					$search_end -= count($items);
				}
				$total_results += $module_results;
			}
		}
	}

	$base = htmlspecialchars($base);
	$q = htmlspecialchars($q);

	$tpl->setArray (
		Array (
			"BLOCK_TITLE"		=> _L("L_SEARCH"),
			"BLOCK_ICON"		=> createIcon("search.png"),
			"BASE"				=> $base,
			"SEARCH_TERMS"		=> $q,
			"PAGER"				=> createPager(ceil($total_results/$items_per_page), $page, urlEvalPrototype("index.php/module=search?q=$q&base=$base&page=%p"))
		)
	);

	$mcBuff = $tpl->make();
?>
