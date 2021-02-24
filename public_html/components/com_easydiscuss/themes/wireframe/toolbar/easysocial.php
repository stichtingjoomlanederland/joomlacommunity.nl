<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-toolbar-dropdown-nav">
	<div class="ed-toolbar-dropdown-nav__item ">
		<a href="" class="ed-toolbar-dropdown-nav__link">
			<div class="ed-toolbar-dropdown-nav__name">
				<b><?php echo JText::_('COM_ED_SOCIAL');?></b>
			</div>
		</a>
	</div>
	<?php if ($esConfig->get('pages.enabled')) { ?>
	<div class="ed-toolbar-dropdown-nav__item ">
		<a href="<?php echo ESR::pages();?>" class="ed-toolbar-dropdown-nav__link">
			<div class="ed-toolbar-dropdown-nav__name">
				<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_PAGES'); ?>
			</div>
		</a>
	</div>
	<?php } ?>

	<?php if ($esConfig->get('groups.enabled')) { ?>
		<div class="ed-toolbar-dropdown-nav__item ">
			<a href="<?php echo ESR::groups();?>" class="ed-toolbar-dropdown-nav__link">
				<div class="ed-toolbar-dropdown-nav__name">
					<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_GROUPS'); ?>
				</div>
			</a>
		</div>
	<?php } ?>

	<?php if ($esConfig->get('events.enabled')) { ?>
	<div class="ed-toolbar-dropdown-nav__item ">
		<a href="<?php echo ESR::events();?>" class="ed-toolbar-dropdown-nav__link">
			<div class="ed-toolbar-dropdown-nav__name">
				<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_EVENTS'); ?>
			</div>
		</a>
	</div>
	<?php } ?>

	<?php if ($esConfig->get('friends.enabled')) { ?>
	<div class="ed-toolbar-dropdown-nav__item ">
		<a href="<?php echo ESR::friends();?>" class="ed-toolbar-dropdown-nav__link">
			<div class="ed-toolbar-dropdown-nav__name">
				<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_FRIENDS'); ?>
			</div>
		</a>
	</div>
	<?php } ?>

	<?php if ($esConfig->get('friends.invites.enabled')) { ?>
	<div class="ed-toolbar-dropdown-nav__item ">
		<a href="<?php echo ESR::friends(array('layout' => 'invite'));?>" class="ed-toolbar-dropdown-nav__link">
			<div class="ed-toolbar-dropdown-nav__name">
				<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_INVITE_FRIENDS'); ?>
			</div>
		</a>
	</div>
	<?php } ?>

	<?php if ($esConfig->get('followers.enabled')) { ?>
	<div class="ed-toolbar-dropdown-nav__item ">
		<a href="<?php echo ESR::followers();?>" class="ed-toolbar-dropdown-nav__link">
			<div class="ed-toolbar-dropdown-nav__name">
				<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_FOLLOWERS'); ?>
			</div>
		</a>
	</div>
	<?php } ?>

	<?php if ($esConfig->get('video.enabled')) { ?>
	<div class="ed-toolbar-dropdown-nav__item ">
		<a href="<?php echo ESR::videos();?>" class="ed-toolbar-dropdown-nav__link">
			<div class="ed-toolbar-dropdown-nav__name">
				<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_VIDEOS'); ?>
			</div>
		</a>
	</div>
	<?php } ?>

	<?php if ($esConfig->get('audio.enabled')) { ?>
	<div class="ed-toolbar-dropdown-nav__item ">
		<a href="<?php echo ESR::audios();?>" class="ed-toolbar-dropdown-nav__link">
			<div class="ed-toolbar-dropdown-nav__name">
				<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_AUDIOS'); ?>
			</div>
		</a>
	</div>
	<?php } ?>

	<?php if ($esConfig->get('photos.enabled')) { ?>
	<div class="ed-toolbar-dropdown-nav__item ">
		<a href="<?php echo ESR::photos();?>" class="ed-toolbar-dropdown-nav__link">
			<div class="ed-toolbar-dropdown-nav__name">
				<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_PHOTOS'); ?>
			</div>
		</a>
	</div>
	<?php } ?>

	<div class="ed-toolbar-dropdown-nav__item ">
		<a href="<?php echo ESR::users();?>" class="ed-toolbar-dropdown-nav__link">
			<div class="ed-toolbar-dropdown-nav__name">
				<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PEOPLE'); ?>
			</div>
		</a>
	</div>

	<?php if ($esConfig->get('polls.enabled')) { ?>
	<div class="ed-toolbar-dropdown-nav__item ">
		<a href="<?php echo ESR::polls();?>" class="ed-toolbar-dropdown-nav__link">
			<div class="ed-toolbar-dropdown-nav__name">
				<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_POLLS'); ?>
			</div>
		</a>
	</div>
	<?php } ?>

	<div class="ed-toolbar-dropdown-nav__item ">
		<a href="<?php echo ESR::conversations();?>" class="ed-toolbar-dropdown-nav__link">
			<div class="ed-toolbar-dropdown-nav__name">
				<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_CONVERSATIONS'); ?>
			</div>
		</a>
	</div>
</div>