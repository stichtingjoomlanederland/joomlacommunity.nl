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
function appendLog( type , message )
{
	EasyDiscuss.$( '#migrator-' + type + '-log' ).append( '<li>' + message + '</li>');
}

function runMigration( type )
{
	// Hide migration button.
	EasyDiscuss.$( '.migrator-button' ).hide();

	disjax.load( 'migrators' , type );
}

function runMigrationCategory( type , categories )
{
	if( categories === 'done' )
	{
		disjax.load( 'migrators' , type + 'CategoryItem' , current , categories );
		return;
	}

	// Removes the first element
	var current	= categories.shift();

	if( categories.length == 0 && !current )
	{
		return;
	}

	if( categories.length == 0 )
	{
		categories	= 'done';
	}

	disjax.load( 'migrators' , type + 'CategoryItem' , current , categories );
}

function runMigrationItem( type , itemstr )
{

	if( itemstr == 'done' )
	{
		disjax.load( 'migrators' , type + 'PostItem' , 'done' , itemstr );
		return;
	}

	var items 	= itemstr.split( '|' );
	var current	= items.shift();
	var nextstr = items.join( '|' );

	if( items.length == 0 )
	{
		nextstr	= 'done';
	}

	disjax.load( 'migrators' , type + 'PostItem' , current , nextstr );
}


function runMigrationReplies( type )
{
	disjax.load( 'migrators' , type + 'PostReplies' );
}


</script>
<div class="row-fluid">
	<div class="span12 panel-title">
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_KUNENA' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_DESC' );?>
		</p>
		<!-- <a href="javascript:void(0)" class="btn btn-success">Documentation</a> -->
	</div>
</div>
<form name="adminForm" id="adminForm">

	<div class="row-fluid ">
		<div class="span6">
			<div class="widget accordion-group">
				<div class="whead accordion-heading">
					<a href="javascript:void(0);">
					<h6><?php echo JText::_( 'COM_EASYDISCUSS_DETAILS' ); ?></h6>
					<!-- <i class="icon-chevron-down"></i> -->
					</a>
				</div>

				<div id="option01" class="accordion-body collapse in">
					<div class="wbody">
						<fieldset>

							<?php if( $this->kunenaExists() ){ ?>
							<p><?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_DESC' );?></p>
							<ul>
								<li><?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_NOTICE_BACKUP' ); ?></li>
								<li><?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_NOTICE_OFFLINE' ); ?></li>
							</ul>
							<?php } else { ?>
							<p><?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_NOT_INSTALLED' ); ?></p>
							<?php } ?>
							<input type="button" class="btn btn-success migrator-button" onclick="runMigration( 'kunena' );" value="<?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_RUN_MIGRATION_TOOL' );?>" />
						</fieldset>
					</div>
				</div>
			</div>
		</div>
		<div class="span6">
			<div class="widget accordion-group">
				<div class="whead accordion-heading">
					<a href="javascript:void(0);">
					<h6><?php echo JText::_( 'COM_EASYDISCUSS_PROGRESS' ); ?></h6>
					<!-- <i class="icon-chevron-down"></i> -->
					</a>
				</div>

				<div id="option01" class="accordion-body collapse in">
					<div class="wbody">
						<fieldset>
							<ul id="migrator-kunena-log" style="max-height: 170px; overflow-y:scroll;list-style:none;">
							</ul>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</div>

</form>
