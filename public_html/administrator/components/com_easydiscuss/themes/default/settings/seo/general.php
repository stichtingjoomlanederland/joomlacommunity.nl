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
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_SEO_ADVANCED'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_sef_unicode', 'COM_EASYDISCUSS_MAIN_SEO_ALLOW_UNICODE_ALIAS'); ?>

					<?php echo $this->html('settings.dropdown', 'main_sef_user', 'COM_EASYDISCUSS_MAIN_SEO_USER_PERMALINK_FORMAT', '', 
						array(
							'default' => 'COM_EASYDISCUSS_MAIN_SEO_DEFAULT',
							'username' => 'COM_EASYDISCUSS_MAIN_SEO_USERNAME',
							'realname' => 'COM_EASYDISCUSS_MAIN_SEO_REALNAME'
						)
					);?>

					<div class="o-form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_MAIN_SEO_ROUTING_BEHAVIOR'); ?>
						</div>
						
						<div class="col-md-7">
							<select name="main_routing" class="o-form-select" data-routing-behavior>
								<option value="currentactive"<?php echo $this->config->get('main_routing') == 'currentactive' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_ROUTING_BEHAVIOR_USE_CURRENT_ACTIVEMENU'); ?></option>
								<option value="auto"<?php echo $this->config->get('main_routing') == 'auto' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_ROUTING_BEHAVIOR_USE_AUTO'); ?></option>
								<option value="menuitem"<?php echo $this->config->get('main_routing') == 'menuitem' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_ROUTING_BEHAVIOR_USE_MENUITEM'); ?></option>
							</select>

							<div class="<?php echo $this->config->get('main_routing') == 'currentactive' ? : 't-hidden';?> t-mt--md form-info" data-routing-info data-type="currentactive">
								<b><?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_ROUTING_BEHAVIOR_USE_CURRENT_ACTIVEMENU'); ?></b>
								<p class="t-mt--sm"><?php echo JText::_('COM_ED_ROUTING_CURRENTACTIVE'); ?></p>
							</div>

							<div class="<?php echo $this->config->get('main_routing') == 'auto' ? : 't-hidden';?> t-mt--md form-info" data-routing-info data-type="auto">
								<b><?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_ROUTING_BEHAVIOR_USE_AUTO'); ?></b>
								<p class="t-mt--sm"><?php echo JText::_('COM_ED_ROUTING_AUTO'); ?></p>
							</div>

							<div class="<?php echo $this->config->get('main_routing') == 'menuitem' ? : 't-hidden';?> t-mt--md form-info" data-routing-info data-type="menuitem">
								<b><?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_ROUTING_BEHAVIOR_USE_MENUITEM'); ?></b>
								<p class="t-mt--sm"><?php echo JText::_('COM_ED_ROUTING_MENUITEM'); ?></p>
								
								<div class="row">
									<div class="col-md-12">
										<?php echo $this->html('form.menus', 'main_routing_itemid', $this->config->get('main_routing_itemid')); ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_ED_AMP_SETTING_TITLE'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_amp', 'COM_ED_AMP_EANBLED_SETTING'); ?>

					<div class="o-form-group">
						<div class="col-md-5 o-form-label">
							<?php echo $this->html('form.label', 'COM_ED_AMP_LOGO'); ?>
						</div>

						<div class="col-md-7">
							<div>
								<div class="ed-img-holder">
									<div class="ed-img-holder__remove" <?php echo ED::hasOverrideLogo('amp') ? '' : 'style="display: none;'; ?>>
										<a href="javascript:void(0);" class="o-btn o-btn--default-o o-btn--sm t-text--danger t-mb--sm" data-amp-logo-restore-default-button>
											<i class="fa fa-times"></i>&nbsp; <?php echo JText::_('COM_EASYDISCUSS_REMOVE'); ?>
										</a>
									</div>
									<img src="<?php echo ED::getLogo('amp'); ?>" width="60" />
								</div>
							</div>
							<div class="t-mt--sm">
								<input type="file" name="amp_logo" class="o-form-control" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_SEO_POST_PERMALINK'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<div class="o-form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_WORKFLOW_SEF_FORMAT'); ?>
						</div>
		
						<div class="col-md-7">
							<select name="main_sef" class="o-form-select" data-routing-post>
								<option value="default"<?php echo $this->config->get('main_sef') == 'default' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_WORKFLOW_SEF_FORMAT_TITLE_TYPE'); ?></option>
								<option value="category"<?php echo $this->config->get('main_sef') == 'category' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_WORKFLOW_SEF_FORMAT_CATEGORY_TYPE'); ?></option>
								<option value="simple"<?php echo $this->config->get('main_sef') == 'simple' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_WORKFLOW_SEF_FORMAT_SIMPLE_TYPE'); ?></option>
							</select>

							<div class="t-mt--sm"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_WORKFLOW_SEF_FORMAT_NOTICE');?></div>

							<div class="t-mt--md form-info">
								<b><?php echo JText::_('COM_ED_EXAMPLE_URL'); ?></b>
								<p class="t-mt--sm">
									<span class="<?php echo $this->config->get('main_sef') == 'default' ? : 't-hidden';?>" data-post-example data-type="default">http://yoursite.com<b>/menu/view/post-permalink</b></span>
									<span class="<?php echo $this->config->get('main_sef') == 'category' ? : 't-hidden';?>" data-post-example data-type="category">http://yoursite.com<b>/menu/category/post-permalink</b></span>
									<span class="<?php echo $this->config->get('main_sef') == 'simple' ? : 't-hidden';?>" data-post-example data-type="simple">http://yoursite.com<b>/menu/post-permalink</b></span>
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>