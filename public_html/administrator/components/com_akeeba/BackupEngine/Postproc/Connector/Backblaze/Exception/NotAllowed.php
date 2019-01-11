<?php
/**
 * Akeeba Engine
 * The PHP-only site backup engine
 *
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 */

namespace Akeeba\Engine\Postproc\Connector\Backblaze\Exception;

// Protection against direct access
use Exception;

defined('AKEEBAENGINE') or die();

class NotAllowed extends Base
{
	public function __construct($errorDescription, $code = '500', Exception $previous = null)
	{
		$message = "The following action is not allowed by the Backblaze B2 Application Key you have provided: $errorDescription";

		parent::__construct($message, (int)$code, $previous);
	}

}
