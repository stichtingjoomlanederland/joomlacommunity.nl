<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

/**
 * PHP IMAP Class for Mailbox Messages
 */
class EasyDiscussMailerMessage extends JObject
{
	protected $stream		= null;
	protected $sequence		= 0;
	protected $structure	= null;
	protected $body			= null;
	protected $plain_data	= '';
	protected $html_data	= '';
	protected $parameters	= array();
	protected $attachment	= array();

	public function __construct($options = array())
	{
		$this->stream = isset($options[0]) ? $options[0] : null;
		$this->sequence = isset($options[1]) ? $options[1] : null;

		if (!$this->fetchStructure())
		{
			return false;
		}

		// If there are no parts, and the sub type is text, we want to just get the contents
		if (!isset($this->structure->parts)) {
			$this->getParts($this->structure, 0);

			return;
		}

		// count and see if it's multipart message
		$count	= count($this->structure->parts);

		if ($count > 0)
		{
			for ($i=0; $i<$count; $i++)
			{
				$section = $i + 1;
				$this->getParts($this->structure->parts[$i], $section);
			}
		}
		else
		{
			$this->getParts($this->structure);
		}

		return parent::__construct();
	}

	private function fetchStructure()
	{
		$this->structure	= @imap_fetchstructure($this->stream, $this->sequence);

		return $this->structure;
	}

	private function fetchBody($section)
	{
		if ($section)
		{
			$data	= @imap_fetchbody($this->stream, $this->sequence, $section);
		}
		else
		{
			$data	= @imap_body($this->stream, $this->sequence);
		}

		return $data;
	}

	private function getParts($part, $section=0)
	{
		$partData	= $this->fetchBody($section);

		$this->extractPart($part, $partData);


		// Sub parts
		if (!empty($part->parts))
		{
			foreach($part->parts as $index => $subpart)
			{
				$this->getParts($subpart, $section.'.'.($index+1));
			}
		}

		return; // nothing
	}

	private function extractPart($part, $data)
	{
		switch ($part->encoding)
		{
			case '0': // 7bit
			case '1': // 8 bit
			case '2': // binary
				break;
			case '3': // base 64
				//$this->body	= base64_decode($this->body);
				$data	= base64_decode($data);
				break;
			case '4': // quoted-printable
				//$this->body	= quoted_printable_decode($this->body);
				$data	= quoted_printable_decode($data);
				break;
			case '5': // other
			default:
				break;
		}

		$params		= self::getformatedParams($part);

		$encoding	= 'UTF-8';
		if (isset($params['charset']))
		{
			$encoding	= $params['charset'];
		}

		$type		= $part->type;
		$subtype	= strtolower($part->subtype);
		$id			= isset($part->id) ? $part->id : '';


		// text
		if ($type == 0 && $subtype == 'plain') {
			$this->plain_data .= self::stringToUTF8($encoding, trim($data));
		} elseif ($type == 0 && $subtype == 'html') {
			$this->html_data .= self::stringToUTF8($encoding, trim($data));
		} elseif ($type == 2) {
			$this->plain_data .= self::stringToUTF8($encoding, trim($data));
		}

		// Application
        elseif ($type == 3) {
            $application = array();
            $application['mime'] = $subtype; // PDF
			$application['data'] = $data; // binary
            $application['name'] = isset($params['name']) ? $params['name'] : $params['filename'];
            $application['id'] = $id;
            $application['size'] = $part->bytes;
            $application['type'] = 3;
            
            $this->attachment[] = $application;
        }		

        // image
		elseif ($type == 5) {
			$image = array();
			$image['mime'] = $subtype; // GIF
			$image['data'] = $data; // binary
			$image['name'] = isset($params['name']) ? $params['name'] : $params['filename']; // 35D.gif
			$image['id'] = $id; // <35D@goomoji.gmail>
			$image['size'] = $part->bytes;

			$this->attachment[]	= $image;
		}

		return;
	}

	private static function getformatedParams($part)
	{
		$parameters	= array();

		if (!$part->parameters)
		{
			return $parameters;
		}

		if ($part->ifparameters)
		{
			foreach($part->parameters as $param)
			{
				$parameters[strtolower($param->attribute)] = $param->value;
			}
		}
		if ($part->ifdparameters)
		{
			foreach($part->dparameters as $param)
			{
				$parameters[strtolower($param->attribute)] = $param->value;
			}
		}

		return $parameters;
	}

	private static function stringToUTF8($in_charset, $string)
	{
		if (function_exists('iconv'))
		{
			return iconv($in_charset, 'UTF-8', $string);
		}

		return $string;
	}

	public function getHTML()
	{
		if ($this->html_data) {
			return $this->html_data;
		}

		return $this->getPlain();
	}

	public function getPlain()
	{
		return $this->plain_data;
	}

	public function getAttachment()
	{
		return $this->attachment;
	}
}
