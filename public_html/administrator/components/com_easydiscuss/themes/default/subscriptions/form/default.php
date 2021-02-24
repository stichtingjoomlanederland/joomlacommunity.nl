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
<form action="index.php" method="post" name="adminForm" id="adminForm" data-ed-form>
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SUBSCRIPTION_SETTINGS'); ?>

				<div class="panel-body">
					<div class="o-form-horizontal">
						<?php echo $this->html('forms.textbox', 'fullname', 'COM_EASYDISCUSS_SUBSCRIPTION_NAME', $subscription->fullname); ?>
						<?php echo $this->html('forms.textbox', 'email', 'COM_EASYDISCUSS_SUBSCRIPTION_EMAIL', $subscription->email); ?>
						
						<?php echo $this->html('forms.dropdown', 'type', 'COM_EASYDISCUSS_SUBSCRIPTION_TYPE', $subscription->type,
							array(
								'site' => 'COM_EASYDISCUSS_SUBSCRIPTION_TYPE_SITE',
								'category' => 'COM_EASYDISCUSS_SUBSCRIPTION_TYPE_CATEGORY',
								'post' => 'COM_EASYDISCUSS_SUBSCRIPTION_TYPE_POST'
							),
							'data-subscription-type'
						); ?>


						<div class="o-form-group <?php echo $subscription->type != 'category' ? 't-hidden' : '';?>" data-subscriptions="category">
							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SUBSCRIPTION_SELECT_CATEGORY'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.categories', 'cid_category', $subscription->type == 'category' ? $subscription->cid : null); ?>
							</div>
						</div>

						<div class="o-form-group <?php echo $subscription->type != 'post' ? 't-hidden' : '';?>" data-subscriptions="post">
							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SUBSCRIPTION_SELECT_POST'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.posts', 'cid_post', $subscription->type == 'post' ? $subscription->cid : null); ?>
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
	<?php echo $this->html('form.action', 'subscription'); ?>
</form>
