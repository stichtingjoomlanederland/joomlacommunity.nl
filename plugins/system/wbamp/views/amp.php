<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.5.0.585
 * @date        2016-08-25
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Responsible for output:
 * - rendering template
 * - output headers, incl. caching
 * - compress
 *
 */
class WbampView_Amp
{
	private $_displayData = array();
	private $_content     = '';

	/**
	 * Trigger rendering of base wbAMP template
	 * which will in turn render all sub-templates
	 *
	 * @param array $displayData all data required to render a page
	 * @param string $mainLayout the global, main layout to uses
	 * @return string
	 */
	public function render($displayData, $mainLayout)
	{
		$this->_displayData = $displayData;
		$this->_content = ShlMvcLayout_Helper::render($mainLayout, $displayData, WbampHelper_Runtime::$layoutsBasePaths);

		// output headers and echo
		$this->outputHeaders()
		     ->compress();

		return $this->_content;
	}

	/**
	 * Ouput headers set by wbAMP
	 *
	 * @return $this
	 */
	private function outputHeaders()
	{
		foreach ($this->_displayData['headers'] as $header)
		{
			header($header, true);
		}

		return $this;
	}

	/**
	 * Optionally compress and/or cache response
	 *
	 * Derived from Joomla Gzip method
	 */
	private function compress()
	{
		if (!$this->_displayData['params']->get('adv-gzip', 0))
		{
			// output no-cache header to try prevent CDN to mess up with content
			header('Expires: Mon, 1 Jan 2001 00:00:00 GMT', true);
			header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0', false);
			// HTTP 1.0
			header('Pragma: no-cache');
			return;
		}

		// don't compress if server is going to do it
		if (ini_get('zlib.output_compression') || (ini_get('output_handler') == 'ob_gzhandler'))
		{
			return;
		}

		// Verify that headers have not yet been sent, and that our connection is still alive.
		if (headers_sent() || connection_status() != CONNECTION_NORMAL)
		{
			return;
		}

		// Supported compression encodings.
		$supported = array(
			'x-gzip' => 'gz',
			'gzip' => 'gz',
			'deflate' => 'deflate'
		);

		// Get the supported encoding.
		$encodings = array_intersect(JFactory::getApplication()->client->encodings, array_keys($supported));

		// If no supported encoding is detected do nothing and return.
		if (empty($encodings))
		{
			return;
		}

		// Iterate through the encodings and attempt to compress the data using any found supported encodings.
		foreach ($encodings as $encoding)
		{
			if (($supported[$encoding] == 'gz') || ($supported[$encoding] == 'deflate'))
			{
				// Verify that the server supports gzip compression before we attempt to gzip encode the data.
				if (!extension_loaded('zlib') || ini_get('zlib.output_compression'))
				{
					continue;
				}

				// Attempt to gzip encode the data with an optimal level 4.
				$data = $this->_content;
				$gzdata = gzencode($data, 4, ($supported[$encoding] == 'gz') ? FORCE_GZIP : FORCE_DEFLATE);

				// If there was a problem encoding the data just try the next encoding scheme.
				if ($gzdata === false)
				{
					continue;
				}

				// Set the encoding headers.
				header('Content-Encoding: ' . $encoding);

				// Replace the output with the encoded data.
				$this->_content = $gzdata;

				// Compression complete, let's break out of the loop.
				break;
			}
		}
	}
}
