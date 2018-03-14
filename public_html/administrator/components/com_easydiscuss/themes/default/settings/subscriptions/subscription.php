<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
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

			<div id="option01" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_SITE_SUBSCRIPTION'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_sitesubscription', $this->config->get('main_sitesubscription')); ?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_CATEGORIES_SUBSCRIPTION'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_ed_categorysubscription', $this->config->get('main_ed_categorysubscription')); ?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SUBSCRIPTION_INCLUDE_REPLIES'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_subscription_include_replies', $this->config->get('main_subscription_include_replies')); ?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SUBSCRIPTION_INCLUDE_COMMENTS'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_subscription_include_comments', $this->config->get('main_subscription_include_comments')); ?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_POST_SUBSCRIPTION'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_postsubscription', $this->config->get('main_postsubscription')); ?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_AUTO_POST_SUBSCRIPTION'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_autopostsubscription', $this->config->get('main_autopostsubscription')); ?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_AUTO_MOD_POST_SUBSCRIPTION'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_automodpostsubscription', $this->config->get('main_automodpostsubscription')); ?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_GUEST_SUBSCRIPTION'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_allowguestsubscribe', $this->config->get('main_allowguestsubscribe')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
	<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_EMAIL_DIGEST'); ?>

			<div id="option01" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_EMAIL_DIGEST'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_email_digest', $this->config->get('main_email_digest')); ?>
						</div>
					</div>

					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_EMAIL_DIGEST_INCLUDE_REPLY'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_email_digest_reply', $this->config->get('main_email_digest_reply')); ?>
						</div>
					</div>

					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_EMAIL_DIGEST_INCLUDE_COMMENT'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_email_digest_comment', $this->config->get('main_email_digest_comment')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>




