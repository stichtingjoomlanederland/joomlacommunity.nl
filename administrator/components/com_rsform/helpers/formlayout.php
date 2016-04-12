<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormProFormLayout
{	
	protected function addStyleSheet($path) {
		$stylesheet = JHtml::stylesheet($path, array(), true, true);
		RSFormProAssets::addStyleSheet($stylesheet);
	}
	
	protected function addScript($path) {
		$script = JHtml::script($path, false, true, true);
		RSFormProAssets::addScript($script);
	} 
	
	protected function addScriptDeclaration($script) {
		RSFormProAssets::addScriptDeclaration($script);
	}
	
	protected function addjQuery() {
		$jversion = new JVersion;
		$is30	  = $jversion->isCompatible('3.0');
		
		if ($is30) {
			JHtml::_('jquery.framework', true);
		} else {
			$this->addScript('com_rsform/jquery/jquery.min.js');
			$this->addScript('com_rsform/jquery/jquery-noconflict.js');
		}
	}
}