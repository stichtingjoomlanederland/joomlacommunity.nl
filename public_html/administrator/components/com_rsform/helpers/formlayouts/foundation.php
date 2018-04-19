<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__).'/../formlayout.php';

class RSFormProFormLayoutFoundation extends RSFormProFormLayout
{
	public $errorClass      = ' has-error-foundation';
    public $fieldErrorClass = 'is-invalid-input';

	public $progressContent = '<div><div class="progress" role="progressbar" tabindex="0" aria-valuenow="{percent}" aria-valuemin="0" aria-valuemax="100"><div class="progress-meter" style="width: {percent}%"><p class="progress-meter-text"><em>{page_lang} <strong>{page}</strong> {of_lang} {total}</em></p></div></div></div>';
	
	public function __construct() {
		if (JFactory::getDocument()->direction == 'rtl') {
			$this->progressContent = '<div><div class="progress" role="progressbar" tabindex="0" aria-valuenow="{percent}" aria-valuemin="0" aria-valuemax="100"><div class="progress-meter" style="width: {percent}%"><p class="progress-meter-text"><em>{total} {of_lang} <strong>{page}</strong> {page_lang}</em></p></div></div></div>';
		}
		$this->progressOverwritten = true;
		$this->addStyleSheet('com_rsform/frameworks/foundation/foundation-errors.css');
		parent::__construct();
	}
	
	public function loadFramework() {
		// Load the CSS files
		$this->addStyleSheet('com_rsform/frameworks/foundation/foundation.css');
		
		// Load the RTL file
		if (JFactory::getDocument()->direction == 'rtl') {
			$this->addStyleSheet('com_rsform/frameworks/foundation/foundation-rtl.css');
		}

		// Load jQuery
		$this->addjQuery();

		// Load Javascript
		$this->addScript('com_rsform/frameworks/foundation/what-input.js');
		$this->addScript('com_rsform/frameworks/foundation/foundation.js');
		$this->addScript('com_rsform/frameworks/foundation/app.js');

	}

    public function generateButton($goto)
    {
        return '<button type="button" class="rsform-submit-button rsform-thankyou-button button" name="continue" onclick="'.$goto.'">'.JText::_('RSFP_THANKYOU_BUTTON').'</button>';
    }
}