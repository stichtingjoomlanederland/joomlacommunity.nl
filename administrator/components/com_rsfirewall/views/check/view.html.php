<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallViewCheck extends JViewLegacy
{
	protected $accessFile;
	protected $defaultAccessFile;
	protected $isWindows;
	protected $isPHP54;
	protected $offset;
	protected $sidebar;
	protected $lastRun;
	
	public function display($tpl = null) {
		$user = JFactory::getUser();
		if (!$user->authorise('check.run', 'com_rsfirewall')) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_rsfirewall', false));
		}
		
		$this->addToolBar();
		
		if ($this->get('IsOldIE')) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('COM_RSFIREWALL_IE_WARNING_DESC'), 'notice');
		}
		
		// the access file depends on the OS we're in
		$this->accessFile 		 = $this->get('accessFile');
		$this->defaultAccessFile = $this->get('defaultAccessFile');
		
		// on Windows we need to skip a few things
		$this->isWindows = $this->get('isWindows');
		
		// on PHP 5.4 we need to skip safe_mode
		$this->isPHP54	= $this->get('isPHP54');
		
		// is Xdebug loaded?
		$this->hasXdebug = extension_loaded('xdebug');
		
		$this->offset = $this->get('Offset');
		$this->config = RSFirewallConfig::getInstance();
		
		// Last time the System Check was run
		$this->lastRun = $this->config->get('system_check_last_run');
		
		// Prettify
		if ($this->lastRun) {
			$this->lastRun = JHtml::_('date.relative', $this->lastRun);
		} else {
			$this->lastRun = JText::_('COM_RSFIREWALL_NEVER');
		}
		
		$this->sidebar = $this->get('SideBar');
		
		parent::display($tpl);
	}
	
	protected function addToolbar() {
		// set title
		JToolBarHelper::title('RSFirewall!', 'rsfirewall');
		
		require_once JPATH_COMPONENT.'/helpers/toolbar.php';
		RSFirewallToolbarHelper::addToolbar('check');
	}
}