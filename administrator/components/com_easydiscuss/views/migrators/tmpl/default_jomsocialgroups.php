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

function runMigrationItem( type , items )
{
	var current	= items.shift();

	if( items.length == 0 )
	{
		items	= 'done';
	}

	disjax.load( 'migrators' , type + 'PostItem' , current , items );
}
</script>
<div class="row-fluid">
	<div class="span12 panel-title">
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_JOMSOCIAL_GROUPS' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_JOMSOCIALGROUPS_DESC' );?>
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
							<?php if( $this->jomsocialExists() ){ ?>
							<p><?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_JOMSOCIALGROUPS_DESC' );?></p>
							<ul>
								<li><?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_NOTICE_BACKUP' ); ?></li>
								<li><?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_NOTICE_OFFLINE' ); ?></li>
							</ul>
							<?php } else { ?>
							<p><?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_JOMSOCIAL_NOT_INSTALLED' ); ?></p>
							<?php } ?>
							<input type="button" class="btn btn-success migrator-button" onclick="runMigration( 'jomsocialgroups' );" value="<?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_RUN_MIGRATION_TOOL' );?>" />
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
							<ul id="migrator-jomsocialgroups-log" style="max-height: 170px; overflow-y:scroll;list-style:none;">
							</ul>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</div>


</form>
