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

	Class gekkoCalendar {

		var $month, $year, $month_days;
		var $day = array();
		var $defaultDayContent = null;
		var $appendDefault = false;
		var $dateFormat = "%e";
		var $monthFormat = "%B %G";
		var $eventSeparator = "<hr />";

		function gekkoCalendar($year, $month) {
			$this->month = $month;
			$this->year = $year;
			$this->month_days = date("t", mktime(0, 0, 0, $month, 1, $year));
		}
		function defaultDayContent($html) {
			$this->defaultDayContent = $html;
		}
		function setDayContent($day, $html, $push = true) {
			if (!isset($this->day[$day]))
				$this->day[$day] = array();

			if ($push)
				$this->day[$day][] = $html;
			else
				array_unshift($this->day[$day], $html);
		}
		function compileDay ($day) {
			return strftime($this->dateFormat, mktime(0, 0, 0, $this->month, $day, $this->year)).(isset($this->day[$day]) ? implode($this->eventSeparator, $this->day[$day]).($this->appendDefault ? $this->defaultDayContent : '') : $this->defaultDayContent);
		} 
		function compile() {

			// TODO: use htmlWidget()

			/* 0 = sunday, 6 = saturday */
			$first = mktime(0, 0, 0, $this->month, 2, $this->year);

			$month_starts = date("w", $first);

			$prevMonth = (($this->month - 1) >= 1) ? $this->month - 1 : 12;
			$nextMonth = (($this->month + 1) <= 12) ? $this->month + 1 : 1;

			$html = "<table class=\"calendar\">\n";
			$html .= "<tr class=\"head\">".
			"<td style=\"vertical-align: middle\"><a href=\"".urlSetArgs("date=".($this->year-1)."-$prevMonth")."\">&laquo;</a> <a href=\"".urlSetArgs("date=".$this->year."-$prevMonth")."\">&lt;</a></td>".
			"<td colspan=\"5\">".strftime($this->monthFormat, $first)."</td>".
			"<td style=\"vertical-align: middle\"><a href=\"".urlSetArgs("date=".$this->year."-$nextMonth")."\">&gt;</a> <a href=\"".urlSetArgs("date=".($this->year+1)."-$nextMonth")."\">&raquo;</a></td>".
			"</tr>";

			$head = $body = "";
			$capt = false;

			// week
			for ($i = $w = 0; $i <= $this->month_days; $w++) {
				
				// beginning of the week
				if (!($w%7)) $body .= "<tr>";
				
				// beginning of the month
				if (!$i && $w == $month_starts)	
					$i = 1;

				// normal day
				$body .= "<td>".($i ? $this->compileDay($i) : "")."</td>";

				// day names
				if ($i) {
					if (!$capt) {
						$wd = strtoupper(substr(strftime("%a", mktime(0,0,0,$this->month,$i,$this->year)), 0, 1));

						if (!$head) {
							if (!($w%7)) {
								$head .= "<td>".$wd."</td>";
							}
						} else {
							if ($w%7 == 6) {
								$capt = true;
							}
							$head .= "<td>".$wd."</td>";
						}
					}
					$i++;
				}

				// week ends
				if ($w%7 == 6) $body .= "</tr>\n";
			}
			if ($w%7) {
				$body .= "</tr>\n";
			}

			$head = "<tr class=\"head\">$head</tr>";

			$html .= $head.$body;

			$html .= "</table>\n";
			
			return $html;
		}
	}
	
	
	/*
		$cal = new Calendar(8, 2005);
		$cal->set_html(5, "<a href=\"#\">5</a>");
		echo $cal->make();
		DEPRECATED
	*/
	class Calendar {
		var $month, $year, $month_days;
		var $day = Array();
		function Calendar($month, $year) {
			$this->month = $month;
			$this->year = $year;
			$this->month_days = date("t", mktime(0, 0, 0, $month, 1, $year));
			for ($i = 1; $i <= $this->month_days; $i++) {
				$this->day[$i] = "";
			}
		}
		function set_html($day, $html) {
			$this->day[$day] = $html;
		}
		function get_html($day) {
			return $this->day[$day];
		}
		function make_day($day) {
			if (isset($this->day[$day])) {
				return $this->day[$day] ? $this->day[$day] : $day;
			}
			return false;
		}
		function make() {

			/* 0 = sunday, 6 = saturday */
			$first = mktime(0, 0, 0, $this->month, 1, $this->year);
			$month_starts = date("w", $first);

			$html = "<table class=\"calendar\">\n";
			$html .= "<tr class=\"head\"><td colspan=\"7\">".htmlentities(ucfirst(strftime("%B %G", $first)))."</td></tr>";

			$head = $body = "";
			$capt = false;
			/* week */

			for ($i = $w = 0; $i <= $this->month_days; $w++) {
				/* week starts */
				if (!($w%7)) $body .= "<tr>";
				/* beggining of the month */
				if (!$i && $w == $month_starts) $i = 1;

				/* normal day */
				$body .= "<td>".($i ? $this->make_day($i) : "")."</td>";

				/* caching day names */
				if ($i) {
					if (!$capt) {
						$wd = strtoupper(substr(strftime("%a", mktime(0,0,0,$this->month,$i,$this->year)), 0, 1));

						if (!$head) {
							if (!($w%7)) {
								$head .= "<td>".$wd."</td>";
							}
						} else {
							if ($w%7 == 6) {
								$capt = true;
							}
							$head .= "<td>".$wd."</td>";
						}
					}
					$i++;
				}

				/* week ends */
				if ($w%7 == 6) $body .= "</tr>\n";
			}
			if ($w%7) {
				//$body .= str_repeat("	<td></td>\n", (7-($w%7)));
				$body .= "</tr>\n";
			}

			$head = "<tr class=\"head\">$head</tr>";

			$html .= $head.$body;

			$html .= "</table>\n";
			return $html;
		}

	}

?>
