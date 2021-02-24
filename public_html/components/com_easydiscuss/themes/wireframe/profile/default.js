ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

var tabs = $('[data-ed-tab]');
var loaded = [];

$('[data-ed-toggle]').on('click', function() {

var element = $(this);
var parent = element.parent();
var filter = element.data('filter');

// Set active tab
tabs.removeClass('active');
parent.addClass('active');


// Since badges are already preloaded, we do not need to do anything here
if (filter == 'badges' || loaded[filter]) {
	return;
}

var userId = element.parents('[data-profile-item]').data('id');
var tabContent = $('#' + filter);

tabContent.addClass('is-loading');

var namespace = 'site/views/profile/render';

if (filter == 'points') {
	namespace = 'site/views/profile/getPointsHistory';
}

EasyDiscuss.ajax(namespace, {
	filter: filter,
	id: userId
})
.done(function(contents) {

	if ($.trim(contents).length <= 0) {
		tabContent.addClass('is-empty');
	}

	tabContent.removeClass('is-loading');
	tabContent.find('[data-ed-list]').html(contents);

	loaded[filter] = true;
});


});


});
