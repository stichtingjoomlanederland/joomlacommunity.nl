<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformModelRsform extends JModelLegacy
{
	var $params;
	
	function __construct()
	{
		parent::__construct();
		
		$app 			= JFactory::getApplication();
		$this->params 	= $app->getParams('com_rsform');
	}

	function getFormId()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');
		return $formId ? $formId : $this->params->get('formId');
	}
	
	function getParams()
	{
		return $this->params;
	}
}