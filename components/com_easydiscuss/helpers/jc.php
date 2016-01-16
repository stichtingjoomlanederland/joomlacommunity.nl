<?php
defined('_JEXEC') or die('Restricted access');

class DiscussJcHelper
{
	public static function time_delta($start='now', $target='', $precision=0, $labels=2, $suffix=true)
	{
		$date = JFactory::getDate($start, 'UTC');
		$config = JFactory::getConfig();
		//$date->setTimezone(new DateTimeZone($config->get('offset')));

		// Transform the date string.
		$start = $date->format('Y-m-d H:i:s', true, false);
		//echo($start);
		//break;

		
		// Define all time units in terms of seconds
		$units = array(
				'y'  => array('jaar', 31556926, 'jaar'), // Source: Google calculator
				'mo' => array('maand', 2629744, 'maanden'), // Source: Google calculator
				'w'  => array('week', 604800, 'weken'),
				'd'  => array('dag', 86400, 'dagen'),
				'h'  => array('uur', 3600, 'uur'),
				'm'  => array('minuut', 60, 'minuten'),
				's'  => array('seconde', 1, 'seconden')
				);
	 
		// Some basic sanity checking
		if (empty($target)) return "No target time specified.\n";
		if ($start < 0) return "Invalid start time.\n";
		if (!array_key_exists($precision, $units) AND $precision > 2) return "Improper value for precision.\n";
		if (!is_int($labels) OR !is_bool($suffix)) return "Improper values for labels and/or suffix.\n";
		if (!is_int($target) AND !strtotime($target)) return "Could not understand your target time.\n";
		if (!is_int($start) AND !strtotime($start)) return "Could not understand your start time.\n";
	 
		// Set some sensible defaults
		$fuzz_factor = 0.8; // How close to the next value will we call it "about" something?
		if ($precision < 0 OR $precision > 2) $precision = 2;
		if ($labels < 0 OR $labels > 2) $labels = 2;
		if (!is_int($start)) $start = strtotime($start);
		if (!is_int($target)) $target = strtotime($target);
	 
		// Are we past or future?
		$ending = ($target > $start) ? " from now" : " geleden";
	 
		// Calculate time difference & initialize output string
		$delta = abs($target - $start);
		$out = '';
	 
		// Calculate for single-unit precision
		if (is_string($precision))
		{
			if ($delta < $units[$precision][1])
			{
				$out .= "Less than one {$units[$precision][0]}";
				return ($suffix === true) ? $out.$ending : $out;
			}
			else
			{
				$out .= intval(($delta / $units[$precision][1]));
				if ($labels == 0) return $out;
				$out .= ($labels == 1) ? $precision : ' '.$units[$precision][0]
				     .  (($out > 1 AND $labels >= 2) ? 's' : '');
				return ($suffix === true) ? $out.$ending : $out;
			}
		}
	 
		/* Calculate fuzzy precision
		   -------------------------
		  Fuzzy precision should output only one unit of precision
		  and use the modifier "about" if the remainder is > $fuzz_factor.
		*/
		if ($precision == 0)
		{
			foreach ($units as $unit => $type)
			{
				if ($delta >= $type[1] * $fuzz_factor)
				{
					$fuzzy = (fmod(($delta / $type[1]), 1) > $fuzz_factor) ? true : false;
					if ($labels > 0 AND $labels >= 2) $out .= ($fuzzy === true) ? '' : '';
					$diff = ($fuzzy === true) ? ceil($delta / $type[1]) : intval($delta / $type[1]);
					if ($diff == 1 AND $fuzzy === true) $out .= ($unit == 'h') ? 'een ' : 'een ';
					else $out .= $diff;
					if ($labels == 0) return $diff;
					$out .= ($labels > 1 OR $fuzzy === false) ? ' ' : ''; 
					$out .= ($labels == 1) ? $unit : '';
					$out .= ($diff > 1 AND $labels > 1) ? ($type[2]) : ($type[0]);
					return ($suffix === true) ? $out.$ending : $out;
				}
			}
		}
			
		/* Calculate approximate and exact precision
		   -----------------------------------------
		  Approximate precision outputs up to 2 units of measure, exact prints
		  as many as we have.
		*/
		else
		{
			$max = ($precision == 1) ? 2 : count($units); // Iterate twice if approximate precision
			$i = 0;
			foreach ($units as $unit => $type)
			{
				if ($delta >= $type[1] AND $i < $max)
				{
					$diff = intval($delta / $type[1]);
					$out .= $diff.(($labels > 1) ? (' ') : (($labels == 0) ? ' ' : $unit)).(($diff > 1 AND $labels > 1) ? ($type[2]) : ($type[0]));
					$delta -= intval($delta / $type[1]) * $type[1];
					$out .= ($i == 0 AND $precision == 1 AND $labels > 0 AND !empty($diff)) ? ' en ' : '';
					$next_index = array_search($unit,array_keys($units)) + 1;
					$units_numeric = array_values($units);
					if (array_key_exists($next_index, $units_numeric)) $next = $units_numeric[$next_index][1];
					$and = ($precision == 2 AND $labels > 0 AND ($delta % $next == 0) AND $unit != 's');
					$out .= $and ? ' en ' : (($labels > 0 AND $unit != 's' AND $precision != 1) ? ', ' : '' );
					$i++;
				}
			}
			return ($suffix === true) ? $out.$ending : $out;
		}
	}
}