<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<form id="frmBanUser" action="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&controller=posts&task=banUser');?>" method="post">
<p><?php echo JText::_('COM_EASYDISCUSS_FORM_BAN_USER_DESC'); ?></p>

<div class="discuss-item-left discuss-user discuss-user-role-<?php echo $post->getOwner()->roleid; ?>">
	<a class="" href="<?php echo $post->getOwner()->link;?>">
		<div class="discuss-avatar avatar-medium avatar-circle">
			<img src="<?php echo $post->getOwner()->avatar;?>" alt="<?php echo $this->escape($post->getOwner()->name);?>"<?php echo DiscussHelper::getHelper('EasySocial')->getPopbox($post->getOwner()->id);?> />
		</div>
		<div class="discuss-user-name mv-5">
			<?php if(!$post->user_id){ ?>
				<?php echo $post->poster_name; ?>
			<?php } else { ?>
				<?php echo $post->getOwner()->name; ?>
			<?php } ?>
		</div>		
	</a>
</div>

<div class="mt-20">
	<select name="duration" class="inputbox full-width">
		<option value="1"><?php echo JText::_('COM_EASYDISCUSS_BAN_DURATION_ONE_HOUR')?></option>
		<option value="3"><?php echo JText::_('COM_EASYDISCUSS_BAN_DURATION_THREE_HOUR')?></option>
		<option value="6"><?php echo JText::_('COM_EASYDISCUSS_BAN_DURATION_SIX_HOUR')?></option>
		<option value="12"><?php echo JText::_('COM_EASYDISCUSS_BAN_DURATION_HALF_DAY')?></option>
		<option value="24"><?php echo JText::_('COM_EASYDISCUSS_BAN_DURATION_ONE_DAY')?></option>
		<option value="48"><?php echo JText::_('COM_EASYDISCUSS_BAN_DURATION_TWO_DAYS')?></option>
		<option value="168"><?php echo JText::_('COM_EASYDISCUSS_BAN_DURATION_A_WEEK')?></option>
		<option value="720"><?php echo JText::_('COM_EASYDISCUSS_BAN_DURATION_A_MONTH')?></option>
	</select>
</div>

<div>
<span class="label label-info small"><?php echo JText::_('COM_EASYDISCUSS_NOTE');?>:</span>
<span class="small"><?php echo JText::_('COM_EASYDISCUSS_BAN_NOTES');?></span>
</div>
<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" id="postid" name="postid" value="<?php echo $post->id; ?>">
	<input type="hidden" id="postName" name="postName" value="<?php echo $post->getOwner()->name; ?>">
</form>


