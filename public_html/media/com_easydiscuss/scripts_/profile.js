EasyDiscuss.module('profile', function($) {

	var module = this;

	EasyDiscuss.require()
	.done(function() {
		EasyDiscuss.Controller(
			'Profile',
			{
				defaultOptions:
				{
					'userid'	: null,
					'defaultTab'	: null,

					'{tabs}'	: '.profileTab',
					'{tabContents}'	: '.tabContents',
					'{loader}'	: '.loader'
				}
			},
			function(self)
			{
				return {

					init: function()
					{
						// Get the current user's id from the element.
						self.options.userid	= self.element.data('id');

						// Initialize tabs.
						self.initializeTabs();
					},

					initializeTabs: function()
					{
						// Find default tab.
						var defaultTab = self.options.defaultTab;

						// Check if there's an anchor already.
						var anchor	= $.uri(window.location).anchor();

						if (anchor)
						{
							defaultTab = anchor.charAt(0).toUpperCase() + anchor.slice(1);
						}


						// Set the default click
						self.tabs('.tab' + defaultTab).click();
					},

					loadTabContents: function(currentTab ) {


						EasyDiscuss.ajax('site.views.profile.tab' ,
							{
								'type'	: currentTab,
								'id' : self.options.userid
							} ,
							{
								beforeSend: function()
								{
									self.tabContents('#' + currentTab).addClass('tab-pane-loading');
								},
								success: function(contents , pagination )
								{
									var html = contents;

									if (pagination != null)
									{
										html += pagination;
									}

									self.tabContents('#' + currentTab).removeClass('tab-pane-loading');

									self.tabContents('#' + currentTab).html(html);
								},
								fail: function()
								{
								}
							});
					},

					'{tabs} click' : function(element )
					{
						var elementId = element.data('id'),
							tabContent = self.tabContents('#' + elementId);

						// Fix conflict with com_profile's tabpane.js
						tabContent
							.removeClass('dynamic-tab-pane-control')
							.find('.tab-row')
							.remove();

						var length = tabContent.children().length;

						if (length <= 0)
						{
							self.loadTabContents(element.data('id'));
						}
					}
				};
			}
		);

		module.resolve();
	});
});
