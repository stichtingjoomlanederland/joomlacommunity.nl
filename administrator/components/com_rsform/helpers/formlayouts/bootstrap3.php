<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__).'/../formlayout.php';

class RSFormProFormLayoutBootstrap3 extends RSFormProFormLayout
{
	public $errorClass = ' has-error';

	public function loadFramework() {
		// Load the CSS files
		$this->addStyleSheet('com_rsform/frameworks/bootstrap3/bootstrap.min.css');
		
		// Load the RTL file if needed
		if (JFactory::getDocument()->direction == 'rtl') {
			$this->addStyleSheet('com_rsform/frameworks/bootstrap3/bootstrap-rtl.css');
		}

		// Load jQuery
		$this->addjQuery();

		// Load Javascript
		$this->addScript('com_rsform/frameworks/bootstrap3/bootstrap.min.js');

		// Set the script for the tooltips
		$script = array();
		$script[] = 'jQuery(function () {';
		$script[] = '	jQuery(\'[data-toggle="tooltip"]\').tooltip({"html": true,"container": "body"});';
		$script[] = '});';

		$this->addScriptDeclaration(implode("\n", $script));
	}
}