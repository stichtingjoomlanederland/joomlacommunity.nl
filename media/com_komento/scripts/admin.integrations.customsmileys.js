Komento.module('admin.integrations.customsmileys', function($) {
	var module = this;

	Komento.Controller('CustomSmileys', {
		defaultOptions: {
			'{smiley}': '[data-smiley]',

			'{addRow}': '[data-smiley-add-row]',

			'{add}': '[data-smiley-add-button]',
			'{delete}': '[data-smiley-delete-button]',

		}
	}, function(self) {
		return {
			init: function() {

			},

			'{add} click': function() {
				var row = self.smiley().eq(0).clone();

				row.find('input').val('');

				self.addRow().before(row);
			},

			'{delete} click': function(el) {
				var row = el.parent('[data-smiley]');

				if(self.smiley().length > 1) {
					row.remove();
				} else {
					row.find('input').val('');
				}
			}
		}
	});

	module.resolve();
});

