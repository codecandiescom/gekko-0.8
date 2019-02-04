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

	class Chart {
		var $totals = Array();
		var $items = Array();
		var $width = "100%";
		var $abs_max;

		function Chart($width = "100%") {
			$this->width = $width;
		}

		function createItem($text) {
			$this->items[] = $text;
			$item_id = (count($this->items)-1);
			$this->nodes[$item_id] = array();

			return $item_id;
		}

		function setTotal($label, $value) {
			$this->totals[md5($label)]["total"] = $value;
			$this->abs_max = $value;
		}

		function addItemNode($item_id, $value, $label = "") {
			$this->nodes[$item_id][] = array (
				"label" => $label,
				"value" => $value
			);

			if (!isset($this->totals[md5($label)]))
				$this->totals[md5($label)] = Array("label" => $label, "total" => 0);

			$this->totals[md5($label)]["total"] += $value;

			if ($value > $this->abs_max)
				$this->abs_max = $value;
		}

		function make($include_total = true, $style = null) {

			if ($this->abs_max) {
				$children = false;

				$buff = "";
				$max_p = 300;

				switch ($style) {
					default:
						foreach ($this->items as $item_id => $text) {
							$nodes =& $this->nodes[$item_id];
							$ncount = count($nodes);

							// displaying percentages or only name depending in how many children does this node have.
							$buff .= "{$text}".($ncount > 1 ? "" : " - ".$nodes[0]["value"]." (".round($nodes[0]["value"]*100/($this->totals[md5($nodes[0]["label"])]["total"]), 1)."%)");

							foreach ($nodes as $node) {
								$perc = round($node["value"]*100/($this->totals[md5($node["label"])]["total"]), 1);
								$buff .= "
								<div class=\"bar\">
								<div class=\"progress\" style=\"width: ".ceil($node["value"]*100/($this->abs_max + 20))."%\"></div>
								<br />
								".(($ncount > 1) ? "{$node["label"]} {$node["value"]} ({$perc}%)" : "")."</div>";
							}

							if (!$children && $ncount > 1)
								$children = true;
						}
					break;
				}

				if ($include_total) {
					$buff .= "<hr />";
					if (!$children && isset($this->totals[md5("")]["total"])) {
						$buff .= "<b>"._L("L_TOTAL")."</b>: ".$this->totals[md5("")]["total"]."<br />\n";
					} else {
						$buff .= "<b>"._L("L_TOTAL")."</b>: <br />\n";
						foreach ($this->totals as $total) {
							$buff .= "<i>{$total["label"]}:</i> {$total["total"]}<br />";
						}
					}
				}

				return "<div class=\"chart\" style=\"width:{$this->width};\">".$buff."</div>";
			}
		}

	}

?>