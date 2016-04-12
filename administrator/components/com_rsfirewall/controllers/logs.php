<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallControllerLogs extends JControllerAdmin
{
	public function __construct($config = array()) {
		parent::__construct($config);
		
		$user = JFactory::getUser();
		if (!$user->authorise('logs.view', 'com_rsfirewall')) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_rsfirewall', false));
		}
	}
	
	public function getModel($name = 'Log', $prefix = 'RSFirewallModel', $config = array('ignore_request' => true)) {
		return parent::getModel($name, $prefix, $config);
	}
	
	public function truncate() {
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		
		$model = $this->getModel();
		$model->truncate();
		
		$this->setRedirect('index.php?option=com_rsfirewall&view=logs', JText::_('COM_RSFIREWALL_LOG_EMPTIED'));
	}
	
	public function download() {
		JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
		
		$model 		= $this->getModel('Logs');
		$app		= JFactory::getApplication();
		$document 	= JFactory::getDocument();
		try {
			if ($document instanceof JDocument) {
				$document->setMimeEncoding('text/csv');
			}
			
			@ob_end_clean();
			
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Cache-Control: public');
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Description: File Transfer');
			header('Content-Disposition: attachment; filename="rsfirewall_logs_'.JFactory::getDate()->format('Y-m-d-H-i', true).'.csv"');
			
			$model->toCSV();
			
			$app->close();
		} catch (Exception $e) {
			$app->enqueueMessage($e->getMessage(), 'error');
			$this->setRedirect('index.php?option=com_rsfirewall&view=logs');
		}
	}
	
	public function addToBlacklist() {
		$this->addToList(0);
	}
	public function addToWhitelist() {
		$this->addToList(1);
	}
	
	public function blockAjax() {
		$id = JFactory::getApplication()->input->getInt('id');

		// Grab IPs from the database
		$data = $this->getModel()->prepareData(array($id));
		
		// Build response
		$response = new stdClass();
		$response->type 	= 0;
		$response->result 	= true;
		
		if ($data) {
			$model = $this->getModel('list');
			$entry = array(
				'type' 	=> 0,
				'ip' 	=> trim($data[0]),
			);
			if (!$model->save($entry)) {
				$response->result = false;
				$response->error = $model->getError();
			} else {
				$response->listId = $model->getState($model->getName() . '.id');
			}
		}
		
		$this->showResponse(true, $response);
		
	}
	
	public function unBlockAjax() {
		$listId = JFactory::getApplication()->input->getInt('listId');
		$model  = $this->getModel('list');
		
		$response = new stdClass();
		$response->type 	= 1;
		$response->result 	= true;
		if (!$model->delete($listId)) {
			$response->result = false;
			$response->error = JText::_('COM_RSFIREWALL_ERROR_UNBLOCK');
		}
		
		$this->showResponse(true, $response);
	}
	
	public function addToList($type) {
		$app 	= JFactory::getApplication();
		$cid 	= $app->input->get('cid', array(), 'array');

		// Grab IPs from the database
		$data = $this->getModel()->prepareData($cid);
		
		$added = 0;
		foreach ($data as $ip) {
			$model = $this->getModel('list');

			$entry = array(
				'type' 	=> $type,
				'ip' 	=> trim($ip),
			); 
			
			if (!$model->save($entry)) {
				$app->enqueueMessage($model->getError(), 'error');
			} else {
				$added++;
			}
		
		}
		
		$this->setMessage(JText::sprintf('COM_RSFIREWALL_ADD_FROM_LOG_ITEM_SAVED_OK', $added));
		$this->setRedirect('index.php?option=com_rsfirewall&view=logs');
	}
	
	protected function showResponse($success, $data=null) {
		// set JSON encoding
		JFactory::getDocument()->setMimeEncoding('application/json');
		
		// compute the response
		$response = new stdClass();
		$response->success = $success;
		if ($data) {
			$response->data = $data;
		}
		
		// show the response
		echo json_encode($response);
		
		// close
		JFactory::getApplication()->close();
	}
}