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
$importHelper = acymailing_get('helper.import');
$importHelper->setciviprefix();
$db = JFactory::getDBO();
try{
	$db->setQuery('SELECT count(*) FROM '.$importHelper->civiprefix.'email WHERE is_primary = 1');
	$resultUsers = $db->loadResult();
	echo JText::sprintf('USERS_IN_COMP', $resultUsers, 'CiviCRM');
}catch(Exception $e){
	echo("Error counting users from CiviCRM. CiviCRM table probably doesn't exists");
}

