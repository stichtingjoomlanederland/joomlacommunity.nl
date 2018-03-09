<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2006-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 * (partial) Dropbox v2 API implementation in PHP
 */

namespace Akeeba\Engine\Postproc\Connector\Backblaze\Exception;

// Protection against direct access
use Exception;

defined('AKEEBAENGINE') or die();

class APIError extends Base
{
	public function __construct($error = "", $errorDescription, $code = '500', Exception $previous = null)
	{
		$message = "Backblaze B2 API Error $error: $errorDescription";

		parent::__construct($message, (int)$code, $previous);
	}

}
