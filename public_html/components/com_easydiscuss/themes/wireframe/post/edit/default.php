<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form autocomplete="off" action="<?php echo JRoute::_('index.php');?>" method="post" enctype="multipart/form-data" data-ed-reply-form>

	<div role="alert" class="o-alert o-alert--danger t-mb--md t-d--none" data-ed-composer-alert>
	</div>

	<div class="ed-ask t-lg-mt--xl <?php echo $composer->classname; ?>" data-ed-composer-wrapper>
		<div class="ed-post-reply-form t-lg-mt--xl">
			<div class="ed-ask__bd">
				<div class="ed-reply-form__title t-mb--lg">
					<b><?php echo JText::_('COM_EASYDISCUSS_EDIT_YOUR_RESPONSE'); ?></b>
				</div>

				<?php if ($this->config->get('main_anonymous_posting')) { ?>
					<?php echo $composer->renderAnonymousField($post->anonymous);?>
				<?php } ?>

				<div class="ed-editor ed-editor--<?php echo $composer->getEditorClass();?>" <?php echo $composer->uid;?>>

					<div class="ed-editor-widget t-pt--no">
						<?php echo $composer->renderEditor(); ?>

						<div style="clear: both;"></div>
					</div>

					<?php if ($composer->hasTabs($currentCatId)) { ?>
					<div class="ed-editor-widget t-pt--no">
						<div data-ed-editor-tabs>
							<?php echo $composer->renderTabs($currentCatId); ?>
						</div>
					</div>
					<?php } ?>

					<?php if ($captcha->enabled() && $operation != 'editing') { ?>
						<?php echo $composer->renderCaptcha($captcha); ?>
					<?php } ?>

					<div class="ed-editor__ft">
						<div class="t-ml--auto">
							<a class="si-link t-font-size--02 t-mr--sm" href="<?php echo $cancel;?>">
								<?php echo JText::_('COM_EASYDISCUSS_CANCEL_AND_DISCARD');?>
							</a>
							<button class="o-btn o-btn--primary" type="button" data-ed-submit-button>
								<?php echo JText::_('COM_EASYDISCUSS_BUTTON_UPDATE_REPLY');?>
							</button>
						</div>
					</div>
				</div>

			</div>
		</div>

		<?php echo $this->html('form.action', 'posts', 'posts', 'saveReply'); ?>

		<?php if (!empty($reference) && $referenceId) { ?>
		<input type="hidden" name="reference" value="<?php echo $reference; ?>" />
		<input type="hidden" name="reference_id" value="<?php echo $referenceId; ?>" />
		<?php } ?>

		<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
		<input type="hidden" name="id" id="id" value="<?php echo $post->id; ?>" />
		<input type="hidden" name="parent_id" id="parent_id" value="<?php echo $post->parent_id; ?>" />
	</div>
</form>
