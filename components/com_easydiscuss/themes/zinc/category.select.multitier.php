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
EasyDiscuss.ready(function($){

	window.eachWidth		= 0;
	window.fullWidth		= 0;
	window.eachWidthHalf	= 0;
	window.position			= 0;

	window.init = function() {
		window.fullWidth		= Math.floor( $( '#discuss-category-tier div.category-wrapper').width() );
		window.eachWidth		= Math.floor( window.fullWidth / 4 );
		window.eachWidthHalf	= Math.floor( window.eachWidth / 2 );

		// set the root selection the width
		$('.category-wrapper ul').width(window.fullWidth);
		$('.category-wrapper ul > li').width(window.eachWidth)+20;
		$('.category-wrapper ul select').css('width', window.eachWidth);

	};

	window.findCategoryChilds = function() {
		var hasChild		= $(this).find( ':selected' ).data( 'haschild' );
		var curTier			= parseInt( $(this).attr('id'), 10);
		var newTier			= parseInt( $( '#discuss-category-tier input[name=current_tier]' ).val(), 10);
		var childSelector	= '#discuss-category-tier .sub-category';

		if( isNaN(curTier) ) {
			curTier = 0;
		}

		var curLi	= $(this).parent().parent();
		var isLast	= $(curLi).is('ul.list-tier li:last-child');

		//clearing the sub-category
		$('#discuss-category-tier li.sub-category').each( function() {
			cId = parseInt( $(this).attr( 'id'), 10);
			if( cId > curTier )
			{
				$(this).remove();
			}
		});

		// reset the element position after clearing up the items.
		window.resetLeft();

		// update category_id
		$( '#discuss-category-tier input[name=category_id]' ).val( this.value );

		if( !hasChild ) {
			var numTier = $( '.category-wrapper ul li' ).length;

			if( numTier <= 4 )
			{
				window.moveLeft();
			}

			return;
		}

		// @task: Perform an ajax call to get the child categories of this item.
		EasyDiscuss.ajax( 'site.views.ask.getcategory' ,
		{
			'id'	: this.value
		},{
			// This get's executed before the ajax calls are made
			beforeSend: function(){
				// Show loader.
				$( '#discuss-category-tier .loader' ).show();

			},
			// This is the success method.
			success: function( categories ){
				$( '#discuss-category-tier .loader' ).hide();

				// If there's no child, just ignore this.
				if( categories.length <= 0 ) {
					// Cleanup all child categories.
					$( childSelector ).remove();
					return;
				}


				var selectItem	= $( '<select size="10" class="sub-category" id="' + newTier + '" style="width: ' + window.eachWidth + 'px">' );

				// After receiving the categories from the server, append the data back to the list
				for( var i = 0; i < categories.length; i++ ) {
					var category	= categories[ i ],
						option		= $( '<option>' )
										.data( 'haschild' , category.hasChild > 0 )
										.attr( 'value' , category.id )
										.append( category.hasChild > 0 ? category.title + '&nbsp;&nbsp;&nbsp;<b style="font-size:13px">&rsaquo;</b>' : category.title  );

					$( selectItem )
						.append( option );
				}

				$( selectItem ).bind( 'change' , findCategoryChilds );

				//add into <li><div> structure
				li	= document.createElement( 'li' );
				div	= document.createElement( 'div' );

				$( selectItem ).appendTo( div );
				$( div ).appendTo( li );
				$( li )
					.attr( 'id' , newTier )
					.attr( 'class' , 'sub-category' )
					.width(window.eachWidth)
					.appendTo( '.category-wrapper > ul' );

				// update the tier
				$( '#discuss-category-tier input[name=current_tier]' ).val( newTier + 1 );

				window.moveLeft();
			},
			// This is the failed method. Anything that fails should end up here.
			fail: function(){

			}
		});
	};

	window.resetLeft = function() {
		var numTier	= $( '.category-wrapper ul li' ).length;
		numTier		= numTier - 4;
		newleft		= window.eachWidthHalf + ( numTier * window.eachWidth);
		$('.category-wrapper ul').css('left', '-' + String( newleft ) + 'px');
	}

	window.moveLeft = function() {

		//check now many tier we have now.
		var numTier	= $( '.category-wrapper ul li' ).length;
		var curLeft	= Math.floor( $('.category-wrapper ul').position().left );

		if( numTier < 4 )
		{
			$('.category-wrapper ul').width(fullWidth);
			$('.category-wrapper ul').css('left', '0px');
		}
		if( numTier == 4 )
		{
			var newwidth	= fullWidth + window.eachWidthHalf;

			$('.category-wrapper ul').width(newwidth);
			$('.category-wrapper ul').css('left', '-' + String( window.eachWidthHalf ) + 'px');
		}
		else if(numTier > 4)
		{
			var newwidth	= numTier * window.eachWidth;
			$('.category-wrapper ul').width(newwidth);
			$('.category-wrapper ul').css('left', '-=' + String(window.eachWidth) + 'px' );
		}

	}

	window.navigate = function( direction ) {

		var newwidth	= Math.floor( $('.category-wrapper ul').width() );
		var viewport	= window.fullWidth;
		var curleft		= Math.floor( $('.category-wrapper ul').position().left );

		var action		= '';
		if( direction == 'lft')
		{
			if( curleft == '0' )
				return;

			if( curleft >= window.eachWidthHalf)
				return;

			action	= '+=';
		}
		else
		{
			var numTier	= $( '.category-wrapper ul li' ).length;
			var curPos	= ((window.eachWidth * numTier) - window.fullWidth) + window.eachWidthHalf;


			if( newwidth <= viewport )
				return;

			if( curleft <= -curPos)
				return;

			action	= '-=';
		}

		// perform the navigation here.
		$('.category-wrapper ul').css('left', action + String(window.eachWidth) );
	}


	window.buildCaregoriesTier = function( id )
	{
		if( id == '')
			return;

		// @task: Perform an ajax call to get the child categories of this item.
		EasyDiscuss.ajax( 'site.views.ask.buildcategorytier' ,
		{
			'id'	: id
		},{
			// This get's executed before the ajax calls are made
			beforeSend: function(){
				// Show loader.
				$( '#discuss-category-tier .loader' ).show();

			},
			// This is the success method.
			success: function( categories ){

				$( '#discuss-category-tier .loader' ).hide();

				// If there's no child, just ignore this.
				if( categories.length <= 0 )
				{
					return;
				}

				var newTier = 1;
				var selectedCat;

				for(var p = 0; p < categories.length; p++)
				{
					var tierid		= categories[ p ].id;
					var tierpid		= categories[ p ].parent_id;
					var tierchilds	= categories[ p ].childs;

					if( categories.length == 1 && tierpid == 0 )
					{
						// if fall into this condition, mean the selection only on the root tier.
						// no futher processing required.

						// only update the root list for the selected option
						$('.category-wrapper select.parent-category').val( tierid );
						continue;
					}
					else
					{
						// lets update the root list the selected option
						if( newTier == 1 )
						{
							$('.category-wrapper select.parent-category').val( tierpid );
						}
					}

					var selectItem	= $( '<select size="10" class="sub-category" id="' + newTier + '" style="width: ' + window.eachWidth + 'px">' );
					// After receiving the categories from the server, append the data back to the list
					for( var i = 0; i < tierchilds.length; i++ )
					{
						var category	= tierchilds[ i ],
							option		= $( '<option>' )
											.data( 'haschild' , category.hasChild > 0 )
											.attr( 'value' , category.id )
											.append( category.hasChild > 0 ? category.title + '&nbsp;&nbsp;&nbsp;<b style="font-size:13px">&rsaquo;</b>' : category.title  );

						if( tierid == category.id )
						{
							$( option ).prop('selected', true);
						}

						$( selectItem )
							.append( option );
					}

					$( selectItem ).bind( 'change' , findCategoryChilds );

					//add into <li><div> structure
					li	= document.createElement( 'li' );
					div	= document.createElement( 'div' );

					$( selectItem ).appendTo( div );
					$( div ).appendTo( li );
					$( li )
						.attr( 'id' , newTier )
						.attr( 'class' , 'sub-category' )
						.width(window.eachWidth)
						.appendTo( '.category-wrapper > ul' );

					newTier++;

					// update the tier
					$( '#discuss-category-tier input[name=current_tier]' ).val( newTier );

					if( p + 1 == categories.length )
					{
						// we know this is the latt item which is also the selected one.
						var hasChild 	= $(selectItem).find( ':selected' ).data( 'haschild' );
						if( hasChild )
						{
							$( selectItem ).trigger('change');
						}
					}

					window.moveLeft();
				}

			},
			// This is the failed method. Anything that fails should end up here.
			fail: function(){

			}
		});


	}

	// initialize the container width calculation.
	window.init();

	//@task: Bind change event on the parent category.
	$( '#discuss-category-tier .parent-category' ).bind( 'change' , findCategoryChilds );

	<?php if( $post->category_id ) { ?>
		// lets build the tier when there is a category_id.
		buildCaregoriesTier('<?php echo $post->category_id; ?>');
	<?php } ?>

});
</script>
<div id="discuss-category-tier" class="discuss-category-tier">

	<div class="row-fluid category-wrapper" style="position:relative;">
		<ul class="discuss-list-tier unstyled" style="left: 0px; position:relative;">
			<li>
				<div>
					<select size="10" class="parent-category">
						<?php foreach( $categories as $category ){ ?>
							<?php if( $category->parent_id == 0 ){ ?>
							<?php $hasChild = $category->getChildCount() ? 'true' : 'false'; ?>
							<?php $rsaquo = $category->getChildCount() ? '&nbsp;&nbsp;&nbsp;<b>&rsaquo;</b>' : ''; ?>
							<option value="<?php echo $category->id;?>" data-haschild="<?php echo $hasChild; ?>"><?php echo $category->title . $rsaquo; ?></option>
							<?php } ?>
						<?php } ?>
					</select>
				</div>
			</li>
		</ul>
	</div>

	<div class="discuss-tier-control tac prel">
		<div class="btn-group">
			<!-- DO NOT move the anchor into two line or else the css will break. !-->
			<a href="javascript:void(0);" onclick="window.navigate('lft');" class="btn btn-large"><i class="icon-chevron-left"></i></a><a href="javascript:void(0);" onclick="window.navigate('rgt');" class="btn btn-large"><i class="icon-chevron-right"></i></a>
			<i class="loader pabs ir" style="display:none;">Loader</i>
			<input type="hidden" name="category_id" value="<?php echo ( $post->category_id ) ? $post->category_id : ''; ?>" />
			<input type="hidden" name="current_tier" value="1" />
		</div>
	</div>

</div>
