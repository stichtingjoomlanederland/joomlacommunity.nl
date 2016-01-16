Komento.module('dashboard.comment.item', function($) {
	var module = this;

	Komento.require()
	.library('dialog')
	.view('dialogs/delete.affectchild', 'comment/edit.form')
	.done(function()
	{
		Komento.Controller(
			'Dashboard.CommentItem',
			{
				defaults: {
					commentId: 0,
					view: {
						editForm: 'comment/edit.form',
						affectChild: 'dialogs/delete.affectchild'
					}
				}
			},
			function(self)
			{ return {
				init: function()
				{

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

							self.statusButton().text($.language('COM_KOMENTO_UNPUBLISHED'));
							self.publishButton().show();
							self.unpublishButton().hide();
							self.statusOptions().hide();
						},

						fail: function()
						{

						}
					});
				},

				unpublishChild: function(id)
				{
					var text = $.language('COM_KOMENTO_UNPUBLISHED');
					$('li[parentid="' + id + '"]').each(function() {
						$(this).find('.kmt-status').text(text);
						$(this).find('.kmt-unpublish').hide();
						$(this).find('.kmt-publish').show();
						self.unpublishChild($(this).attr('id'));
					})
				}
			} }
		);
	});
});
