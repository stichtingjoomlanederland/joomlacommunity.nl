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
	public $errorClass      = ' has-error';
    public $fieldErrorClass = '';

	public $progressContent = '<div><div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="{percent}" aria-valuemin="0" aria-valuemax="100" style="width: {percent}%;"><em>{page_lang} <strong>{page}</strong> {of_lang} {total}</em></div></div></div>';
	
	public function __construct() {
		if (JFactory::getDocument()->direction == 'rtl') {
			$this->progressContent = '<div><div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="{percent}" aria-valuemin="0" aria-valuemax="100" style="width: {percent}%;"><em>{total} {of_lang} <strong>{page}</strong> {page_lang}</em></div></div></div>';
		}
		$this->progressOverwritten = true;
		parent::__construct();
	}
	
	public function loadFramework() {
		// Load the CSS files
		$this->addStyleSheet('com_rsform/frameworks/bootstrap3/bootstrap.min.css');
		
		// Load the RTL file
		if (JFactory::getDocument()->direction == 'rtl') {
			$this->addStyleSheet('com_rsform/frameworks/bootstrap3/bootstrap-rtl.min.css');
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

    public function generateButton($goto)
    {
        return '<button type="button" class="rsform-submit-button rsform-thankyou-button btn btn-primary" name="continue" onclick="'.$goto.'">'.JText::_('RSFP_THANKYOU_BUTTON').'</button>';
    }
}