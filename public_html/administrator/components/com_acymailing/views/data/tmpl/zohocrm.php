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
$listClass = acymailing_get('class.list');
$this->data = $listClass->getLists('listid');
$this->values = array();
$this->values[] = JHTML::_('select.option', '0', '- - -');
foreach($this->data as $onelist){
	$this->values[] = JHTML::_('select.option', $onelist->listid, $onelist->name);
}
$zohoFields = $this->config->get('zoho_fields');
$value['zoho_fields'] = empty($zohoFields) ? array() : unserialize($zohoFields);
$zohoList = $this->config->get('zoho_list');
$value['zoho_list'] = empty($zohoList) ? 'Leads' : $zohoList;

if(empty($value['zoho_fields'])) $value['zoho_fields'] = array('First Name' => 'name');
?>
<span class="acyblocktitle"><?php echo JText::_('Options'); ?></span>
<table <?php echo $this->isAdmin ? 'class="acymailing_table"' : 'class="admintable table" cellspacing="1"' ?>>
	<?php if($this->config->get('require_confirmation')){ ?>
		<tr id="trfileconfirm">
			<td class="acykey">
				<?php echo JText::_('IMPORT_CONFIRMED'); ?>
			</td>
			<td>
				<?php
				echo JHTML::_('acyselect.booleanlist', "zoho_confirmed", '', $this->config->get('zoho_confirmed'), JText::_('JOOMEXT_YES'), JTEXT::_('JOOMEXT_NO'));
				?>
			</td>
		</tr>
	<?php } ?>
	<tr id="trfileoverwrite">
		<td class="acykey">
			<?php echo JText::_('OVERWRITE_EXISTING'); ?>
		</td>
		<td>
			<?php
			echo JHTML::_('acyselect.booleanlist', "zoho_overwrite", '', $this->config->get('zoho_overwrite'), JText::_('JOOMEXT_YES'), JTEXT::_('JOOMEXT_NO')); ?>
		</td>
	</tr>
	<tr id="trzohodelete">
		<td class="acykey">
			<?php echo JText::_('DELETE_USERS'); ?>
		</td>
		<td>
			<?php
			echo JHTML::_('acyselect.booleanlist', "zoho_delete", '', $this->config->get('zoho_delete'), JText::_('JOOMEXT_YES'), JTEXT::_('JOOMEXT_NO')); ?>
		</td>
	</tr>
	<tr id="trzohoimportnew">
		<td class="acykey">
			<?php echo JText::_('ACY_ZOHO_IMPORT_NEW'); ?>
		</td>
		<td>
			<?php
			echo JHTML::_('acyselect.booleanlist', "zoho_importnew", '', $this->config->get('zoho_importnew'), JText::_('JOOMEXT_YES'), JTEXT::_('JOOMEXT_NO').' : '.JText::_('ALL_USERS')); ?>
		</td>
	</tr>
	<tr id="trzohogeneratename">
		<td class="acykey">
			<?php echo acymailing_tooltip(JText::_('ACY_ZOHO_GENERATE_NAME_DESC'), JText::_('ACY_ZOHO_GENERATE_NAME'), '', JText::_('ACY_ZOHO_GENERATE_NAME')); ?>
		</td>
		<td>
			<?php $generateFrom = array();
			$generateFrom[] = JHTML::_('select.option', 'fromemail', JText::_('ACY_ZOHO_GENERATE_NAME_FROM_EMAIL'));
			$generateFrom[] = JHTML::_('select.option', 'fromconcat', JTEXT::_('ACY_ZOHO_GENERATE_NAME_FROM_FIELDS'));
			echo JHTML::_('select.radiolist', $generateFrom, "zoho_generate_name", 'class="inputbox" size="1"', 'value', 'text', $this->config->get('zoho_generate_name', 'fromemail')); ?>
		</td>
	</tr>
	<tr id="trzohoapikey">
		<td class="acykey">
			<?php echo 'Auth Token'; ?>
		</td>
		<td>
			<input class="inputbox" type="text" name="zoho_apikey" size="35" value="<?php echo $this->escape($this->config->get('zoho_apikey')); ?>">
		</td>
	</tr>
	<tr id="trzoholist">
		<td class="acykey">
			<?php echo JText::_('ACY_ZOHOLIST'); ?>
		</td>
		<td>
			<?php $lists = array();
			$lists[] = JHTML::_('select.option', 'Leads', 'Leads');
			$lists[] = JHTML::_('select.option', 'Contacts', 'Contacts');
			$lists[] = JHTML::_('select.option', 'Vendors', 'Vendors');
			echo JHTML::_('select.genericlist', $lists, "zoho_list", 'class="inputbox" size="1"', 'value', 'text', $value['zoho_list']); ?>
		</td>
	</tr>
	<tr id="trzohocv">
		<td class="acykey">
			<?php echo acymailing_tooltip(JText::_('CUSTOM_VIEW_DESC'), JText::_('CUSTOM_VIEW'), '', JText::_('CUSTOM_VIEW')); ?>
		</td>
		<td>
			<input class="inputbox" type="text" name="zoho_cv" size="35" value="<?php echo $this->escape($this->config->get('zoho_cv')); ?>">
		</td>
	</tr>
</table>


<span class="acyblocktitle" style="margin-top: 20px;"><?php echo JText::_('FIELD'); ?></span>
<?php
$db = JFactory::getDBO();
$subfields = acymailing_getColumns('#__acymailing_subscriber');
$acyfields = array();
$acyfields[] = JHTML::_('select.option', '', ' - - - ');
if(!empty($subfields)){
	foreach($subfields as $oneField => $typefield){
		if(in_array($oneField, array('subid', 'confirmed', 'enabled', 'key', 'userid', 'accept', 'html', 'created', 'zohoid', 'zoholist', 'email'))) continue;
		$acyfields[] = JHTML::_('select.option', $oneField, $oneField);
	}
}
?>
<table <?php echo $this->isAdmin ? 'class="acymailing_table"' : 'class="admintable table" cellspacing="1"' ?>>
	<?php
	echo '<tr><td class="acykey">'.JText::_('ACY_LOADZOHOFIELDS').'</td><td>';
	if(!ACYMAILING_J30){
		echo '<input type="submit" class="btn" onclick="javascript: submitbutton(\'loadZohoFields\')" value="'.JText::_('ACY_LOADFIELDS').'"></td></tr>';
	}else{
		echo '<input type="submit" class="btn" onclick="Joomla.submitbutton(\'loadZohoFields\')" value="'.JText::_('ACY_LOADFIELDS').'"></td></tr>';
	}

	$fields = explode(',', $config->get('zoho_fieldsname', 'First Name,Last Name,Date of Birth'));

	foreach($fields as $oneField){
		$fieldValue = '';
		if(!empty($value['zoho_fields'][$oneField])) $fieldValue = $value['zoho_fields'][$oneField];
		echo '<tr><td class="acykey">'.$oneField.'</td><td><div id="zoho_fields">'.JHTML::_('select.genericlist', $acyfields, "zoho_fields[".$oneField."]", 'class="inputbox" size="1"', 'value', 'text', $fieldValue).'</div></td></tr>';
	}
	?>
</table>

