<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      wbAmp
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      1.5.0.585
 * @date        2016-08-25
 */

defined('_JEXEC') or die;

/**
 * Form Field class for the Joomla Platform.
 * Supports a generic list of options.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 */
class JFormFieldWbconfigcheck extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	protected $type = 'wbconfigcheck';

	/*private $_messages     = null;*/

	public function getLabel()
	{
		return '';
	}

	public function getInput()
	{
		$this->_app = JFactory::getApplication();
		$this->_messages = array(
			'error' => array(),
			'warning' => array(),
			'info' => array()
		);

		// global config
		if (class_exists('WbampModel_Config'))
		{
			$this->_config = new WbampModel_Config();
		}
		else
		{
			// no need for error display.
			// this error will happen if shLib didn't fire up
			// and wbAMP system plugin will already display
			// an error message
			return '';
		}

		// current state of parameters
		$this->_pluginParams = WbampHelper_Plugins::getParams('system', 'wbamp');

		// run the checks
		WbampHelper_Systemcheck::updateSystemMessages($this->_pluginParams, $this->_config);

		// use shLib messages for control panel
		$messageList = ShlMsg_Manager::getInstance()->get(array('scope' => 'plg_wbamp', 'acknowledged' => false));

		$document = JFactory::getDocument();
		$htmlManager = ShlHtml_Manager::getInstance();
		ShlMsg_Manager::getInstance()->addAssets($document);
		$htmlManager->addAssets($document)
		            ->addSpinnerAssets($document);

		$renderedMessages = empty($messageList) ? '' : ShlMvcLayout_Helper::render('shlib.msg.list', array('msgs' => $messageList, 'id' => 'plg_wbamp-cp-msg-container'), SHLIB_LAYOUTS_PATH);
		$hash = md5($renderedMessages);

		// build the message list
		$renderedOutput = '<div style="display:none" id="wbl-plg-wbamp-cp-msg-center" class="row-fluid" data-token="' . JSession::getFormToken()
			. '" data-msgs-hash="' . $hash . '">' . $renderedMessages . '</div>';

		// and move it to top of page, where it'll be seen
		// regardless of which tab is selected
		$js = 'jQuery(document).ready(function(){
		var $src = jQuery(\'#wbl-plg-wbamp-cp-msg-center\');
		var $target = jQuery(\'#content\');
		$src.detach().prependTo($target).slideDown();
		});
		';

		$document->addScriptDeclaration($js);

		return $renderedOutput;
	}
}
