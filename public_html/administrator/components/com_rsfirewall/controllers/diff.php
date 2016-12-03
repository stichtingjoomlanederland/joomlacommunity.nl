<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallControllerDiff extends JControllerLegacy
{
	protected $folder_permissions = 755;
	protected $file_permissions = 644;

	public function __construct()
	{
		parent::__construct();

		$user = JFactory::getUser();
		if (!$user->authorise('check.run', 'com_rsfirewall'))
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_rsfirewall', false));
		}

		$config                   = RSFirewallConfig::getInstance();
		$this->folder_permissions = $config->get('folder_permissions');
		$this->file_permissions   = $config->get('file_permissions');
	}

	public function download()
	{
		$app       = JFactory::getApplication();
		$model     = $this->getModel('diff');
		$localFile = $app->input->get('localFile', '', 'path');

		$model->downloadOriginalFile($localFile);

		$app->close();
	}
}