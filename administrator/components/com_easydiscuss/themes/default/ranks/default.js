
var COM_EASYDISCUSS_RANKING_DELETE = '<?php echo JText::_('COM_EASYDISCUSS_RANKING_DELETE'); ?>';
var COM_EASYDISCUSS_RANKING_ERR_ENTER_TITLE = '<?php echo JText::_('COM_EASYDISCUSS_RANKING_ERR_ENTER_TITLE'); ?>';
var COM_EASYDISCUSS_RANKING_ERR_ONLY_NUMBER = '<?php echo JText::_('COM_EASYDISCUSS_RANKING_ERR_ONLY_NUMBER'); ?>';
var COM_EASYDISCUSS_RANKING_ERR_GREATER_THAN_ZERO = '<?php echo JText::_('COM_EASYDISCUSS_RANKING_ERR_GREATER_THAN_ZERO'); ?>';
var COM_EASYDISCUSS_RANKING_ERR_END_CANNOT_SMALLER_THAN_START = '<?php echo JText::_('COM_EASYDISCUSS_RANKING_ERR_END_CANNOT_SMALLER_THAN_START'); ?>';
var COM_EASYDISCUSS_RANKING_ERR_CANNOT_HAVE_GAPS = '<?php echo JText::_('COM_EASYDISCUSS_RANKING_ERR_CANNOT_HAVE_GAPS'); ?>';
var COM_EASYDISCUSS_RANKING_ERR_ALL_VALUE_IS_CORRECT = '<?php echo JText::_('COM_EASYDISCUSS_RANKING_ERR_ALL_VALUE_IS_CORRECT'); ?>';

function showDescription( id )
{
	EasyDiscuss.$( '.rule-description' ).hide();
	EasyDiscuss.$( '#rule-' + id ).show();
}

ed.require(['edq'], function($) {

	$.Joomla( 'submitbutton' , function(action){
		if ( action != 'cancel' ) {
			window.location.href = 'index.php?option=com_easydiscuss&view=ranks';
		}
		$.Joomla( 'submitform' , [action] );
	});
});