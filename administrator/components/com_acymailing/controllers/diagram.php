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

class DiagramController extends acymailingController{

	function listing(){
		if(!$this->isAllowed('statistics','manage')) return;
		JRequest::setVar( 'layout', 'listing'  );
		return parent::display();
	}

	function mailing(){
		if(!$this->isAllowed('statistics','manage')) return;
		JRequest::setVar( 'layout', 'mailing'  );
		return parent::display();
	}
}
