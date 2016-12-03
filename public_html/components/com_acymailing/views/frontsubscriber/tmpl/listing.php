<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?>
<fieldset id="acy_subscriber_listing_menu">
	<div class="toolbar" id="acytoolbar" style="float: right;">
		<table><tr>
		<?php if(acymailing_isAllowed($this->config->get('acl_subscriber_import','all'))){ ?><td id="acybutton_subscriber_import"><a href="<?php echo JRoute::_('index.php?option=com_acymailing&ctrl=frontdata&task=import&listid='.JRequest::getInt('listid'))?>" ><span class="icon-32-import" title="<?php echo JText::_('IMPORT'); ?>"></span><?php echo JText::_('IMPORT'); ?></a></td><?php } ?>
		<?php if(acymailing_isAllowed($this->config->get('acl_subscriber_export','all'))){ ?><td id="acybutton_subscriber_export"><a onclick="<?php if(ACYMAILING_J16){ echo 'Joomla.';} ?>submitbutton('export'); return false;" href="#" ><span class="icon-32-acyexport" title="<?php echo JText::_('ACY_EXPORT'); ?>"></span><?php echo JText::_('ACY_EXPORT'); ?></a></td><?php } ?>
		<?php if(acymailing_isAllowed($this->config->get('acl_subscriber_import','all')) || acymailing_isAllowed($this->config->get('acl_subscriber_export','all'))){ ?><td id="acybuttondivider"><span class="divider"></span></td><?php } ?>
		<?php if(acymailing_isAllowed($this->config->get('acl_subscriber_manage','all'))){ ?><td id="acybutton_subscriber_add"><a onclick="javascript:submitbutton('add'); return false;" href="#" ><span class="icon-32-new" title="<?php echo JText::_('ACY_NEW'); ?>"></span><?php echo JText::_('ACY_NEW'); ?></a></td>
		<td id="acybutton_subscriber_edit"><a onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('PLEASE_SELECT',true);?>');}else{  submitbutton('edit')} return false;" href="#" ><span class="icon-32-edit" title="<?php echo JText::_('ACY_EDIT'); ?>"></span><?php echo JText::_('ACY_EDIT'); ?></a></td><?php } ?>
		<?php if(acymailing_isAllowed($this->config->get('acl_subscriber_delete','all'))){ ?><td id="acybutton_subscriber_delete"><a onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('PLEASE_SELECT',true);?>');}else{if(confirm('<?php echo JText::_('ACY_VALIDDELETEITEMS',true); ?>')){submitbutton('remove');}} return false;" href="#" ><span class="icon-32-delete" title="<?php echo JText::_('ACY_DELETE'); ?>"></span><?php echo JText::_('ACY_DELETE'); ?></a></td><?php } ?>
		</tr></table>
	</div>
	<div class="acyheader" style="float: left;"><h1><?php echo JText::_('USERS'); ?></h1></div>
</fieldset>

<?php
include(ACYMAILING_BACK.'views'.DS.'subscriber'.DS.'tmpl'.DS.'listing.php');
