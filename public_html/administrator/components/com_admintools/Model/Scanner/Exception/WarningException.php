<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model\Scanner\Exception;

defined('_JEXEC') or die;

/**
 * Indicates a non-fatal exception which should be reported but does not prevent restarting the execution
 */
class WarningException extends FileScannerException
{

}