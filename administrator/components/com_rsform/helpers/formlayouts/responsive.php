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
    public $errorClass = '';

    public function loadFramework() {
        // Load the CSS files
        $this->addStyleSheet('com_rsform/frameworks/responsive/responsive.css');
		
		if (JFactory::getDocument()->getDirection() == 'rtl') {
			$this->addStyleSheet('com_rsform/frameworks/responsive/responsive-rtl.css');
		}
    }
}