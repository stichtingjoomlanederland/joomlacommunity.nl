Komento.module('migrator.actions', function($) {
var module = this;
Komento.require().script('admin.language','komento.common').done(function() {
Komento.Controller(
	'Migrator.Actions',
	{
		defaults: {
			'{migrateButton}': '.migrateButton',
			'{deleteButton}': '.deleteButton'
		}
	},
	function(self)
	{ return {
		init: function() {
			self.progress = self.element.find('.migratorProgress');
			self.migrator = self.element.find('.migratorTable');
		},

		'{migrateButton} click': function(el) {
			if(el.checkClick())
			{
				self.migrateStart();
			}
		},

		migrateStart: function() {
			self.progress.controller().migratedComments().text('0');
			self.progress.controller().progressBar().width('0%');
			self.progress.controller().progressPercentage().text('0');
			self.progress.controller().progressStatus().html('<img src="' + Komento.options.spinner + '" /> Migrating...');
			self.migrator.controller().getStatistic();
		},

		migrateComplete: function() {
			self.progress.controller().progressStatus().text($.language('COM_KOMENTO_MIGRATORS_PROGRESS_DONE'));
			self.progress.controller().log($.language('COM_KOMENTO_MIGRATORS_LOG_COMPLETE'));
			self.migrateButton().enable();
		}
	}}
);
module.resolve();
});
});
