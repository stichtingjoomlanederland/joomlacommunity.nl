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
$db = JFactory::getDBO();
$db->setQuery('SELECT count(*) FROM '.acymailing_table('ccnewsletter_subscribers', false));
$resultUsers = $db->loadResult();

$resultLists = array();
$resultNews = array();

if(in_array($db->getPrefix().'ccnewsletter_groups', $this->tables)){
	$db->setQuery('SELECT count(id) FROM '.acymailing_table('ccnewsletter_groups', false));
	$resultLists = $db->loadResult();

	$db->setQuery('SELECT count(id) FROM '.acymailing_table('ccnewsletter_newsletters', false));
	$resultNews = $db->loadResult();
}

echo JText::sprintf('USERS_IN_COMP', $resultUsers, 'ccNewsletter');

if(!empty($resultLists)){
	echo '<div class="acyblockoptions"><span class="acyblocktitle">'.JText::sprintf('LISTS_IN_COMP', $resultLists, 'ccNewsletter').'</span>';
	echo JText::sprintf('IMPORT_X_LISTS', $resultLists).'<br />';
	echo JText::sprintf('IMPORT_LIST_TOO', 'ccNewsletter').JHTML::_('acyselect.booleanlist', "ccNewsletter_lists");
	echo '</div>';
}
if(!empty($resultNews)){
	echo '<div class="acyblockoptions"><span class="acyblocktitle">'.JText::sprintf('LISTS_IN_COMP', $resultLists, 'ccNewsletter').'</span>';
	echo JText::sprintf('IMPORT_NEWSLETTERS_TOO', 'ccNewsletter').JHTML::_('acyselect.booleanlist', "ccNewsletter_news");
}
