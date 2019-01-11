<?php
/**
 * Akeeba Engine
 * The PHP-only site backup engine
 *
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 */

namespace Akeeba\Engine\Postproc\Connector\Dropbox2\Exception;

// Protection against direct access
use Exception;

defined('AKEEBAENGINE') or die();

class UnexpectedHTTPStatus extends Base
{
	public function __construct($errNo = "500", $code = 0, Exception $previous = null)
	{
		$message = "Unexpected HTTP status $errNo";

		parent::__construct($message, (int)$errNo, $previous);
	}

}
