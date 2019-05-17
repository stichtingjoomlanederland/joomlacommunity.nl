<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.1.4
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php

class AutomationViewAutomation extends acymView
{
    public function __construct()
    {
        parent::__construct();

        if (empty(acym_getVar('int', 'id')) && acym_getVar('string', 'layout') != 'info') {
            $this->steps = array(
                'action' => 'ACYM_ACTIONS',
                'filter' => 'ACYM_ACTIONS_TARGETS',
                'summary' => 'ACYM_SUMMARY',
            );
        } else {
            $this->steps = array(
                'info' => 'ACYM_INFORMATION',
                'condition' => 'ACYM_CONDITIONS',
                'action' => 'ACYM_ACTIONS',
                'filter' => 'ACYM_ACTIONS_TARGETS',
                'summary' => 'ACYM_SUMMARY',
            );
        }
    }
}
