<?php
/**
 * Akeeba Engine
 * The PHP-only site backup engine
 *
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 */

/**
 * Copyright (c) 2009, RealDolmen
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of RealDolmen nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY RealDolmen ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL RealDolmen BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Microsoft
 * @package    Microsoft
 * @copyright  Copyright (c) 2009, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */

namespace Akeeba\Engine\Postproc\Connector\Azure\Blob;

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2009, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 *
 * @property string  $Container       Container name
 * @property string  $Name            Name
 * @property string  $Etag            Etag
 * @property string  $LastModified    Last modified date
 * @property string  $Url             Url
 * @property int     $Size            Size
 * @property string  $ContentType     Content Type
 * @property string  $ContentEncoding Content Encoding
 * @property string  $ContentLanguage Content Language
 * @property boolean $IsPrefix        Is Prefix?
 * @property array   $Metadata        Key/value pairs of meta data
 */
class Instance
{
	/**
	 * Data
	 *
	 * @var array
	 */
	protected $_data = null;

	/**
	 * Constructor
	 *
	 * @param string  $containerName   Container name
	 * @param string  $name            Name
	 * @param string  $etag            Etag
	 * @param string  $lastModified    Last modified date
	 * @param string  $url             Url
	 * @param int     $size            Size
	 * @param string  $contentType     Content Type
	 * @param string  $contentEncoding Content Encoding
	 * @param string  $contentLanguage Content Language
	 * @param boolean $isPrefix        Is Prefix?
	 * @param array   $metadata        Key/value pairs of meta data
	 */
	public function __construct($containerName, $name, $etag, $lastModified, $url = '', $size = 0, $contentType = '', $contentEncoding = '', $contentLanguage = '', $isPrefix = false, $metadata = array())
	{
		$this->_data = array(
			'container'       => $containerName,
			'name'            => $name,
			'etag'            => $etag,
			'lastmodified'    => $lastModified,
			'url'             => $url,
			'size'            => $size,
			'contenttype'     => $contentType,
			'contentencoding' => $contentEncoding,
			'contentlanguage' => $contentLanguage,
			'isprefix'        => $isPrefix,
			'metadata'        => $metadata
		);
	}

	/**
	 * Magic overload for setting properties
	 *
	 * @param string $name  Name of the property
	 * @param string $value Value to set
	 */
	public function __set($name, $value)
	{
		if (array_key_exists(strtolower($name), $this->_data))
		{
			$this->_data[strtolower($name)] = $value;

			return;
		}

		throw new \Exception("Unknown property: " . $name);
	}

	/**
	 * Magic overload for getting properties
	 *
	 * @param string $name Name of the property
	 */
	public function __get($name)
	{
		if (array_key_exists(strtolower($name), $this->_data))
		{
			return $this->_data[strtolower($name)];
		}

		throw new \Exception("Unknown property: " . $name);
	}
}