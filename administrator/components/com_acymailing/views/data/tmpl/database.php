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
$subfields = acymailing_getColumns('#__acymailing_subscriber');

$config = acymailing_config();
$postFields = (array)@unserialize($config->get('import_db_fields', ''));
?>
<table <?php echo $this->isAdmin ? '' : 'class="admintable table" cellspacing="1"' ?>>
	<tr>
		<td class="acykey"><?php echo JText::_('TABLENAME'); ?></td>
		<td><input type="text" name="tablename" style="width:200px" size="80" value="<?php echo $this->escape($config->get('import_db_table', '')); ?>"/></td>
	</tr>
	<?php
	if(!empty($subfields)){
		foreach($subfields as $oneField => $type){
			if(in_array($oneField, array('subid', 'confirmed', 'confirmed_date', 'confirmed_ip', 'lastopen_date', 'lastsent_date', 'lastclick_date', 'enabled', 'key', 'userid', 'accept', 'html', 'created'))) continue;
			echo '<tr><td class="acykey">'.$oneField.'</td><td><input style="width:200px" type="text" name="fields['.$oneField.']" value="'.@$postFields[$oneField].'" /></td></tr>';
		}
	}
	if($this->config->get('require_confirmation')){ ?>
		<tr id="trdbconfirm">
			<td class="acykey">
				<?php echo JText::_('IMPORT_CONFIRMED'); ?>
			</td>
			<td>
				<?php echo JHTML::_('acyselect.booleanlist', "import_confirmed_database", '', JRequest::getInt('import_confirmed_database', 1), JText::_('JOOMEXT_YES'), JTEXT::_('JOOMEXT_NO')); ?>
			</td>
		</tr>
	<?php }
	?>
</table>
