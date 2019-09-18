<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="o-nav__item">
	<a href="<?php echo $this->profile->getEditProfileLink();?>" class="o-nav__link ed-toolbar__link">
		<i class="fa fa-cog t-sm-visible"></i>
		<span>
			<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_EDIT_PROFILE'); ?>
		</span>
	</a>
</div>

<div class="o-nav__item">
	<a href="<?php echo EDR::_('view=mypost');?>" class="o-nav__link ed-toolbar__link">
		<i class="fa fa-file-text-o t-sm-visible"></i>
		<span>
			<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_POSTS'); ?>
		</span>
	</a>
</div>

<?php if (ED::isModerator()) { ?>
<div class="o-nav__item">
	<a href="<?php echo EDR::_('view=assigned');?>" class="o-nav__link ed-toolbar__link">
		<i class="fa fa-file-text-o t-sm-visible"></i>
		<span>
			<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_ASSIGNED_POSTS'); ?>
		</span>
	</a>
</div>
<?php } ?>

<?php if ($this->config->get('main_favorite')) { ?>
<div class="o-nav__item">
	<a href="<?php echo EDR::_('view=favourites');?>" class="o-nav__link ed-toolbar__link">
		<i class="fa fa-heart-o t-sm-visible"></i>
		<span>
			<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_FAVOURITES'); ?>
		</span>
	</a>
</div>
<?php } ?>

<?php if ($showManageSubscription) { ?>
<div class="o-nav__item">
	<a href="<?php echo EDR::_('view=subscription');?>" class="o-nav__link ed-toolbar__link">
		<i class="fa fa-inbox t-sm-visible"></i>
		<span>
			<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_SUBSCRIPTION'); ?>
		</span>
	</a>
</div>
<?php } ?>

<?php if (($this->acl->allowed('manage_holiday') && $this->config->get('main_work_schedule')) || $this->acl->allowed('manage_pending') || ED::isSiteAdmin()) { ?>
<div class="o-nav__item">
	<a href="<?php echo EDR::_('view=dashboard');?>" class="o-nav__link ed-toolbar__link">
		<i class="fa fa-dashboard t-sm-visible"></i>
		<span>
			<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_DASHBOARD'); ?>
		</span>
	</a>
</div>
<?php } ?>

<div class="o-nav__item">
	<a href="javascript:void(0);" class="o-nav__link ed-toolbar__link" data-ed-toolbar-logout>
		<i class="fa fa-power-off t-sm-visible"></i>
		<span>
			<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_LOGOUT'); ?>
		</span>
	</a>

	<form method="post" action="<?php echo JRoute::_('index.php');?>" data-ed-toolbar-logout-form>
		<input type="hidden" value="com_users"  name="option">
		<input type="hidden" value="user.logout" name="task">
		<input type="hidden" name="<?php echo ED::getToken();?>" value="1" />
		<input type="hidden" value="<?php echo EDR::getLogoutRedirect(); ?>" name="return" />
	</form>
</div>