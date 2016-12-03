ed.require(['edq'], function($) {

	window.deleteConfirm = function()
	{
		if (confirm('<?php echo JText::_('COM_EASYDISCUSS_BANS_CONFIRM_DELETE');?>'))
		{
			return true;
		}

		return false;
	}

	window.purgeConfirm	= function(){
		if (confirm('<?php echo JText::_('COM_EASYDISCUSS_BANS_CONFIRM_PURGE');?>'))
		{
			return true;
		}

		return false;
	}

	$.Joomla( 'submitbutton' , function(action){

		if( action == 'purge')
		{
			if( !purgeConfirm() )
			{
				return false;
			}
		}

		if( action == 'remove' )
		{
			if( !deleteConfirm() )
			{
				return false;
			}
		}

		$.Joomla( 'submitform' , [action] );

	});

});