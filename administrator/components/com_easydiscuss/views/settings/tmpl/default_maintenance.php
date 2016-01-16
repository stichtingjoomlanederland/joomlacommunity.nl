<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="row-fluid">
	<div class="span12 panel-title">
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_MAINTENANCE_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_MAINTENANCE_DESC' );?>
		</p>
	</div>
</div>
<div class="row-fluid ">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_CLEANUP' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_AUTO_PRUNE_NOTIFICATIONS_ON_CRON' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_AUTO_PRUNE_NOTIFICATIONS_ON_CRON' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_AUTO_PRUNE_NOTIFICATIONS_ON_CRON_DESC'); ?>">
							<?php echo $this->renderCheckbox( 'prune_notifications_cron' , $this->config->get( 'prune_notifications_cron' ) ); ?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_AUTO_PRUNE_NOTIFICATIONS_ON_PAGE_LOAD' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_AUTO_PRUNE_NOTIFICATIONS_ON_PAGE_LOAD' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_AUTO_PRUNE_NOTIFICATIONS_ON_PAGE_LOAD_DESC'); ?>">
							<?php echo $this->renderCheckbox( 'prune_notifications_onload' , $this->config->get( 'prune_notifications_onload' ) ); ?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_AUTO_PRUNE_NOTIFICATIONS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_AUTO_PRUNE_NOTIFICATIONS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_AUTO_PRUNE_NOTIFICATIONS_DESC'); ?>">
							<input type="text" name="notifications_history" class="input-mini" style="text-align:center;" value="<?php echo $this->config->get( 'notifications_history' );?>" /> <span><?php echo JText::_( 'COM_EASYDISCUSS_DAYS' ); ?></span>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_OWNER_FOR_ORPHANED_ITEMS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_OWNER_FOR_ORPHANED_ITEMS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_OWNER_FOR_ORPHANED_ITEMS_DESC'); ?>"
						>
							<input type="text" name="main_orphanitem_ownership" class="input-mini" style="text-align: center;" value="<?php echo $this->config->get('main_orphanitem_ownership', $this->defaultSAId );?>" />
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option03	">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_GUEST' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div id="option03" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_GUEST_USERGROUP' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_GUEST_USERGROUP' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_GUEST_USERGROUP_DESC'); ?>"
						>
							<input type="text" name="guest_usergroup" class="input-mini" style="text-align: center;" value="<?php echo $this->config->get('guest_usergroup', 1 );?>" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="span6">

		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option03	">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_JAVASCRIPT_COMPRESSION' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div id="option03" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SYSTEM_ENVIRONMENT' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SYSTEM_ENVIRONMENT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SYSTEM_ENVIRONMENT_DESC'); ?>"
						>
							<select name="easydiscuss_environment">
								<option value="static"<?php echo $this->config->get( 'easydiscuss_environment' ) == 'static' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYDISCUSS_SYSTEM_ENVIRONMENT_STATIC' ); ?></option>
								<option value="optimized"<?php echo $this->config->get( 'easydiscuss_environment' ) == 'optimized' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYDISCUSS_SYSTEM_ENVIRONMENT_OPTIMIZED' ); ?></option>
								<option value="development"<?php echo $this->config->get( 'easydiscuss_environment' ) == 'development' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYDISCUSS_SYSTEM_ENVIRONMENT_DEVEL' ); ?></option>
							</select>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SYSTEM_JS_COMPRESSION' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SYSTEM_JS_COMPRESSION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SYSTEM_JS_COMPRESSION_DESC'); ?>"
						>
							<select name="easydiscuss_mode">
								<option value="compressed"<?php echo $this->config->get( 'easydiscuss_mode' ) == 'compressed' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYDISCUSS_COMPRESSED' ); ?></option>
								<option value="uncompressed"<?php echo $this->config->get( 'easydiscuss_mode' ) == 'uncompressed' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYDISCUSS_UNCOMPRESSED' ); ?></option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option02">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_MAIL_SPOOL' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div id="option02" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SEND_EMAIL_ON_PAGE_LOAD' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SEND_EMAIL_ON_PAGE_LOAD' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SEND_EMAIL_ON_PAGE_LOAD_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_mailqueueonpageload' , $this->config->get( 'main_mailqueueonpageload' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS_HTML_FORMAT' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS_HTML_FORMAT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_NOTIFICATIONS_HTML_FORMAT_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'notify_html_format' , $this->config->get( 'notify_html_format' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_MAILNUMBER_PERLOAD' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_MAILNUMBER_PERLOAD' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_MAILNUMBER_PERLOAD_DESC'); ?>"
							>
							<input type="text" name="main_mailqueuenumber" style="text-align:center;" class="input-mini" value="<?php echo $this->config->get('main_mailqueuenumber' );?>" />

						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_TRUNCATE_EMAIL_LENGTH' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_TRUNCATE_EMAIL_LENGTH' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_TRUNCATE_EMAIL_LENGTH_DESC'); ?>"
						>
							<input type="text" name="main_notification_max_length" class="input-mini" style="text-align:center;" size="5" value="<?php echo $this->config->get('main_notification_max_length');?>" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

