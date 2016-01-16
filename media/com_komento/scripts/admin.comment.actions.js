Komento.module('admin.comment.actions', function($) {
var module = this;
Komento.require().library('dialog').script('komento.common', 'admin.language').done(function() {

	var icon = {};

	if( Komento.options.jversion == '1.5' ) {
		icon.published = 'images/tick.png';
		icon.unpublished = 'images/publish_x.png';
	} else {
		icon.published = 'templates/bluestork/images/admin/tick.png';
		icon.unpublished = 'templates/bluestork/images/admin/publish_x.png';
	}

	Komento.actions = {
		loadReplies: function( parentId ) {
			var startCount = $('.kmt-row').length;

			Komento.ajax('admin.views.comments.loadreplies', {
				parentId: parentId,
				startCount: startCount
			}, {
				success: function(html) {
					$('#kmt-' + parentId).after(html).find('.linked-cell').text('-');

					$('.kmt-row').each(function(index, element) {
						var classindex = index % 2;
						element.removeClass('row1', 'row0').addClass('row' + classindex);
					});

					if( Komento.options.jversion < '3.0' ) {
						$('#toggle').attr('onClick', 'checkAll(' + $('.kmt-row').length + ');');
					}
				}
			});
		},

		submit: function(action, affectchild) {
			if($('.foundryDialog').length != 0) {
				$('.foundryDialog').controller().close();
			}

			Komento.actions.affectchild = affectchild;

			var ids = new Array();
			var elements = new Array();

			$('input[type="checkbox"]:checked').each(function(i, e) {
				if(e.value != '') {
					ids.push(e.value);
					elements.push($('#kmt-' + e.value));

					var cellname;
					if( action == 'unstick' || action == 'stick' ) {
						cellname = 'sticked';
					} else {
						cellname = 'published';
					}
					$('#kmt-' + e.value).find('.' + cellname + '-cell a img').attr('src', Komento.options.spinner);
				}
			});

			Komento.ajax('admin.views.comments.' + action, {
				ids: ids,
				affectchild: affectchild
			},
			{
				success: function() {
					var childs = [];
					var parents = [];

					$.each(elements, function(i, e) {
						Komento.actions[action](e);

						if(e.attr('childs') > 0) {
							childs.push(1);
						}

						if(e.attr('parentid') != 0) {
							parents.push(1);
						}
					});

					if((action == 'publish' && parents.length > 0) || (action != 'publish' && childs.length > 0)) {
						Komento.actions[action + 'Dialog']();
					}
				},

				fail: function() {

				}
			});
		},

		publish: function(e) {

			var onclick = e.find('.published-cell a').attr('onclick').replace('unpublish', 'publish').replace('publish', 'unpublish');
			e.find('.published-cell a').attr('onclick', onclick).attr('title', $.language( 'COM_KOMENTO_UNPUBLISH_ITEM' ) );

			if( Komento.options.jversion < '3.0' )
			{
				e.find('.published-cell a img').attr('src', icon.published);
			}
			else
			{
				e.find('.published-cell i').removeClass('icon-unpublish').addClass('icon-publish');

				// for joomla 3.4.3
				e.find('.published-cell span').removeClass('icon-unpublish').addClass('icon-publish');
			}

			Komento.actions.publishParent(e);
			Komento.actions.publishChild(e);
		},

		publishParent: function(e) {

			if( !e.exists() ) {
				return;
			}

			var onclick = e.find('.published-cell a').attr('onclick').replace('unpublish', 'publish').replace('publish', 'unpublish');
			e.find('.published-cell a').attr('onclick', onclick).attr('title', $.language( 'COM_KOMENTO_UNPUBLISH_ITEM' ) );

			if( Komento.options.jversion < '3.0' )
			{
				e.find('.published-cell a img').attr('src', icon.published);
			}
			else
			{
				e.find('.published-cell i').removeClass('icon-unpublish').addClass('icon-publish');

				// for joomla 3.4.3
				e.find('.published-cell span').removeClass('icon-unpublish').addClass('icon-publish');
			}

			if(e.attr('parentid') != 0) {
				Komento.actions.publishParent($('#kmt-' + e.attr('parentid')));
			}
		},

		publishChild: function(e) {
			if( !e.exists() ) {
				return;
			}
			var onclick = e.find('.published-cell a').attr('onclick').replace('unpublish', 'publish').replace('publish', 'unpublish');
			e.find('.published-cell a').attr('onclick', onclick).attr('title', $.language( 'COM_KOMENTO_UNPUBLISH_ITEM' ) );

			if( Komento.options.jversion < '3.0' )
			{
				e.find('.published-cell a img').attr('src', icon.published);
			}
			else
			{
				e.find('.published-cell i').removeClass('icon-unpublish').addClass('icon-publish');

				// for joomla 3.4.3
				e.find('.published-cell span').removeClass('icon-unpublish').addClass('icon-publish');
			}

			if(Komento.actions.affectchild == 1 & e.attr('childs') > 0) {
				var commentId = e.attr('id').split('-')[1];
				Komento.actions.publishChild($('.kmt-row[parentid="' + commentId + '"]'));
			}
		},

		publishDialog: function() {
			$.dialog('<p>' + $.language('COM_KOMENTO_PARENT_PUBLISHED') + '</p>');
		},

		publishParentDialog: function() {
			$.dialog('<p>' + $.language('COM_KOMENTO_PARENT_PUBLISHED') + '</p>');
		},

		unpublish: function(e) {
			if( !e.exists() ) {
				return;
			}
			var onclick = e.find('.published-cell a').attr('onclick').replace('unpublish', 'publish');
			e.find('.published-cell a').attr('onclick', onclick).attr('title', $.language( 'COM_KOMENTO_PUBLISH_ITEM' ) );

			if( Komento.options.jversion < '3.0' )
			{
				e.find('.published-cell a img').attr('src', icon.unpublished);
			}
			else
			{
				e.find('.published-cell i').removeClass('icon-publish').addClass('icon-unpublish');

				// for Joomla 3.4.3
				e.find('.published-cell span').removeClass('icon-publish').addClass('icon-unpublish');				
			}

			if(e.attr('childs') > 0) {
				var commentId = e.attr('id').split('-')[1];
				Komento.actions.unpublish($('.kmt-row[parentid="' + commentId + '"]'));
			}
		},

		unpublishDialog: function() {
			$.dialog('<p>' + $.language('COM_KOMENTO_CHILD_UNPUBLISHED') + '</p>');
		},

		stick: function(e) {
			var onclick = e.find('.sticked-cell a').attr('onclick').replace('stick', 'unstick');
			e.find('.sticked-cell a').attr('onclick', onclick);

			if( Komento.options.jversion < '3.0' )
			{
				e.find('.sticked-cell a img').attr('src', 'components/com_komento/assets/images/sticked.png');
			}
			else
			{
				e.find('.sticked-cell i').removeClass('icon-star-empty').addClass('icon-star');
			}

		},

		stickDialog: function() {

		},

		unstick: function(e) {
			var onclick = e.find('.sticked-cell a').attr('onclick').replace('unstick', 'stick');
			e.find('.sticked-cell a').attr('onclick', onclick);

			if( Komento.options.jversion < '3.0' )
			{
				e.find('.sticked-cell a img').attr('src', 'components/com_komento/assets/images/unsticked.png');
			}
			else
			{
				e.find('.sticked-cell i').removeClass('icon-star').addClass('icon-star-empty');
			}
		},

		unstickDialog: function() {

		}
	};

	Komento.prepare = {
		checkChild: function() {
			var childs = [];
			$('input[type="checkbox"]:checked').each(function(i, e) {
				if(e.value != '') {
					if($('#kmt-' + e.value).attr('childs') > 0) {
						childs.push(1);
					}
				}
			});

			if(childs.length > 0) {
				return true;
			} else {
				return false;
			}
		},

		remove: function() {
			var warningText, buttons;

			if(Komento.prepare.checkChild()) {
				warningText = $.language('COM_KOMENTO_CONFIRM_DELETE_AFFECT_ALL_CHILD');
				buttons = '<button onclick="Komento.prepare.removeall()">' + $.language('COM_KOMENTO_DELETE_ALL_CHILD') + '</button>';
				buttons += '<button onclick="Komento.prepare.removesingle()">' + $.language('COM_KOMENTO_DELETE_MOVE_CHILD_UP') + '</button>';
			} else {
				warningText = $.language('COM_KOMENTO_CONFIRM_DELETE');
				buttons = '<button onclick="Komento.prepare.removeall()">' + $.language('COM_KOMENTO_DELETE_COMMENT') + '</button>';
			}

			var content = '<div style="text-align: center;"><p>' + warningText + '</p>' + buttons + '</div>';

			$.dialog(content);
		},

		removeall: function() {
			prepareSubmit('remove', 1);
		},

		removesingle: function() {
			prepareSubmit('remove', 0);
		},

		publish: function() {
			if(Komento.prepare.checkChild()) {
				var warningText = $.language('COM_KOMENTO_CONFIRM_PUBLISH_AFFECT_ALL_CHILD');
				var buttons = '<button onclick="Komento.prepare.publishall()">' + $.language('COM_KOMENTO_PUBLISH_ALL_CHILD') + '</button>';
				buttons += '<button onclick="Komento.prepare.publishsingle()">' + $.language('COM_KOMENTO_PUBLISH_SINGLE') + '</button>';

				var content = '<div style="text-align: center;"><p>' + warningText + '</p>' + buttons + '</div>';

				$.dialog(content);
			} else {
				Komento.actions.submit('publish', 1);
			}
		},

		publishall: function() {
			Komento.actions.submit('publish', 1);
		},

		publishsingle: function() {
			Komento.actions.submit('publish', 0);
		},

		unpublish: function() {
			Komento.actions.submit('unpublish', 1);
		},

		stick: function() {
			Komento.actions.submit('stick', 1);
		},

		unstick: function() {
			Komento.actions.submit('unstick', 1);
		},

		saveColumns: function() {
			submitform('saveColumns');
		}
	};

	window.submitbutton = function(action) {
		// route everything to Komento.prepare
		Komento.prepare[action]();
	};

	window.prepareSubmit = function(action, affectchild) {
		if($('.foundryDialog').length != 0) {
			$('.foundryDialog').controller().close();
		}

		document.adminForm.affectchild.value = affectchild;
		submitform(action);
	};

	// function unchanged from Joomla's library
	// reason to put here is to route submitbutton(task) to our custom submitbutton
	// instead of joomla's native submitbutton() function
	window.listItemTask = function(id, task) {
		var f = document.adminForm;
		var cb = f[id];
		if (cb) {
			for (var i = 0; true; i++) {
				var cbx = f['cb'+i];
				if (!cbx)
					break;
				cbx.checked = false;
			} // for
			cb.checked = true;
			f.boxchecked.value = 1;
			submitbutton(task);
		}
		return false;
	};

	module.resolve();
});
});
