Komento.module('migrator.custom', function($) {
var module = this;

Komento.Controller(
	'Migrator.Custom',
	{
		defaults: {
			'{migrateTable}'	: '#migrate-table',
			'{componentFilter}'	: '#migrate-component-filter',

			'{tableColumns}'	: '.table-columns',

			'{cycleAmount}'		: '#migrate-cycle'
		}
	},
	function(self)
	{ return {
		init: function() {
			self.loadTableColumns();
		},

		'{migrateTable} change': function() {
			self.loadTableColumns();
		},

		loadTableColumns: function() {
			var tableName = self.migrateTable().val();
			var params = {'tableName': tableName};

			Komento.ajax('admin.views.migrators.getmigrator', {
				"type": 'custom',
				"function": 'getColumns',
				"params": params
			}, {
				success: function(columns) {
					self.tableColumns().each(function(index, element) {
						var tmp = columns;
						if(!$(element).data('required')) {
							tmp = '<option value="kmt-none">none</option>' + columns;
						}
						$(element).html(tmp);
					});
				}
			});
		},

		getData: function() {
			var data = {};

			data.table = self.migrateTable().val();

			self.tableColumns().each(function(index, element) {
				var key = $(element).attr('id').slice(15);
				data[key] = $(element).val();
			});

			data.componentFilter = self.componentFilter().val();
			data.cycle = self.cycleAmount().val();

			return data;
		}
	} }
);

module.resolve();
});
