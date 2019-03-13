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
	public $errorClass      = ' error';
    public $fieldErrorClass = '';

	public $progressContent = '<div><div class="progress progress-info"><div class="bar" style="width: {percent}%"><em>{page_lang} <strong>{page}</strong> {of_lang} {total}</em></div></div></div>';
	
	
	public function __construct() {
		if (JFactory::getDocument()->direction == 'rtl') {
			$this->progressContent = '<div><div class="progress progress-info"><div class="bar" style="width: {percent}%{direction}"><em>{total} {of_lang} <strong>{page}</strong> {page_lang}</em></div></div></div>';
		}
		$this->progressOverwritten = true;
		parent::__construct();
		
	}
	
	public function loadFramework() {
		JHtml::_('bootstrap.framework');
		JHtml::_('bootstrap.loadCss', true, JFactory::getDocument()->direction);
		
		JHtml::_('behavior.core');

		// Load tooltips
		JHtml::_('bootstrap.tooltip');
	}

    public function generateButton($goto)
    {
        return '<button type="button" class="rsform-submit-button rsform-thankyou-button btn btn-primary" name="continue" onclick="'.$goto.'">'.JText::_('RSFP_THANKYOU_BUTTON').'</button>';
    }
}