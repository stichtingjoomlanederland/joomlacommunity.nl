<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__).'/../formlayout.php';

class RSFormProFormLayoutResponsive extends RSFormProFormLayout
{
    public $errorClass      = '';
    public $fieldErrorClass = 'rsform-error';

	public $progressContent = '<div><div class="rsformProgressContainer"><div class="rsformProgressBar" style="width: {percent}%;"><em>{page_lang} <strong>{page}</strong> {of_lang} {total}</em></div></div></div>';
	
	public function __construct() {
		if (JFactory::getDocument()->direction == 'rtl') {
			$this->progressContent = '<div><div class="rsformProgressContainer"><div class="rsformProgressBar" style="width: {percent}%;"><em>{total} {of_lang} <strong>{page}</strong> {page_lang}</em></div></div></div>';
		}
		$this->progressOverwritten = true;
		parent::__construct();
		
	}
    public function loadFramework() {
        // Load the CSS files
        $this->addStyleSheet('com_rsform/frameworks/responsive/responsive.css');
		
		if (JFactory::getDocument()->getDirection() == 'rtl') {
			$this->addStyleSheet('com_rsform/frameworks/responsive/responsive-rtl.css');
		}
    }

    public function generateButton($goto)
    {
        return
            '<div class="formResponsive">'.
                '<button type="button" class="rsform-submit-button rsform-thankyou-button" name="continue" onclick="'.$goto.'">'.JText::_('RSFP_THANKYOU_BUTTON').'</button>'.
            '</div>';
    }
}