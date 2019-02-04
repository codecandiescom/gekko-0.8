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

	class gekko_Menu {
		var $last_level;
		var $buff;
		var $p;
		var $id;
		var $options;
		var $i;
		function Gekko_Menu() {
			$this->id = "dd_".rand(0, 10);
			$this->buff = array();
			$this->last_level = 0;
			$this->i = 1;
			$this->p =& $this->buff;
			$this->options = Array(
				'dropdown' => 0
			);
		}
		function set($opt, $value) {
			$this->options[$opt] = $value;
		}
		function getid($id) {
			return $this->id."_$id".($this->i++)."";
		}
		function addItem($html, $level = 0) {
			$p =& $this->buff;
			// allocating new element in an existing branch
			for ($i = 0; $i < $level; $i++) {
				$c = count($p)-1;
				if (is_array($p[$c])) {
					// $p[$c] is a branch and can hold new elements
					$p =& $p[$c];
				} else {
					// $p[$c] is a node, pointing to a new one
					$p =& $p[];
				}
			}
			$p[] = $html;
		}
		function make_list($buff = "") {
			$return = array();

			if (!$buff)
				$buff = $this->buff;

			if (is_array($buff)) {
				// don't allow null level skips
				if (isset($buff[0]) && is_array($buff[0]))
					$return[] = $this->make_list("&nbsp;");

				foreach ($buff as $b)
					$return[] = $this->make_list($b);

				return sprintf('<ul>%s</ul>', implode("\n", $return));
			} else {
				return sprintf('<li>%s</li>', $buff);
			}

		}
		function make() {
			if ($this->options["dropdown"]) {
				$buff = $this->make_dropdown();
			} else {
				$buff = $this->make_list();
			}
			return "<div class=\"MenuList\">$buff</div>";
		}
		function make_dropdown($buff = "", $level = 0, $id = null) {
			$return = "";

			if (!$buff)
				$buff = $this->buff;

			// pretty code :)
			$tab = str_repeat(" ", $level);

			$c = count($buff);
			for ($i = 0; $i < $c; $i++) {

				$k = $this->getid($level);

				if (isset($buff[$i+1]) && is_array($buff[$i+1])) {
					// next element is an array, this is the top level link
					$buff[$i] = "$tab<li onmouseover=\"ddisplay(this, 1)\" onmouseout=\"ddisplay(this, 0)\">".$buff[$i]."\n".$this->make_dropdown($buff[$i+1], $level+1, $k)."$tab</li>\n";
					unset($buff[$i+1]);
					$i++;
				} else {
					if (is_array($buff[$i])) {
						// level skip? no problem
						$buff[$i] = "$tab<li>&nbsp;\n".$this->make_dropdown($buff[$i], $level+1, $k)."$tab</li>\n";
					} else {
						// simple link
						$buff[$i] = "$tab<li>".$buff[$i]."</li>\n";
					}
				}
			}
			if ($level) {
				return "$tab<ul>\n".implode("", $buff)."$tab</ul>\n";
			} else {
				return "<ul class=\"dropdown\">\n".implode("", $buff)."$tab</ul>\n";
			}
		}
	}
/*
	$ddown = new DropDownList();
	$ddown->additem("cero", 0);
	$ddown->additem("cero", 0);

	$ddown->additem("uno", 1);
	$ddown->additem("uno", 1);
	$ddown->additem("uno", 1);
	$ddown->additem("uno", 1);
	$ddown->additem("dos", 2);
	$ddown->additem("dos", 2);
	$ddown->additem("dos", 2);
	$ddown->additem("tres", 3);
	$ddown->additem("tres", 3);
	$ddown->additem("tres", 3);
	$ddown->additem("uno", 1);
	$ddown->additem("uno", 1);
	//$ddown->additem("seis", 6);

	$ddown->additem("uno", 1);

	$ddown->additem("cero", 0);
	$ddown->additem("cero", 0);
	$ddown->additem("cero", 0);

	$ddown->additem("uno", 1);
	$ddown->additem("uno", 1);
	$ddown->additem("uno", 1);
	$ddown->additem("uno", 1);
	$ddown->additem("dos", 2);
	$ddown->additem("dos", 2);
	$ddown->additem("dos", 2);
	$ddown->additem("tres", 3);
	$ddown->additem("tres", 3);
	$ddown->additem("tres", 3);
	$ddown->additem("uno", 1);
	$ddown->additem("uno", 1);
	//$ddown->additem("seis", 6);

	$ddown->additem("uno", 1);

	$ddown->additem("cero", 0);

	echo $ddown->make();
	*/
?>