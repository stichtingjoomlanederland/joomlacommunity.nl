Komento.module('dashboard.flag.item', function($) {
	var module = this;

	Komento.require()
	.library('ui/effect', 'dialog')
	.language(
		'COM_KOMENTO_UNPUBLISHED'
	)
	.view(
		'dialogs/delete.affectchild',
		'dialogs/unpublish.affectchild',
		'comment/edit.form'
	)
	.done(function()
	{
		Komento.Controller(
			'Dashboard.FlagItem',
			{
				defaults: {
					'commentId': 0,
					'permalink': 0,
					'{commentText}': '.kmt-text',
					'{commentInfo}': '.kmt-info',
					'{commentStatus}': '.kmt-status',
					'{noflagButton}': '.kmt-noflag',
					'{spamButton}': '.kmt-spam',
					'{offensiveButton}': '.kmt-offensive',
					'{offtopicButton}': '.kmt-offtopic',
					'{publishButton}': '.kmt-publish',
					'{unpublishButton}': '.kmt-unpublish',
					'{deleteButton}': '.kmt-delete',
					view: {
						editForm: 'comment/edit.form',
						deleteDialog: 'dialogs/delete.affectchild',
						unpublishDialog: 'dialogs/unpublish.affectchild'
					}
				}
			},
			function(self)
			{ return {
				init: function()
				{
				},

				closeDialog: function()
				{
					$('.foundryDialog').controller().close();
				},

				'{noflagButton} click': function()
				{
					self.markComment('0');
				},

				'{spamButton} click': function()
				{
					self.markComment('1');
				},

				'{offensiveButton} click': function()
				{
					self.markComment('2');
				},

				'{offtopicButton} click': function()
				{
					self.markComment('3');
				},

				'{publishButton} click': function()
				{
					self.publishComment();
				},

				'{unpublishButton} click': function()
				{
					self.showUnpublishDialog();
				},

				'{deleteButton} click': function()
				{
					self.showDeleteDialog();
				},

				markComment: function(type)
				{
					var commentId = self.options.commentId;
					var id = commentId.split('-')[1];

					Komento.ajax('site.views.komento.mark',
					{
						id: id,
						type: type
					},
					{
						success: function()
						{
							self.element.hide('fade', function() {
								self.element.remove();
							});
						},

						fail: function()
						{

						}
					});
				},

				showPublishDialog: function()
				{

				},

				publishComment: function()
				{
					var commentId = self.options.commentId;
					var id = commentId.split('-')[1];

					Komento.ajax('site.views.komento.publish',
					{
						id: id,
						affectChild: 0
					},
					{
						success: function()
						{
							self.element.hide('fade', function() {
								self.element.remove();
							});
						},

						fail: function()
						{

						}
					});
				},

				showUnpublishDialog: function()
				{
					$.dialog({
						content: self.view.unpublishDialog(true),
						afterShow: function() {
							$('.foundryDialog').find('.unpublish-affectChild').click(function() {
								self.unpublishComment();
							});
						}
					});
				},

				unpublishComment: function()
				{
					var commentId = self.options.commentId;
					var id = commentId.split('-')[1];

					Komento.ajax('site.views.komento.unpublish',
					{
						id: id
					},
					{
						success: function()
						{
							self.closeDialog();
							self.unpublishChild(self.element.attr('id'));

							self.commentStatus().text($.language('COM_KOMENTO_UNPUBLISHED'));
							self.unpublishButton().parent().hide('drop');
						},

						fail: function()
						{

						}
					});
				},

				unpublishChild: function(id)
				{
					$('tr[parentid="' + id + '"]').each(function() {
						$(this).find('.kmt-unpublish').parent().hide('drop');
						$(this).find('.kmt-status').text($.language('COM_KOMENTO_UNPUBLISHED'));
						self.unpublishChild($(this).attr('id'));
					})
				},

				showDeleteDialog: function()
				{
					$.dialog({
						content: self.view.deleteDialog(true),
						afterShow: function() {
							$('.foundryDialog').find('.delete-affectChild').click(function() {
								self.deleteComment(1);
							});

							$('.foundryDialog').find('.delete-moveChild').click(function() {
								self.deleteComment(0);
							});
						}
					});
				},

				deleteComment: function(affectChild)
				{
					var commentId = self.options.commentId;
					var id = commentId.split('-')[1];

					Komento.ajax('site.views.komento.deletecomment',
					{
						id: id,
						affectChild: affectChild
					},
					{
						success: function()
						{
							self.closeDialog();

							if(affectChild)
							{
								self.deleteChild(self.element.attr('id'));
							}

							self.element.hide('fade', function() {
								self.element.remove();
							});
						},

						fail: function()
						{
						}
					});
				},

				deleteChild: function(id)
				{
					$('tr[parentid="' + id + '"]').each(function() {
						self.deleteChild($(this).attr('id'));
					}).hide('fade', function() {
						$(this).remove();
					});
				}

			} }
		);

		module.resolve();
	});
});
