<?php
/**
 * @package         Regular Labs Extension Manager
 * @version         7.4.7
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2021 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\MVC\View\HtmlView as JView;
use Joomla\CMS\Object\CMSObject as JObject;
use Joomla\CMS\Toolbar\Toolbar as JToolbar;
use RegularLabs\Library\Parameters as RL_Parameters;

jimport('joomla.application.component.view');

/**
 * View class for default list view
 */
class RegularLabsManagerViewDefault extends JView
{
	protected $items  = null;
	protected $config = null;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->items = $this->get('Items');

		switch (JFactory::getApplication()->input->get('task'))
		{
			case 'update':
				$tpl = 'update';
				break;

			case 'storekey':
				$this->getModel()->storeKey();
				JFactory::getApplication()->redirect('index.php?option=com_regularlabsmanager', '', 'message', true);

				return;

			default:
				$this->addToolbar();
		}

		// Include the component HTML helpers.
		JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
		parent::display($tpl);
	}

	/**
	 * Function that gets the config settings
	 */
	protected function getConfig()
	{
		if ( ! is_null($this->config))
		{
			return $this->config;
		}

		$this->config = RL_Parameters::getInstance()->getComponentParams('regularlabsmanager');

		return $this->config;
	}

	/**
	 * Add the page title and toolbar
	 */
	protected function addToolbar()
	{
		$canDo = $this->getActions();

		JFactory::getDocument()->setTitle(JText::_('REGULARLABSEXTENSIONMANAGER'));

		JToolbarHelper::title(JText::_('REGULARLABSEXTENSIONMANAGER'), 'regularlabsmanager icon-reglab');

		$config = $this->getConfig();
		RegularLabsManagerToolbarHelper::addButtons($config);

		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_regularlabsmanager', '400');
		}
	}

	/**
	 * Gets a list of the actions that can be performed.
	 */
	public static function getActions()
	{
		$user      = JFactory::getUser();
		$result    = new JObject;
		$assetName = 'com_regularlabsmanager';

		$actions = [
			'core.admin', 'core.manage',
		];

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}

class RegularLabsManagerToolbarHelper extends JToolbarHelper
{
	public static function addButtons($config)
	{
		$bar = JToolbar::getInstance('toolbar');

		$html = '
			</div>
			<div class="btn-wrapper">
				<span class="refresh btn btn-small btn-success" onclick="rlem_function(\'refresh\');" rel="tooltip" title="' . JText::_('RLEM_REFRESH_DESC') . '">
					<span class="icon-refresh"></span>
				</span>
			</div>

			<div class="btn-wrapper hidden-phone installselected_disabled" id="toolbar-installselected_disabled">
				<span class="btn btn-small disabled">
					<span class="icon-box-add"></span> ' . JText::_('RLEM_INSTALL_SELECTED') . '
				</span>
			</div>
			<div class="btn-wrapper hidden-phone installselected" id="toolbar-installselected">
				<span class="btn btn-small btn-info hidden-phone" onclick="rlem_function(\'installselected\');" rel="tooltip" title="' . JText::_('RLEM_INSTALL_SELECTED_DESC') . '">
					<span class="icon-box-add"></span> ' . JText::_('RLEM_INSTALL_SELECTED') . '
				</span>
			</div>

			<div class="btn-wrapper updateall_disabled" id="toolbar-updateall_disabled">
				<span class="btn btn-small disabled">
					<span class="icon-upload"></span> ' . JText::_('RLEM_UPDATE_ALL') . '
				</span>
			</div>
			<div class="btn-wrapper updateall" id="toolbar-updateall">
				<span class="btn btn-small btn-warning" onclick="rlem_function(\'updateall\');" rel="tooltip" title="' . JText::_('RLEM_UPDATE_ALL_DESC') . '">
					<span class="icon-upload"></span> ' . JText::_('RLEM_UPDATE_ALL') . '
				</span>
		';

		if ($config->show_reinstall_all)
		{
			$html .= '
			</div>

			<div class="btn-wrapper reinstallall_disabled" id="toolbar-reinstallall_disabled">
				<span class="btn btn-small disabled">
					' . JText::_('RLEM_REINSTALL_ALL') . '
				</span>
			</div>
			<div class="btn-wrapper reinstallall" id="toolbar-reinstallall">
				<span class="btn btn-small btn-default" onclick="rlem_function(\'reinstallall\');" rel="tooltip" title="' . JText::_('RLEM_reinstall_ALL_DESC') . '">
					' . JText::_('RLEM_REINSTALL_ALL') . '
				</span>
			';
		}

		$bar->appendButton('Custom', $html);
	}
}
