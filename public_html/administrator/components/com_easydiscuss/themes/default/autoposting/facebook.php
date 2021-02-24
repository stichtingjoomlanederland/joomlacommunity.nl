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
<form name="adminForm" id="adminForm" action="index.php" method="post" class="adminForm">
	<div class="row">
		<div class="col-lg-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_AUTOPOST_FACEBOOK_APP_SETTINGS', '', '/docs/easydiscuss/administrators/autoposting/facebook-application'); ?>

				<div class="panel-body">
					<div class="o-form-horizontal">
						<?php echo $this->html('settings.toggle', 'main_autopost_facebook', 'COM_EASYDISCUSS_FB_ENABLE_AUTOPOST'); ?>

						<div class="o-form-group" data-facebook-api>
							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_ED_FB_AUTOPOST_OAUTH_REDIRECT_URI'); ?>
							</div>

							<div class="col-md-7">
								<?php echo JText::_('COM_ED_FB_FACEBOOK_OAUTH_REDIRECT_URI_INFO'); ?>
								<?php 
								$i = 1;
								foreach ($oauthURIs as $oauthURI) { ?>
									<div class="o-input-group">
										<input type="text" id="oauth-uri-<?php echo $i?>" data-oauthuri-input name="main_autopost_facebook_oauth_redirect_uri" class="o-form-control" value="<?php echo $oauthURI;?>" size="60" style="pointer-events:none;" />
										<a href="javascript:void(0);" class="o-btn o-btn--default-o"
											data-oauthuri-button
											data-original-title="<?php echo JText::_('COM_ED_COPY_TOOLTIP')?>"
											data-placement="bottom"
											data-eb-provide="tooltip"
										>
											<i class="fa fa-copy"></i>
										</a>
									</div>
								<?php $i++; } ?>
							</div>
						</div>						

						<?php echo $this->html('settings.textbox', 'main_autopost_facebook_id', 'COM_EASYDISCUSS_FB_AUTOPOST_APP_ID'); ?>
						<?php echo $this->html('settings.textbox', 'main_autopost_facebook_secret', 'COM_EASYDISCUSS_FB_AUTOPOST_APP_SECRET'); ?>

						<div class="o-form-group">
							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_ED_FB_AUTOPOST_SCOPE_PERMISSIONS'); ?>
							</div>
							<div class="col-md-7">
								<?php echo JText::_('COM_ED_FB_AUTOPOST_SCOPE_PERMISSIONS_INFO'); ?>
								<?php echo $this->html('form.scopes', 'main_autopost_facebook_scope_permissions[]', 'main_autopost_facebook_scope_permissions', $selectedScopePermissions); ?>
							</div>
						</div>

						<div class="o-form-group">
							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_FB_AUTOPOST_SIGN_IN'); ?>
							</div>
							<div class="col-md-7">
								<?php if ($associated) { ?>
								<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&controller=autoposting&task=revoke&type=facebook');?>" class="o-btn o-btn--danger">
									<?php echo JText::_('COM_EASYDISCUSS_FB_AUTOPOST_REVOKE_ACCESS');?>
								</a>
								<?php } else { ?>
								<a href="javascript:void(0);" class="o-btn o-btn--fb" data-facebook-login>
									<i class="fab fa-facebook"></i>&nbsp; <?php echo JText::_('Sign in with Facebook'); ?>
								</a>

								<div class="small t-mt--md">
									<?php echo JText::_('COM_EASYDISCUSS_FB_AUTOPOST_SIGN_IN_FOOTNOTE'); ?>
								</div>
								<?php } ?>
							</div>
						</div>

						<?php echo $this->html('settings.textbox', 'main_autopost_facebook_max_content', 'COM_EASYDISCUSS_AUTOPOST_FACEBOOK_MAX_CONTENT_LENGTH', '', array('size' => 5), '', '', 'text-center'); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<?php if ($associated) { ?>
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_AUTOPOST_FACEBOOK_PAGES'); ?>

				<div class="panel-body">
					<div class="o-form-horizontal">
						<?php echo $this->html('settings.toggle', 'main_autopost_facebook_page', 'COM_EASYDISCUSS_ENABLE_AUTOPOST_PAGES'); ?>

						<div class="o-form-group">
							<div class="col-md-5 o-form-label">
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

								<p class="t-mt--sm"><?php echo JText::_('COM_EASYDISCUSS_AUTOPOST_SELECT_MULTIPLE'); ?></p>
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
					<div class="o-form-horizontal">
						<?php echo $this->html('settings.toggle', 'main_autopost_facebook_group', 'COM_EASYDISCUSS_ENABLE_AUTOPOST_GROUPS'); ?>

						<div class="o-form-group">
							<div class="col-md-5 o-form-label">
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
	</div>

	<?php echo $this->html('form.action', 'autoposting', 'autoposting', 'save'); ?>
	<input type="hidden" name="step" value="completed" />
	<input type="hidden" name="type" value="facebook" />
</form>
