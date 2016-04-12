<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallViewDbcheck extends JViewLegacy
{
	protected $supported;
	protected $tables;
	protected $sidebar;
	
	public function display($tpl = null)
	{
		$user = JFactory::getUser();
		if (!$user->authorise('dbcheck.run', 'com_rsfirewall')) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_rsfirewall', false));
		}
		
		$this->addToolBar();
		
		$this->supported = $this->get('IsSupported');
		$this->tables 	 = $this->get('Tables');
		
		$config = RSFirewallConfig::getInstance();
		$this->request_timeout = $config->get('request_timeout');
		
		$this->sidebar	 = $this->get('SideBar');
		
		parent::display($tpl);
	}
	
	protected function addToolbar() {
		// set title
		JToolBarHelper::title('RSFirewall!', 'rsfirewall');
		
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSFirewallToolbarHelper::addToolbar('dbcheck');
	}
	
	protected function _convert($b) {
		if ($b < 1)
			return '0.00';
			
		return number_format($b/1024, 2, '.', ' ');
	}
}