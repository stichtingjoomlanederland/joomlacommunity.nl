<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallViewRsfirewall extends JViewLegacy
{
	public function display($tpl = null) {
		JFactory::getApplication()->enqueueMessage(JText::_('COM_RSFIREWALL_FRONTEND_MESSAGE'), 'warning');
	}
}