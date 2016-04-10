<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__).'/../formlayout.php';

class RSFormProFormLayoutBootstrap2 extends RSFormProFormLayout
{
	public $errorClass = ' error';

	public function loadFramework() {
		if (!RSFormProHelper::isJ('3.0')) {
			return $this->loadFrameworkForJoomla25();
		}

		JHtml::_('bootstrap.framework');
		JHtml::_('bootstrap.loadCss', true, JFactory::getDocument()->direction);

		if (RSFormProHelper::isJ('3.3')) {
			JHtml::_('behavior.core');
		}

		// Load tooltips
		JHtml::_('bootstrap.tooltip');
	}

	protected function loadFrameworkForJoomla25() {
		// Load the CSS files
		$this->addStyleSheet('com_rsform/frameworks/bootstrap2/bootstrap.min.css');
		$this->addStyleSheet('com_rsform/frameworks/bootstrap2/bootstrap-extended.css');
		$this->addStyleSheet('com_rsform/frameworks/bootstrap2/bootstrap-responsive.min.css');
		if (JFactory::getDocument()->direction == 'rtl') {
			$this->addStyleSheet('com_rsform/frameworks/bootstrap2/bootstrap-rtl.css');
		}
		// Load jQuery
		$this->addjQuery();

		// Load Javascript
		$this->addScript('com_rsform/frameworks/bootstrap2/bootstrap.min.js');
		// Set the script for the tooltips
		$script = array();
		$script[] = 'jQuery(document).ready(function(){';
		$script[] = '	jQuery(\'.hasTooltip\').tooltip({"html": true,"container": "body"});';
		$script[] = '});';

		$this->addScriptDeclaration(implode("\n", $script));
	}
}