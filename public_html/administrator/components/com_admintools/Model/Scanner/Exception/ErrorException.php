<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model\Scanner\Exception;

defined('_JEXEC') or die;

/**
 * Indicates a fatal exception which prevents restarting the execution
 */
class ErrorException extends FileScannerException
{

}