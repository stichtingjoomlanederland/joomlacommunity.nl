<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<div class="fd-cf">
	<h3 class="pull-left"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_COMMENTS_TITLE'); ?></h3>
	<div class="pull-right">
		<a href="<?php echo $feedUrl; ?>" target="_blank"><i class="icon-ed-rss"></i><?php echo JText::_( 'COM_KOMENTO_SUBSCRIBE_RSS' ); ?></a>
	</div>
</div>
<hr />
<?php if( $comments ) { ?>
<ul class="unstyled discuss-comments-listing">
	<?php foreach( $comments as $comment ) { ?>
		<li>
			<div class="discuss-comment-item">
				<div class="discuss-comment-head"> <i class="stream-type"></i>
					<?php echo JText::_( 'COM_KOMENTO_ACTIVITY_COMMENTED_ON' ); ?>
					<a href="<?php echo $comment->pagelink; ?>"><?php echo $comment->contenttitle; ?></a>
				</div>
				<div class="discuss-comment-body">
					<div class="kmt-comment-text">
						<?php echo $comment->comment; ?>
					</div>
				</div>
				<div class="discuss-comment-foot">
					<a href="<?php echo $comment->permalink; ?>"><?php echo $comment->created; ?></a>
				</div>
			</div>
		</li>
	<?php } ?>
</ul>
<?php } else { ?>
<div class="empty">
	<?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_COMMENT_NO_COMMENTS_YET' ); ?>
</div>
<?php } ?>
