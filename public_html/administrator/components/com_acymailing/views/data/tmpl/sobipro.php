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
	$config = acymailing_config();
	$sobiproInfo = unserialize($config->get('sobipro_import'));

	$query='SELECT a.fid, a.nid, fieldType, section, b.name, filter FROM #__sobipro_field as a JOIN #__sobipro_object as b ON a.section = b.id  WHERE (fieldType = "inbox" AND ( filter = "title" OR filter = "0" OR filter = "")) OR (fieldType = "inbox" AND filter = "email") ORDER BY `section`';
	$db->setQuery($query);
	$nidResult = $db->loadObjectList();

	$section = array();

	foreach($nidResult as $oneResult){
		if(!isset($section[$oneResult->section])) {
			$section[$oneResult->section] = array();
			$section[$oneResult->section]['sectionName'] = $oneResult->name;
			$section[$oneResult->section]['sectionID'] = $oneResult->section;
			$section[$oneResult->section]['email'] = array(JHTML::_('select.option', '','- - -'));
			$section[$oneResult->section]['name'] = array(JHTML::_('select.option', '','- - -'));
		}
		if(($oneResult->fieldType=='inbox' && $oneResult->filter=='email')){
			$section[$oneResult->section]['email'][] = JHTML::_('select.option', $oneResult->fid, $oneResult->nid);
		}
		if(($oneResult->fieldType == 'inbox' && (($oneResult->filter == "title") || ($oneResult->filter == "0") || ($oneResult->filter == "")))){
			$section[$oneResult->section]['name'][] = JHTML::_('select.option', $oneResult->fid, $oneResult->nid);
		}
	}
	?>
	<table>
	<thead>
	<tr>
		<th><?php echo JText::_('TAG_CATEGORIES');?></th><th><?php echo JText::_('JOOMEXT_EMAIL'); ?></th><th><?php echo JText::_('JOOMEXT_NAME'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach($section as $oneSection){
	?>
		<tr>
			<td><?php echo $oneSection['sectionName']; ?></td>
			<td><?php echo JHTML::_('select.genericlist', $oneSection['email'], 'config['.$oneSection['sectionID'].'][sobiEmail]' , 'size="1"', 'value', 'text', isset($sobiproInfo[$oneSection['sectionID']]['sobiEmail']) ? $sobiproInfo[$oneSection['sectionID']]['sobiEmail'] : ''); ?></td>
			<td><?php echo JHTML::_('select.genericlist', $oneSection['name'], 'config['.$oneSection['sectionID'].'][sobiName]' , 'size="1"', 'value', 'text', isset($sobiproInfo[$oneSection['sectionID']]['sobiName']) ? $sobiproInfo[$oneSection['sectionID']]['sobiName'] : '' ); ?></td>
		</tr>
	<?php
	}
	?>
	</tbody>
	</table>
