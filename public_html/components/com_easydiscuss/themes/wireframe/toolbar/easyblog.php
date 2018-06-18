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
			<div class="ed-toolbar-dropdown-nav__name"><?php echo JText::_('COM_ED_BLOG');?></div>

			<ol class="g-list-unstyled ed-toolbar-dropdown-nav__meta-lists">
				<li>
					<a href="<?php echo EBR::_('index.php?option=com_easyblog');?>"><?php echo JText::_('COM_ED_EB_RECENT_POSTS');?></a>
				</li>

				<?php if ($config->get('layout_bloggers')) { ?>
				<li>
					<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=blogger');?>"><?php echo JText::_('COM_EASYBLOG_TOOLBAR_BLOGGERS');?></a>
				</li>
				<?php } ?>

				<?php if ($config->get('layout_categories')) { ?>
				<li>
					<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=categories');?>"><?php echo JText::_('COM_EASYBLOG_TOOLBAR_CATEGORIES');?></a>
				</li>
				<?php } ?>

				<?php if ($config->get('layout_tags')) { ?>
				<li>
					<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=tags');?>"><?php echo JText::_('COM_EASYBLOG_TOOLBAR_TAGS');?></a>
				</li>
				<?php } ?>

				<?php if ($config->get('layout_archives')) { ?>
				<li>
					<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=archive');?>"><?php echo JText::_('COM_EASYBLOG_TOOLBAR_ARCHIVES');?></a>
				</li>
				<?php } ?>

				<?php if ($config->get('main_favourite_post')) { ?>
				<li>
					<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=favourites');?>"><?php echo JText::_('COM_EB_FAVOURITE_POSTS');?></a>
				</li>
				<?php } ?>
			</ol>
		</span>
	</div>

	<?php if ($showManage) { ?>
	<div class="ed-toolbar-dropdown-nav__item">
		<span class="ed-toolbar-dropdown-nav__link">
			<div class="ed-toolbar-dropdown-nav__name"><?php echo JText::_('COM_ED_EB_MANAGE_BLOG');?></div>
			
			<ol class="g-list-unstyled ed-toolbar-dropdown-nav__meta-lists">
				<?php if ($acl->get('add_entry')) { ?>
				<li>
					<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=entries&filter=drafts');?>">
						<?php echo JText::_('COM_EB_TOOLBAR_DRAFTS');?>
					</a>
				</li>
				<li>
					<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=entries');?>">
						<?php echo JText::_('COM_EASYBLOG_TOOLBAR_MANAGE_POSTS');?>
					</a>
				</li>
				<?php } ?>

				<?php if ($acl->get('create_post_templates')) { ?>
				<li>
					<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=templates');?>">
						<?php echo JText::_('COM_EASYBLOG_DASHBOARD_HEADING_POST_TEMPLATES');?>
					</a>
				</li>
				<?php } ?>

				<?php if (EB::isSiteAdmin() || ($acl->get('moderate_entry') || ($acl->get('manage_pending') && $acl->get('publish_entry')))) { ?>
				<li>
					<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=moderate');?>">
						<?php echo JText::_('COM_EASYBLOG_TOOLBAR_MANAGE_PENDING');?>
					</a>
				</li>
				<?php } ?>

				<?php if ($acl->get('create_category')) { ?>
				<li>
					<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=categories');?>">
						<?php echo JText::_('COM_EASYBLOG_TOOLBAR_MANAGE_CATEGORIES');?>
					</a>
				</li>
				<?php } ?>

				<?php if ($acl->get('create_tag')) { ?>
				<li>
					<a href="<?php echo EB::_('index.php?option=com_easyblog&view=dashboard&layout=tags');?>">
						<?php echo JText::_('COM_EASYBLOG_TOOLBAR_MANAGE_TAGS');?>
					</a>
				</li>
				<?php } ?>
			</ol>
		</span>
	</div>
	<?php } ?>
</div>