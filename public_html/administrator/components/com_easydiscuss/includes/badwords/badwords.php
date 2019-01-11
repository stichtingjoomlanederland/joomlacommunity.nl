<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussBadwords extends EasyDiscuss
{
	public $strings;
	public $text;
	protected $keep_first_last;
	protected $replace_matches_inside_words;

	public function __construct()
	{
		parent::__construct();
		
		$this->keep_first_last = false;
		$this->replace_matches_inside_words = false;
	}

	/**
	 * Filters for badwords
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function filter($str, $contentType = 'bbcode', $debug = null)
	{
		$config = ED::config();

		if (!$config->get('main_filterbadword')) {
			return $str;
		}

		// We need to determine whether we should use nl2br or not during the process #633
		// By default BBcode content will not use nl2br
		$useBrTag = false;

		// For codemirror and none editor, we need to use nl2br
		if ($contentType == 'html') {

			// Get current editor being used
			$editor = $this->config->get('layout_editor');

			if ($editor == 'codemirror' || $editor == 'none') {
				$useBrTag = true;
			}
		}

		$decoda = ED::decoda($str);
		$decoda->initHook('CensorHook');
		$decoda->setEscaping(false);

		$decoda->setNl2br($useBrTag);

		$result = $decoda->parse();

		return $result;
	}

	public function filterOld()
	{
		$new_text = '';

		$regex = '/<\/?(?:\w+(?:=["\'][^\'"]*["\'])?\s*)*>/'; // Tag Extractor

		preg_match_all($regex, $this->text, $out, PREG_OFFSET_CAPTURE);

		$array = $out[0];

		if (!empty($array)) {

			if ($array[0][1] > 0) {
				$new_text .= $this->do_filter(JString::substr($this->text, 0, $array[0][1]));
			}

			foreach ($array as $value) {
				$tag = $value[0];
				$offset = $value[1];

				$strlen = JString::strlen($tag); // characters length of the tag

				$start_str_pos = ($offset + $strlen); // start position for the non-tag element
				$next = next($array);

				// End position for the non-tag element
				$end_str_pos = $next[1];

				// No end position?
				// This is the last text from the string and it is not followed by any tags
				if (!$end_str_pos) {
					$end_str_pos = JString::strlen($this->text);
				}

				// Start constructing the new resulted string. We'll add tags now!
				$new_text .= JString::substr($this->text, $offset, $strlen);

				$diff = ($end_str_pos - $start_str_pos);

				// Is this a simple string without any tags? Apply the filter to it
				if ($diff > 0) {
					
					$str = JString::substr($this->text, $start_str_pos, $diff);

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
	}

	protected function do_filter($var)
	{
		if (is_string($this->strings)) {
			$this->strings = array($this->strings);
		}

		foreach ($this->strings as $word) {

			// Check for custom replacement
			$customReplacement = '';

			if (JString::stristr($word, '=')) {
				$tmp = explode('=', $word);
				$customReplacement = JString::trim($tmp[1]);
				$word = JString::trim($tmp[0]);
			}

			// $word = preg_replace('#[^A-Za-z0-9\*\$\^]#', '', JString::trim($word));

			
			$replacement = '';

			if ((JString::stristr($word, '*') === false) && (JString::stristr($word, '$') === false) && (JString::stristr($word, '^') === false)) {
				
				$str = JString::strlen($word);

				$first = ($this->keep_first_last) ? $word[0] : '';
				$str = ($this->keep_first_last) ? $str - 2 : $str;
				$last = ($this->keep_first_last) ? $word[JString::strlen($word) - 1] : '';

				if ($customReplacement == '') {
					$replacement = str_repeat('*', $str);
				} else {
					$replacement = $customReplacement;
				}

				if ($this->replace_matches_inside_words) {
					$var = JString::str_replace($word, $first.$replacement.$last, $var);
				} else {
					$var = preg_replace('/\b'.$word.'\b/ui', $first.$replacement.$last, $var);
				}
			} else {


				// Rebuiling the regex
				$keySearch	= array('/\*/ms', '/\$/ms');
				$keyReplace	= array('%', '#');

				$word		= preg_replace( $keySearch , $keyReplace, $word);

				$keySearch	= array('/\%/ms', '/\#/ms');
				$keyReplace	= array('.?', '.*?');

				$word		= preg_replace( $keySearch , $keyReplace, $word);

				if ($customReplacement != '') {
					$replacement = str_repeat('*', JString::strlen($word));
				} else {
					$replacement = $customReplacement;
				}

				$var = preg_replace( '/\b'.$word.'\b/uims', $replacement , $var );
			}
		}


		return $var;
	}
}
