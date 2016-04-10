<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallControllerConfiguration extends JControllerLegacy
{
	public function __construct() {
		parent::__construct();
		
		$user = JFactory::getUser();
		if (!$user->authorise('core.admin', 'com_rsfirewall')) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_rsfirewall', false));
		}
		
		$this->registerTask('apply', 'save');
	}
	
	public function cancel() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$this->setRedirect(JRoute::_('index.php?option=com_rsfirewall', false));
	}
	
	public function export() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$model 		= $this->getModel('configuration');
		$document 	= JFactory::getDocument();
		
		if ($document instanceof JDocument) {
			$document->setMimeEncoding('application/json');
		}
		
		@ob_end_clean();
		
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: public');
		header('Content-Type: application/json; charset=utf-8');
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename="configuration_'.JUri::getInstance()->getHost().'.json"');
		
		echo $model->toJSON();
		
		JFactory::getApplication()->close();
	}
	
	public function save() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$app   = JFactory::getApplication();
		$data  = $app->input->get('jform', array(), 'array');
		$model = $this->getModel('configuration');
		$form  = $model->getForm();
		
		// Validate the posted data.
		$return = $model->validate($form, $data);
		
		// Check for validation errors.
		if ($return === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();
			
			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_rsfirewall&view=configuration', false));
			return false;
		}
		
		$data = $return;
		
		if (!$model->save($data)) {
			$this->setMessage($model->getError(), 'error');
		} else {
			$this->setMessage(JText::_('COM_RSFIREWALL_CONFIGURATION_SAVED'));
		}
		
		$task = $this->getTask();
		if ($task == 'save') {
			$this->setRedirect(JRoute::_('index.php?option=com_rsfirewall', false));
		} elseif ($task == 'apply') {
			$this->setRedirect(JRoute::_('index.php?option=com_rsfirewall&view=configuration', false));
		}
	}
}