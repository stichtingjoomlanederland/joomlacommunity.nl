Komento.module('migrator.common', function($) {
var module = this;

Komento.Controller(
	'Migrator.Common',
	{
		defaults: {
			'{categoriesList}': '#category',
			'{componentsList}': '#components',
			'{publishingState}': '#publishingState',
			'{migrateLikes}': '#migrateLikes'
		}
	},
	function(self)
	{ return {
		init: function() {
			self.migrationType = self.element.attr('migration-type');
			self.migratorType = self.element.attr('migrator-type');
			self.progress = self.element.find('.migratorProgress');
			self.actions = self.element.parents('.tab');
		},

		getStatistic: function() {
			var selected;
			var key;

			switch(self.migrationType) {
				case 'component':
					key = 'components';
					selected = self.componentsList().val();
					break;
				case 'article':
					key = 'categories';
					selected = self.categoriesList().val();
					break;
				case 'custom':
					key = 'data';
					selected = $('.migrator-custom-data').controller().getData();
			}

			var params = {};
			params[key] = selected;

			Komento.ajax('admin.views.migrators.getmigrator', {
				"type": self.migratorType,
				"function": 'getstatistic',
				"params": params
			}, {
				success: function(cids, totalComments) {
					self.progress.controller().setTotalPosts(cids.length);
					self.progress.controller().setTotalComments(totalComments);

					if(totalComments > 0) {
						if(self.migratorType == 'custom') {
							self.migrateCustom(params, 0, totalComments);
						} else {
							self.migrate(cids, 0, cids[0]);
						}
					}
				},

				log: function(data) {
					self.progress.controller().log(data);
				}
			});
		},

		migrate: function(cids, index, cid) {
			if(cid === undefined) {
				self.actions.controller().migrateComplete();
				return;
			}

			switch(self.migrationType) {
				case 'article':
					var tmp = cid;
					cid = {
						component: self.migratorType,
						cid: tmp
					}
					break;
			}

			var publishingState = self.publishingState().val();
			var migrateLikes = self.migrateLikes().is(':checked') ? 1 : 0;

			Komento.ajax('admin.views.migrators.getmigrator', {
				"type": self.migratorType,
				"function": 'migrate',
				"params": {
					component: cid.component,
					cid: cid.cid,
					publishingState: publishingState,
					migrateLikes: migrateLikes
				}
			}, {
				success: function() {
					self.migrate(cids, index + 1, cids[index + 1]);
				},

				fail: function(data) {
					self.progress.controller().log('error: ' + data);
				},

				update: function(count) {
					self.progress.controller().updateMigratedComments(count);
				},

				log: function(data) {
					self.progress.controller().log(data);
				}
			});
		},

		migrateCustom: function(params, start, total) {
			if(start >= total) {
				self.actions.controller().migrateComplete();
				return;
			}

			params.data.start = start;

			Komento.ajax('admin.views.migrators.getmigrator', {
				"type": self.migratorType,
				"function": 'migrate',
				"params": params
			}, {
				success: function(newStart) {
					self.migrateCustom(params, newStart, total);
				},

				fail: function(data) {
					self.progress.controller().log('error: ' + data);
				},

				update: function(count) {
					self.progress.controller().updateMigratedComments(count);
				},

				log: function(data) {
					self.progress.controller().log(data);
				}
			})
		}
	}}
);
module.resolve();

});
