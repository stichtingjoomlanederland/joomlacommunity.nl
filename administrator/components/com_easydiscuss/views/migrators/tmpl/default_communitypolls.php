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

	// Bind migration button.
	$( '.migrateNow' ).bind( 'click' , function(){

		var button 	= $(this);

		// Hide the button.
		button.hide();

		EasyDiscuss.ajax(
			'admin.views.migrators.communitypolls',
			{},
			{
				updateLog: function(message){
					window.updateLog( message );
				}
			})
			.done(function( categories ){

				window.migrateCategory( categories );

			});
	});

	window.migratePolls = function( items ){

		var current = !items ? 'done' : items.shift();

		EasyDiscuss.ajax( 'admin.views.migrators.communitypollsPostItem' , {
			"current"	: current,
			"items"		: items
		},
		{
			updateLog: function(message){
				window.updateLog( message );
			}
		})
		.done( function( items ){

			// If there is nothing else to import
			if( !items )
			{

				window.updateLog( '<?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_MIGRATION_COMPLETED' );?>' );

				$( '.migrateNow' ).show();
			}

			if( items )
			{
				window.migratePolls( items )
			}

		})
		.fail( function($){

		});
	};

	window.migrateCategory = function( items ){

		if( !items )
		{
			// There's no more category to import
			EasyDiscuss.ajax(
				'admin.views.migrators.communitypollsCategoryItem',
				{},
				{
					updateLog: function(message)
					{
						window.updateLog( message );
					},
					migratePolls : function( items )
					{
						window.migratePolls( items );
					}
				})
				.done(function( categories , doneState ){

				})
				.fail(function($){

				});

			return false;
		}

		// Removes the first element
		var current	= items.shift();

		// Migrate category items.
		EasyDiscuss.ajax(
			'admin.views.migrators.communitypollsCategoryItem',
			{
				'current'		: current,
				'categories'	: items
			},
			{
				updateLog: function(message)
				{
					window.updateLog( message );
				}
			})
			.done(function( categories , doneState ){
				window.migrateCategory( categories , doneState );
			})
			.fail(function($){

			});
	};


	window.updateLog = function( message )
	{
		$( '#migrator-communitypolls-log' ).append( '<li>' + message + '</li>' );
	};
});
</script>
<div class="row-fluid">
	<div class="span12 panel-title">
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_COMMUNITY_POLLS' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_COMMUNITY_POLLS_DESC' );?>
		</p>
	</div>
</div>
<form name="adminForm" id="adminForm">

	<div class="row-fluid ">
		<div class="span6">
			<div class="widget accordion-group">
				<div class="whead accordion-heading">
					<a href="javascript:void(0);"><h6><?php echo JText::_( 'COM_EASYDISCUSS_DETAILS' ); ?></h6></a>
				</div>

				<div id="option01" class="accordion-body collapse in">
					<div class="wbody">
						<?php if( $this->installed ){ ?>
						<p><?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_COMMUNITY_POLLS_INFO' );?></p>
						<ul style="list-style-position: inside;">
							<li><?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_NOTICE_BACKUP' ); ?></li>
							<li><?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_NOTICE_OFFLINE' ); ?></li>
						</ul>

						<div class="pull-right">
							<input type="button" class="btn btn-success migrateNow mt-10" value="<?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_RUN_MIGRATION_TOOL' );?>" />
						</div>
						<div class="clearfix"></div>
						<?php } else { ?>
						<p><?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_COMMUNITY_POLLS_NOT_INSTALLED' ); ?></p>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>

		<div class="span6">
			<div class="widget accordion-group">
				<div class="whead accordion-heading">
					<a href="javascript:void(0);"><h6><?php echo JText::_( 'COM_EASYDISCUSS_PROGRESS' ); ?></h6></a>
				</div>

				<div class="accordion-body collapse in">
					<div class="wbody">
						<ul id="migrator-communitypolls-log" style="max-height: 170px; overflow-y:scroll;list-style:none;">
						</ul>
					</div>
				</div>
			</div>
		</div>

	</div>

</form>
