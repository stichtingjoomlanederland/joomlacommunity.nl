<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

class CronController extends acymailingController{
	function __construct($config = array()){
		parent::__construct($config);
		$this->registerDefaultTask('cron');
		JRequest::setVar('tmpl', 'component');
	}

	function cron(){

		header("Content-type:text/html; charset=utf-8");
		if(strlen(ACYMAILING_LIVE) < 10) die('Process blocked because your domain name is not valid ('.ACYMAILING_LIVE.'). If you use your own cron system, please make sure you trigger AcyMailing with the full domain name and not just index.php?...');

		echo '<html><head><meta http-equiv="Content-Type" content="text/html;charset=utf-8" /><title>Cron</title></head><body>';
		$cronHelper = acymailing_get('helper.cron');
		$cronHelper->report = true;
		$cronHelper->skip = explode(',', JRequest::getString('skip'));
		$emailtypes = JRequest::getString('emailtypes');
		if(!empty($emailtypes)) $cronHelper->emailtypes = explode(',', $emailtypes);
		$cronHelper->cron();
		$cronHelper->report();
		echo '</body></html>';

		exit;
	}
}
