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
<script type="text/javascript">
EasyDiscuss.ready(function($)
{
	$( '[data-show-child]' ).on( 'click' , function()
	{
		var parentId	= $( this ).data( 'id' ),
			childs 		= $( '[data-ed-parent-id="' + parentId + '"]' );

		// Display all child items
		$( childs ).show();

		// Replace the icon now.
		$( this ).parent().addClass( 'expanded' );
	});

	$( '[data-hide-child]' ).on( 'click' , function()
	{
		var parentId	= $( this ).data( 'id' ),
			childs 		= $( '[data-ed-parent-id="' + parentId + '"]' );

		// Display all child items
		$( childs ).hide();

		// Replace the icon now.
		$( this ).parent().removeClass( 'expanded' );
	});
});
</script>

<div class="discuss-mod discuss-mod-categories discuss-categories<?php echo $params->get( 'moduleclass_sfx' ) ?>">
	<?php if( $categories ){ ?>
	<ul class="toggleModuleCategories unstyled">
		<?php echo modEasydiscussCategoriesHelper::accessNestedCategories( $categories , $selected , $params ); ?>
	</ul>
	<?php } else { ?>
	<div class="no-item">
		<?php echo JText::_('MOD_DISCUSSIONSCATEGORIES_NO_ENTRIES'); ?>
	</div>
	<?php } ?>
</div>
