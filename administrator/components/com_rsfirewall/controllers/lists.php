<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallControllerLists extends JControllerAdmin
{
	function __construct($config = array()) {
		parent::__construct($config);
		
		$user = JFactory::getUser();
		if (!$user->authorise('lists.manage', 'com_rsfirewall')) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_rsfirewall', false));
		}
		
		$this->registerTask('trash', 'delete');
	}
	
	public function getModel($name = 'List', $prefix = 'RSFirewallModel', $config = array('ignore_request' => true)) {
		return parent::getModel($name, $prefix, $config);
	}
}