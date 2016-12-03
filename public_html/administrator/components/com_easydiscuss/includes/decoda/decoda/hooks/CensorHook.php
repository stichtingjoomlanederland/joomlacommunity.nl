<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

/**
 * CensorHook
 *
 * Censors words found within the censored.txt blacklist.
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/php/decoda
 */

class CensorHook extends EdDecodaHook {

	/**
	 * List of words to censor.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_censored = array();

	/**
	 * Configuration.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_config = array(
		'suffix' => array('ing', 'in', 'er', 'r', 'ed', 'd')
	);

	/**
	 * Load the censored words from the text file.
	 *
	 * @access public
	 * @param array $config
	 */
	public function __construct(array $config = array()) {
		parent::__construct($config);

		$config = ED::config();
		$badwords = $config->get('main_filtertext');
		$badwords = trim($badwords);
		$badwords = explode(',', $badwords);

		$this->blacklist($badwords);
	}

	/**
	 * Parse the content by censoring blacklisted words.
	 *
	 * @access public
	 * @param string $content
	 * @return string
	 */
	public function beforeParse($content) {
		if (!empty($this->_censored)) {
			foreach ($this->_censored as $word) {
				$content = preg_replace_callback('/(^|\s|\n)?' . $this->_prepare($word) . '(\s|\n|$)?/is', array($this, '_callback'), $content);
			}
		}

		return $content;
	}

	/**
	 * Add words to the blacklist.
	 *
	 * @access public
	 * @param array $words
	 * @return DecodaHook
	 * @chainable
	 */
	public function blacklist(array $words) {
		$this->_censored = array_map('trim', array_filter($words)) + $this->_censored;
		$this->_censored = array_unique($this->_censored);

		return $this;
	}

	/**
	 * Censor a word if its only by itself.
	 *
	 * @access protected
	 * @param array $matches
	 * @return string
	 */
	protected function _callback($matches) {
		if (count($matches) === 1) {
			return $matches[0];
		}

		$length = mb_strlen(trim($matches[0]));
		$censored = '';
		$symbols = '********';

		$l = isset($matches[1]) ? $matches[1] : '';
		$r = isset($matches[2]) ? $matches[2] : '';
		$i = 0;
		$s = 0;

		while ($i < $length) {
			$censored .= $symbols[$s];

			$i++;
			$s++;

			if ($s > 7) {
				$s = 0;
			}
		}

		return $l . $censored . $r;
	}

	/**
	 * Prepare the regex pattern for each word.
	 *
	 * @access protected
	 * @param string $word
	 * @return string
	 */
	protected function _prepare($word) {
		$letters = str_split($word);
		$regex = '';

		foreach ($letters as $letter) {
			$regex .= preg_quote($letter, '/') .'{1,}';
		}
		
		// Added by Nik. \b is for word boundary
		// If the badword is in the middle of a word, don't censor it
		// Example:  'awebadsome'. It shouldn't be awe***some.
		$regex = '\b' . $regex . '\b';

		$suffix = $this->config('suffix');

		if (is_array($suffix)) {
			$suffix = implode('|', $suffix);
		}

		$regex .= '(?:' . $suffix .')?';

		return $regex;
	}

}
