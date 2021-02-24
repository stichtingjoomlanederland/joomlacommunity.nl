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
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_AUTOPOST_TWITTER_APP'); ?>

				<div class="panel-body">
					<div class="o-form-horizontal">
						<?php echo $this->html('settings.toggle', 'main_autopost_twitter', 'COM_EASYDISCUSS_TWITTER_AUTOPOST_ENABLE'); ?>

						<div class="o-form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_ED_TWITTER_AUTOPOST_OAUTH_CALLBACK_URL'); ?>
							</div>
							<div class="col-md-7">
								<p>Effective <b>June 12th</b>, Twitter has enforced <a href="https://twittercommunity.com/t/action-required-sign-in-with-twitter-users-must-whitelist-callback-urls/105342" target="_blank">Callback URLs to be whitelisted</a>. You will need to copy the links below and add it under the valid Callback URLs section of the Twitter app.</p>
								<?php 
								$i = 1;
								
								foreach ($oauthURIs as $oauthURI) { ?>
									<div class="o-input-group t-mb--sm">
										<input type="text" id="oauth-uri-<?php echo $i?>" data-oauthuri-input name="main_autopost_twitter_oauth_callback" class="o-form-control" value="<?php echo $oauthURI;?>" size="60" style="pointer-events:none;" />
										<a href="javascript:void(0);" class="o-btn o-btn--default-o"
											data-oauthuri-button
											data-original-title="<?php echo JText::_('COM_ED_COPY_TOOLTIP')?>"
											data-placement="bottom"
											data-ed-provide="tooltip"
										>
											<i class="fa fa-copy"></i>
										</a>
									</div>
								<?php $i++; } ?>
							</div>
						</div>

						<?php echo $this->html('settings.textbox', 'main_autopost_twitter_id', 'COM_EASYDISCUSS_AUTOPOST_TWITTER_CONSUMER_KEY', '', array(), '<a href="https://stackideas.com/docs/easydiscuss/administrators/autoposting/twitter-application" target="_blank">' . JText::_('COM_EASYDISCUSS_WHAT_IS_THIS') . '</a>'); ?>

						<?php echo $this->html('settings.textbox', 'main_autopost_twitter_secret', 'COM_EASYDISCUSS_AUTOPOST_TWITTER_CONSUMER_SECRET', '', array(), '<a href="https://stackideas.com/docs/easydiscuss/administrators/autoposting/twitter-application" target="_blank">' . JText::_('COM_EASYDISCUSS_WHAT_IS_THIS') . '</a>'); ?>

						<div class="o-form-group">

							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_AUTOPOST_TWITTER_SIGN_IN'); ?>
							</div>

							<div class="col-md-7">
								<?php if ($associated) { ?>
								<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&controller=autoposting&task=revoke&type=twitter');?>" class="o-btn o-btn--danger">
									<?php echo JText::_('COM_EASYDISCUSS_AUTOPOST_REVOKE_ACCCESS');?>
								</a>
								<?php } else { ?>
								<a href="javascript:void(0)" class="o-btn o-btn--twitter" data-twitter-login>
									<i class="fab fa-twitter"></i>&nbsp; Sign in with Twitter
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
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_TWITTER_AUTOPOST_GENERAL'); ?>

				<div class="panel-body">
					<div class="o-form-horizontal">
						<?php echo $this->html('settings.textarea', 'main_autopost_twitter_message', 'COM_EASYDISCUSS_TWITTER_AUTOPOST_POST_MESSAGE', '', array(), 'COM_EASYDISCUSS_TWITTER_AUTOPOST_POST_MESSAGE_FOOTNOTE'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo $this->html('form.action', 'autoposting', 'save'); ?>
	<input type="hidden" name="type" value="twitter" />
</form>
