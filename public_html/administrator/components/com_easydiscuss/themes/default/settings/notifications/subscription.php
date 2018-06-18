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
defined('_JEXEC') or die('Restricted access');
?>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SUBSCRIPTION'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_sitesubscription', 'COM_EASYDISCUSS_ENABLE_SITE_SUBSCRIPTION'); ?>
					<?php echo $this->html('settings.toggle', 'main_ed_categorysubscription', 'COM_EASYDISCUSS_ENABLE_CATEGORIES_SUBSCRIPTION'); ?>
					<?php echo $this->html('settings.toggle', 'main_subscription_include_replies', 'COM_EASYDISCUSS_SUBSCRIPTION_INCLUDE_REPLIES'); ?>
					<?php echo $this->html('settings.toggle', 'main_subscription_include_comments', 'COM_EASYDISCUSS_SUBSCRIPTION_INCLUDE_COMMENTS'); ?>
					<?php echo $this->html('settings.toggle', 'main_postsubscription', 'COM_EASYDISCUSS_ENABLE_POST_SUBSCRIPTION'); ?>
					<?php echo $this->html('settings.toggle', 'main_autopostsubscription', 'COM_EASYDISCUSS_ENABLE_AUTO_POST_SUBSCRIPTION'); ?>
					<?php echo $this->html('settings.toggle', 'main_automodpostsubscription', 'COM_EASYDISCUSS_ENABLE_AUTO_MOD_POST_SUBSCRIPTION'); ?>
					<?php echo $this->html('settings.toggle', 'main_allowguestsubscribe', 'COM_EASYDISCUSS_ENABLE_GUEST_SUBSCRIPTION'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_EMAIL_DIGEST'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_email_digest', 'COM_EASYDISCUSS_ENABLE_EMAIL_DIGEST'); ?>

					<div class="form-group <?php echo !$this->config->get('main_email_digest') ? ' hide' : '';?>" data-subscription-interval>
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_ED_EMAIL_DIGEST_INTERVAL_DEFAULT'); ?>
						</div>
						<div class="col-md-7">
							<select name="main_email_digest_interval" class="form-control" >
								<option value="instant" <?php echo $this->config->get('main_email_digest_interval') == 'instant' ? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_ED_SUBSCRIBE_INSTANT'); ?></option>
								<option value="daily" <?php echo $this->config->get('main_email_digest_interval') == 'daily' ? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_ED_SUBSCRIBE_DAILY'); ?></option>
								<option value="weekly" <?php echo $this->config->get('main_email_digest_interval') == 'weekly' ? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_ED_SUBSCRIBE_WEEKLY'); ?></option>
								<option value="monthly" <?php echo $this->config->get('main_email_digest_interval') == 'monthly' ? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_ED_SUBSCRIBE_MONTHLY'); ?></option>
							</select>
						</div>
					</div>

					<?php echo $this->html('settings.toggle', 'main_email_digest_reply', 'COM_EASYDISCUSS_EMAIL_DIGEST_INCLUDE_REPLY'); ?>
					<?php echo $this->html('settings.toggle', 'main_email_digest_comment', 'COM_EASYDISCUSS_EMAIL_DIGEST_INCLUDE_COMMENT'); ?>

				</div>
			</div>
		</div>
	</div>
</div>




