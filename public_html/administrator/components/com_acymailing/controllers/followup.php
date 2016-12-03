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

acymailing_get('controller.newsletter');
class FollowupController extends NewsletterController{
	var $aclCat = 'campaign';
	var $copySendDate = true;

	function listing(){
		$this->setRedirect(acymailing_completeLink('campaign',false,true));
	}

	 function copy(){
		$followupid = array();
		$followupid[] = JRequest::getVar('followupid');
		if(empty($followupid)) return $this->listing();

		$this->copySendDate = true;
	 	JRequest::setVar('cid', $followupid);
		parent::copy();

		return $this->listing();

	 }
}
