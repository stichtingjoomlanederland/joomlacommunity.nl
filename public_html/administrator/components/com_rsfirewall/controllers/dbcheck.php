<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallControllerDbCheck extends JControllerLegacy
{
	public function __construct() {
		parent::__construct();
		
		$user = JFactory::getUser();
		if (!$user->authorise('dbcheck.run', 'com_rsfirewall')) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_rsfirewall', false));
		}
	}
	
	public function optimize() {
		$app 	= JFactory::getApplication();
		$model 	= $this->getModel('DbCheck');
		
		if (!($result = $model->optimizeTables())) {
			echo $model->getError();
		} else {
			echo JText::sprintf('COM_RSFIREWALL_OPTIMIZE_REPAIR_RESULT', $result['optimize'], $result['repair']);
		}
		
		$app->close();
	}
}