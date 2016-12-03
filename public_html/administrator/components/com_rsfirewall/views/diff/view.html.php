<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallViewDiff extends JViewLegacy
{
	public function display($tpl = null) {
		$user = JFactory::getUser();
		if (!$user->authorise('check.run', 'com_rsfirewall')) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_rsfirewall', false));
		}
		
		try {
			// Get local file properties
			$this->localFilename 	= $this->get('LocalFilename');
			$this->local  			= $this->get('LocalFile');
			
			// Get remote file properties
			$this->remoteFilename 	= $this->get('RemoteFilename');
			$this->remote 			= $this->get('RemoteFile');
			
			// Get file without root path
			$this->filename = $this->get('File');
			
			$this->hashId = $this->get('hashId');
			
			parent::display($tpl);
		} catch (Exception $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}
}