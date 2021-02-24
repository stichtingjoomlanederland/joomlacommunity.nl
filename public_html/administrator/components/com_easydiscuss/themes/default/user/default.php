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
<form name="adminForm" id="adminForm" action="index.php" method="post" enctype="multipart/form-data">
<div class="wrapper accordion">
	<div class="tab-box tab-box-alt">
		<div class="tabbable">
			<ul class="nav nav-tabs nav-tabs-icons">
				<li class="tabItem <?php echo $active == 'account' ? ' active' : '';?>">
					<a href="#account" data-ed-toggle="tab">
						<?php echo JText::_('COM_EASYDISCUSS_USER_TAB_ACCOUNT');?>
					</a>
				</li>
				<li class="tabItem <?php echo $active == 'badges' ? ' active' : '';?>">
					<a href="#badges" data-ed-toggle="tab">
						<?php echo JText::_('COM_EASYDISCUSS_USER_TAB_BADGES');?>
					</a>
				</li>
				
				<li class="tabItem <?php echo $active == 'history' ? ' active' : '';?>">
					<a href="#history" data-ed-toggle="tab">
						<?php echo JText::_('COM_EASYDISCUSS_USER_TAB_HISTORY');?>
					</a>
				</li>
			</ul>

			<div class="tab-content">
				<?php echo $this->output('admin/user/account'); ?>

				<?php echo $this->output('admin/user/badges'); ?>
				
				<?php echo $this->output('admin/user/history'); ?>
			</div>
		</div>
	</div>

	<?php echo $this->html('form.action', 'user', 'user', 'save'); ?>
	<input type="hidden" name="id" value="<?php echo $user->id;?>" />
</div>
</form>
