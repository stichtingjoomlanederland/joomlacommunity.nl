<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model\Scanner\Logger;

defined('_JEXEC') || die;

/**
 * Log levels
 */
abstract class LogLevel
{
	public const ERROR = 1;

	public const WARNING = 2;

	public const INFO = 3;

	public const DEBUG = 4;
}
