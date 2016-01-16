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

$labelsModel = DiscussHelper::getModel( 'Labels' );
$labels = $labelsModel->getLabels();

if( !isset( $post->label ) )
{
	$post->getLabel();
}
?>
<script type="text/javascript">
EasyDiscuss.ready(function($){
	$( '.post-labels-list' ).click( function() {
		var labelId = $(this).data('labelid');
		var postId = $(this).data('postid');

		EasyDiscuss.ajax( "site.views.post.ajaxSaveLabel",
			{
				"labelId": labelId,
				"postId": postId
			},
			{
				success: function(message) {
					$( '.discuss-post-label' ).html(message);
				},
				fail: function(message) {
					$( '.discuss-post-label' ).html(message);
				}
			});
	});
});
</script>

<div class="dropdown_" style="display:inline-block">
	<?php echo JText::_('COM_EASYDISCUSS_LABELS_LABEL'); ?>
	: <a href="#" class="btn btn-mini" data-foundry-toggle="dropdown">
		<i class="icon-plus-sign"></i>
		<?php if( !$post->label->id ){ ?>
		<?php echo JText::_( 'COM_EASYDISCUSS_LABELS_UNASSIGNED' ); ?>
		<?php } else { ?>
			<?php echo $this->escape( $post->label->getTitle() ); ?>
		<?php } ?>
	</a>
	<ul class="dropdown-menu" role="menu" aria-labelledby="labels-dropdown">
		<?php foreach ($labels as $label) { ?>
			<li><a href="javascript:void(0);" class="post-labels-list" data-labelid="<?php echo $label->id; ?>" data-postid="<?php echo $post->id; ?>"><?php echo $this->escape( $label->title ); ?></a></li>
		<?php } ?>
	</ul>
</div>
