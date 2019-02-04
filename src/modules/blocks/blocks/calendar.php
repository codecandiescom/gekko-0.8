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

	if (!defined("IN-BLOCK")) die();

	/*
	<%%>
	<?xml version="1.0" encoding="UTF-8"?>
	<blockscript>
		<info lang="en">
			<title>Calendar</title>
			<description>Shows a calendar containing relevant links (depending on the module the user is viewing) for the given month</description>
			<author><![CDATA[xiam &lt;<a href="mailto:xiam@users.sourceforge.net">xiam@users.sourceforge.net</a>&gt;]]></author>
			<homepage><![CDATA[<a href="http://www.gekkoware.org">http://www.gekkoware.org</a>]]></homepage>
		</info>
		<variable name="month">0</variable>
		<variable name="year">0</variable>
	</blockscript>
	<%%>
	*/

	appLoadLibrary (
		"calendar.lib.php"
	);

	Extract (
		// yyyy[.mm]
		urlImportMap("?date=[0-9.]")
	);

	if (isset($date))
		$date = explode(".", $date);

	$year = $month = 0;

	if (isset($date[1])) {
		// yyyy.mm
		list($year, $month) = $date;
	} elseif (isset($date[0])) {
		// yyyy
		list($year) = $date;
	}

	if ($month <= 0 || $month > 12)
		$month = date("m");

	if ($year <= 0)
		$year = date("Y");

	// initializing calendar object
	$calendar = new Calendar($month, $year);

	// lower and higher time range
	$lo = "$year-$month-1 00:00:00";
	$hi = "$year-$month-".date("t", fromDate($lo))." 23:59:59";

	// events buffer
	$events = Array();

	// fetching events for the current module
	switch (GEKKO_REQUEST_MODULE) {

		// those modules are _very_ similar.
		case "blog": case "news":

			appLoadLibrary (
				"categories"
			);

			$cat_lock = cacheFunc("categories::fetchAllowed", true);

			$q = $GLOBALS["db"]->query("SELECT id, title, date_created
			FROM ".dbTable(GEKKO_REQUEST_MODULE)."
			WHERE status = '1'
			AND date_created >= '{$lo}' AND date_created <= '{$hi}'",
			__FILE__, __LINE__, true);

			// fetching events
			while ($row = $GLOBALS["db"]->fetchRow($q)) {

				saneHTML($row);

				// getting day of the event
				$day = date("j", fromDate($row["date_created"]));

				// increasing event counter
				$events[$day] = isset($events[$day]) ? $events[$day]+1 : 1;

				if ($events[$day] > 1) {
					// there are more events registered for this day
					$calendar->set_html($day, sprintf("<a href=\"%s\" title=\"%d\">%d</a>", urlEvalPrototype("index.php/module=".GEKKO_REQUEST_MODULE."?date={$year}.{$month}.{$day}"), $events[$day], $day));
				} else {
					// the first (or the only) event for this day, direct link
					$calendar->set_html($day, sprintf("<a href=\"%s\" title=\"%s\">%d</a>", urlEvalPrototype("index.php/module=".GEKKO_REQUEST_MODULE."/action=view/title=".urlTitleEncode($row["title"])."?id={$row['id']}"), htmlspecialchars($row["title"]), $day));
				}
			}
		break;
	}

	// compiling calendar
	$return = $calendar->make();

?>