Komento.module('admin.database', function($) {
	var module = this;

	Komento
	.require()
	.language(
		'COM_KOMENTO_SETTINGS_DATABASE_POPULATE_DEPTH_STATUS_UPDATING',
		'COM_KOMENTO_SETTINGS_DATABASE_POPULATE_DEPTH_STATUS_COMPLETED',
		'COM_KOMENTO_SETTINGS_DATABASE_POPULATE_DEPTH_STATUS_ERROR',
		'COM_KOMENTO_SETTINGS_DATABASE_FIX_STRUCTURE_STATUS_UPDATING_STAGE1',
		'COM_KOMENTO_SETTINGS_DATABASE_FIX_STRUCTURE_STATUS_UPDATING_STAGE2',
		'COM_KOMENTO_SETTINGS_DATABASE_FIX_STRUCTURE_STATUS_ERROR',
		'COM_KOMENTO_SETTINGS_DATABASE_FIX_STRUCTURE_STATUS_COMPLETED'
	)
	.done(function($) {
		Komento.Controller('Database.DepthMaintenance', {
			defaultOptions: {
				'{start}'			: '.start',
				'{status}'			: '.status',
				'{total}'			: '.total',
				'{count}'			: '.count',

				'{statusWrapper}'	: '.statusWrapper',
				'{totalWrapper}'	: '.totalWrapper',
				'{countWrapper}'	: '.countWrapper'
			}
		}, function(self) {
			return {
				init: function() {
				},

				'{start} click': function(el) {
					if(el.enabled()) {
						self.counter = 0;

						el.disabled(true);

						self.statusWrapper().show();
						self.totalWrapper().show();
						self.countWrapper().show();

						self.status().html( '<img src="' + Komento.options.spinner + '" />' + $.language('COM_KOMENTO_SETTINGS_DATABASE_POPULATE_DEPTH_STATUS_UPDATING'));
						self.totalArticle(0);
						self.countArticle(0);

						Komento.ajax('admin.views.system.getArticleStatistics').done(function(articles) {
							self.totalArticle(articles.length);

							self.articles = articles;
							self.populateDepth();
						}).fail(function() {
							self.status().html($.language('COM_KOMENTO_SETTINGS_DATABASE_POPULATE_DEPTH_STATUS_ERROR'));
						});
					}
				},

				populateDepth: function() {
					var data = self.articles[self.counter];

					if(data === undefined) {
						self.populateComplete();
						return;
					}

					Komento.ajax('admin.views.system.populateDepth', {
						component: data.component,
						cid: data.cid
					}).done(function(count) {
						self.counter++;

						self.countArticle(parseInt(self.countArticle()) + 1);

						self.populateDepth();
					}).fail(function() {
						self.status().html($.language('COM_KOMENTO_SETTINGS_DATABASE_POPULATE_DEPTH_STATUS_ERROR'));
					});
				},

				totalArticle: function(total) {
					return total === undefined? self.total().html() : self.total().html(total);
				},

				countArticle: function(count) {
					return count === undefined ? self.count().html() : self.count().html(count);
				},

				populateComplete: function() {
					self.status().html($.language('COM_KOMENTO_SETTINGS_DATABASE_POPULATE_DEPTH_STATUS_COMPLETED'));
					self.start().enabled(true);
				}
			}
		});

		Komento.Controller('Database.FixStructure', {
			defaultOptions: {
				'{component}'		: '.componentSelection',
				'{article}'			: '.articleSelection',

				'{start}'			: '.start',
				'{fixstatus}'		: '.fixStatus',
				'{total}'			: '.total',
				'{count}'			: '.count',

				'{statusWrapper}'	: '.statusWrapper',
				'{totalWrapper}'	: '.totalWrapper',
				'{countWrapper}'	: '.countWrapper'
			}
		}, function(self) {
			return {
				init: function() {
				},

				'{component} change': function(el) {
					var component = el.val();

					self.article().html(self.createOption('all', '*'));

					if(component !== 'all') {
						Komento.ajax('admin.views.system.getArticles', {
							component: component
						}).done(function(articles) {
							$.each(articles, function(i, article) {
								self.article().append(self.createOption(article, article));
							});
						});
					}
				},

				createOption: function(value, text) {
					var option = $('<option></option>');
					option.attr('value', value);
					option.text(text);

					return option;
				},

				'{start} click': function(el) {
					if(el.enabled()) {
						self.counter = 0;

						el.disabled(true);

						self.statusWrapper().show();
						self.totalWrapper().show();
						self.countWrapper().show();

						self.fixstatus().html( '<img src="' + Komento.options.spinner + '" />' + $.language('COM_KOMENTO_SETTINGS_DATABASE_FIX_STRUCTURE_fixstatus_UPDATING_STAGE1'));
						self.totalArticle(0);
						self.countArticle(0);

						var component = self.component().val(),
							article = self.article().val();

						Komento.ajax('admin.views.system.getArticleStatistics', {
							component: component,
							cid: article
						}).done(function(articles) {
							self.totalArticle(articles.length);

							self.articles = articles;
							self.normalizeStructure();
						}).fail(function() {
							self.fixstatus().html($.language('COM_KOMENTO_SETTINGS_DATABASE_FIX_STRUCTURE_STATUS_ERROR'));
						});
					}
				},

				normalizeStructure: function() {
					var data = self.articles[self.counter];

					if(data === undefined) {
						self.fixstatus().html( '<img src="' + Komento.options.spinner + '" />' + $.language('COM_KOMENTO_SETTINGS_DATABASE_FIX_STRUCTURE_STATUS_UPDATING_STAGE2'));
						self.countArticle(0);
						self.counter = 0;
						self.fixStructure();
						return;
					}

					Komento.ajax('admin.views.system.normalizeStructure', {
						component: data.component,
						cid: data.cid
					}).done(function(count) {
						self.counter++;

						self.countArticle(parseInt(self.countArticle()) + 1);

						self.normalizeStructure();
					})
				},

				fixStructure: function() {
					var data = self.articles[self.counter];

					if(data === undefined) {
						self.fixComplete();
						return;
					}

					Komento.ajax('admin.views.system.fixStructure', {
						component: data.component,
						cid: data.cid
					}).done(function(count) {
						self.counter++;

						self.countArticle(parseInt(self.countArticle()) + 1);

						self.fixStructure();
						self.fixComplete();
					}).fail(function() {
						self.fixstatus().html($.language('COM_KOMENTO_SETTINGS_DATABASE_FIX_STRUCTURE_STATUS_ERROR'));
					});
				},

				totalArticle: function(total) {
					return total === undefined? self.total().html() : self.total().html(total);
				},

				countArticle: function(count) {
					return count === undefined ? self.count().html() : self.count().html(count);
				},

				fixComplete: function() {
					//console.log('a');
					self.fixstatus().html($.language('COM_KOMENTO_SETTINGS_DATABASE_FIX_STRUCTURE_STATUS_COMPLETED'));
					self.start().enabled(true);
				}
			}
		})

		module.resolve();
	});
});
