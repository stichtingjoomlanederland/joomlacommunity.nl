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

$canAddTag = ( $system->config->get('main_allowcreatetag') && $acl->allowed('add_tag') );

$selectedTags = array();

if( !empty($post->tags) )
{
	foreach ($post->tags as $tag) {
		$selectedTags[] = $tag->title;
	}
}
?>
<script type="text/javascript">

EasyDiscuss.require()
	.library(
		'chosen'
	)
	.done( function($) {

		var checkTagCount = function(n) {
			var tagCount = $(".discuss-tag-list .chzn-choices .search-choice").length;
			$(".total-tags").html(tagCount);
		}

		$('.discuss-tag-select').chosen({
			no_results_text: "<?php echo JText::_('COM_EASYDISCUSS_NO_RESULTS'); ?>",
			max_selected_options: <?php echo $system->config->get('max_tags_allowed'); ?>
		});

		$('.discuss-tag-select').bind("change.chosen", checkTagCount);
		$('.discuss-tag-list').on("keypress.chosen keyup.chosen", ".chzn-choices .search-field input", checkTagCount);

		<?php if ( $canAddTag ) { ?>
		$( '.search-field' ).bind( 'keyup' , function( e ){
			var code = (e.keyCode ? e.keyCode : e.which);
			if( code == 13 )
			{
				var newTag = $('.search-field :input').val();
				$('.discuss-tag-select').append('<option value="'+newTag+'" selected="selected">'+newTag+'</option>');
				$('.discuss-tag-select').trigger('liszt:updated');
			}
		});
		<?php } ?>

	});

</script>

<div class="dc-tag-form">
	<?php if( $system->config->get( 'max_tags_allowed' ) > 0 ) { ?>
	<div class="tag-limit mb-10">
		<span class="total-tags">0</span>/<span class="max-tags"><?php echo $system->config->get( 'max_tags_allowed' ); ?> <?php echo JText::_( 'COM_EASYDISCUSS_NUMBER_TAGS_ALLOWED' ); ?></span>
	</div>
	<?php } ?>
	<div class="write-taglist">
		<div class="discuss-tag-list creation unstyled" >
			<select name="tags[]" data-placeholder="<?php echo JText::_('COM_EASYDISCUSS_ADD_TAG'); ?>" class="discuss-tag-select full-width" multiple>
				<?php echo JHTML::_('select.options', $tags, 'title', 'title', $selectedTags); ?>
			</select>
		</div>
	</div>
</div>
