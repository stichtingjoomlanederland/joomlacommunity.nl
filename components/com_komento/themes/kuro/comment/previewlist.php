<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

if( $system->my->allow( 'read_comment' ) && !empty( $comments ) ) { ?>
<div id="section-kmt" class="theme-<?php echo $system->config->get( 'layout_theme' ); ?> kmt-preview">
	<script type="text/javascript">
	Komento.require()
	.library('dialog')
	.script('komento.common', 'komento.commentlist')
	.done(function($){
		var commentList = $('.commentList-<?php echo $cid; ?>');
		if(commentList.exists()) {
			commentList.implement('Komento.Controller.CommentList');
		}
	});
	</script>

	<h3 class="kmt-preview-title">
		<?php if( $system->config->get( 'preview_sort' ) == 'popular' ) {
			echo JText::_( 'COM_KOMENTO_PREVIEW_POPULAR_COMMENTS_TITLE' );
		} else {
			echo JText::_( 'COM_KOMENTO_PREVIEW_RECENT_COMMENTS_TITLE' );
		} ?>
	</h3>

	<div class="commentList kmt-list-wrap commentList-<?php echo $cid; ?>">
		<?php echo $this->fetch( 'comment/preview/comments.php' ); ?>
	</div>

	<a class="kmt-btn-viewmore" href="<?php echo $componentHelper->getContentPermalink() . '#section-kmt'; ?>"><?php echo JText::_( 'COM_KOMENTO_PREVIEW_COMMENTS_VIEW_OTHER_COMMENTS' ); ?></a>
</div>
<?php }
