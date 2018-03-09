<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\WAFEmailTemplates;

defined('_JEXEC') or die;

use FOF30\View\DataView\Html as BaseView;
use JText;

class Html extends BaseView
{
	/** @var  string	Order column */
	public $order = 'id';

	/** @var  string Order direction, ASC/DESC */
	public $order_Dir = 'DESC';

	/** @var  array	Sorting order options */
	public $sortFields = [];

	public $filters = [];

	protected function onBeforeBrowse()
	{
		parent::onBeforeBrowse();

		$hash = 'admintoolswafemailtemplates';

		// ...ordering
		$platform        = $this->container->platform;
		$input           = $this->input;
		$this->order     = $platform->getUserStateFromRequest($hash . 'filter_order', 'filter_order', $input, 'id');
		$this->order_Dir = $platform->getUserStateFromRequest($hash . 'filter_order_Dir', 'filter_order_Dir', $input, 'DESC');

		// ...filter state
		$this->filters['reason'] 	 	  = $platform->getUserStateFromRequest($hash . 'filter_reason', 'reason', $input);
		$this->filters['subject'] 	 	  = $platform->getUserStateFromRequest($hash . 'filter_subject', 'subject', $input);
		$this->filters['enabled'] 	 	  = $platform->getUserStateFromRequest($hash . 'filter_enabled', 'enabled', $input);
		$this->filters['language'] 	 	  = $platform->getUserStateFromRequest($hash . 'filter_language', 'language', $input);

		// Construct the array of sorting fields
		$this->sortFields = array(
			'reason' 	 		=> JText::_('COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_REASON_SELECT'),
			'subject' 	 		=> JText::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_FIELD_SUBJECT_LBL'),
			'enabled' 	 		=> JText::_('JPUBLISHED'),
			'language' 	 		=> JText::_('COM_ADMINTOOLS_COMMON_LANGUAGE'),
		);
	}
}
