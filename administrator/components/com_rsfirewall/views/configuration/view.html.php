<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallViewConfiguration extends JViewLegacy
{
	protected $tabs;
	protected $field;
	protected $forms;
	protected $fieldsets;
	protected $geoip;
	protected $config;
	protected $sidebar;
	
	function display($tpl = null) {
		$user = JFactory::getUser();
		if (!$user->authorise('core.admin', 'com_rsfirewall')) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_rsfirewall', false));
		}
		
		$this->addToolBar();
		
		// tabs
		$this->tabs		 = $this->get('RSTabs');
		$this->field	 = $this->get('RSFieldset');
		
		// form
		$this->form		 = $this->get('Form');
		$this->fieldsets = $this->form->getFieldsets();
		
		// geoip db
		$model = $this->getModel('configuration');
		$this->geoip = (object) array(
			'uploaded' 	=> $this->get('isGeoIPUploaded'),
			'path' 		=> $this->get('geoIPPath'),
			'native'	=> $model->isGeoIPNative(),
			'nativedb'	=> $model->hasGeoIPNativeDatabase()
		);
		
		// config
		$this->config	= $this->get('Config');
		
		$this->sidebar	= $this->get('SideBar');
		
		$this->isJ30	= $this->get('isJ30');
		
		parent::display($tpl);
	}
	
	protected function addToolbar() {
		// set title
		JToolBarHelper::title('RSFirewall!', 'rsfirewall');
		
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSFirewallToolbarHelper::addToolbar('configuration');
		
		JToolBarHelper::apply('configuration.apply');
		JToolBarHelper::save('configuration.save');
		JToolBarHelper::cancel('configuration.cancel');
		
		$class = $this->get('isJ30') ? 'download' : 'export';
		
		JToolBarHelper::custom('configuration.export', $class, $class, JText::_('COM_RSFIREWALL_EXPORT_CONFIGURATION'), false, false);
	}
}