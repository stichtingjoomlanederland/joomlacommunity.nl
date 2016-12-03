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
 * DefaultFilter
 *
 * Provides tags for basic font styling.
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/php/decoda
 */

class DefaultFilter extends DecodaFilter {

	/**
	 * Supported tags.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_tags = array(
		'b' => array(
			'tag' => array('b', 'strong'),
			'type' => self::TYPE_INLINE,
			'allowed' => self::TYPE_INLINE
		),
		'i' => array(
			'tag' => array('i', 'em'),
			'type' => self::TYPE_INLINE,
			'allowed' => self::TYPE_INLINE
		),
		'u' => array(
			'tag' => 'u',
			'type' => self::TYPE_INLINE,
			'allowed' => self::TYPE_INLINE
		),
		's' => array(
			'tag' => 'del',
			'type' => self::TYPE_INLINE,
			'allowed' => self::TYPE_INLINE
		),
		'sub' => array(
			'tag' => 'sub',
			'type' => self::TYPE_INLINE,
			'allowed' => self::TYPE_INLINE
		),
		'sup' => array(
			'tag' => 'sup',
			'type' => self::TYPE_INLINE,
			'allowed' => self::TYPE_INLINE
		)
	);

}
