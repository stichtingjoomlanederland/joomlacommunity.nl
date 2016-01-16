Komento.module('komento.profile', function($) {
var module = this;

Komento.require()
	.script('komento.common')
	.done(function() {
		Komento.Controller(
			'Profile', {
				defaults: {
					uid: 0,
					'{navComments}': '.navComments',
					'{navActivities}': '.navActivities',
					'{navPopular}': '.navPopular',
					'{navSticked}': '.navSticked',
					'{navs}': '.navs',
					'{tabs}': '.tabs',
					'{commentsTab}': '#kmt-comments',
					'{activitiesTab}': '#kmt-activities',
					'{popularTab}': '#kmt-popular',
					'{stickedTab}': '#kmt-sticked',
					'{loadMore}': '.loadMore'
				}
			},
			function(self)
			{ return {
				init: function() {
					// self.element.tabs();

					// self.navActivities().trigger('click');
					self.navs(':eq(0)').trigger('click');

					self.loaded = {};
					self.total = {};
				},

				'{navs} click': function(el) {
					var funcName = $(el).attr('func');
					var tabName = $(el).attr('tab');

					self.tabs().hide();
					$('#' + tabName).show();

					self.navs().removeClass('active');
					el.addClass('active');

					self[funcName] && self[funcName]();
				},

				'{loadMore} click': function(el) {
					if(el.checkClick()) {
						var tmp = el.parents('.tabs').attr('id').substring(4);
						var funcName = 'loadMore' + tmp.charAt(0).toUpperCase() + tmp.slice(1);
						self[funcName]();
					}
				},

				loadComments: function() {
					if(!self.commentsTab().attr('loaded'))
					{
						self.commentsTab().html('<img src="' + Komento.options.spinner + '" />' + $.language('COM_KOMENTO_LOADING'));
						Komento.ajax('site.views.profile.getcomments', {
							uid: self.options.uid
						},
						{
							success: function(html, count, total) {
								self.commentsTab().html(html);
								self.commentsTab().attr('loaded', true);

								self.loaded.comments = parseInt(count);
								self.total.comments = parseInt(total);
							},

							fail: function() {

							}
						});

					}
				},

				loadMoreComments: function() {
					Komento.ajax('site.views.profile.getcomments', {
						loadMore: 1,
						uid: self.options.uid,
						start: self.loaded.comments
					},
					{
						success: function(html, count, total) {
							self.commentsTab().children('ul').append(html);
							self.loaded.comments += parseInt(count);

							if(self.loaded.comments >= self.total.comments) {
								self.hideLoadMore('comments');
							}

							self.loadMore()
								.enable();
						},

						fail: function() {
						}
					});
				},

				loadActivities: function() {
					if(!self.activitiesTab().attr('loaded'))
					{
						self.activitiesTab().html('<img src="' + Komento.options.spinner + '" />' + $.language('COM_KOMENTO_LOADING'));
						Komento.ajax('site.views.profile.getactivities', {
							uid: self.options.uid
						},
						{
							success: function(html, count, total) {
								self.activitiesTab().html(html);
								self.activitiesTab().attr('loaded', true);

								self.loaded.activities = parseInt(count);
								self.total.activities = parseInt(total);
							},

							fail: function() {
							}
						});
					}
				},

				loadMoreActivities: function() {
					Komento.ajax('site.views.profile.getactivities', {
						loadMore: 1,
						uid: self.options.uid,
						start: self.loaded.activities
					},
					{
						success: function(html, count, total) {
							self.activitiesTab().children('ul').append(html);
							self.loaded.activities += parseInt(count);

							if(self.loaded.activities >= self.total.activities) {
								self.hideLoadMore('activities');
							}

							self.loadMore()
								.enable();
						},

						fail: function() {
						}
					});
				},

				loadPopular: function() {
					if(!self.popularTab().attr('loaded'))
					{
						self.popularTab().html('<img src="' + Komento.options.spinner + '" />' + $.language('COM_KOMENTO_LOADING'));

						Komento.ajax('site.views.profile.getpopularcomments', {
							uid: self.options.uid
						},
						{
							success: function(html, count, total) {
								self.popularTab().html(html);
								self.popularTab().attr('loaded', true);

								self.loaded.popular = parseInt(count);
								self.total.popular = parseInt(total);
							},

							fail: function() {
							}
						});
					}
				},

				loadMorePopular: function() {
					Komento.ajax('site.views.profile.getpopularcomments', {
						loadMore: 1,
						uid: self.options.uid,
						start: self.loaded.popular
					},
					{
						success: function(html, count, total) {
							self.popularTab().children('ul').append(html);
							self.loaded.popular += parseInt(count);

							if(self.loaded.popular >= self.total.popular) {
								self.hideLoadMore('popular');
							}

							self.loadMore()
								.enable();
						},

						fail: function() {

						}
					});
				},

				loadSticked: function() {
					if(!self.stickedTab().attr('loaded'))
					{
						self.stickedTab().html('<img src="' + Komento.options.spinner + '" />' + $.language('COM_KOMENTO_LOADING'));

						Komento.ajax('site.views.profile.getstickedcomments', {
							uid: self.options.uid
						},
						{
							success: function(html, count, total) {
								self.stickedTab().html(html);
								self.stickedTab().attr('loaded', true);

								self.loaded.sticked = parseInt(count);
								self.total.sticked = parseInt(total);
							},

							fail: function() {
							}
						});
					}
				},

				loadMoreSticked: function() {
					Komento.ajax('site.views.profile.getstickedcomments', {
						loadMore: 1,
						uid: self.options.uid,
						start: self.loaded.sticked
					},
					{
						success: function(html, count, total) {
							self.stickedTab().children('ul').append(html);
							self.loaded.sticked += parseInt(count);

							if(self.loaded.sticked >= self.total.sticked) {
								self.hideLoadMore('sticked');
							}

							self.loadMore()
								.enable();
						},

						fail: function() {
						}
					});
				},

				hideLoadMore: function(tab) {
					$('#kmt-' + tab).find('.loadMore').remove();
				}
			} }
		);

		module.resolve();
	});
});
