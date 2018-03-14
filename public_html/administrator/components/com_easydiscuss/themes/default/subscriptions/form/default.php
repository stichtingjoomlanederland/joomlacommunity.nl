<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" data-ed-form>
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SUBSCRIPTION_SETTINGS'); ?>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SUBSCRIPTION_TYPE'); ?>
							</div>
							<div class="col-md-7">
								<select name="type" class="form-control" data-subscription-type>
									<option value="site" <?php echo $subscription->type == 'site' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIPTION_TYPE_SITE');?></option>
									<option value="category" <?php echo $subscription->type == 'category' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIPTION_TYPE_CATEGORY');?></option>
									<option value="post" <?php echo $subscription->type == 'post' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIPTION_TYPE_POST');?></option>
								</select>
							</div>
						</div>

						<div class="form-group <?php echo $subscription->type != 'category' ? 'hide' : '';?>" data-subscriptions="category">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SUBSCRIPTION_SELECT_CATEGORY'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.categories', 'cid_category', $subscription->type == 'category' ? $subscription->cid : null); ?>
							</div>
						</div>

						<div class="form-group <?php echo $subscription->type != 'post' ? 'hide' : '';?>" data-subscriptions="post">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SUBSCRIPTION_SELECT_POST'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.posts', 'cid_post', $subscription->type == 'post' ? $subscription->cid : null); ?>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SUBSCRIPTION_NAME'); ?>
							</div>
							<div class="col-md-7">
								<input type="text" class="form-control" id="fullname" name="fullname" size="55" maxlength="255" value="<?php echo $subscription->fullname;?>" />
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SUBSCRIPTION_EMAIL'); ?>
							</div>
							<div class="col-md-7">
								<input type="text" class="form-control" id="email" name="email" size="55" maxlength="255" value="<?php echo $subscription->email;?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-6">
		</div>
	</div>
	<input type="hidden" name="id" value="<?php echo $subscription->id;?>" />
	<?php echo $this->html('form.hidden', 'subscription'); ?>
</form>
