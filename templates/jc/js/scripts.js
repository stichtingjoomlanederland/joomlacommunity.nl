jQuery( document ).ready(function( $ ) {

    // Expand button for touch devices
    var toggleSub = $('.toggle-sub');

    if( toggleSub.length ) {
        toggleSub.on('click', function(){
            $(this).toggleClass('active');
            expandSubmenu( $(this).closest('li') );
            collapseSiblings( $(this).closest('li').siblings() );
        });
    }
    function expandSubmenu(el) {
        el.toggleClass('expand');
    }
    function collapseSiblings(siblings) {
        siblings.removeClass('expand');
        siblings.find('.toggle-sub').removeClass('active');
    }
});