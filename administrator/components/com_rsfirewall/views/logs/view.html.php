<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallViewLogs extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $levels;
	protected $filterbar;
	protected $sidebar;
	protected $isJ30;
	
	function display( $tpl = null ) {
		$user = JFactory::getUser();
		if (!$user->authorise('logs.view', 'com_rsfirewall')) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_rsfirewall', false));
		}
		
		$this->addToolBar();
		
		$this->isJ30		= $this->get('isJ30');
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->state 		= $this->get('State');
		$this->levels		= $this->get('Levels');
		
		$this->filterbar	= $this->get('FilterBar');		
		$this->sidebar 		= $this->get('SideBar');
		
		// Load GeoIP helper class
		require_once JPATH_ADMINISTRATOR.'/components/com_rsfirewall/helpers/geoip/geoip.php';
		$this->geoip = RSFirewallGeoIP::getInstance();
		
		parent::display($tpl);
	}
	
	protected function addToolBar() {
		// set title
		JToolBarHelper::title('RSFirewall!', 'rsfirewall');
		
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSFirewallToolbarHelper::addToolbar('logs');
		
		JToolBarHelper::addNew('logs.addtoblacklist', JText::_('COM_RSFIREWALL_LOG_ADD_BLACKLIST'), true);
		JToolBarHelper::addNew('logs.addtowhitelist', JText::_('COM_RSFIREWALL_LOG_ADD_WHITELIST'), true);
		JToolBarHelper::deleteList('COM_RSFIREWALL_CONFIRM_DELETE', 'logs.delete');
		JToolBarHelper::divider();
		JToolBarHelper::custom('logs.truncate', 'delete', 'delete', JText::_('COM_RSFIREWALL_EMPTY_LOG'), false, false);
		
		$class = $this->get('isJ30') ? 'download' : 'export';
		
		JToolBarHelper::custom('logs.download', $class, $class, JText::_('COM_RSFIREWALL_DOWNLOAD_LOG'), false, false);
	}
	
	protected function showDate($date) {
		return JHTML::_('date', $date, 'Y-m-d H:i:s');
	}
}