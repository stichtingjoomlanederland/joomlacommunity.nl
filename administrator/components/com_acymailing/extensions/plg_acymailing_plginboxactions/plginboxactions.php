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

class plgAcymailingPlginboxactions extends JPlugin
{
	function __construct(&$subject, $config){
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('acymailing', 'inboxactions');
			$this->params = new acyParameter($plugin->params);
		}
	}

	function acymailing_replacetags(&$email){
		if(empty($email->params) || empty($email->params['action']) || $email->params['action'] == 'none' || empty($email->params['actionbtntext']) || empty($email->params['actionurl'])) return;
		$actionbtntext = JText::_($email->params['actionbtntext']);

		if(in_array($email->params['action'], array('confirm', 'save'))){
			$microdata = '
<span itemscope itemtype="http://schema.org/EmailMessage">
	<span itemprop="action" itemscope itemtype="http://schema.org/'.($email->params['action'] == 'confirm' ? 'ConfirmAction' : 'SaveAction').'">
		<meta itemprop="name" content="'.$actionbtntext.'"/>
		<span itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler">
			<link itemprop="url" href="'.$email->params['actionurl'].'"/>
		</span>
	</span>
</span>';
		}elseif($email->params['action'] == 'goto'){
			$microdata = '
<span itemscope itemtype="http://schema.org/EmailMessage">
	<span itemprop="action" itemscope itemtype="http://schema.org/ViewAction">
		<meta itemprop="name" content="'.$actionbtntext.'"/>
		<link itemprop="url" href="'.$email->params['actionurl'].'"/>
	</span>
</span>';
		}
		if(empty($microdata)) return;

		if(strpos($email->body,'</body>')) $email->body = str_replace('</body>',$microdata.'</body>',$email->body);
		else $email->body .= $microdata;
	 }
}//endclass
