<?php
/**
 * @version		$Id: view.html.php 20630 2011-02-09 19:16:03Z infograf768 $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of tracks.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class SpUpgradeViewLog extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;       

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{

		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		require_once JPATH_COMPONENT .'/models/fields/tables.php';
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/helpers/spupgrade.php';

                $canDo = SPUpgradeHelper::getActions();
		
		JToolBarHelper::title(JText::_('COM_SPUPGRADE_TABLES_TITLE'), 'install.png');
                
                $view = JRequest::setVar('view', JRequest::getCmd('view', 'tables'));
                SPUpgradeHelper::addSubmenu($view);
                
		if ($canDo->get('core.admin')) 
		{
                    $bar = JToolBar::getInstance('toolbar');
                    if ($canDo->get('core.delete')) {
                            $bar->appendButton('Confirm','COM_SPUPGRADE_CONFIRM_MSG', 'delete', 'COM_SPUPGRADE_LOG_DELETE_INDIVIDUAL', 'log.delete_ind',true);
                            $bar->appendButton('Confirm','COM_SPUPGRADE_CONFIRM_MSG', 'delete', 'COM_SPUPGRADE_LOG_DELETE_MASS', 'log.delete',false);
                    }			
                    JToolBarHelper::divider();
                    JToolBarHelper::preferences('com_spupgrade');
		}
                $bar = JToolBar::getInstance( 'toolbar' );
                $bar->appendButton( 'Help', 'help', 'JTOOLBAR_HELP', 'http://www.kainotomo.com/products/sp-transfer/documentation', 640, 480 );
	}
}
