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
				<b><?php echo JText::_('COM_ED_BLOG');?></b>
			</div>
		</a>
	</div>

	<div class="ed-toolbar-dropdown-nav__item ">
		<a href="<?php echo EBR::_('index.php?option=com_easyblog');?>" class="ed-toolbar-dropdown-nav__link">
			<div class="ed-toolbar-dropdown-nav__name">
				<?php echo JText::_('COM_ED_EB_RECENT_POSTS');?>
			</div>
		</a>
	</div>

	<?php if ($config->get('layout_bloggers')) { ?>
	<div class="ed-toolbar-dropdown-nav__item ">
		<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=blogger');?>" class="ed-toolbar-dropdown-nav__link">
			<div class="ed-toolbar-dropdown-nav__name">
				<?php echo JText::_('COM_EASYBLOG_TOOLBAR_BLOGGERS');?>
			</div>
		</a>
	</div>
	<?php } ?>

	<?php if ($config->get('layout_categories')) { ?>
	<div class="ed-toolbar-dropdown-nav__item ">
		<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=categories');?>" class="ed-toolbar-dropdown-nav__link">
			<div class="ed-toolbar-dropdown-nav__name">
				<?php echo JText::_('COM_EASYBLOG_TOOLBAR_CATEGORIES');?>
			</div>
		</a>
	</div>
	<?php } ?>

	<?php if ($config->get('layout_tags')) { ?>
	<div class="ed-toolbar-dropdown-nav__item ">
		<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=tags');?>" class="ed-toolbar-dropdown-nav__link">
			<div class="ed-toolbar-dropdown-nav__name">
				<?php echo JText::_('COM_EASYBLOG_TOOLBAR_TAGS');?>
			</div>
		</a>
	</div>
	<?php } ?>

	<?php if ($config->get('layout_archives')) { ?>
	<div class="ed-toolbar-dropdown-nav__item ">
		<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=archive');?>" class="ed-toolbar-dropdown-nav__link">
			<div class="ed-toolbar-dropdown-nav__name">
				<?php echo JText::_('COM_EASYBLOG_TOOLBAR_ARCHIVES');?>
			</div>
		</a>
	</div>
	<?php } ?>

	<?php if ($config->get('main_favourite_post')) { ?>
	<div class="ed-toolbar-dropdown-nav__item ">
		<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=favourites');?>" class="ed-toolbar-dropdown-nav__link">
			<div class="ed-toolbar-dropdown-nav__name">
				<?php echo JText::_('COM_EB_FAVOURITE_POSTS');?>
			</div>
		</a>
	</div>
	<?php } ?>

	<?php if ($showManage) { ?>
	<div class="ed-toolbar-dropdown-nav__item">
		<div class="ed-toolbar-dropdown-nav__item t-mt--lg">
			<a href="" class="ed-toolbar-dropdown-nav__link">
				<div class="ed-toolbar-dropdown-nav__name">
					<b><?php echo JText::_('COM_ED_EB_MANAGE_BLOG');?></b>
				</div>
			</a>
		</div>
		
		<?php if ($acl->get('add_entry')) { ?>
		<div class="ed-toolbar-dropdown-nav__item ">
			<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries&filter=drafts');?>" class="ed-toolbar-dropdown-nav__link">
				<div class="ed-toolbar-dropdown-nav__name">
					<?php echo JText::_('COM_EB_TOOLBAR_DRAFTS');?>
				</div>
			</a>
		</div>
		<div class="ed-toolbar-dropdown-nav__item ">
			<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=entries');?>" class="ed-toolbar-dropdown-nav__link">
				<div class="ed-toolbar-dropdown-nav__name">
					<?php echo JText::_('COM_EASYBLOG_TOOLBAR_MANAGE_POSTS');?>
				</div>
			</a>
		</div>
		<?php } ?>

		<?php if ($acl->get('create_post_templates')) { ?>
		<div class="ed-toolbar-dropdown-nav__item ">
			<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=templates');?>" class="ed-toolbar-dropdown-nav__link">
				<div class="ed-toolbar-dropdown-nav__name">
					<?php echo JText::_('COM_EASYBLOG_DASHBOARD_HEADING_POST_TEMPLATES');?>
				</div>
			</a>
		</div>
		<?php } ?>

		<?php if (EB::isSiteAdmin() || ($acl->get('moderate_entry') || ($acl->get('manage_pending') && $acl->get('publish_entry')))) { ?>
		<div class="ed-toolbar-dropdown-nav__item ">
			<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=moderate');?>" class="ed-toolbar-dropdown-nav__link">
				<div class="ed-toolbar-dropdown-nav__name">
					<?php echo JText::_('COM_EASYBLOG_TOOLBAR_MANAGE_PENDING');?>
				</div>
			</a>
		</div>
		<?php } ?>

		<?php if ($acl->get('create_category')) { ?>
		<div class="ed-toolbar-dropdown-nav__item ">
			<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=categories');?>" class="ed-toolbar-dropdown-nav__link">
				<div class="ed-toolbar-dropdown-nav__name">
					<?php echo JText::_('COM_EASYBLOG_TOOLBAR_MANAGE_CATEGORIES');?>
				</div>
			</a>
		</div>
		<?php } ?>

		<?php if ($acl->get('create_tag')) { ?>
		<div class="ed-toolbar-dropdown-nav__item ">
			<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=tags');?>" class="ed-toolbar-dropdown-nav__link">
				<div class="ed-toolbar-dropdown-nav__name">
					<?php echo JText::_('COM_EASYBLOG_TOOLBAR_MANAGE_TAGS');?>
				</div>
			</a>
		</div>
		<?php } ?>
	</div>
	<?php } ?>
</div>