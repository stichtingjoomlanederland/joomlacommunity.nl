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
class Excludefiles extends Base
{
	public function __construct()
	{
		$this->object      = 'file';
		$this->subtype     = 'all';
		$this->method      = 'direct';
		$this->filter_name = 'Excludefiles';

		// We take advantage of the filter class magic to inject our custom filters
		$allFiles = explode('|', Factory::getConfiguration()->get('akeeba.basic.exclude_files'));
		$this->filter_data['[SITEROOT]'] = array_unique($allFiles);

		parent::__construct();
	}

}