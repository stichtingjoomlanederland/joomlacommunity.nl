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
Komento.require()
.library( 'dialog' )
.done( function($){

	$( '.preview-mail' ).each(function(){
		var id	= $(this).data( 'id' );

		$( this ).bind( 'click' , function(){

			var content	= '';

			Komento.ajax( 'admin.views.mailq.preview' ,
			{
				'id'	: id
			},
			{
				success: function( html, type ) {
					if(type == 'text') {
						html = '<p>' + html.replace(/\n/g, '<br />') + '</p>';
					}

					$.dialog( {
						title: '<?php echo JText::_( 'COM_KOMENTO_MAIL_PREVIEW' , true );?>',
						content: html,
						width: 600,
						height: 350
					});
				}
			});
		});
	});
});
</script>
<form action="index.php?option=com_komento&view=mailq" method="post" name="adminForm" id="adminForm">
<?php echo $this->loadTemplate('mailq'); ?>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_komento" />
<input type="hidden" name="view" value="mailq" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="c" value="mailq" />
</form>
