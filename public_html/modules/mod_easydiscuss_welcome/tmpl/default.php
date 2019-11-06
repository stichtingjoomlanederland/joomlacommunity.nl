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
<div id="ed" class="ed-mod m-welcome <?php echo $params->get('moduleclass_sfx');?>">
<?php if ($isLoggedIn) { ?>
<div class="ed-mod__section">
    <div class="m-welcome__content">
    	
		<div class="ed-list--vertical has-dividers--bottom-space">
			<div class="ed-list__item">
		    	<div class="o-flag">
					<?php if ($params->get('showavatar')) {?>
		    			<div class="o-flag__image">
		                    <?php echo ED::themes()->html('user.avatar', $my, array('rank' => true, 'status' => true, 'size' => 'md')); ?>
		    			</div>
					<?php } ?>
		    		<div class="o-flag__body">
		    			<?php echo ED::themes()->html('user.username', $my); ?>
		    			<?php if ($params->get('showranks')) { ?>
		    			<div class="t-fs--sm">( <?php echo $ranking; ?> )</div>
		    			<?php } ?>
		    		</div>
		    	</div>
			</div>
			<?php if ($params->get('showbadges')) { ?>
			<div class="ed-list__item">
				<div class="user-badges-heading"><?php echo JText::_('MOD_EASYDISCUSS_WELCOME_YOUR_BADGES'); ?></div>
				<?php if ($badges) { ?>
					<?php foreach ($badges as $badge) { ?>
					<span>
						<a href="<?php echo EDR::_('index.php?option=com_easydiscuss&view=badges&layout=listings&id=' . $badge->id);?>">
						<img src="<?php echo $badge->getAvatar();?>" width="22" title="<?php ED::string()->escape($badge->title);?>" />
						</a>
					</span>
					<?php }?>
				<?php } else { ?>
				<div class="t-fs--sm">
					<?php echo JText::_('MOD_EASYDISCUSS_WELCOME_NO_BADGES_YET'); ?>
				</div>
				<?php } ?>
			</div>
			<?php } ?>

			<div class="ed-list__item">
				<a class="edit-profile" href="<?php echo ED::getEditProfileLink();?>"><i class="fa fa-cog t-lg-mr--sm"></i> <span><?php echo JText::_('MOD_EASYDISCUSS_WELCOME_EDIT_PROFILE');?></span></a>
			</div>
			
			<?php if ($params->get('showfavourites')) { ?>
				<div class="ed-list__item">
					<a class="my-favourites" href="<?php echo EDR::_('index.php?option=com_easydiscuss&view=favourites'); ?>"><i class="fa fa-heart-o t-lg-mr--sm"></i> <span><?php echo JText::_('MOD_EASYDISCUSS_WELCOME_VIEW_FAVOURITE');?></span></a>
				</div>
			<?php } ?>

			<?php if ($params->get('showsubscriptions')) { ?>
				<div class="ed-list__item">
					<a class="my-subscriptions" href="<?php echo EDR::_('index.php?option=com_easydiscuss&view=subscription'); ?>"><i class="fa fa-inbox t-lg-mr--sm"></i> <span><?php echo JText::_('MOD_EASYDISCUSS_WELCOME_VIEW_SUBSCRIPTIONS');?></span></a>
				</div>
			<?php } ?>

			<?php if (ED::isSiteAdmin($my->id) && $params->get('show_assignedposts')) { ?>
				<div class="ed-list__item">
					<a class="my-assigned" href="<?php echo EDR::_('index.php?option=com_easydiscuss&view=assigned'); ?>"><i class="fa fa-file-text-o t-lg-mr--sm"></i> <span><?php echo JText::_('MOD_EASYDISCUSS_WELCOME_VIEW_ASSIGNED_POST');?></span></a>
				</div>
			<?php } ?>

			<?php if ($params->get('show_mydiscussions')) { ?>
				<div class="ed-list__item">
					<a class="user-discussions" href="<?php echo $my->getLink(); ?>"><i class="fa fa-file-text-o t-lg-mr--sm"></i> <span><?php echo JText::_('MOD_EASYDISCUSS_WELCOME_MY_DISCUSSIONS');?></span></a>
				</div>
			<?php } ?>

			<?php if ($params->get('show_browsediscussions')) { ?>
				<div class="ed-list__item">
					<a class="all-discussions" href="<?php echo EDR::_('index.php?option=com_easydiscuss');?>"><i class="fa fa-file-text-o t-lg-mr--sm"></i> <span><?php echo JText::_('MOD_EASYDISCUSS_WELCOME_BROWSE_DISCUSSIONS');?></span></a>
				</div>
			<?php } ?>

			<?php if ($params->get('show_browsecategories')) { ?>
				<div class="ed-list__item">
					<a class="discuss-categories" href="<?php echo EDR::_('index.php?option=com_easydiscuss&view=categories');?>"><i class="fa fa-folder t-lg-mr--sm"></i> <span><?php echo JText::_('MOD_EASYDISCUSS_WELCOME_BROWSE_CATEGORIES');?></span></a>
				</div>
			<?php } ?>

			<?php if ($params->get('show_browsetags')) { ?>
				<div class="ed-list__item">
					<i class="ico"></i><a class="discuss-tags" href="<?php echo EDR::_('index.php?option=com_easydiscuss&view=tags');?>"><i class="fa fa-tag t-lg-mr--sm"></i> <span><?php echo JText::_('MOD_EASYDISCUSS_WELCOME_BROWSE_TAGS');?></span></a>
				</div>
			<?php } ?>

			<?php if ($params->get('show_browsebadges')) { ?>
				<div class="ed-list__item">
					<i class="ico"></i><a class="discuss-badges" href="<?php echo EDR::_('index.php?option=com_easydiscuss&view=badges');?>"><i class="fa fa-trophy t-lg-mr--sm"></i> <span><?php echo JText::_('MOD_EASYDISCUSS_WELCOME_BROWSE_BADGES');?></span></a>
				</div>
			<?php } ?>

			<div class="ed-list__item">
				<a class="discuss-logout" href="<?php echo JRoute::_('index.php?option=com_users&task=user.logout&' . ED::getToken() . '=1&return='.$return);?>"><i class="fa fa-sign-out t-lg-mr--sm"></i> <span><?php echo JText::_('MOD_EASYDISCUSS_WELCOME_SIGN_OUT');?></span></a>
			</div>
		</div>
	</div>
</div>	
<?php } else if ($params->get('enablelogin')) { ?>
<div class="ed-mod__section">
	<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" name="login" id="form-login" >
		<?php echo $params->get('pretext'); ?>
		<ul class="g-list-unstyled">
				<label for="discuss-welcome-username" class="input-label"><?php echo JText::_('MOD_EASYDISCUSS_WELCOME_USERNAME'); ?></label>
				<input type="text" id="discuss-welcome-username" name="username" class="form-control" size="18">
			</li>
				<label for="discuss-welcome-password" class="input-label"><?php echo JText::_('MOD_EASYDISCUSS_WELCOME_PASSWORD'); ?></label>
				<input type="password" id="discuss-welcome-password" name="password" class="form-control" size="18" >
			</li>
			<?php if (JPluginHelper::isEnabled('system', 'remember')) { ?>
				<li class="form-inline">
					<input type="checkbox" id="modlgn_remember" name="remember" value="yes" title="<?php echo JText::_('MOD_EASYDISCUSS_WELCOME_REMEMBER_ME');?>" alt="<?php echo JText::_('MOD_EASYDISCUSS_WELCOME_REMEMBER_ME');?>">
					<label for="modlgn_remember"><?php echo JText::_('MOD_EASYDISCUSS_WELCOME_REMEMBER_ME');?></label>
				</li>
			<?php } ?>
			<li>
				<input type="submit" value="<?php echo JText::_('MOD_EASYDISCUSS_WELCOME_SIGN_IN');?>" name="Submit" class="btn btn-primary">
			</li>
		</ul>

		<div class="account-register">
			<?php echo JText::sprintf('MOD_EASYDISCUSS_WELCOME_FORGOT_PASSOWRD_OR_USERNAME', JRoute::_('index.php?option=com_users&view=reset'), JRoute::_('index.php?option=com_users&view=remind')) ?>
			<br>
			<?php if ($allowRegister) { ?>
				<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>"><?php echo JText::_('MOD_EASYDISCUSS_WELCOME_CREATE_ACCOUNT');?></a>
			<?php } ?>
		</div>
		<?php echo $params->get('posttext'); ?>
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.login" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>
<?php } ?>
</div>
