<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

 /*
Credits: Bit Repository
*/

defined('_JEXEC') or die('Restricted access');

class KomentoFilterHelper
{
	var $strings;
	var $text;
	var $keep_first_last;
	var $replace_matches_inside_words;

	function __construct()
	{
		$this->keep_first_last 				= false;
		$this->replace_matches_inside_words = false;
	}

	function filter()
	{
		$new_text = '';

		$regex = '/<\/?(?:\w+(?:=["\'][^\'"]*["\'])?\s*)*>/'; // Tag Extractor

		preg_match_all($regex, $this->text, $out, PREG_OFFSET_CAPTURE);

		$array = $out[0];

		if(!empty($array))
		{
			if($array[0][1] > 0)
			{
				$new_text .= $this->do_filter(substr($this->text, 0, $array[0][1]));
			}

		   foreach($array as $value)
		   {
			   $tag = $value[0];
			   $offset = $value[1];

			   $strlen = strlen($tag); // characters length of the tag

			   $start_str_pos = ($offset + $strlen); // start position for the non-tag element
			   $next = next($array);

			   // end position for the non-tag element
			   $end_str_pos = $next[1];

			   // no end position?
			   // This is the last text from the string and it is not followed by any tags
			   if(!$end_str_pos) $end_str_pos = strlen($this->text);

				// Start constructing the new resulted string. We'll add tags now!
			   $new_text .= substr($this->text, $offset, $strlen);

			   $diff = ($end_str_pos - $start_str_pos);

			   // Is this a simple string without any tags? Apply the filter to it
			   if($diff > 0)
			   {
				   $str = substr($this->text, $start_str_pos, $diff);

				   $str = $this->do_filter($str);

				   $new_text .= $str; // Continue constructing the text with the (filtered) text
			   }
		   }
		}
		else // No tags were found in the string? Just apply the filter
		{
			$new_text = $this->do_filter($this->text);
		}

		return $new_text;
	}// end filter()

	function do_filter($var)
	{
		if(is_string($this->strings)) $this->strings = array($this->strings);

		foreach($this->strings as $word)
		{
			$word = trim( $word );

			// check for custom replacement
			$customReplacement = '';
			if( stristr( $word, '=' ) )
			{
				$tmp = explode('=', $word);
				$customReplacement = trim($tmp[1]);
				$word = trim($tmp[0]);
			}
			else
			{
				$customReplacement = str_repeat('*', strlen($word));
			}

			if($this->replace_matches_inside_words)
			{
				$word = preg_replace('#[\*\$\^]#', '', $word );
				$var = str_replace($word, $customReplacement, $var);
			}
			else
			{
				$escapedWord = preg_quote( $word, '/' );
				$var = preg_replace('/'.$escapedWord.'/i', $customReplacement, $var);
			}

			// $word = preg_replace('#[^A-Za-z0-9\*\$\^]#u', '', trim($word) );

			// bugged due to unicode and wildcard filter not working properly

			// if( (stristr($word, '*') === false) && (stristr($word, '$') === false) && (stristr($word, '^') === false) )
			// {
			// 	$str = strlen($word);

			// 	$first = ($this->keep_first_last) ? $word[0] : '';
			// 	$str = ($this->keep_first_last) ? $str - 2 : $str;
			// 	$last = ($this->keep_first_last) ? $word[strlen($word) - 1] : '';

			// 	if($customReplacement == '')
			// 	{
			// 		$replacement = str_repeat('*', $str);
			// 	}
			// 	else
			// 	{
			// 		$replacement = $customReplacement;
			// 	}

			// 	if($this->replace_matches_inside_words)
			// 	{
			// 		$var = str_replace($word, $first.$replacement.$last, $var);
			// 	}
			// 	else
			// 	{
			// 		$var = preg_replace('/\b'.$word.'\b/ui', $first.$replacement.$last, $var);
			// 	}
			// }
			// else
			// {
			// 	// rebuiling the regex
			// 	$keySearch  = array('/\*/ms', '/\$/ms');
			// 	$keyReplace	= array('%', '#');

			// 	$word 		= preg_replace( $keySearch , $keyReplace, $word);

			// 	$keySearch  = array('/\%/ms', '/\#/ms');
			// 	$keyReplace	= array('.?', '.*?');

			// 	$word 		= preg_replace( $keySearch , $keyReplace, $word);

			// 	if($customReplacement != '')
			// 	{
			// 		$replacement = str_repeat('*', strlen($word));
			// 	}
			// 	else
			// 	{
			// 		$replacement = $customReplacement;
			// 	}

			// 	$var 		= preg_replace( '/\b'.$word.'\b/ims', $replacement , $var );
			// }
	   }

		return $var;
	}
}
