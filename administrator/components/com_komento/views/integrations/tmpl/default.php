<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access'); ?>
<script type="text/javascript">
Komento.ready( function($) {

	$.Joomla("submitbutton", function(task) {

		$('#submenu li').children().each( function(){
			if( $(this).hasClass( 'active' ) )
			{
				$( '#active' ).val( $(this).attr('id') );
			}
		});

		$('dl#subtabs').children().each(function(){
			if( $(this).hasClass( 'open' ) )
			{
				$( '#activechild' ).val( $(this).attr('class').split(" ")[0] );
			}
		});

		$.Joomla("submitform", [task]);
	});

	window.changeComponent = function(component) {
		document.adminForm.target.value = component;
		document.adminForm.submit();
	}
} );

</script>
<?php
// There seems to be some errors when suhosin is configured with the following settings
// which most hosting provider does! :(
//
// suhosin.post.max_vars = 200
// suhosin.request.max_vars = 200
if(in_array('suhosin', get_loaded_extensions()))
{
	$max_post		= @ini_get( 'suhosin.post.max_vars');
	$max_request	= @ini_get( 'suhosin.request.max_vars' );

	if( !empty( $max_post ) && $max_post < 300 || !empty( $max_request ) && $max_request < 300 )
	{
?>
	<div class="error" style="background: #ffcccc;border: 1px solid #cc3333;padding: 5px;">
		<?php echo JText::_( 'COM_KOMENTO_SETTINGS_SUHOSIN_CONFLICTS' );?>
		<?php echo JText::sprintf( 'COM_KOMENTO_SETTINGS_SUHOSIN_CONFLICTS_MAX', $max_post, $max_request );?>
		<?php echo JText::_( 'COM_KOMENTO_SETTINGS_SUHOSIN_RESOLVE_MESSAGE' ); ?>
	</div>
<?php
	}
}
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">

<?php echo $this->loadTemplate( 'integration' ); ?>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="active" id="active" value="" />
<input type="hidden" name="activechild" id="activechild" value="" />
<input type="hidden" name="task" value="change" />
<input type="hidden" name="option" value="com_komento" />
<input type="hidden" name="c" value="integrations" />
<input type="hidden" name="component" value="<?php echo $this->escape($this->component); ?>" />
<input type="hidden" name="target" value="<?php echo $this->escape($this->component); ?>" />
</form>
