<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model\Scanner\Logger;

defined('_JEXEC') or die;

/**
 * Log levels
 */
abstract class LogLevel
{
	const ERROR = 1;
	const WARNING = 2;
	const INFO = 3;
	const DEBUG = 4;
}