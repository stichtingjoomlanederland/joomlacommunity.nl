<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Base\Exceptions;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use RuntimeException;

/**
 * An exception which leads to an error (and complete halt) in the backup process
 */
class ErrorException extends RuntimeException
{

}