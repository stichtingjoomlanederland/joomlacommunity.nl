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
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div class="wrapper accordion">
	<div class="tab-box tab-box-alt">
		<div class="tabbable">
			<ul class="nav nav-tabs nav-tabs-icons">
				<li class="tabItem <?php echo $active == 'general' ? ' active' : '';?>" data-ed-tab data-id="general">
					<a href="#general" data-ed-toggle="tab">
						<?php echo JText::_('COM_EASYDISCUSS_CATEGORY_GENERAL');?>
					</a>
				</li>
				<li class="tabItem <?php echo $active == 'permissions' ? ' active' : '';?>" data-ed-tab data-id="permissions">
					<a href="#permissions" data-ed-toggle="tab">
						<?php echo JText::_('COM_EASYDISCUSS_CATEGORY_PERMISSIONS');?>
					</a>
				</li>
				<li class="tabItem <?php echo $active == 'moderator' ? ' active' : '';?>" data-ed-tab data-id="moderator">
					<a href="#moderator" data-ed-toggle="tab">
						<?php echo JText::_('COM_ED_CATEGORY_MODERATOR_ASSIGNMENT');?>
					</a>
				</li>
			</ul>
				<div class="tab-content">
					<?php echo $this->output('admin/categories/form/general'); ?>

					<?php echo $this->output('admin/categories/form/permissions'); ?>

					<?php echo $this->output('admin/categories/form/moderator'); ?>
				</div>
		</div>
	</div>	

	<?php echo $this->html('form.action', 'category', 'categories', ''); ?>
	<input type="hidden" name="id" value="<?php echo $category->id;?>" />
	<input type="hidden" name="private" value="<?php echo (empty($category->private)) ? DISCUSS_PRIVACY_ACL : $category->private ;?>">
	<input type="hidden" name="active" value="<?php echo $active;?>" data-ed-active-tab />
</div>
</form>
