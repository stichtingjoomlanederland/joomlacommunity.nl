<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallController extends JControllerLegacy
{
	public function __construct() {
		parent::__construct();
		
		require_once JPATH_COMPONENT.'/helpers/version.php';
		$version 	= (string) new RSFirewallVersion;
		$jversion 	= new JVersion;
		$document 	= JFactory::getDocument();
		
		// Load the framework
		JHtml::_('behavior.framework');
		
		// Load stylesheet
		$document->addStyleSheet(JURI::root(true).'/administrator/components/com_rsfirewall/assets/css/com_rsfirewall.css?v='.$version);
		
		if ($jversion->isCompatible('3.0')) {
			// Load jQuery from Joomla! 3
			JHtml::_('jquery.framework');
		} else {
			// Load our own copy of jQuery
			$document->addScript(JURI::root(true).'/administrator/components/com_rsfirewall/assets/js/jquery.js');
			
			// Load our 2.5 stylesheet
			$document->addStyleSheet(JURI::root(true).'/administrator/components/com_rsfirewall/assets/css/style25.css?v='.$version);
		}
		
		// Load our scripts
		$document->addScript(JURI::root(true).'/administrator/components/com_rsfirewall/assets/js/jquery.knob.js');
		$document->addScript(JURI::root(true).'/administrator/components/com_rsfirewall/assets/js/rsfirewall.js?v='.$version);
		
		// load language, english first
		$lang = JFactory::getLanguage();
		$lang->load('com_rsfirewall', JPATH_ADMINISTRATOR, 'en-GB', true);
		$lang->load('com_rsfirewall', JPATH_ADMINISTRATOR, $lang->getDefault(), true);
		$lang->load('com_rsfirewall', JPATH_ADMINISTRATOR, null, true);
		
		// load the frontend language
		// this language file contains some event log translations
		// it's usually loaded by the System Plugin, but if it's disabled, we need to load it here
		$model = $this->getModel('rsfirewall');
		if (!$model->isPluginEnabled()) {
			$lang->load('com_rsfirewall', JPATH_SITE, 'en-GB', true);
			$lang->load('com_rsfirewall', JPATH_SITE, $lang->getDefault(), true);
			$lang->load('com_rsfirewall', JPATH_SITE, null, true);
		}
	}
	
	public function display($cachable = false, $urlparams = false) {
		parent::display($cachable, $urlparams);
	}
	
	public function acceptModifiedFiles() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$input = JFactory::getApplication()->input;
		$cid   = $input->get('cid', '', 'array');
		
		JArrayHelper::toInteger($cid);
		
		if ($cid) {
			$model = $this->getModel('rsfirewall');
			$model->acceptModifiedFiles($cid);
		}
		
		$this->setRedirect('index.php?option=com_rsfirewall', JText::_('COM_RSFIREWALL_HASH_CHANGED_SUCCESS'));
	}
	
	protected function showResponse($success, $data=null) {
		$app 		= JFactory::getApplication();
		$document 	= JFactory::getDocument();
		
		// set JSON encoding
		$document->setMimeEncoding('application/json');
		
		// compute the response
		$response = new stdClass();
		$response->success = $success;
		if ($data) {
			$response->data = $data;
		}
		
		// show the response
		echo json_encode($response);
		
		// close
		$app->close();
	}
	
	public function getLatestJoomlaVersion() {
		$model = $this->getModel('check');
		$data  = new stdClass();
		if ($response = $model->checkJoomlaVersion()) {
			$success = true;
			list($data->current, $data->latest, $data->is_latest) = $response;
		} else {
			// error
			$success = false;
			$data->message = $model->getError();
		}
		
		$this->showResponse($success, $data);
	}
	
	public function getLatestFirewallVersion() {
		$model = $this->getModel('check');
		$data  = new stdClass();
		if ($response = $model->checkRSFirewallVersion()) {
			$success = true;
			list($data->current, $data->latest, $data->is_latest) = $response;
		} else {
			// error
			$success = false;
			$data->message = $model->getError();
		}
		
		$this->showResponse($success, $data);
	}
}