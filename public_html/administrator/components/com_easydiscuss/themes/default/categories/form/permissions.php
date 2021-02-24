<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="permissions" class="tab-pane <?php echo $active == 'permissions' ? 'active in' : '';?>">
	<div class="panel">
		<div class="panel-head t-p--no">
			<div class="o-form-check t-mb--no">
				<input id="cat-inherit-acl" class="o-form-check-input" type="checkbox" name="global_acl" value="1" <?php echo $category->global_acl ? 'checked="checked"' : '';?> data-category-inherit-acl />
				<label for="cat-inherit-acl" class="o-form-check-label">
					<?php echo JText::sprintf('COM_ED_CATEGORY_ACL_INHERIT_GLOBAL_CHECKBOX', 'index.php?option=com_easydiscuss&view=settings&layout=categories');?>
				</label>
			</div>
		</div>
	</div>

	<div class="tab-box tab-box--sidenav <?php echo $category->global_acl ? 't-hidden' : '';?>" data-category-acl-wrapper>
		<div class="tabbable t-d--flex t-w--100">
			<div class="tab-box__sidenav">
				<ul class="o-tabs o-tabs--stacked" data-behavior="sample_code">
					<li class="o-tabs__item active">
						<a href="#cat-view" data-ed-toggle="tab" class="o-tabs__link">
							<?php echo JText::_('COM_ED_CATEGORY_ACL_VIEW_POSTS'); ?>
						</a>
					</li>
					<li class="o-tabs__item">
						<a href="#cat-select" data-ed-toggle="tab" class="o-tabs__link">
							<?php echo JText::_('COM_ED_CATEGORY_ACL_CREATE_POSTS'); ?>
						</a>
					</li>
					<li class="o-tabs__item">
						<a href="#cat-viewreply" data-ed-toggle="tab" class="o-tabs__link">
							<?php echo JText::_('COM_ED_CATEGORY_ACL_VIEW_REPLIES'); ?>
						</a>
					</li>
					<li class="o-tabs__item">
						<a href="#cat-reply" data-ed-toggle="tab" class="o-tabs__link">
							<?php echo JText::_('COM_ED_CATEGORY_ACL_ADD_REPLY'); ?>
						</a>
					</li>
					<li class="o-tabs__item">
						<a href="#cat-comment" data-ed-toggle="tab" class="o-tabs__link">
							<?php echo JText::_('COM_ED_CATEGORY_ACL_ADD_COMMENT'); ?>
						</a>
					</li>
					<li class="o-tabs__item">
						<a href="#cat-moderate" data-ed-toggle="tab" class="o-tabs__link" data-cat-moderate>
							<?php echo JText::_('COM_ED_CATEGORY_ACL_MODERATE_POSTS'); ?>
						</a>
					</li>
					<li class="o-tabs__item">
						<a href="#cat-uploadattachment" data-ed-toggle="tab" class="o-tabs__link">
							<?php echo JText::_('COM_ED_CATEGORY_ACL_UPLOAD_ATTACHMENT'); ?>
						</a>
					</li>
				</ul>
			</div>

			<div class="tab-content tab-content--side">
				<div id="cat-view" class="tab-pane active">
					<div class="panel">
						<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CATEGORIES_ACL_VIEW'); ?>

						<div class="panel-body" data-permissions-container>
							<?php echo $this->output('admin/categories/form/selector'); ?>

							<?php echo $this->html('form.usergroups', 'acl_group_view', $category->getAssignedGroups('view'));?>
						</div>
					</div>
				</div>
				<div id="cat-select" class="tab-pane">
					<div class="panel">
						<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CATEGORIES_ACL_SELECT'); ?>

						<div class="panel-body" data-permissions-container>
							<?php echo $this->output('admin/categories/form/selector'); ?>

							<?php echo $this->html('form.usergroups', 'acl_group_select', $category->getAssignedGroups('select'));?>
						</div>
					</div>
				</div>
				<div id="cat-viewreply" class="tab-pane">
					<div class="panel">
						<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CATEGORIES_ACL_VIEWREPLY'); ?>

						<div class="panel-body" data-permissions-container>
							<?php echo $this->output('admin/categories/form/selector'); ?>

							<?php echo $this->html('form.usergroups', 'acl_group_viewreply', $category->getAssignedGroups('viewreply'));?>
						</div>
					</div>
				</div>
				<div id="cat-reply" class="tab-pane">
					<div class="panel">
						<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CATEGORIES_ACL_REPLY'); ?>

						<div class="panel-body" data-permissions-container>
							<?php echo $this->output('admin/categories/form/selector'); ?>
							<?php echo $this->html('form.usergroups', 'acl_group_reply', $category->getAssignedGroups('reply'));?>
						</div>
					</div>
				</div>
				<div id="cat-comment" class="tab-pane">
					<div class="panel">
						<?php echo $this->html('panel.head', 'COM_ED_CATEGORIES_ACL_COMMENT'); ?>

						<div class="panel-body" data-permissions-container>
							<?php echo $this->output('admin/categories/form/selector'); ?>
							<?php echo $this->html('form.usergroups', 'acl_group_comment', $category->getAssignedGroups('comment'));?>
						</div>
					</div>
				</div>
				<div id="cat-moderate" class="tab-pane">
					<div class="panel">
						<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CATEGORIES_ACL_MODERATE'); ?>

						<div class="panel-body" data-permissions-container>
							<?php echo $this->output('admin/categories/form/selector'); ?>
							<?php echo $this->html('form.usergroups', 'acl_group_moderate', $category->getAssignedGroups('moderate'));?>
						</div>
					</div>
				</div>
				<div id="cat-uploadattachment" class="tab-pane">
					<div class="panel">
						<?php echo $this->html('panel.head', 'COM_ED_CATEGORIES_ACL_UPLOADATTACHMENT'); ?>

						<div class="panel-body" data-permissions-container>
							<?php echo $this->output('admin/categories/form/selector'); ?>
							<?php echo $this->html('form.usergroups', 'acl_group_uploadattachment', $category->getAssignedGroups('uploadattachment'));?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


</div>
