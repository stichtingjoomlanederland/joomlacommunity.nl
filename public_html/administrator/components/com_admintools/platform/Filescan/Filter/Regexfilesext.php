<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Engine\Filter;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Factory;

/**
 * Subdirectories exclusion filter. Excludes temporary, cache and backup output
 * directories' contents from being backed up.
 */
class Regexfilesext extends Base
{
	public function __construct()
	{
		$this->object      = 'file';
		$this->subtype     = 'all';
		$this->method      = 'regex';
		$this->filter_name = 'Regexfilesext';

		$extensions = Factory::getConfiguration()->get('akeeba.basic.file_extensions', 'php|phps|php3|inc');
		$extensions = str_replace('.', '\\.', $extensions);
		$this->filter_data['[SITEROOT]'] = array(
			"!#\.(" . $extensions . ")$#"
		);

		parent::__construct();
	}

}