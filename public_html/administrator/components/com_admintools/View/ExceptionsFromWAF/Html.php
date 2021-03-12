<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\ExceptionsFromWAF;

defined('_JEXEC') || die;

use Akeeba\AdminTools\Admin\View\Mixin\SystemPluginExists;
use FOF40\View\DataView\Html as BaseView;
use Joomla\CMS\Language\Text;


class Html extends BaseView
{
	/** @var  string    Order column */
	public $order = 'id';

	/** @var  string Order direction, ASC/DESC */
	public $order_Dir = 'DESC';

	/** @var  array    Sorting order options */
	public $sortFields = [];

	public $filters = [
		'foption' => '',
		'fview'   => '',
		'fquery'  => '',
	];

	use SystemPluginExists;

	protected function onBeforeBrowse()
	{
		$this->populateSystemPluginExists();

		$hash = 'admintoolsexcetpionswaf';

		// ...ordering
		$platform        = $this->container->platform;
		$input           = $this->input;
		$this->order     = $platform->getUserStateFromRequest($hash . 'filter_order', 'filter_order', $input, 'id');
		$this->order_Dir = $platform->getUserStateFromRequest($hash . 'filter_order_Dir', 'filter_order_Dir', $input, 'DESC');

		// ...filter state
		$this->filters['foption'] = $platform->getUserStateFromRequest($hash . 'filter_foption', 'foption', $input);
		$this->filters['fview']   = $platform->getUserStateFromRequest($hash . 'filter_fview', 'fview', $input);
		$this->filters['fquery']  = $platform->getUserStateFromRequest($hash . 'filter_fquery', 'fquery', $input);

		$this->populateSystemPluginExists();

		// Construct the array of sorting fields
		$this->sortFields = [
			'id'      => Text::_('ID'),
			'foption' => Text::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_OPTION'),
			'fview'   => Text::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_VIEW'),
			'fquery'  => Text::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_QUERY'),
		];

		parent::onBeforeBrowse();
	}
}
