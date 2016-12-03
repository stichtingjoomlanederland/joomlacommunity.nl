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

class FrontnewsletterViewFrontnewsletter extends NewsletterViewNewsletter
{

	var $ctrl='frontnewsletter';

	function display($tpl = null)
	{
		$doc = JFactory::getDocument();
		$doc->addStyleSheet( ACYMAILING_CSS.'frontendedition.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'frontendedition.css'));

		JHTML::_('behavior.tooltip');

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

	function preview(){
		JHTML::_('behavior.modal','a.modal');
		$config = acymailing_config();
		$this->assignRef('config',$config);
		return parent::preview();
	}

	function form(){
		JHTML::_('behavior.modal','a.modal');
		return parent::form();
	}

	function scheduleconfirm(){
		JRequest::setVar('tmpl','component');
		$mailid = acymailing_getCID('mailid');
		$listmailClass = acymailing_get('class.listmail');
		$mailClass = acymailing_get('class.mail');
		$this->assign('lists',$listmailClass->getReceivers($mailid));
		$this->assign('mail',$mailClass->get($mailid));
	}

	function sendconfirm(){
		$mailid = acymailing_getCID('mailid');
		$mailClass = acymailing_get('class.mail');
		$listmailClass = acymailing_get('class.listmail');
		$queueClass = acymailing_get('class.queue');
		$mail = $mailClass->get($mailid);

		$values = new stdClass();
		$values->nbqueue = $queueClass->nbQueue($mailid);

		if(empty($values->nbqueue)){
			$lists = $listmailClass->getReceivers($mailid);
			$this->assignRef('lists',$lists);

			$db = JFactory::getDBO();
			$db->setQuery('SELECT count(subid) FROM `#__acymailing_userstats` WHERE `mailid` = '.intval($mailid));
			$values->alreadySent = $db->loadResult();
		}

		$this->assignRef('values',$values);
		$this->assignRef('mail',$mail);
	}

	function stats(){
		include(ACYMAILING_BACK.'views'.DS.'diagram'.DS.'view.html.php');
		$diagramClass = new DiagramViewDiagram();

		$doc = JFactory::getDocument();
		$doc->addScript("https://www.google.com/jsapi");

		$diagramClass->mailing();

		$this->assignRef('mailing',$diagramClass->mailing);
		$this->assignRef('mailingstats',$diagramClass->mailingstats);
		$this->assignRef('openclick',$diagramClass->openclick);
		$this->assignRef('openclickday',$diagramClass->openclickday);
		$this->assignRef('mailinglinks',$diagramClass->mailinglinks);
		$this->assignRef('config',acymailing_config());
	}

	function statsclick(){
		$mailid = JRequest::getInt('mailid');
		if(empty($mailid)) return;

		JRequest::setVar('filter_mail',$mailid);

		include(ACYMAILING_BACK.'views'.DS.'statsurl'.DS.'view.html.php');
		$statsclick = new StatsurlViewStatsurl();

		$statsclick->detaillisting();

		$statsclick->filters->mail = '<input type="hidden" value="'.$mailid.'" name="mailid" />';

		$this->assignRef('filters',$statsclick->filters);
		$this->assignRef('rows',$statsclick->rows);
		$this->assignRef('pageInfo',$statsclick->pageInfo);
		$this->assignRef('pagination',$statsclick->pagination);
		$this->assignRef('config',$statsclick->config);
	}

	function detailstats(){
		$mailid = JRequest::getInt('mailid');
		if(empty($mailid)) return;

		JRequest::setVar('filter_mail',$mailid);

		include(ACYMAILING_BACK.'views'.DS.'stats'.DS.'view.html.php');
		$detailstats = new StatsViewStats();

		$detailstats->detaillisting();

		$detailstats->filters->mail = '<input type="hidden" value="'.$mailid.'" name="mailid" />';

		$this->assignRef('filters',$detailstats->filters);
		$this->assignRef('toggleClass',$detailstats->toggleClass);
		$this->assignRef('rows',$detailstats->rows);
		$this->assignRef('pageInfo',$detailstats->pageInfo);
		$this->assignRef('pagination',$detailstats->pagination);
		$this->assignRef('mailing',$detailstats->mailing);
	}

	function unsubscribed(){
		$mailid = JRequest::getInt('filter_mail');
		if(empty($mailid)) $mailid = JRequest::getInt('mailid');
		if(empty($mailid)) return;

		JRequest::setVar('filter_mail',$mailid);

		include(ACYMAILING_BACK.'views'.DS.'stats'.DS.'view.html.php');
		$detailstats = new StatsViewStats();
		$detailstats->setLayout('unsubscribed');
		$detailstats->unsubscribed();

		$this->assignRef('filterMail',$detailstats->filterMail);
		$this->assignRef('filter_mail',$detailstats->filter_mail);
		$this->assignRef('filters',$detailstats->filters);
		$this->assignRef('toggleClass',$detailstats->toggleClass);
		$this->assignRef('rows',$detailstats->rows);
		$this->assignRef('pageInfo',$detailstats->pageInfo);
		$this->assignRef('pagination',$detailstats->pagination);
		$this->assignRef('mailing',$detailstats->mailing);
		$this->assign('app', $detailstats->app);
	}
}
