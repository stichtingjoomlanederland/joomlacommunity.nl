<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__).'/../formlayout.php';

class RSFormProFormLayoutUIkit extends RSFormProFormLayout
{
	public $errorClass      = '';
    public $fieldErrorClass = 'uk-form-danger';

	public $progressContent = '<div><div class="uk-progress"><div class="uk-progress-bar" style="width: {percent}%"><em>{page_lang} <strong>{page}</strong> {of_lang} {total}</em></div></div></div>';
	
	public function __construct() {
		if (JFactory::getDocument()->direction == 'rtl') {
			$this->progressContent = '<div><div class="uk-progress"><div class="uk-progress-bar" style="width: {percent}%{direction}"><em>{total} {of_lang} <strong>{page}</strong> {page_lang}</em></div></div></div>';
		}
		$this->progressOverwritten = true;
		parent::__construct();
		
	}
	
	public function loadFramework() {
		// Load the CSS files
		if (JFactory::getDocument()->direction == 'rtl') {
			$this->addStyleSheet('com_rsform/frameworks/uikit/uikit-rtl.css');
		} else {
			$this->addStyleSheet('com_rsform/frameworks/uikit/uikit.min.css');
		}
        $this->addStyleSheet('com_rsform/frameworks/uikit/uikit-grid.css');
		$this->addStyleSheet('com_rsform/frameworks/uikit/tooltip.min.css');
		$this->addStyleSheet('com_rsform/frameworks/uikit/form-advanced.min.css');
		$this->addStyleSheet('com_rsform/frameworks/uikit/progress.min.css');


		// Load jQuery
		$this->addjQuery();

		// Load Javascript
		$this->addScript('com_rsform/frameworks/uikit/uikit.min.js');
		$this->addScript('com_rsform/frameworks/uikit/tooltip.min.js');
	}

    public function generateButton($goto)
    {
        return '<button type="button" class="rsform-submit-button rsform-thankyou-button uk-button uk-button-primary" name="continue" onclick="'.$goto.'">'.JText::_('RSFP_THANKYOU_BUTTON').'</button>';
    }
}