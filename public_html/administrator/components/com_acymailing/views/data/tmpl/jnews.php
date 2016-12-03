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
$db->setQuery('SELECT count(id) FROM '.acymailing_table('jnews_subscribers', false));
$resultUsers = $db->loadResult();
$db->setQuery('SELECT count(id) FROM '.acymailing_table('jnews_lists', false));
$resultLists = $db->loadResult();
$db->setQuery('SELECT count(id) FROM '.acymailing_table('jnews_mailings', false));
$resultNews = $db->loadResult();

echo JText::sprintf('USERS_IN_COMP', $resultUsers, 'jNews');
if(!empty($resultLists)){
	echo '<div class="acyblockoptions"><span class="acyblocktitle">'.JText::sprintf('LISTS_IN_COMP', $resultLists, 'jNews').'</span>';
	echo JText::sprintf('IMPORT_X_LISTS', $resultLists).'<br />';
	echo JText::sprintf('IMPORT_LIST_TOO', 'jNews').JHTML::_('acyselect.booleanlist', "jnews_lists");
	echo '</div>';
}
if(!empty($resultNews)){
	echo '<div class="acyblockoptions"><span class="acyblocktitle">'.JText::sprintf('LISTS_IN_COMP', $resultLists, 'jNews').'</span>';
	echo JText::sprintf('IMPORT_NEWSLETTERS_TOO', 'jNews').JHTML::_('acyselect.booleanlist', "jnews_news");
}
