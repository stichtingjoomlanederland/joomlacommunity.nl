<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model\Scanner\Mixin;


defined('_JEXEC') || die;

trait Singleton
{
	/**
	 * Singleton instance
	 *
	 * @var   static
	 */
	protected static $instance = null;

	/**
	 * Singleton implementation.
	 *
	 * @return  static
	 */
	public static function getInstance()
	{
		if (!empty(static::$instance))
		{
			return static::$instance;
		}

		static::$instance = new static();

		return static::$instance;
	}

}
