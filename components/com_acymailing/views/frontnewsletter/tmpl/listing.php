<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><fieldset id="acy_newsletter_listing_menu">
	<div class="toolbar" id="acytoolbar" style="float: right;">
		<table><tr>
			<td id="acybutton_newsletter_preview"><a onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('PLEASE_SELECT',true);?>');}else{  submitbutton('preview')} return false;" href="#"><span class="icon-32-acypreview" title="<?php echo JText::_('ACY_PREVIEW').'/'.JText::_('SEND'); ?>"></span><?php echo JText::_('ACY_PREVIEW').'/'.JText::_('SEND'); ?></a></td>
			<td id="acybuttondivider"><span class="divider"></span></td>
			<?php if(acymailing_isAllowed($this->config->get('acl_newsletters_manage','all'))){ ?><td id="acybutton_newsletter_add"><a onclick="javascript:submitbutton('form'); return false;" href="#" ><span class="icon-32-new" title="<?php echo JText::_('ACY_NEW'); ?>"></span><?php echo JText::_('ACY_NEW'); ?></a></td>
			<td id="acybutton_subscriber_edit"><a onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('PLEASE_SELECT',true);?>');}else{  submitbutton('edit')} return false;" href="#" ><span class="icon-32-edit" title="<?php echo JText::_('ACY_EDIT'); ?>"></span><?php echo JText::_('ACY_EDIT'); ?></a></td><?php } ?>
			<?php if(acymailing_isAllowed($this->config->get('acl_newsletters_delete','all'))){ ?><td id="acybutton_newsletter_delete"><a onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('PLEASE_SELECT',true);?>');}else{if(confirm('<?php echo JText::_('ACY_VALIDDELETEITEMS',true); ?>')){submitbutton('remove');}} return false;" href="#" ><span class="icon-32-delete" title="<?php echo JText::_('ACY_DELETE'); ?>"></span><?php echo JText::_('ACY_DELETE'); ?></a></td><?php } ?>
			<?php if(acymailing_isAllowed($this->config->get('acl_newsletters_copy','all'))){ ?><td id="acybutton_newsletter_copy"><a onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('PLEASE_SELECT',true);?>');}else{  submitbutton('copy')} return false;" href="#"><span class="icon-32-copy" title="<?php echo JText::_('ACY_COPY'); ?>"></span><?php echo JText::_('ACY_COPY'); ?></a></td><?php } ?>
		</tr></table>
	</div>
	<div class="acyheader" style="float: left;"><h1><?php echo JText::_('NEWSLETTER'); ?></h1></div>
</fieldset>
<?php
include(ACYMAILING_BACK.'views'.DS.'newsletter'.DS.'tmpl'.DS.'listing.php');
