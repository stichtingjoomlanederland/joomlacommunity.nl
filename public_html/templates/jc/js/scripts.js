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

// Override RSComments to make it compatible with Bootstrap 3
function rscomments_show_report(id) {
    var modal =  jQuery('#rscomments-report');
    var root  = typeof rsc_root != 'undefined' ? rsc_root : '';

    modal.find('.modal-body').load(root + 'index.php?option=com_rscomments&task=report&id=' + id);
    modal.modal();
}