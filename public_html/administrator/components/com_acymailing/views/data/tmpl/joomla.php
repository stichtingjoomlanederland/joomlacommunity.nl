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
$db->setQuery('SELECT count(id) FROM '.acymailing_table('users', false));
$resultUsers = $db->loadResult();

$db->setQuery('SELECT count(subid) FROM '.acymailing_table('subscriber').' WHERE userid > 0');
$resultAcymailing = $db->loadResult();

echo JText::sprintf('ACY_IMPORT_NB_J_USERS', $resultUsers).'<br />';
echo JText::sprintf('ACY_IMPORT_NB_ACY_USERS', $resultAcymailing).'<br />';
?>
<br/>
<br/>
<?php echo JText::_('ACY_IMPORT_JOOMLA_1'); ?>
<ol>
	<li><?php echo JText::_('ACY_IMPORT_JOOMLA_2'); ?></li>
	<li><?php echo JText::_('ACY_IMPORT_JOOMLA_3'); ?></li>
	<li><?php echo JText::_('ACY_IMPORT_JOOMLA_4'); ?></li>
	<li><?php echo JText::_('ACY_IMPORT_JOOMLA_5'); ?></li>
</ol>
