<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

/**
 * ImageFilter
 *
 * Provides tags for images.
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/php/decoda
 */

class ImageFilter extends DecodaFilter {

	/**
	 * Regex pattern.
	 */
	const IMAGE_PATTERN = '/^(?:https?:\/\/)?(.*?)\.(jpg|jpeg|png|gif|bmp)$/is';

	/**
	 * Supported tags.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_tags = array(
		'img' => array(
			'tag' => 'img',
			'type' => self::TYPE_INLINE,
			'allowed' => self::TYPE_NONE,
			'pattern' => self::IMAGE_PATTERN,
			'autoClose' => true,
			'attributes' => array(
				'width' => '/[0-9%]{1,4}+/',
				'height' => '/[0-9%]{1,4}+/',
				'alt' => '/.*?/'
			)
		),
		'image' => array(
			'tag' => 'img',
			'type' => self::TYPE_INLINE,
			'allowed' => self::TYPE_NONE,
			'pattern' => self::IMAGE_PATTERN,
			'autoClose' => true,
			'attributes' => array(
				'width' => '/[0-9%]{1,4}+/',
				'height' => '/[0-9%]{1,4}+/',
				'alt' => '/.*?/'
			)
		)
	);

	/**
	 * Use the content as the image source.
	 *
	 * @access public
	 * @param array $tag
	 * @param string $content
	 * @return string
	 */
	public function parse(array $tag, $content) {
		// If more than 1 http:// is found in the string, possible XSS attack
		if (substr_count($content, 'http://') > 1) {
			return;
		}

		$tag['attributes']['src'] = $content;

		if (empty($tag['attributes']['alt'])) {
			$tag['attributes']['alt'] = '';
		}

		return parent::parse($tag, $content);
	}

}
