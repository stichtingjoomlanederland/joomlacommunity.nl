<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__).'/../formlayout.php';

class RSFormProFormLayoutUIkit3 extends RSFormProFormLayout
{
	public $errorClass      = '';
    public $fieldErrorClass = 'uk-form-danger';

	public $progressContent = '<progress class="uk-progress" value="{percent}" max="100"></progress>';
	
	public function loadFramework() {
		// Load the CSS files
		if (JFactory::getDocument()->direction == 'rtl') {
			$this->addStyleSheet('com_rsform/frameworks/uikit3/uikit-rtl.min.css');
		} else {
			$this->addStyleSheet('com_rsform/frameworks/uikit3/uikit.min.css');
		}

		// Load jQuery
		$this->addjQuery();

		// Load Javascript
		$this->addScript('com_rsform/frameworks/uikit3/uikit.min.js');
		$this->addScript('com_rsform/frameworks/uikit3/uikit-icons.min.js');
	}

    public function generateButton($goto)
    {
        return '<button type="button" class="rsform-submit-button rsform-thankyou-button uk-button uk-button-primary" name="continue" onclick="'.$goto.'">'.JText::_('RSFP_THANKYOU_BUTTON').'</button>';
    }
}