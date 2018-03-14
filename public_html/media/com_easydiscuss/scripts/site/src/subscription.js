ed.define('site/src/subscription', ['edq', 'easydiscuss', 'selectize'], function($, EasyDiscuss){

	var tabs =  $('[data-subscription-tab]');
	var itemList = $('[data-ed-subscription-item]');

	var pagination = $('[data-ed-subscription-pagination]');
	var subscribeButton = $('[data-ed-subscribe-action]');
	var textToggle = $('[data-ed-site-active]');
	var isSubscribedToggle = $('[data-ed-subscription-action]');
	var userId = $('[data-ed-subscription]').data('id');

	window.initSelectize = function(wrapper) {

		self = $(wrapper);

		var dropdowns = self.find('[data-ed-subscription-settings]');

		dropdowns.each(function() {

			var dropdown = $(this);
			var method = dropdown.data('method');
			var element = dropdown.parents(wrapper);
			var id = element.data('id');

			var selectDiv = dropdown.parents('[data-ed-susbcribe-select]');
			var loader = selectDiv.find('[data-ed-subscribe-select-loading]');

			dropdown.selectize({
				isFocused: true,
				onChange: function(data) {
					loader.show();
					EasyDiscuss.ajax('site/views/subscription/' + method,{
						  'id' : id,
						  'data' : data
						}).done(function(contents){
							return;
						}).always(function(){
							loader.hide();
						});
				}
			});

		});
	};

	window.initSelectize('[data-ed-subscription-settings-site]');
	window.initSelectize('[data-ed-subscription-settings-category]');

	tabs.live('click', function(el) {

		el.preventDefault();

		var self = $(this);

		var loader = $('[data-ed-subscription-loading]');
		var emptyList = $('[data-ed-subscription-empty]');

		filterType = self.data("filterType");

		tabs.removeClass('active');
		self.addClass("active");

		// show loading-bar
		loader.show();

		// hide empty div if there is any
		emptyList.removeClass('is-empty');

		// list.children('li').remove();
		itemList.empty();           

		EasyDiscuss.ajax('site/views/subscription/tab', {
				'type'  : filterType,
				'id' : userId
			}).done(function(contents, paginationHTML){
				if (!contents) {
					emptyList.addClass('is-empty');
					pagination.html('');
				} else {
					itemList.append(contents);
					pagination.html(paginationHTML);

					// Re-initialize the selectize for category
					window.initSelectize('[data-ed-subscription-settings-category]');
				}
			})
			.always(function(){
				loader.hide();
			});
	});

	subscribeButton.live('click', function(){
		var id = subscribeButton.data('id');
		var isSubscribed = subscribeButton.data('is-subscribe');

		// If the user has subscribed previously, just toggle the settings.
		if (isSubscribed === 1 || isSubscribed === 0) {
			subscribeToggle(id);
			return;
		}

		// If the user haven't subscribe yet, display the subscribe dialog.            
		subscribe(id);
	});

	function subscribeToggle(id) {

		var textValue = textToggle.data('id');

		if (textValue == 1) {
			textToggle.text("Site subscription is not active.");
			textToggle.data('id', 0);
			isSubscribedToggle.removeClass("is-subscribe");
		} else {
			textToggle.text("Site subscription is active.");
			textToggle.data('id', 1);
			isSubscribedToggle.addClass("is-subscribe");
		}

		EasyDiscuss.ajax('site.views.subscription.subscribeToggle',{
		  'id' : id
		}).done(function(contents){
			return;
		})
	};

	function subscribe(id) {
		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('site/views/subscription/form',{
				'cid' : 0,
				'type' : 'site'
			})
		})
	};
});