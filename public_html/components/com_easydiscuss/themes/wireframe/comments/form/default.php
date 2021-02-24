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
<form id="discussCommentForm" data-ed-comment-form-item>
	<div class="ed-comment-form t-px--md">
		<div class="commentFormContainer <?php echo $isEdit ? '' : 't-d--none';?>" data-ed-comment-form>
			<div class="ed-comment-form t-mt--md l-stack l-spaces--sm">
				
				<textarea name="comment" id="comment" cols="30" rows="5" class="o-form-control" data-ed-comment-message></textarea>
				
				<div class="t-d--flex">
					<?php if ($this->config->get('main_tnc_comment') && !$isEdit) { ?>
					<div class="t-flex-grow--1">
						<div class="">
							<div class="o-form-check">
								<input type="checkbox" class="o-form-check-input" name="tnc-<?php echo $post->id;?>" id="tnc-<?php echo $post->id;?>" data-ed-comment-tnc-checkbox <?php echo ED::tnc()->hasAcceptedTnc('comment') ? 'checked="checked"' : '' ?>/>
								<label class="o-form-check-label" for="tnc-<?php echo $post->id;?>">
									<?php echo JText::_('COM_EASYDISCUSS_I_HAVE_READ_AND_AGREED');?> 
									<a href="javascript:void(0);" class="si-link t-text-decoration--underline" data-ed-comment-tnc-link>
										<?php echo JText::_('COM_EASYDISCUSS_TERMS_AND_CONDITIONS');?>
									</a>
								</label>
								<?php echo $this->html('form.honeypot', array('data-comment-hp')); ?>
							</div>
						</div>
					</div>
					<?php } ?>
					<div class="t-ml--auto">
						<?php if ($isEdit) { ?>
						<button type="button" class="o-btn o-btn--default-o o-btn--sm" data-id="<?php echo $comment->id; ?>" data-ed-comment-cancel><?php echo JText::_('COM_ED_CANCEL_COMMENT_BUTTON'); ?></button>
						&nbsp;
						<button type="button" class="o-btn o-btn--primary o-btn--sm" data-id="<?php echo $comment->id; ?>" data-ed-comment-update><?php echo JText::_('COM_ED_UPDATE_COMMENT_BUTTON'); ?></button>
						<?php } else { ?>
						 &nbsp;
						<button type="button" class="o-btn o-btn--primary o-btn--sm" data-ed-comment-submit><?php echo JText::_('COM_EASYDISCUSS_BUTTON_SUBMIT'); ?></button>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>