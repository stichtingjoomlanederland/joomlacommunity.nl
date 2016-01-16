<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.modal' , 'a.modal' );
?>
<script type="text/javascript">
EasyDiscuss.ready(function($){


	$( '.badgeList' ).on( 'click' , '.removeBadge' , function(){

		if( confirm( '<?php echo JText::_( 'COM_EASYDISCUSS_CONFIRM_REMOVE_BADGE' , true );?>' ) )
		{
			var badgeId 	= $( this ).data( 'id' ),
				element 	= $( this ).parents( 'li' );

			EasyDiscuss.ajax( 'admin.views.user.deleteBadge' , {
				"id"		: badgeId,
				"userId"	: <?php echo $this->profile->id; ?>
			})
			.done( function(){

				$( element ).remove();

				if( $('.badgeList' ).children().length == 1 )
				{
					$( '.badgeList .emptyList' ).show();
				}
			})
			.fail( function( message ){
				console.log( message );
			});
		}
	});

	window.saveMessage = function( id ){
		disjax.load( 'user' , 'saveMessage' , id , $( '#customMessage' ).val() );
	}

	$( '.badgeList' ).on( 'click' , '.addCustomMessage' , function(){

		var id = $( this ).data( 'id' );
		disjax.load( 'user' , 'customMessage' , id.toString() );
	});

	window.insertBadge = function( id ){

		EasyDiscuss.ajax( 'admin.views.user.insertBadge' , {
			"id"	: id,
			"userId": <?php echo $this->profile->id; ?>,
		})
		.done( function( html ){

			// Close the modal window.
			$.Joomla("squeezebox").close();

			// Hide any empty list.
			$( '.emptyList' ).hide();

			// Append html code on the page.
			$( '.badgeList' ).append( html );
		})
		.fail( function( message ){
			console.log( message );
		});
	};

});
</script>
<div class="row-fluid ">
	<div class="span12">
		<h3>
			<?php echo JText::_( 'COM_EASYDISCUSS_USER_BADGES' ); ?>
			<a class="modal btn btn-success btn-medium pull-right addBadge" rel="{handler: 'iframe', size: {x: 500, y: 500}}" href="<?php echo JURI::root();?>administrator/index.php?option=com_easydiscuss&view=badges&tmpl=component&browse=1&browseFunction=insertBadge&exclude=<?php echo $this->badgeIds;?>">
				<i class="icon-plus-sign"></i> <?php echo JText::_( 'COM_EASYDISCUSS_ASSIGN_BADGE' );?>
			</a>
		</h3>
		<hr />


		<ul class="user-badges unstyled badgeList">
			<?php if( $this->badges ){ ?>
				<?php echo $this->loadTemplate( 'badge_item' ); ?>
			<?php } ?>

			<li class="emptyList" style="display:<?php echo $this->badges ? 'none':'block';?>">
				<img src="<?php echo JURI::root();?>/media/com_easydiscuss/badges/empty.png" width="48" />
				<div class="small"><?php echo JText::_( 'COM_EASYDISCUSS_USER_NO_BADGES_YET' ); ?></div>
			</li>
		</ul>

	</div>
</div>
