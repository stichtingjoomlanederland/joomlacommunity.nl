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

include(ACYMAILING_BACK.'views'.DS.'newsletter'.DS.'view.html.php');
class FollowupViewFollowup extends NewsletterViewNewsletter
{
	var $type = 'followup';
	var $ctrl = 'followup';
	var $nameForm = 'FOLLOWUP';
	var $aclCat = 'campaign';
	var $doc = 'campaign';
}
