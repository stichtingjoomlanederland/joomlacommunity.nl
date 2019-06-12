<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformModelRsform extends JModelLegacy
{
	public $params;
	
	public function __construct()
	{
		parent::__construct();

		$this->params = JFactory::getApplication()->getParams('com_rsform');
	}

	public function getFormId()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');
		return $formId ? $formId : $this->params->get('formId');
	}
	
	public function getParams()
	{
		return $this->params;
	}
}