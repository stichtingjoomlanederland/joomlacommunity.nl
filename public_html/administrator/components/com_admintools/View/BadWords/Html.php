<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\BadWords;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\View\Mixin\SystemPluginExists;
use FOF30\View\DataView\Html as BaseView;
use JText;

class Html extends BaseView
{
	use SystemPluginExists;

	/** @var  string	Order column */
	public $order = 'id';

	/** @var  string Order direction, ASC/DESC */
	public $order_Dir = 'DESC';

	/** @var  array	Sorting order options */
	public $sortFields = [];

	public $filters = [];

	protected function onBeforeBrowse()
	{
		$this->populateSystemPluginExists();

		parent::onBeforeBrowse();

		$hash = 'admintoolsbadwords';

		// ...ordering
		$platform        = $this->container->platform;
		$input           = $this->input;
		$this->order     = $platform->getUserStateFromRequest($hash . 'filter_order', 'filter_order', $input, 'id');
		$this->order_Dir = $platform->getUserStateFromRequest($hash . 'filter_order_Dir', 'filter_order_Dir', $input, 'DESC');

		// ...filter state
		$this->filters['word'] 	 	  = $platform->getUserStateFromRequest($hash . 'filter_word', 'word', $input);

		// Construct the array of sorting fields
		$this->sortFields = array(
			'word' 	 		=> JText::_('COM_ADMINTOOLS_LBL_BADWORD_WORD'),
		);
	}
}
