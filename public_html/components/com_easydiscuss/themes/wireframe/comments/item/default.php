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
<div class="ed-comment-item" data-ed-comment-item data-id="<?php echo $comment->id;?>" data-is-new="<?php echo $isNew ? 1 : 0; ?>">

	<div class="lg:t-d--flex t-align-items--xc sm:t-flex-direction--c">
		<div class="t-flex-grow--1 t-min-width--0 lg:t-pr--lg sm:t-mb--md">
			<div class="o-media o-media--top">
				<div class="o-media__image">
					<?php echo $this->html('user.avatar', $comment->getAuthor()); ?>
				</div>

				<div class="o-media__body">
					<a name="comments-<?php echo $comment->id;?>"></a>
					
					<div>
						<ol class="g-list-inline g-list-inline--dashed t-font-size--01">
							<li>
								<?php echo $this->html('user.username', $comment->getAuthor(), ['popbox' => true]); ?>
							</li>
							
							<li>
								<?php echo $comment->getDuration(); ?>
							</li>

							<?php if ($this->config->get('main_comment_permalink')) { ?>
							<li>
								<a class="si-link" href="<?php echo $comment->getPermalink();?>">#<?php echo $comment->id;?></a>
							</li>
							<?php } ?>
						</ol>
					</div>

					<div class="t-mt--md">
						<div data-ed-comment-content><?php echo $comment->getMessage();?></div>

						<div data-ed-comment-edit-form></div>
					</div>
				</div>
			</div>
		</div>

		<div class="lg:t-ml--auto">
			<?php if ($comment->canEdit() || $comment->canConvert() || $comment->canDelete()) { ?>
			<div class="ed-entry-actions">
				<div class="ed-entry-actions-toolbar" role="toolbar">
					<div class="ed-entry-actions-group o-dropdown" role="group">
						<button id="btnGroupDrop1" type="button" class="o-btn dropdown-toggle_" data-ed-toggle="dropdown">
							<i class="fas fa-ellipsis-h"></i>
						</button>
						<ul class="o-dropdown-menu o-dropdown-menu--right t-mt--2xs" data-ed-comment-item-action aria-labelledby="btnGroupDrop1">
							<?php if ($comment->canEdit()) { ?>
								<li>
									<a href="javascript:void(0);" class="o-dropdown__item" data-ed-comment-edit>
										<?php echo JText::_('COM_ED_EDIT');?>
									</a>
								</li>
							<?php } ?>

							<?php if ($comment->canConvert()) { ?>
								<li>
									<a href="javascript:void(0);" class="o-dropdown__item" data-comment-convert-link>
										<?php echo JText::_('COM_EASYDISCUSS_CONVERT_THIS_COMMENT_TO_REPLY'); ?>
									</a>
								</li>
							<?php } ?>

							<?php if ($comment->canDelete()) { ?>
								<li>
									<a href="javascript:void(0);" class="o-dropdown__item" data-ed-comments-delete>
										<?php echo JText::_('COM_EASYDISCUSS_COMMENTS_REMOVE', true); ?>
									</a>
								</li>
							<?php } ?>
						</ul>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>



