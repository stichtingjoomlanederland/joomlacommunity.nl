EasyDiscuss.module('customfields', function($) {
	var module = this;

	EasyDiscuss
	.require()
	.language(
		'COM_EASYDISCUSS_CUSTOMFIELDS_DISPLAY_ERROR'
	)
	.done(function($) {

		EasyDiscuss.Controller('Administrator.CustomFields' ,
		{
			defaultOptions:
			{
				'customId'	: null,
				'defaultType'	: null,
				'optionCount'	: '.optionCount',
				'{optionType}'	: '#type',
				'{advanceOptions}'	: '.customFieldAdvanceOption',
				'{addButton}'	: 'button[name="Add"]',
				'{removeButton}'	: 'button[name="Remove"]',
				'{fieldLoader}'	: '.fieldLoader',
				'{addContainer}'	: '.addContainer'
			}
		},
		function(self) {
			return {
				init: function()
				{
					// Load back the values when editing
					// self.advanceOptions().implement( EasyDiscuss.Controller.Administrator.CustomFields.AdvanceOptions );
					// self.getAdvanceOption( self.options.defaultType );
				},

				'{optionType} change' : function(element )
				{
					self.getAdvanceOption(element.val());
				},

				'{addButton} click': function(element )
				{
					self.addFieldOption(element.data('fieldtype'));
				},

				'{removeButton} click': function(element )
				{
					self.removeFieldOption(element, element.attr('id'), element.data('removetype'));
				},

				getAdvanceOption: function(type )
				{
					// GET NEW OR LOAD PREVIOUS CUSTOMFIELD VALUES
					EasyDiscuss.ajax('admin.views.customfields.getAdvanceOption' ,
					{
						'activeType' : type,
						'customId'	: self.options.customId
					},
					{
						beforeSend: function()
						{
							$('.fieldLoader').show();
						}
					})
					.done(function(addButton, html, count )
					{
						self.advanceOptions().html(html);
						$('.addContainer').html(addButton);
						$('.optionCount').attr('totalcount', count);
						$('.fieldLoader').hide();
					})
					.fail(function(message )
					{
						// show error message
						self.advanceOptions().append($.language('COM_EASYDISCUSS_CUSTOMFIELDS_DISPLAY_ERROR'));
					})
					.always(function() {
						//remove the loading here
						$('.fieldLoader').hide();
					});
				},

				addFieldOption: function(type )
				{
					// ADD BUTTON FOR BACKEND
					var totalCount = $('.optionCount').attr('totalcount');
					EasyDiscuss.ajax('admin.views.customfields.addFieldOption' ,
					{
						'activeType' : type,
						'customId'	: self.options.customId,
						'fieldCount'	: totalCount
					},
					{
						beforeSend: function()
						{
							$('.fieldLoader').show();
						}
					})
					.done(function(value, count )
					{
						self.advanceOptions().append(value);
						$('.optionCount').attr('totalcount', count);
						$('.fieldLoader').hide();
					})
					.fail(function(message )
					{
						// show error message
						self.advanceOptions().append($.language('COM_EASYDISCUSS_CUSTOMFIELDS_DISPLAY_ERROR'));
					})
					.always(function() {
						//remove the loading here
						$('.fieldLoader').hide();
					});
				},

				removeFieldOption: function(id, count, type )
				{
					$('#' + type + '_' + count).remove();
					$('.remove' + type + '_' + count).remove();
					id.remove();
				}
			};
		});
		module.resolve();
	});
});
