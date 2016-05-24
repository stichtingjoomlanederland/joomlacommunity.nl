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

namespace Akeeba\Engine\Util\Pushbullet;

// Protection against direct access
defined('AKEEBAENGINE') or die();

class ApiException extends \Exception
{
	// Exception thrown by Pushbullet
}