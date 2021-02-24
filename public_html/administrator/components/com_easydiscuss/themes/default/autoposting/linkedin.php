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
		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_AUTOPOST_LINKEDIN_APP'); ?>

				<div class="panel-body">
					<div class="o-form-horizontal">
						<?php echo $this->html('settings.toggle', 'main_autopost_linkedin', 'COM_EASYDISCUSS_LINKEDIN_AUTOPOST_ENABLE'); ?>

						<div class="o-form-group" data-linkedin-api>
							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_ED_FB_AUTOPOST_OAUTH_REDIRECT_URI'); ?>
							</div>

							<div class="col-md-7">
								<p>Effective <b>May 18th, 2018</b>, LinkedIn has <a href="https://developer.linkedin.com/docs/guide/v2/compliance/release-notes#2018-04-11" target="_blank">announced</a> that V1 Company Pages API will no longer work with OAuth 1.0. It is recommended for all apps to switchover OAuth 2.0 authentication before then. You will need to copy the links below and add it under the Authorized Redirect URLs section of the LinkedIn app.</p>
							<?php
								$i = 1;
								foreach ($oauthURIs as $oauthURI) { ?>
								<div class="o-input-group t-mb--sm">
									<input type="text" id="oauth-uri-<?php echo $i?>" data-oauthuri-input name="integrations_linkedin_oauth_redirect_uri" class="o-form-control" value="<?php echo $oauthURI;?>" size="60" style="pointer-events:none;" />
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

						<?php echo $this->html('settings.textbox', 'main_autopost_linkedin_id', 'COM_EASYDISCUSS_AUTOPOST_LINKEDIN_CLIENT_ID', '', array(), '<a href="https://stackideas.com/docs/easydiscuss/administrators/autoposting/linkedin-application" target="_blank">' . JText::_('COM_EASYDISCUSS_WHAT_IS_THIS') . '</a>'); ?>
						<?php echo $this->html('settings.textbox', 'main_autopost_linkedin_secret', 'COM_EASYDISCUSS_AUTOPOST_LINKEDIN_CLIENT_SECRET', '', array(), '<a href="https://stackideas.com/docs/easydiscuss/administrators/autoposting/linkedin-application" target="_blank">' . JText::_('COM_EASYDISCUSS_WHAT_IS_THIS') . '</a>'); ?>

						<div class="o-form-group">

							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_AUTOPOST_LINKEDIN_SIGN_IN'); ?>
							</div>

							<div class="col-md-7">
								<?php if ($associated) { ?>
								<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&controller=autoposting&task=revoke&type=linkedin');?>" class="o-btn o-btn--danger">
									<?php echo JText::_('COM_EASYDISCUSS_AUTOPOST_REVOKE_ACCCESS');?>
								</a>
								<?php } else { ?>
								<a href="javascript:void(0)" class="o-btn o-btn--linkedin" data-linkedin-login>
									<i class="fab fa-linkedin"></i>&nbsp; Sign in with Linkedin
								</a>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_LINKEDIN_AUTOPOST_COMPANIES'); ?>

				<div class="panel-body">
					<?php if ($associated) { ?>
					<div class="o-form-horizontal">
						<div class="o-form-group">
							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_LINKEDIN_COMPANIES'); ?>
							</div>

							<div class="col-md-7 o-form-label">
								<?php if ($companies) { ?>
								<select name="main_autopost_linkedin_company_id[]" class="o-form-select" multiple="multiple" size="10">
									<?php foreach ($companies as $company) { ?>
									<option value="<?php echo $company->id;?>" <?php echo in_array($company->id, $storedCompanies) ? ' selected="selected"' : '';?>>
										<?php echo $company->title;?>
									</option>
									<?php } ?>
								</select>

								<p class="t-mt--sm"><?php echo JText::_('COM_EASYDISCUSS_AUTOPOST_SELECT_MULTIPLE'); ?></p>
								<?php } else { ?>
									<p><?php echo JText::sprintf('COM_ED_AUTOPOSTING_LINKEDIN_COMPANIES_UNAVAILABLE_REVIEW_REQUIRED', '<a href="https://stackideas.com/docs/easyblog/administrators/autoposting/linkedin-autoposting" target="_blank">', '</a>');?></p>
								<?php } ?>
							</div>
						</div>
					</div>
					<?php } else { ?>
						<p><?php echo JText::_('COM_EASYDISCUSS_LINKEDIN_AUTOPOST_SIGNIN_FIRST');?></p>
					<?php } ?>
				</div>
			</div>

			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_LINKEDIN_AUTOPOST_GENERAL'); ?>

				<div class="panel-body">
					<div class="o-form-horizontal">
						<?php echo $this->html('settings.textarea', 'main_autopost_linkedin_message', 'COM_EASYDISCUSS_LINKEDIN_AUTOPOST_POST_MESSAGE', '', array(), 'COM_EASYDISCUSS_LINKEDIN_AUTOPOST_POST_MESSAGE_FOOTNOTE'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<?php echo $this->html('form.action', 'autoposting', 'save'); ?>
	<input type="hidden" name="type" value="linkedin" />
</form>
