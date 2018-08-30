<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussClosure
{
	const URL = 'http://deployer.stackideas.com:1280';

	/**
	 * Allows caller to pass in a block of javascript code that should be minified and compressed
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function minify($contents)
	{
		$body = $this->buildBody($contents);

		// Wtf is this?
		$bytes = (function_exists('mb_strlen') && ((int)ini_get('mbstring.func_overload') & 2)) ? mb_strlen($body, '8bit') : strlen($body);

		// Query the server now
		$contents = $this->query($body);

		if (preg_match('/^Error\(\d\d?\):/', $contents)) {
			return JError::raiseError(500, $contents);
		}

		return $contents;
	}

	/**
	 * Main section to ping to the server
	 *
	 * @since   4.0
	 * @access  public
	 */
	protected function query($body)
	{
		$ch = curl_init(self::URL);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
		$contents = curl_exec($ch);
		curl_close($ch);

		if (false === $contents) {
			return false;
		}

		return trim($contents);
	}

	/**
	 * Constructs the post body
	 *
	 * @since   4.0
	 * @access  public
	 */
	protected function buildBody($contents, $returnErrors = false)
	{
		return http_build_query(array(
			'js_code' => $contents,
			'output_info' => ($returnErrors ? 'errors' : 'compiled_code'),
			'output_format' => 'text',
			'compilation_level' => 'SIMPLE_OPTIMIZATIONS',
			'language'   => 'ECMASCRIPT5'
		), null, '&');
	}
}