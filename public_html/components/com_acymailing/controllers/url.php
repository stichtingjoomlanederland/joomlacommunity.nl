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

class UrlController extends acymailingController{

	function __construct($config = array())
	{
		parent::__construct($config);

		JRequest::setVar('tmpl','component');
		$this->registerDefaultTask('click');

	}

	function click(){
		$urlid = JRequest::getInt('urlid');
		$mailid = JRequest::getInt('mailid');
		$subid = JRequest::getInt('subid');

		$urlClass = acymailing_get('class.url');
		$urlObject = $urlClass->get($urlid);

		if(empty($urlObject->urlid)){
			return JError::raiseError( 404, JText::_( 'Page not found'));
		}

		$urlClickClass = acymailing_get('class.urlclick');
		if(!acymailing_isRobot()) $urlClickClass->addClick($urlObject->urlid,$mailid,$subid);

		$this->setRedirect($urlObject->url);
	}
}
