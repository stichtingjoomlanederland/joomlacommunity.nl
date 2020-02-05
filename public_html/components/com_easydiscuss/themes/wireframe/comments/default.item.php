<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-comment-item" data-ed-comment-item data-id="<?php echo $comment->id;?>">
	<div class="o-media o-media--top">
		<div class="o-media__image ">
			<?php echo $this->html('user.avatar', $comment->creator); ?>
		</div>

		<div class="o-media__body">
			<div class="ed-comment-item__action">
				<ol class="g-list-inline g-list-inline--dashed">
					<li>
						<a href="<?php echo $comment->creator->getLink();?>">
							<?php echo $comment->creator->getName(); ?>
						</a>
					</li>
					<li>
						<?php echo $comment->duration; ?>
					</li>
					<?php if ($this->config->get('main_comment_permalink')) { ?>
					<li>
						<a href="<?php echo $comment->getPermalink();?>">#<?php echo $comment->id;?></a>
					</li>
					<?php } ?>
				</ol>
			</div>

			<a name="comments-<?php echo $comment->id;?>"></a>

			<div class="ed-comment-item__content t-lg-mt--md t-lg-mb--md">
				<?php echo $comment->comment; ?>
			</div>

			<div class="ed-comment-item__action">
				<ol class="g-list-inline g-list-inline--delimited">
					<?php if ($comment->canConvert()) { ?>
					<li>
						<a href="javascript:void(0);" class="btn btn-default btn-xs" data-comment-convert-link>
							<?php echo JText::_('COM_EASYDISCUSS_CONVERT_THIS_COMMENT_TO_REPLY'); ?>
						</a>
					</li>
					<?php } ?>

					<?php if ($comment->canDeleteComment()) { ?>
					<li>
						<a href="javascript:void(0);" class="btn btn-danger btn-xs" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_COMMENTS_REMOVE', true); ?>" data-ed-comments-delete>
							<i class="fa fa-times"></i>
						</a>
					</li>
					<?php } ?>
				</ol>
			</div>
		</div>
	</div>
</div>



