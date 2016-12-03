function showDescription( id )
{
    EasyDiscuss.$( '.rule-description' ).hide();
    EasyDiscuss.$( '#rule-' + id ).show();
}
EasyDiscuss(function($){
    $.Joomla( 'submitbutton' , function(action){
        $.Joomla( 'submitform' , [action] );
    });
});