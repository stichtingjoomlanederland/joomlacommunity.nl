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
include(ACYMAILING_BACK.'views'.DS.'subscriber'.DS.'view.html.php');

class FrontsubscriberViewFrontsubscriber extends SubscriberViewSubscriber
{

	var $ctrl='frontsubscriber';

	function display($tpl = null)
	{
		$doc = JFactory::getDocument();
		$doc->addStyleSheet( ACYMAILING_CSS.'frontendedition.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'frontendedition.css'));

		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.modal');

		global $Itemid;
		$this->assignRef('Itemid',$Itemid);

		parent::display($tpl);
	}

	function listing(){

		if(empty($_POST) && !JRequest::getInt('start') && !JRequest::getInt('limitstart')){
			JRequest::setVar('limitstart',0);
		}

		return parent::listing();
	}

	function addqueue(){
		include_once(ACYMAILING_BACK.'views'.DS.'send'.DS.'view.html.php');
		$sendView = new SendViewSend();
		$values = $sendView->addqueue();

		if(!empty($values)){
			$this->assignRef('subscriber',$values['subscriber']);
			$this->assignRef('emaildrop',$values['emaildrop']);
			$this->assign('hours', $values['hours']);
			$this->assign('minutes', $values['minutes']);
		}
	}
}
