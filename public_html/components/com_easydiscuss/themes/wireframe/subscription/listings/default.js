ed.require(['edq'], function($) {

var tabs = $('[data-ed-tab]');
var loaded = [];

/**
 * Tabs API
 */
$('[data-ed-toggle=tab]').on('click', function() {
	var element = $(this);
	var parent = element.parent();
	var filter = element.data('filter');
	var url = element.data('url');

	// Update the url
	history.pushState({}, document.title, url);

	// Set active tab
	tabs.removeClass('active');
	parent.addClass('active');

	var tabContent = $('#' + filter);
	var tabContentList = tabContent.find('[data-ed-list]');
	var hasContents = tabContentList.children().length > 0;

	if (hasContents) {
		return;
	}

	tabContent.addClass('is-loading');

	EasyDiscuss.ajax('site/views/subscription/renderTab', {
		type: filter == 'categories' ? 'category' : filter
	})
	.done(function(contents, pagination) {

		tabContent.removeClass('is-loading');

		if ($.trim(contents).length <= 0) {
			tabContent.addClass('is-empty');
		}

		tabContent.removeClass('is-loading');
		tabContentList.html(contents);
	});
});


/**
 * Sitewide subscription on/off 
 */
this.toggleSiteSubscription = function(checked) {

	var text = '<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_SITE_IS_ACTIVE');?>';

	if (!checked) {
		text = '<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_SITE_IS_INACTIVE');?>';
	}

	$('[data-ed-site-message]').text(text);

	EasyDiscuss.ajax('site/views/subscription/toggleSiteSubscription', {
	}).done(function(contents){
		return;
	})
};

$(document).on('click.site.subscription', '[data-ed-subscribe-action]', function() {
	var element = $(this);
	var id = element.data('id');
	var wrapper = element.parents('[data-site-subscription]');

	// This determines if the user has subscribed before or is a new subscription altogether
	var subscribed = wrapper.data('subscribed');
	var hasSubscribed = subscribed === 1 || subscribed === 0 ? true : false;

	// If the user has subscribed previously, just toggle the settings.
	if (hasSubscribed) {
		toggleSiteSubscription(element.is(':checked'));
		return;
	}

	// If the user haven't subscribe yet, display the subscribe dialog.            
	EasyDiscuss.dialog({
		content: EasyDiscuss.ajax('site/views/subscription/form',{
			'cid' : 0,
			'type' : 'site'
		})
	});
});


/**
 * Subscription settings (interval, limit and sorting)
 */
$(document).on('click.subscription.settings', '[data-settings]', function() {
	var element = $(this);
	var value = element.data('settings');

	var wrapper = element.parents('[data-subscription-settings]');
	var method = wrapper.data('method');
	var preview = wrapper.find('[data-preview]');

	var subscriptionType = element.parents('[data-subscription-settings-wrapper]');
	var subscriptionId = subscriptionType.data('id');

	wrapper.find('[data-settings]').removeClass('active');
	element.addClass('active');

	var text = element.find('> a').text();
	preview.text(text);

	// Updte the subscription settings
	EasyDiscuss.ajax('site/views/subscription/' + method, {
		id: subscriptionId,
		data: value
	});

});


});