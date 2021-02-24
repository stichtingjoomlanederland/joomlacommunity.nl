ed.require(['edq'], function($) {


$('[data-ed-profile-tab] [data-ed-toggle="tab"]').on('click', function() {

var element = $(this);

$('[data-ed-profile-tab]').removeClass('active');
element.parent().addClass('active');

});

});
