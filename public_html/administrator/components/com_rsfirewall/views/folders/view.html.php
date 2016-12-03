<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallViewFolders extends JViewLegacy
{	
	protected $elements;
	protected $folders;
	protected $files;
	protected $DS;
	
	protected $allowFolders;
	protected $allowFiles;
	
	public function display( $tpl = null ) {
		$user = JFactory::getUser();
		if (!$user->authorise('core.admin', 'com_rsfirewall')) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_rsfirewall', false));
		}
		
		$this->name		= $this->get('Name');
		$this->elements = $this->get('Elements');
		$this->previous	= $this->get('Previous');
		$this->folders 	= $this->get('Folders');
		$this->files	= $this->get('Files');
		$this->path		= $this->get('Path');
		$this->DS		= $this->get('DS');
		
		$this->allowFolders = $this->get('allowFolders');
		$this->allowFiles 	= $this->get('allowFiles');
		
		parent::display($tpl);
	}
}