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
	<div class="ed-toolbar-dropdown-nav__item">
		<span class="ed-toolbar-dropdown-nav__link">
			<div class="ed-toolbar-dropdown-nav__name"><?php echo JText::_('COM_ED_SOCIAL');?></div>

			<ol class="g-list-unstyled ed-toolbar-dropdown-nav__meta-lists">
				<?php if ($esConfig->get('pages.enabled')) { ?>
				<li>
					<a href="<?php echo ESR::pages();?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_PAGES'); ?>
					</a>
				</li>
				<?php } ?>

				<?php if ($esConfig->get('groups.enabled')) { ?>
				<li>
					<a href="<?php echo ESR::groups();?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_GROUPS'); ?>
					</a>
				</li>
				<?php } ?>

				<?php if ($esConfig->get('events.enabled')) { ?>
				<li>
					<a href="<?php echo ESR::events();?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_EVENTS'); ?>
					</a>
				</li>
				<?php } ?>

				<?php if ($esConfig->get('friends.enabled')) { ?>
				<li>
					<a href="<?php echo ESR::friends();?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_FRIENDS'); ?>
					</a>
				</li>
				<?php } ?>

				<?php if ($esConfig->get('friends.invites.enabled')) { ?>
				<li>
					<a href="<?php echo ESR::friends(array('layout' => 'invite'));?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_INVITE_FRIENDS'); ?>
					</a>
				</li>
				<?php } ?>

				<?php if ($esConfig->get('followers.enabled')) { ?>
				<li>
					<a href="<?php echo ESR::followers();?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_FOLLOWERS'); ?>
					</a>
				</li>
				<?php } ?>

				<?php if ($esConfig->get('video.enabled')) { ?>
				<li>
					<a href="<?php echo ESR::videos();?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_VIDEOS'); ?>
					</a>
				</li>
				<?php } ?>

				<?php if ($esConfig->get('audio.enabled')) { ?>
				<li>
					<a href="<?php echo ESR::audios();?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_AUDIOS'); ?>
					</a>
				</li>
				<?php } ?>

				<?php if ($esConfig->get('photos.enabled')) { ?>
				<li>
					<a href="<?php echo ESR::photos();?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_PHOTOS'); ?>
					</a>
				</li>
				<?php } ?>

				<li>
					<a href="<?php echo ESR::users();?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PEOPLE'); ?>
					</a>
				</li>

				<?php if ($esConfig->get('polls.enabled')) { ?>
				<li>
					<a href="<?php echo ESR::polls();?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_POLLS');?>
					</a>
				</li>
				<?php } ?>

				<li>
					<a href="<?php echo ESR::conversations();?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_CONVERSATIONS');?>
					</a>
				</li>
			</ol>
		</span>
	</div>
</div>