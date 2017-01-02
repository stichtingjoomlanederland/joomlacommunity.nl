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
 * Folder exclusion filter. Excludes certain hosting directories.
 */
class Excludefolders extends Base
{
	public function __construct()
	{
		$this->object      = 'dir';
		$this->subtype     = 'all';
		$this->method      = 'direct';
		$this->filter_name = 'Excludefolders';

		// We take advantage of the filter class magic to inject our custom filters
		$allFolders = explode('|', Factory::getConfiguration()->get('akeeba.basic.exclude_folders'));
		$this->filter_data['[SITEROOT]'] = array_unique($allFolders);

		parent::__construct();
	}

}