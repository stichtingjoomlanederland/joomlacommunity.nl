<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form name="adminForm" id="adminForm" action="index.php" method="post" class="adminForm">
	<div class="row">
		<div class="col-lg-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_AUTOPOST_FACEBOOK_APP_SETTINGS'); ?>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_FB_ENABLE_AUTOPOST'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.boolean', 'main_autopost_facebook', $this->config->get('main_autopost_facebook')); ?>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_FB_AUTOPOST_APP_ID'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.textbox', 'main_autopost_facebook_id', $this->config->get('main_autopost_facebook_id')); ?>
								<div class="small">
									<a href="https://stackideas.com/docs/easydiscuss/administrators/autoposting/facebook-application" target="_blank"><?php echo JText::_('COM_EASYDISCUSS_WHAT_IS_THIS');?></a>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_FB_AUTOPOST_APP_SECRET'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.textbox', 'main_autopost_facebook_secret', $this->config->get('main_autopost_facebook_secret')); ?>

								<div class="small">
									<a href="https://stackideas.com/docs/easydiscuss/administrators/autoposting/facebook-application" target="_blank"><?php echo JText::_('COM_EASYDISCUSS_WHAT_IS_THIS');?></a>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_FB_AUTOPOST_SIGN_IN'); ?>
							</div>
							<div class="col-md-7">
								<?php if ($associated) { ?>
								<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&controller=autoposting&task=revoke&type=facebook');?>" class="btn btn-danger">
									<?php echo JText::_('COM_EASYDISCUSS_FB_AUTOPOST_REVOKE_ACCESS');?>
								</a>
								<?php } else { ?>
								<a href="javascript:void(0);" data-facebook-login>
									<img src="<?php echo JURI::root();?>media/com_easydiscuss/images/facebook_signon.png" />
								</a>

								<div class="small mt-10">
									<?php echo JText::_('COM_EASYDISCUSS_FB_AUTOPOST_SIGN_IN_FOOTNOTE'); ?>
								</div>
								<?php } ?>
							</div>
						</div>

					</div>
				</div>
			</div>

			<?php if ($associated) { ?>
			<div class="panel">

				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_AUTOPOST_FACEBOOK_PAGES'); ?>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_AUTOPOST_PAGES'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.boolean', 'main_autopost_facebook_page', $this->config->get('main_autopost_facebook_page')); ?>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_INTEGRATIONS_FACEBOOK_SELECT_PAGE'); ?>
							</div>
							<div class="col-md-7">

								<?php if ($pages) { ?>
								<select name="main_autopost_facebook_page_id[]" class="form-control" multiple="multiple" size="10">
									<?php foreach ($pages as $page) { ?>
									<option value="<?php echo $page->id;?>" <?php echo in_array($page->id, $storedPages) ? ' selected="selected"' : '';?>>
										<?php echo $page->name;?>
									</option>
									<?php } ?>
								</select>

								<p class="mt-5 small"><?php echo JText::_('COM_EASYDISCUSS_AUTOPOST_SELECT_MULTIPLE'); ?></p>
								<?php } else { ?>
									<p><?php echo JText::_('COM_EASYDISCUSS_FB_AUTOPOST_NO_PAGES_YET'); ?></p>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_AUTOPOST_FACEBOOK_GROUPS'); ?>
				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_AUTOPOST_GROUPS'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.boolean', 'main_autopost_facebook_group', $this->config->get('main_autopost_facebook_group')); ?>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_INTEGRATIONS_FACEBOOK_SELECT_GROUPS'); ?>
							</div>
							<div class="col-md-7">
								<?php if ($groups) { ?>
								<select name="main_autopost_facebook_group_id[]" class="form-control" multiple="multiple" size="10">
									<?php foreach ($groups as $group) { ?>
									<option value="<?php echo $group->id;?>" <?php echo in_array($group->id, $storedGroups) ? ' selected="selected"' : '';?>>
										<?php echo $group->name;?>
									</option>
									<?php } ?>
								</select>

								<p class="mt-5 small"><?php echo JText::_('COM_EASYDISCUSS_AUTOPOST_SELECT_MULTIPLE'); ?></p>
								<?php } else { ?>
									<p><?php echo JText::_('COM_EASYDISCUSS_FB_AUTOPOST_NO_GROUPS_YET'); ?></p>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>

		</div>

		<div class="col-lg-6">

			<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_AUTOPOST_FACEBOOK_AUTOPOST_SETTINGS'); ?>
				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_AUTOPOST_FACEBOOK_MAX_CONTENT_LENGTH'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.textbox', 'main_autopost_facebook_max_content', $this->config->get('main_autopost_facebook_max_content', 200), '', 'form-control-sm text-center'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>	
	</div>

	<input type="hidden" name="step" value="completed" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="layout" value="facebook" />
	<input type="hidden" name="controller" value="autoposting" />
	<input type="hidden" name="option" value="com_easydiscuss" />
</form>
