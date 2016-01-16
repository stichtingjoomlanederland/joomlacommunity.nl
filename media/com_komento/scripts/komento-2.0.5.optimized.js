FD31.installer("Komento", "definitions", function($){
$.module(["komento/admin.comment.actions","komento/komento.common","komento/komento.language","komento/syntaxhighlighter","komento/admin.language","komento/admin.database","komento/admin.integrations.customsmileys","komento/admin.report.actions","komento/dashboard.comment.item","komento/dashboard.flag.item","komento/komento.admincomments","komento/komento.bbcode","komento/komento.commentform","komento/komento.commentitem","komento/komento.commentlist","komento/sharelinks","komento/markitup","komento/komento.commenttools","komento/komento.famelist","komento/komento.insertvideo","komento/komento","komento/komento.profile","komento/komento.ratings","komento/komento.upload","komento/location","komento/migrator.actions","komento/migrator.common","komento/migrator.custom","komento/migrator.progress"]);
$.require.template.loader(["komento/dialogs/error","komento/dialogs/delete.affectchild","komento/comment/edit.form","komento/dialogs/unpublish.affectchild","komento/dialogs/delete.single","komento/comment/item/edit.form","komento/dialogs/delete.attachment","komento/notifications/new.comment","komento/comment/form/uploadrow"]);
$.require.language.loader(["COM_KOMENTO_ERROR","COM_KOMENTO_LOADING","COM_KOMENTO_UNPUBLISHED","COM_KOMENTO_PUBLISHED","COM_KOMENTO_NOFLAG","COM_KOMENTO_SPAM","COM_KOMENTO_OFFENSIVE","COM_KOMENTO_OFFTOPIC","COM_KOMENTO_COMMENTS_LOADING","COM_KOMENTO_COMMENT_EDIT","COM_KOMENTO_COMMENT_EDIT_CANCEL","COM_KOMENTO_COMMENT_EDITTED_BY","COM_KOMENTO_COMMENT_REPLY","COM_KOMENTO_COMMENT_REPLY_CANCEL","COM_KOMENTO_COMMENT_REPORT","COM_KOMENTO_COMMENT_REPORTED","COM_KOMENTO_COMMENT_SHARE","COM_KOMENTO_COMMENT_SHARE_CANCEL","COM_KOMENTO_COMMENT_LIKE","COM_KOMENTO_COMMENT_UNLIKE","COM_KOMENTO_COMMENT_STICK","COM_KOMENTO_COMMENT_UNSTICK","COM_KOMENTO_COMMENT_WHERE_ARE_YOU","COM_KOMENTO_COMMENT_PEOPLE_WHO_LIKED_THIS","COM_KOMENTO_FORM_LEAVE_YOUR_COMMENTS","COM_KOMENTO_FORM_IN_REPLY_TO","COM_KOMENTO_FORM_SUBMIT","COM_KOMENTO_FORM_REPLY","COM_KOMENTO_FORM_NOTIFICATION_SUBMITTED","COM_KOMENTO_FORM_NOTIFICATION_PENDING","COM_KOMENTO_FORM_NOTIFICATION_COMMENT_REQUIRED","COM_KOMENTO_FORM_NOTIFICATION_COMMENT_TOO_SHORT","COM_KOMENTO_FORM_TNC","COM_KOMENTO_FORM_AGREE_TNC","COM_KOMENTO_FORM_OR_DROP_FILES_HERE","COM_KOMENTO_FORM_NOTIFICATION_NOTIFICATION_USERNAME_REQUIRED","COM_KOMENTO_FORM_NOTIFICATION_NAME_REQUIRED","COM_KOMENTO_FORM_NOTIFICATION_EMAIL_REQUIRED","COM_KOMENTO_FORM_NOTIFICATION_EMAIL_INVALID","COM_KOMENTO_FORM_NOTIFICATION_WEBSITE_REQUIRED","COM_KOMENTO_FORM_NOTIFICATION_WEBSITE_INVALID","COM_KOMENTO_FORM_NOTIFICATION_TNC_REQUIRED","COM_KOMENTO_FORM_NOTIFICATION_CAPTCHA_REQUIRED","COM_KOMENTO_FORM_NOTIFICATION_SUBSCRIBED","COM_KOMENTO_FORM_NOTIFICATION_SUBSCRIBE_CONFIRMATION_REQUIRED","COM_KOMENTO_FORM_NOTIFICATION_SUBSCRIBE_ERROR","COM_KOMENTO_FORM_NOTIFICATION_UNSUBSCRIBED","COM_KOMENTO_FORM_NOTIFICATION_MAX_FILE_SIZE","COM_KOMENTO_FORM_NOTIFICATION_MAX_FILE_ITEM","COM_KOMENTO_FORM_NOTIFICATION_FILE_EXTENSION","COM_KOMENTO_FORM_NOTIFICATION_UPLOAD_NOT_ALLOWED","COM_KOMENTO_FORM_LOCATION_AUTODETECT","COM_KOMENTO_FORM_LOCATION_DETECTING","COM_KOMENTO_BBCODE_BOLD","COM_KOMENTO_BBCODE_ITALIC","COM_KOMENTO_BBCODE_UNDERLINE","COM_KOMENTO_BBCODE_LINK","COM_KOMENTO_BBCODE_LINK_TEXT","COM_KOMENTO_BBCODE_PICTURE","COM_KOMENTO_BBCODE_VIDEO","COM_KOMENTO_BBCODE_BULLETLIST","COM_KOMENTO_BBCODE_NUMERICLIST","COM_KOMENTO_BBCODE_BULLET","COM_KOMENTO_BBCODE_QUOTE","COM_KOMENTO_BBCODE_CLEAN","COM_KOMENTO_BBCODE_SMILE","COM_KOMENTO_BBCODE_HAPPY","COM_KOMENTO_BBCODE_SURPRISED","COM_KOMENTO_BBCODE_TONGUE","COM_KOMENTO_BBCODE_UNHAPPY","COM_KOMENTO_BBCODE_WINK","COM_KOMENTO_INSERT_VIDEO","COM_KOMENTO_CONFIRM_DELETE_AFFECT_ALL_CHILD","COM_KOMENTO_CONFIRM_DELETE","COM_KOMENTO_DELETE_COMMENT","COM_KOMENTO_DELETE_ALL_CHILD","COM_KOMENTO_DELETE_MOVE_CHILD_UP","COM_KOMENTO_DELETING","COM_KOMENTO_CONFIRM_PUBLISH_AFFECT_ALL_CHILD","COM_KOMENTO_PUBLISH_ALL_CHILD","COM_KOMENTO_PUBLISH_SINGLE","COM_KOMENTO_CHILD_UNPUBLISHED","COM_KOMENTO_PARENT_PUBLISHED","COM_KOMENTO_MIGRATORS_LOG_COMPLETE","COM_KOMENTO_MIGRATORS_PROGRESS_DONE","COM_KOMENTO_YES_OPTION","COM_KOMENTO_NO_OPTION","COM_KOMENTO_ACL_RECOMMENDED","COM_KOMENTO_PUBLISH_ITEM","COM_KOMENTO_UNPUBLISH_ITEM","COM_KOMENTO_SETTINGS_DATABASE_POPULATE_DEPTH_STATUS_UPDATING","COM_KOMENTO_SETTINGS_DATABASE_POPULATE_DEPTH_STATUS_COMPLETED","COM_KOMENTO_SETTINGS_DATABASE_POPULATE_DEPTH_STATUS_ERROR","COM_KOMENTO_SETTINGS_DATABASE_FIX_STRUCTURE_STATUS_UPDATING_STAGE1","COM_KOMENTO_SETTINGS_DATABASE_FIX_STRUCTURE_STATUS_UPDATING_STAGE2","COM_KOMENTO_SETTINGS_DATABASE_FIX_STRUCTURE_STATUS_ERROR","COM_KOMENTO_SETTINGS_DATABASE_FIX_STRUCTURE_STATUS_COMPLETED"]);
});
FD31.installer("Komento", "scripts", function($){
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

Komento.module('komento.common', function($) {
var module = this;

Komento.require().library('ui/effect', 'scrollTo').script('komento.language').done(function() {
	var originalHide = $.fn.hide;
	var originalShow = $.fn.show;
	var originalScrollTo = function( element ) {
		$.scrollTo(element, 500);
	};

	$.fn.hide = function() {
		originalHide.apply(this, arguments);
		this.addClass('hidden');
		return this;
	}

	$.fn.show = function() {
		originalShow.apply(this, arguments);
		this.removeClass('hidden');
		return this;
	}

	$.fn.scroll = function() {
		originalScrollTo(this);

		/*return this.each(function ()
		{
			$('html, body').animate({scrollTop: $(this).offset().top}, 'fast');
		});*/
	};

	$.fn.highlight = function() {
		this.effect("highlight", {color: '#FDFFE0'}, 2000);
		return this;
	};

	$.fn.enable = function() {
		this.removeClass('disabled');
		return this;
	};

	$.fn.disable = function() {
		this.addClass('disabled');
		return this;
	};

	$.fn.switchOn = function() {
		this.removeClass('cancel');
		return this;
	};

	$.fn.switchOff = function() {
		this.addClass('cancel');
		return this;
	};

	$.fn.checkSwitch = function() {
		if(this.hasClass('cancel')) {
			return false;
		} else {
			return true;
		}
	};

	$.fn.checkClick = function() {
		if(this.hasClass('disabled')) {
			return false;
		} else {
			this.addClass('disabled');
			return true;
		}
	};

	$.fn.clearClick = function() {
		this.removeClass('disabled');
		return this;
	};

	$.fn.loading = function() {
		this.addClass('loading');
		return this;
	};

	$.fn.doneLoading = function() {
		this.removeClass('loading');
		return this;
	};

	$.fn.exists = function() {
		return this.length > 0 ? true : false;
	};

	$.fn.acl = function(component, rule, callback) {
		Komento.ajax('site.views.komento.checkAcl', {
			component: component,
			rule: rule
		},
		{
			success: function() {
				return callback && callback();
			}
		});
	};

	$.fn.permission = function(id, action, callback) {
		Komento.ajax('site.views.komento.checkPermission', {
			id: id,
			action: action
		},
		{
			success: function() {
				return callback && callback();
			}
		});
	};

	$.shortenlink = function(url, callback) {
		Komento.ajax('site.views.komento.shortenLink', {
			url: url
		},
		{
			success: function(link) {
				Komento.shortenLink = link;
				return callback && callback(link);
			}
		});
	};

	$.stripHtml = function(html) {
		return $('<div>' + html + '</div>').text();
	};

	$.bugReport = function(error) {
		Komento.require().library('dialog').view('dialogs/error').done(function() {
			$.dialog({
				title: $.language( 'COM_KOMENTO_ERROR' ),
				width: 500,
				customClass: 'kmt-dialog',
				content: Komento.View('dialogs/error', {
					status: error.status,
					statusText: error.statusText,
					responseText: $.stripHtml(error.responseText)
				})
			});
		});
	};

	$.loadSHBrushes = function() {
		Komento.require().script('syntaxhighlighter')
			.done(function() {
				$('.kmt-item pre code').each(function(i, e) {
					hljs.highlightBlock(e);
				});
			});
	};

	module.resolve();
});
});

Komento.module('komento.language', function($) {
	var module = this;

	Komento.require()
		.language(
			'COM_KOMENTO_ERROR',
			'COM_KOMENTO_LOADING',
			'COM_KOMENTO_UNPUBLISHED',
			'COM_KOMENTO_PUBLISHED',
			'COM_KOMENTO_NOFLAG',
			'COM_KOMENTO_SPAM',
			'COM_KOMENTO_OFFENSIVE',
			'COM_KOMENTO_OFFTOPIC',
			'COM_KOMENTO_COMMENTS_LOADING',
			'COM_KOMENTO_COMMENT_EDIT',
			'COM_KOMENTO_COMMENT_EDIT_CANCEL',
			'COM_KOMENTO_COMMENT_EDITTED_BY',
			'COM_KOMENTO_COMMENT_REPLY',
			'COM_KOMENTO_COMMENT_REPLY_CANCEL',
			'COM_KOMENTO_COMMENT_REPORT',
			'COM_KOMENTO_COMMENT_REPORTED',
			'COM_KOMENTO_COMMENT_SHARE',
			'COM_KOMENTO_COMMENT_SHARE_CANCEL',
			'COM_KOMENTO_COMMENT_LIKE',
			'COM_KOMENTO_COMMENT_UNLIKE',
			'COM_KOMENTO_COMMENT_STICK',
			'COM_KOMENTO_COMMENT_UNSTICK',
			'COM_KOMENTO_COMMENT_WHERE_ARE_YOU',
			'COM_KOMENTO_COMMENT_PEOPLE_WHO_LIKED_THIS',
			'COM_KOMENTO_FORM_LEAVE_YOUR_COMMENTS',
			'COM_KOMENTO_FORM_IN_REPLY_TO',
			'COM_KOMENTO_FORM_SUBMIT',
			'COM_KOMENTO_FORM_REPLY',
			'COM_KOMENTO_FORM_NOTIFICATION_SUBMITTED',
			'COM_KOMENTO_FORM_NOTIFICATION_PENDING',
			'COM_KOMENTO_FORM_NOTIFICATION_COMMENT_REQUIRED',
			'COM_KOMENTO_FORM_NOTIFICATION_COMMENT_TOO_SHORT',
			'COM_KOMENTO_FORM_TNC',
			'COM_KOMENTO_FORM_AGREE_TNC',
			'COM_KOMENTO_FORM_OR_DROP_FILES_HERE',
			'COM_KOMENTO_FORM_NOTIFICATION_NOTIFICATION_USERNAME_REQUIRED',
			'COM_KOMENTO_FORM_NOTIFICATION_NAME_REQUIRED',
			'COM_KOMENTO_FORM_NOTIFICATION_EMAIL_REQUIRED',
			'COM_KOMENTO_FORM_NOTIFICATION_EMAIL_INVALID',
			'COM_KOMENTO_FORM_NOTIFICATION_WEBSITE_REQUIRED',
			'COM_KOMENTO_FORM_NOTIFICATION_WEBSITE_INVALID',
			'COM_KOMENTO_FORM_NOTIFICATION_TNC_REQUIRED',
			'COM_KOMENTO_FORM_NOTIFICATION_CAPTCHA_REQUIRED',
			'COM_KOMENTO_FORM_NOTIFICATION_SUBSCRIBED',
			'COM_KOMENTO_FORM_NOTIFICATION_SUBSCRIBE_CONFIRMATION_REQUIRED',
			'COM_KOMENTO_FORM_NOTIFICATION_SUBSCRIBE_ERROR',
			'COM_KOMENTO_FORM_NOTIFICATION_UNSUBSCRIBED',
			'COM_KOMENTO_FORM_NOTIFICATION_MAX_FILE_SIZE',
			'COM_KOMENTO_FORM_NOTIFICATION_MAX_FILE_ITEM',
			'COM_KOMENTO_FORM_NOTIFICATION_FILE_EXTENSION',
			'COM_KOMENTO_FORM_NOTIFICATION_UPLOAD_NOT_ALLOWED',
			'COM_KOMENTO_FORM_LOCATION_AUTODETECT',
			'COM_KOMENTO_FORM_LOCATION_DETECTING',
			'COM_KOMENTO_BBCODE_BOLD',
			'COM_KOMENTO_BBCODE_ITALIC',
			'COM_KOMENTO_BBCODE_UNDERLINE',
			'COM_KOMENTO_BBCODE_LINK',
			'COM_KOMENTO_BBCODE_LINK_TEXT',
			'COM_KOMENTO_BBCODE_PICTURE',
			'COM_KOMENTO_BBCODE_VIDEO',
			'COM_KOMENTO_BBCODE_BULLETLIST',
			'COM_KOMENTO_BBCODE_NUMERICLIST',
			'COM_KOMENTO_BBCODE_BULLET',
			'COM_KOMENTO_BBCODE_QUOTE',
			'COM_KOMENTO_BBCODE_CLEAN',
			'COM_KOMENTO_BBCODE_SMILE',
			'COM_KOMENTO_BBCODE_HAPPY',
			'COM_KOMENTO_BBCODE_SURPRISED',
			'COM_KOMENTO_BBCODE_TONGUE',
			'COM_KOMENTO_BBCODE_UNHAPPY',
			'COM_KOMENTO_BBCODE_WINK',
			'COM_KOMENTO_INSERT_VIDEO'
		)
		.done(function() {
			module.resolve();
		});
});

Komento.module('syntaxhighlighter', function($) {
	var module = this;

	var hljs=new function(){function m(p){return p.replace(/&/gm,"&amp;").replace(/</gm,"&lt;")}function f(r,q,p){return RegExp(q,"m"+(r.cI?"i":"")+(p?"g":""))}function b(r){for(var p=0;p<r.childNodes.length;p++){var q=r.childNodes[p];if(q.nodeName=="CODE"){return q}if(!(q.nodeType==3&&q.nodeValue.match(/\s+/))){break}}}function h(t,s){var p="";for(var r=0;r<t.childNodes.length;r++){if(t.childNodes[r].nodeType==3){var q=t.childNodes[r].nodeValue;if(s){q=q.replace(/\n/g,"")}p+=q}else{if(t.childNodes[r].nodeName=="BR"){p+="\n"}else{p+=h(t.childNodes[r])}}}if(/MSIE [678]/.test(navigator.userAgent)){p=p.replace(/\r/g,"\n")}return p}function a(s){var r=s.className.split(/\s+/);r=r.concat(s.parentNode.className.split(/\s+/));for(var q=0;q<r.length;q++){var p=r[q].replace(/^language-/,"");if(e[p]||p=="no-highlight"){return p}}}function c(r){var p=[];(function q(t,u){for(var s=0;s<t.childNodes.length;s++){if(t.childNodes[s].nodeType==3){u+=t.childNodes[s].nodeValue.length}else{if(t.childNodes[s].nodeName=="BR"){u+=1}else{if(t.childNodes[s].nodeType==1){p.push({event:"start",offset:u,node:t.childNodes[s]});u=q(t.childNodes[s],u);p.push({event:"stop",offset:u,node:t.childNodes[s]})}}}}return u})(r,0);return p}function k(y,w,x){var q=0;var z="";var s=[];function u(){if(y.length&&w.length){if(y[0].offset!=w[0].offset){return(y[0].offset<w[0].offset)?y:w}else{return w[0].event=="start"?y:w}}else{return y.length?y:w}}function t(D){var A="<"+D.nodeName.toLowerCase();for(var B=0;B<D.attributes.length;B++){var C=D.attributes[B];A+=" "+C.nodeName.toLowerCase();if(C.value!==undefined&&C.value!==false&&C.value!==null){A+='="'+m(C.value)+'"'}}return A+">"}while(y.length||w.length){var v=u().splice(0,1)[0];z+=m(x.substr(q,v.offset-q));q=v.offset;if(v.event=="start"){z+=t(v.node);s.push(v.node)}else{if(v.event=="stop"){var p,r=s.length;do{r--;p=s[r];z+=("</"+p.nodeName.toLowerCase()+">")}while(p!=v.node);s.splice(r,1);while(r<s.length){z+=t(s[r]);r++}}}}return z+m(x.substr(q))}function j(){function q(w,y,u){if(w.compiled){return}var s=[];if(w.k){var r={};function x(D,C){var A=C.split(" ");for(var z=0;z<A.length;z++){var B=A[z].split("|");r[B[0]]=[D,B[1]?Number(B[1]):1];s.push(B[0])}}w.lR=f(y,w.l||hljs.IR,true);if(typeof w.k=="string"){x("keyword",w.k)}else{for(var v in w.k){if(!w.k.hasOwnProperty(v)){continue}x(v,w.k[v])}}w.k=r}if(!u){if(w.bWK){w.b="\\b("+s.join("|")+")\\s"}w.bR=f(y,w.b?w.b:"\\B|\\b");if(!w.e&&!w.eW){w.e="\\B|\\b"}if(w.e){w.eR=f(y,w.e)}}if(w.i){w.iR=f(y,w.i)}if(w.r===undefined){w.r=1}if(!w.c){w.c=[]}w.compiled=true;for(var t=0;t<w.c.length;t++){if(w.c[t]=="self"){w.c[t]=w}q(w.c[t],y,false)}if(w.starts){q(w.starts,y,false)}}for(var p in e){if(!e.hasOwnProperty(p)){continue}q(e[p].dM,e[p],true)}}function d(D,E){if(!j.called){j();j.called=true}function s(r,O){for(var N=0;N<O.c.length;N++){var M=O.c[N].bR.exec(r);if(M&&M.index==0){return O.c[N]}}}function w(M,r){if(p[M].e&&p[M].eR.test(r)){return 1}if(p[M].eW){var N=w(M-1,r);return N?N+1:0}return 0}function x(r,M){return M.i&&M.iR.test(r)}function L(O,P){var N=[];for(var M=0;M<O.c.length;M++){N.push(O.c[M].b)}var r=p.length-1;do{if(p[r].e){N.push(p[r].e)}r--}while(p[r+1].eW);if(O.i){N.push(O.i)}return N.length?f(P,N.join("|"),true):null}function q(N,M){var O=p[p.length-1];if(O.t===undefined){O.t=L(O,F)}var r;if(O.t){O.t.lastIndex=M;r=O.t.exec(N)}return r?[N.substr(M,r.index-M),r[0],false]:[N.substr(M),"",true]}function A(O,r){var M=F.cI?r[0].toLowerCase():r[0];var N=O.k[M];if(N&&N instanceof Array){return N}return false}function G(M,Q){M=m(M);if(!Q.k){return M}var r="";var P=0;Q.lR.lastIndex=0;var N=Q.lR.exec(M);while(N){r+=M.substr(P,N.index-P);var O=A(Q,N);if(O){y+=O[1];r+='<span class="'+O[0]+'">'+N[0]+"</span>"}else{r+=N[0]}P=Q.lR.lastIndex;N=Q.lR.exec(M)}return r+M.substr(P)}function B(M,N){var r;if(N.sL==""){r=g(M)}else{r=d(N.sL,M)}if(N.r>0){y+=r.keyword_count;C+=r.r}return'<span class="'+r.language+'">'+r.value+"</span>"}function K(r,M){if(M.sL&&e[M.sL]||M.sL==""){return B(r,M)}else{return G(r,M)}}function J(N,r){var M=N.cN?'<span class="'+N.cN+'">':"";if(N.rB){z+=M;N.buffer=""}else{if(N.eB){z+=m(r)+M;N.buffer=""}else{z+=M;N.buffer=r}}p.push(N);C+=N.r}function H(O,N,R){var S=p[p.length-1];if(R){z+=K(S.buffer+O,S);return false}var Q=s(N,S);if(Q){z+=K(S.buffer+O,S);J(Q,N);return Q.rB}var M=w(p.length-1,N);if(M){var P=S.cN?"</span>":"";if(S.rE){z+=K(S.buffer+O,S)+P}else{if(S.eE){z+=K(S.buffer+O,S)+P+m(N)}else{z+=K(S.buffer+O+N,S)+P}}while(M>1){P=p[p.length-2].cN?"</span>":"";z+=P;M--;p.length--}var r=p[p.length-1];p.length--;p[p.length-1].buffer="";if(r.starts){J(r.starts,"")}return S.rE}if(x(N,S)){throw"Illegal"}}var F=e[D];var p=[F.dM];var C=0;var y=0;var z="";try{var t,v=0;F.dM.buffer="";do{t=q(E,v);var u=H(t[0],t[1],t[2]);v+=t[0].length;if(!u){v+=t[1].length}}while(!t[2]);return{r:C,keyword_count:y,value:z,language:D}}catch(I){if(I=="Illegal"){return{r:0,keyword_count:0,value:m(E)}}else{throw I}}}function g(t){var p={keyword_count:0,r:0,value:m(t)};var r=p;for(var q in e){if(!e.hasOwnProperty(q)){continue}var s=d(q,t);s.language=q;if(s.keyword_count+s.r>r.keyword_count+r.r){r=s}if(s.keyword_count+s.r>p.keyword_count+p.r){r=p;p=s}}if(r.language){p.second_best=r}return p}function i(r,q,p){if(q){r=r.replace(/^((<[^>]+>|\t)+)/gm,function(t,w,v,u){return w.replace(/\t/g,q)})}if(p){r=r.replace(/\n/g,"<br>")}return r}function n(t,w,r){var x=h(t,r);var v=a(t);var y,s;if(v=="no-highlight"){return}if(v){y=d(v,x)}else{y=g(x);v=y.language}var q=c(t);if(q.length){s=document.createElement("pre");s.innerHTML=y.value;y.value=k(q,c(s),x)}y.value=i(y.value,w,r);var u=t.className;if(!u.match("(\\s|^)(language-)?"+v+"(\\s|$)")){u=u?(u+" "+v):v}if(/MSIE [678]/.test(navigator.userAgent)&&t.tagName=="CODE"&&t.parentNode.tagName=="PRE"){s=t.parentNode;var p=document.createElement("div");p.innerHTML="<pre><code>"+y.value+"</code></pre>";t=p.firstChild.firstChild;p.firstChild.cN=s.cN;s.parentNode.replaceChild(p.firstChild,s)}else{t.innerHTML=y.value}t.className=u;t.result={language:v,kw:y.keyword_count,re:y.r};if(y.second_best){t.second_best={language:y.second_best.language,kw:y.second_best.keyword_count,re:y.second_best.r}}}function o(){if(o.called){return}o.called=true;var r=document.getElementsByTagName("pre");for(var p=0;p<r.length;p++){var q=b(r[p]);if(q){n(q,hljs.tabReplace)}}}function l(){if(window.addEventListener){window.addEventListener("DOMContentLoaded",o,false);window.addEventListener("load",o,false)}else{if(window.attachEvent){window.attachEvent("onload",o)}else{window.onload=o}}}var e={};this.LANGUAGES=e;this.highlight=d;this.highlightAuto=g;this.fixMarkup=i;this.highlightBlock=n;this.initHighlighting=o;this.initHighlightingOnLoad=l;this.IR="[a-zA-Z][a-zA-Z0-9_]*";this.UIR="[a-zA-Z_][a-zA-Z0-9_]*";this.NR="\\b\\d+(\\.\\d+)?";this.CNR="\\b(0[xX][a-fA-F0-9]+|(\\d+(\\.\\d*)?|\\.\\d+)([eE][-+]?\\d+)?)";this.BNR="\\b(0b[01]+)";this.RSR="!|!=|!==|%|%=|&|&&|&=|\\*|\\*=|\\+|\\+=|,|\\.|-|-=|/|/=|:|;|<|<<|<<=|<=|=|==|===|>|>=|>>|>>=|>>>|>>>=|\\?|\\[|\\{|\\(|\\^|\\^=|\\||\\|=|\\|\\||~";this.BE={b:"\\\\.",r:0};this.ASM={cN:"string",b:"'",e:"'",i:"\\n",c:[this.BE],r:0};this.QSM={cN:"string",b:'"',e:'"',i:"\\n",c:[this.BE],r:0};this.CLCM={cN:"comment",b:"//",e:"$"};this.CBLCLM={cN:"comment",b:"/\\*",e:"\\*/"};this.HCM={cN:"comment",b:"#",e:"$"};this.NM={cN:"number",b:this.NR,r:0};this.CNM={cN:"number",b:this.CNR,r:0};this.BNM={cN:"number",b:this.BNR,r:0};this.inherit=function(r,s){var p={};for(var q in r){p[q]=r[q]}if(s){for(var q in s){p[q]=s[q]}}return p}}();hljs.LANGUAGES.bash=function(a){var f="true false";var c={cN:"variable",b:"\\$([a-zA-Z0-9_]+)\\b"};var b={cN:"variable",b:"\\$\\{(([^}])|(\\\\}))+\\}",c:[a.CNM]};var g={cN:"string",b:'"',e:'"',i:"\\n",c:[a.BE,c,b],r:0};var d={cN:"string",b:"'",e:"'",c:[{b:"''"}],r:0};var e={cN:"test_condition",b:"",e:"",c:[g,d,c,b,a.CNM],k:{literal:f},r:0};return{dM:{k:{keyword:"if then else fi for break continue while in do done echo exit return set declare",literal:f},c:[{cN:"shebang",b:"(#!\\/bin\\/bash)|(#!\\/bin\\/sh)",r:10},c,b,a.HCM,a.CNM,g,d,a.inherit(e,{b:"\\[ ",e:" \\]",r:0}),a.inherit(e,{b:"\\[\\[ ",e:" \\]\\]"})]}}}(hljs);hljs.LANGUAGES.cs=function(a){return{dM:{k:"abstract as base bool break byte case catch char checked class const continue decimal default delegate do double else enum event explicit extern false finally fixed float for foreach goto if implicit in int interface internal is lock long namespace new null object operator out override params private protected public readonly ref return sbyte sealed short sizeof stackalloc static string struct switch this throw true try typeof uint ulong unchecked unsafe ushort using virtual volatile void while ascending descending from get group into join let orderby partial select set value var where yield",c:[{cN:"comment",b:"///",e:"$",rB:true,c:[{cN:"xmlDocTag",b:"///|<!--|-->"},{cN:"xmlDocTag",b:"</?",e:">"}]},a.CLCM,a.CBLCLM,{cN:"preprocessor",b:"#",e:"$",k:"if else elif endif define undef warning error line region endregion pragma checksum"},{cN:"string",b:'@"',e:'"',c:[{b:'""'}]},a.ASM,a.QSM,a.CNM]}}}(hljs);hljs.LANGUAGES.ruby=function(e){var a="[a-zA-Z_][a-zA-Z0-9_]*(\\!|\\?)?";var k="[a-zA-Z_]\\w*[!?=]?|[-+~]\\@|<<|>>|=~|===?|<=>|[<>]=?|\\*\\*|[-/+%^&*~`|]|\\[\\]=?";var g={keyword:"and false then defined module in return redo if BEGIN retry end for true self when next until do begin unless END rescue nil else break undef not super class case require yield alias while ensure elsif or def",keymethods:"__id__ __send__ abort abs all? allocate ancestors any? arity assoc at at_exit autoload autoload? between? binding binmode block_given? call callcc caller capitalize capitalize! casecmp catch ceil center chomp chomp! chop chop! chr class class_eval class_variable_defined? class_variables clear clone close close_read close_write closed? coerce collect collect! compact compact! concat const_defined? const_get const_missing const_set constants count crypt default default_proc delete delete! delete_at delete_if detect display div divmod downcase downcase! downto dump dup each each_byte each_index each_key each_line each_pair each_value each_with_index empty? entries eof eof? eql? equal? eval exec exit exit! extend fail fcntl fetch fileno fill find find_all first flatten flatten! floor flush for_fd foreach fork format freeze frozen? fsync getc gets global_variables grep gsub gsub! has_key? has_value? hash hex id include include? included_modules index indexes indices induced_from inject insert inspect instance_eval instance_method instance_methods instance_of? instance_variable_defined? instance_variable_get instance_variable_set instance_variables integer? intern invert ioctl is_a? isatty iterator? join key? keys kind_of? lambda last length lineno ljust load local_variables loop lstrip lstrip! map map! match max member? merge merge! method method_defined? method_missing methods min module_eval modulo name nesting new next next! nil? nitems nonzero? object_id oct open pack partition pid pipe pop popen pos prec prec_f prec_i print printf private_class_method private_instance_methods private_method_defined? private_methods proc protected_instance_methods protected_method_defined? protected_methods public_class_method public_instance_methods public_method_defined? public_methods push putc puts quo raise rand rassoc read read_nonblock readchar readline readlines readpartial rehash reject reject! remainder reopen replace require respond_to? reverse reverse! reverse_each rewind rindex rjust round rstrip rstrip! scan seek select send set_trace_func shift singleton_method_added singleton_methods size sleep slice slice! sort sort! sort_by split sprintf squeeze squeeze! srand stat step store strip strip! sub sub! succ succ! sum superclass swapcase swapcase! sync syscall sysopen sysread sysseek system syswrite taint tainted? tell test throw times to_a to_ary to_f to_hash to_i to_int to_io to_proc to_s to_str to_sym tr tr! tr_s tr_s! trace_var transpose trap truncate tty? type ungetc uniq uniq! unpack unshift untaint untrace_var upcase upcase! update upto value? values values_at warn write write_nonblock zero? zip"};var c={cN:"yardoctag",b:"@[A-Za-z]+"};var l=[{cN:"comment",b:"#",e:"$",c:[c]},{cN:"comment",b:"^\\=begin",e:"^\\=end",c:[c],r:10},{cN:"comment",b:"^__END__",e:"\\n$"}];var d={cN:"subst",b:"#\\{",e:"}",l:a,k:g};var j=[e.BE,d];var b=[{cN:"string",b:"'",e:"'",c:j,r:0},{cN:"string",b:'"',e:'"',c:j,r:0},{cN:"string",b:"%[qw]?\\(",e:"\\)",c:j},{cN:"string",b:"%[qw]?\\[",e:"\\]",c:j},{cN:"string",b:"%[qw]?{",e:"}",c:j},{cN:"string",b:"%[qw]?<",e:">",c:j,r:10},{cN:"string",b:"%[qw]?/",e:"/",c:j,r:10},{cN:"string",b:"%[qw]?%",e:"%",c:j,r:10},{cN:"string",b:"%[qw]?-",e:"-",c:j,r:10},{cN:"string",b:"%[qw]?\\|",e:"\\|",c:j,r:10}];var i={cN:"function",b:"\\bdef\\s+",e:" |$|;",l:a,k:g,c:[{cN:"title",b:k,l:a,k:g},{cN:"params",b:"\\(",e:"\\)",l:a,k:g}].concat(l)};var h={cN:"identifier",b:a,l:a,k:g,r:0};var f=l.concat(b.concat([{cN:"class",bWK:true,e:"$|;",k:"class module",c:[{cN:"title",b:"[A-Za-z_]\\w*(::\\w+)*(\\?|\\!)?",r:0},{cN:"inheritance",b:"<\\s*",c:[{cN:"parent",b:"("+e.IR+"::)?"+e.IR}]}].concat(l)},i,{cN:"constant",b:"(::)?([A-Z]\\w*(::)?)+",r:0},{cN:"symbol",b:":",c:b.concat([h]),r:0},{cN:"number",b:"(\\b0[0-7_]+)|(\\b0x[0-9a-fA-F_]+)|(\\b[1-9][0-9_]*(\\.[0-9_]+)?)|[0_]\\b",r:0},{cN:"number",b:"\\?\\w"},{cN:"variable",b:"(\\$\\W)|((\\$|\\@\\@?)(\\w+))"},h,{b:"("+e.RSR+")\\s*",c:l.concat([{cN:"regexp",b:"/",e:"/[a-z]*",i:"\\n",c:[e.BE]}]),r:0}]));d.c=f;i.c[1].c=f;return{dM:{l:a,k:g,c:f}}}(hljs);hljs.LANGUAGES.diff=function(a){return{cI:true,dM:{c:[{cN:"chunk",b:"^\\@\\@ +\\-\\d+,\\d+ +\\+\\d+,\\d+ +\\@\\@$",r:10},{cN:"chunk",b:"^\\*\\*\\* +\\d+,\\d+ +\\*\\*\\*\\*$",r:10},{cN:"chunk",b:"^\\-\\-\\- +\\d+,\\d+ +\\-\\-\\-\\-$",r:10},{cN:"header",b:"Index: ",e:"$"},{cN:"header",b:"=====",e:"=====$"},{cN:"header",b:"^\\-\\-\\-",e:"$"},{cN:"header",b:"^\\*{3} ",e:"$"},{cN:"header",b:"^\\+\\+\\+",e:"$"},{cN:"header",b:"\\*{5}",e:"\\*{5}$"},{cN:"addition",b:"^\\+",e:"$"},{cN:"deletion",b:"^\\-",e:"$"},{cN:"change",b:"^\\!",e:"$"}]}}}(hljs);hljs.LANGUAGES.javascript=function(a){return{dM:{k:{keyword:"in if for while finally var new function do return void else break catch instanceof with throw case default try this switch continue typeof delete",literal:"true false null undefined NaN Infinity"},c:[a.ASM,a.QSM,a.CLCM,a.CBLCLM,a.CNM,{b:"("+a.RSR+"|\\b(case|return|throw)\\b)\\s*",k:"return throw case",c:[a.CLCM,a.CBLCLM,{cN:"regexp",b:"/",e:"/[gim]*",c:[{b:"\\\\/"}]}],r:0},{cN:"function",bWK:true,e:"{",k:"function",c:[{cN:"title",b:"[A-Za-z$_][0-9A-Za-z$_]*"},{cN:"params",b:"\\(",e:"\\)",c:[a.CLCM,a.CBLCLM],i:"[\"'\\(]"}],i:"\\[|%"}]}}}(hljs);hljs.LANGUAGES.css=function(a){var b={cN:"function",b:a.IR+"\\(",e:"\\)",c:[{eW:true,eE:true,c:[a.NM,a.ASM,a.QSM]}]};return{cI:true,dM:{i:"[=/|']",c:[a.CBLCLM,{cN:"id",b:"\\#[A-Za-z0-9_-]+"},{cN:"class",b:"\\.[A-Za-z0-9_-]+",r:0},{cN:"attr_selector",b:"\\[",e:"\\]",i:"$"},{cN:"pseudo",b:":(:)?[a-zA-Z0-9\\_\\-\\+\\(\\)\\\"\\']+"},{cN:"at_rule",b:"@(font-face|page)",l:"[a-z-]+",k:"font-face page"},{cN:"at_rule",b:"@",e:"[{;]",eE:true,k:"import page media charset",c:[b,a.ASM,a.QSM,a.NM]},{cN:"tag",b:a.IR,r:0},{cN:"rules",b:"{",e:"}",i:"[^\\s]",r:0,c:[a.CBLCLM,{cN:"rule",b:"[^\\s]",rB:true,e:";",eW:true,c:[{cN:"attribute",b:"[A-Z\\_\\.\\-]+",e:":",eE:true,i:"[^\\s]",starts:{cN:"value",eW:true,eE:true,c:[b,a.NM,a.QSM,a.ASM,a.CBLCLM,{cN:"hexcolor",b:"\\#[0-9A-F]+"},{cN:"important",b:"!important"}]}}]}]}]}}}(hljs);hljs.LANGUAGES.xml=function(a){var c="[A-Za-z0-9\\._:-]+";var b={eW:true,c:[{cN:"attribute",b:c,r:0},{b:'="',rB:true,e:'"',c:[{cN:"value",b:'"',eW:true}]},{b:"='",rB:true,e:"'",c:[{cN:"value",b:"'",eW:true}]},{b:"=",c:[{cN:"value",b:"[^\\s/>]+"}]}]};return{cI:true,dM:{c:[{cN:"pi",b:"<\\?",e:"\\?>",r:10},{cN:"doctype",b:"<!DOCTYPE",e:">",r:10,c:[{b:"\\[",e:"\\]"}]},{cN:"comment",b:"<!--",e:"-->",r:10},{cN:"cdata",b:"<\\!\\[CDATA\\[",e:"\\]\\]>",r:10},{cN:"tag",b:"<style(?=\\s|>|$)",e:">",k:{title:"style"},c:[b],starts:{e:"</style>",rE:true,sL:"css"}},{cN:"tag",b:"<script(?=\\s|>|$)",e:">",k:{title:"script"},c:[b],starts:{e:"<\/script>",rE:true,sL:"javascript"}},{b:"<%",e:"%>",sL:"vbscript"},{cN:"tag",b:"</?",e:"/?>",c:[{cN:"title",b:"[^ />]+"},b]}]}}}(hljs);hljs.LANGUAGES.http=function(a){return{dM:{i:"\\S",c:[{cN:"status",b:"^HTTP/[0-9\\.]+",e:"$",c:[{cN:"number",b:"\\b\\d{3}\\b"}]},{cN:"request",b:"^[A-Z]+ (.*?) HTTP/[0-9\\.]+$",rB:true,e:"$",c:[{cN:"string",b:" ",e:" ",eB:true,eE:true}]},{cN:"attribute",b:"^\\w",e:": ",eE:true,i:"\\n",starts:{cN:"string",e:"$"}},{b:"\\n\\n",starts:{sL:"",eW:true}}]}}}(hljs);hljs.LANGUAGES.java=function(a){return{dM:{k:"false synchronized int abstract float private char boolean static null if const for true while long throw strictfp finally protected import native final return void enum else break transient new catch instanceof byte super volatile case assert short package default double public try this switch continue throws",c:[{cN:"javadoc",b:"/\\*\\*",e:"\\*/",c:[{cN:"javadoctag",b:"@[A-Za-z]+"}],r:10},a.CLCM,a.CBLCLM,a.ASM,a.QSM,{cN:"class",bWK:true,e:"{",k:"class interface",i:":",c:[{bWK:true,k:"extends implements",r:10},{cN:"title",b:a.UIR}]},a.CNM,{cN:"annotation",b:"@[A-Za-z]+"}]}}}(hljs);hljs.LANGUAGES.php=function(a){var e={cN:"variable",b:"\\$+[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*"};var b=[a.inherit(a.ASM,{i:null}),a.inherit(a.QSM,{i:null}),{cN:"string",b:'b"',e:'"',c:[a.BE]},{cN:"string",b:"b'",e:"'",c:[a.BE]}];var c=[a.CNM,a.BNM];var d={cN:"title",b:a.UIR};return{cI:true,dM:{k:"and include_once list abstract global private echo interface as static endswitch array null if endwhile or const for endforeach self var while isset public protected exit foreach throw elseif include __FILE__ empty require_once do xor return implements parent clone use __CLASS__ __LINE__ else break print eval new catch __METHOD__ case exception php_user_filter default die require __FUNCTION__ enddeclare final try this switch continue endfor endif declare unset true false namespace trait goto instanceof insteadof __DIR__ __NAMESPACE__ __halt_compiler",c:[a.CLCM,a.HCM,{cN:"comment",b:"/\\*",e:"\\*/",c:[{cN:"phpdoc",b:"\\s@[A-Za-z]+"}]},{cN:"comment",eB:true,b:"__halt_compiler.+?;",eW:true},{cN:"string",b:"<<<['\"]?\\w+['\"]?$",e:"^\\w+;",c:[a.BE]},{cN:"preprocessor",b:"<\\?php",r:10},{cN:"preprocessor",b:"\\?>"},e,{cN:"function",bWK:true,e:"{",k:"function",i:"\\$|\\[|%",c:[d,{cN:"params",b:"\\(",e:"\\)",c:["self",e,a.CBLCLM].concat(b).concat(c)}]},{cN:"class",bWK:true,e:"{",k:"class",i:"[:\\(\\$]",c:[{bWK:true,eW:true,k:"extends",c:[d]},d]},{b:"=>"}].concat(b).concat(c)}}}(hljs);hljs.LANGUAGES.python=function(a){var c=[{cN:"string",b:"(u|b)?r?'''",e:"'''",r:10},{cN:"string",b:'(u|b)?r?"""',e:'"""',r:10},{cN:"string",b:"(u|r|ur)'",e:"'",c:[a.BE],r:10},{cN:"string",b:'(u|r|ur)"',e:'"',c:[a.BE],r:10},{cN:"string",b:"(b|br)'",e:"'",c:[a.BE]},{cN:"string",b:'(b|br)"',e:'"',c:[a.BE]}].concat([a.ASM,a.QSM]);var e={cN:"title",b:a.UIR};var d={cN:"params",b:"\\(",e:"\\)",c:["self",a.CNM].concat(c)};var b={bWK:true,e:":",i:"[${=;\\n]",c:[e,d],r:10};return{dM:{k:{keyword:"and elif is global as in if from raise for except finally print import pass return exec else break not with class assert yield try while continue del or def lambda nonlocal|10",built_in:"None True False Ellipsis NotImplemented"},i:"(</|->|\\?)",c:c.concat([a.HCM,a.inherit(b,{cN:"function",k:"def"}),a.inherit(b,{cN:"class",k:"class"}),a.CNM,{cN:"decorator",b:"@",e:"$"},{b:"\\b(print|exec)\\("}])}}}(hljs);hljs.LANGUAGES.sql=function(a){return{cI:true,dM:{i:"[^\\s]",c:[{cN:"operator",b:"(begin|start|commit|rollback|savepoint|lock|alter|create|drop|rename|call|delete|do|handler|insert|load|replace|select|truncate|update|set|show|pragma|grant)\\b",e:";",eW:true,k:{keyword:"all partial global month current_timestamp using go revoke smallint indicator end-exec disconnect zone with character assertion to add current_user usage input local alter match collate real then rollback get read timestamp session_user not integer bit unique day minute desc insert execute like ilike|2 level decimal drop continue isolation found where constraints domain right national some module transaction relative second connect escape close system_user for deferred section cast current sqlstate allocate intersect deallocate numeric public preserve full goto initially asc no key output collation group by union session both last language constraint column of space foreign deferrable prior connection unknown action commit view or first into float year primary cascaded except restrict set references names table outer open select size are rows from prepare distinct leading create only next inner authorization schema corresponding option declare precision immediate else timezone_minute external varying translation true case exception join hour default double scroll value cursor descriptor values dec fetch procedure delete and false int is describe char as at in varchar null trailing any absolute current_time end grant privileges when cross check write current_date pad begin temporary exec time update catalog user sql date on identity timezone_hour natural whenever interval work order cascade diagnostics nchar having left call do handler load replace truncate start lock show pragma",aggregate:"count sum min max avg"},c:[{cN:"string",b:"'",e:"'",c:[a.BE,{b:"''"}],r:0},{cN:"string",b:'"',e:'"',c:[a.BE,{b:'""'}],r:0},{cN:"string",b:"`",e:"`",c:[a.BE]},a.CNM]},a.CBLCLM,{cN:"comment",b:"--",e:"$"}]}}}(hljs);hljs.LANGUAGES.ini=function(a){return{cI:true,dM:{i:"[^\\s]",c:[{cN:"comment",b:";",e:"$"},{cN:"title",b:"^\\[",e:"\\]"},{cN:"setting",b:"^[a-z0-9_\\[\\]]+[ \\t]*=[ \\t]*",e:"$",c:[{cN:"value",eW:true,k:"on off true false yes no",c:[a.QSM,a.NM]}]}]}}}(hljs);hljs.LANGUAGES.perl=function(e){var a="getpwent getservent quotemeta msgrcv scalar kill dbmclose undef lc ma syswrite tr send umask sysopen shmwrite vec qx utime local oct semctl localtime readpipe do return format read sprintf dbmopen pop getpgrp not getpwnam rewinddir qqfileno qw endprotoent wait sethostent bless s|0 opendir continue each sleep endgrent shutdown dump chomp connect getsockname die socketpair close flock exists index shmgetsub for endpwent redo lstat msgctl setpgrp abs exit select print ref gethostbyaddr unshift fcntl syscall goto getnetbyaddr join gmtime symlink semget splice x|0 getpeername recv log setsockopt cos last reverse gethostbyname getgrnam study formline endhostent times chop length gethostent getnetent pack getprotoent getservbyname rand mkdir pos chmod y|0 substr endnetent printf next open msgsnd readdir use unlink getsockopt getpriority rindex wantarray hex system getservbyport endservent int chr untie rmdir prototype tell listen fork shmread ucfirst setprotoent else sysseek link getgrgid shmctl waitpid unpack getnetbyname reset chdir grep split require caller lcfirst until warn while values shift telldir getpwuid my getprotobynumber delete and sort uc defined srand accept package seekdir getprotobyname semop our rename seek if q|0 chroot sysread setpwent no crypt getc chown sqrt write setnetent setpriority foreach tie sin msgget map stat getlogin unless elsif truncate exec keys glob tied closedirioctl socket readlink eval xor readline binmode setservent eof ord bind alarm pipe atan2 getgrent exp time push setgrent gt lt or ne m|0";var d={cN:"subst",b:"[$@]\\{",e:"\\}",k:a,r:10};var b={cN:"variable",b:"\\$\\d"};var i={cN:"variable",b:"[\\$\\%\\@\\*](\\^\\w\\b|#\\w+(\\:\\:\\w+)*|[^\\s\\w{]|{\\w+}|\\w+(\\:\\:\\w*)*)"};var f=[e.BE,d,b,i];var h={b:"->",c:[{b:e.IR},{b:"{",e:"}"}]};var g={cN:"comment",b:"^(__END__|__DATA__)",e:"\\n$",r:5};var c=[b,i,e.HCM,g,{cN:"comment",b:"^\\=\\w",e:"\\=cut",eW:true},h,{cN:"string",b:"q[qwxr]?\\s*\\(",e:"\\)",c:f,r:5},{cN:"string",b:"q[qwxr]?\\s*\\[",e:"\\]",c:f,r:5},{cN:"string",b:"q[qwxr]?\\s*\\{",e:"\\}",c:f,r:5},{cN:"string",b:"q[qwxr]?\\s*\\|",e:"\\|",c:f,r:5},{cN:"string",b:"q[qwxr]?\\s*\\<",e:"\\>",c:f,r:5},{cN:"string",b:"qw\\s+q",e:"q",c:f,r:5},{cN:"string",b:"'",e:"'",c:[e.BE],r:0},{cN:"string",b:'"',e:'"',c:f,r:0},{cN:"string",b:"`",e:"`",c:[e.BE]},{cN:"string",b:"{\\w+}",r:0},{cN:"string",b:"-?\\w+\\s*\\=\\>",r:0},{cN:"number",b:"(\\b0[0-7_]+)|(\\b0x[0-9a-fA-F_]+)|(\\b[1-9][0-9_]*(\\.[0-9_]+)?)|[0_]\\b",r:0},{b:"("+e.RSR+"|\\b(split|return|print|reverse|grep)\\b)\\s*",k:"split return print reverse grep",r:0,c:[e.HCM,g,{cN:"regexp",b:"(s|tr|y)/(\\\\.|[^/])*/(\\\\.|[^/])*/[a-z]*",r:10},{cN:"regexp",b:"(m|qr)?/",e:"/[a-z]*",c:[e.BE],r:0}]},{cN:"sub",bWK:true,e:"(\\s*\\(.*?\\))?[;{]",k:"sub",r:5},{cN:"operator",b:"-\\w\\b",r:0}];d.c=c;h.c[1].c=c;return{dM:{k:a,c:c}}}(hljs);hljs.LANGUAGES.json=function(a){var e={literal:"true false null"};var d=[a.QSM,a.CNM];var c={cN:"value",e:",",eW:true,eE:true,c:d,k:e};var b={b:"{",e:"}",c:[{cN:"attribute",b:'\\s*"',e:'"\\s*:\\s*',eB:true,eE:true,c:[a.BE],i:"\\n",starts:c}],i:"\\S"};var f={b:"\\[",e:"\\]",c:[a.inherit(c,{cN:null})],i:"\\S"};d.splice(d.length,0,b,f);return{dM:{c:d,k:e,i:"\\S"}}}(hljs);hljs.LANGUAGES.cpp=function(a){var b={keyword:"false int float while private char catch export virtual operator sizeof dynamic_cast|10 typedef const_cast|10 const struct for static_cast|10 union namespace unsigned long throw volatile static protected bool template mutable if public friend do return goto auto void enum else break new extern using true class asm case typeid short reinterpret_cast|10 default double register explicit signed typename try this switch continue wchar_t inline delete alignof char16_t char32_t constexpr decltype noexcept nullptr static_assert thread_local restrict _Bool complex",built_in:"std string cin cout cerr clog stringstream istringstream ostringstream auto_ptr deque list queue stack vector map set bitset multiset multimap unordered_set unordered_map unordered_multiset unordered_multimap array shared_ptr"};return{dM:{k:b,i:"</",c:[a.CLCM,a.CBLCLM,a.QSM,{cN:"string",b:"'\\\\?.",e:"'",i:"."},{cN:"number",b:"\\b(\\d+(\\.\\d*)?|\\.\\d+)(u|U|l|L|ul|UL|f|F)"},a.CNM,{cN:"preprocessor",b:"#",e:"$"},{cN:"stl_container",b:"\\b(deque|list|queue|stack|vector|map|set|bitset|multiset|multimap|unordered_map|unordered_set|unordered_multiset|unordered_multimap|array)\\s*<",e:">",k:b,r:10,c:["self"]}]}}}(hljs);

	window.hljs = hljs;

	module.resolve();
});

Komento.module('admin.language', function($) {
	var module = this;

	Komento.require()
		.language(
			'COM_KOMENTO_CONFIRM_DELETE_AFFECT_ALL_CHILD',
			'COM_KOMENTO_CONFIRM_DELETE',
			'COM_KOMENTO_DELETE_COMMENT',
			'COM_KOMENTO_DELETE_ALL_CHILD',
			'COM_KOMENTO_DELETE_MOVE_CHILD_UP',
			'COM_KOMENTO_DELETING',
			'COM_KOMENTO_CONFIRM_PUBLISH_AFFECT_ALL_CHILD',
			'COM_KOMENTO_PUBLISH_ALL_CHILD',
			'COM_KOMENTO_PUBLISH_SINGLE',
			'COM_KOMENTO_CHILD_UNPUBLISHED',
			'COM_KOMENTO_PARENT_PUBLISHED',
			'COM_KOMENTO_MIGRATORS_LOG_COMPLETE',
			'COM_KOMENTO_MIGRATORS_PROGRESS_DONE',
			'COM_KOMENTO_YES_OPTION',
			'COM_KOMENTO_NO_OPTION',
			'COM_KOMENTO_ACL_RECOMMENDED',
			'COM_KOMENTO_PUBLISH_ITEM',
			'COM_KOMENTO_UNPUBLISH_ITEM'
		)
		.done(function() {
			module.resolve();
		});
});

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


Komento.module('admin.report.actions', function($) {
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

				// for joomla 3.4.3
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

		clear: function() {
			submitform('clear');
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

Komento.module('komento.admincomments', function($) {
	var module = this;

	Komento.require()
		.library(
			'dialog'
		)
		.done(function() {
			Komento.Controller('AdminComments',
			{
				defaults: {

				}
			},
			function(self)
			{ return {
				init: function()
				{

				}
			} });

			module.resolve();
		});
});

Komento.module('komento.bbcode', function($) {
	var module = this;

	$.getBBcodeSettings = function() {
		var settings = {
			previewParserVar: 'data',
			markupSet: []
		};

		if(Komento.options.config.bbcode_bold == 1) {
			settings.markupSet.push({
				name: $.language('COM_KOMENTO_BBCODE_BOLD'),
				key:'B',
				openWith:'[b]',
				closeWith:'[/b]',
				className:'kmt-markitup-bold'
			});
		}

		if(Komento.options.config.bbcode_italic == 1) {
			settings.markupSet.push({
				name: $.language('COM_KOMENTO_BBCODE_ITALIC'),
				key:'I',
				openWith:'[i]',
				closeWith:'[/i]',
				className:'kmt-markitup-italic'
			});
		}

		if(Komento.options.config.bbcode_underline == 1) {
			settings.markupSet.push({
				name: $.language('COM_KOMENTO_BBCODE_UNDERLINE'),
				key:'U',
				openWith:'[u]',
				closeWith:'[/u]',
				className:'kmt-markitup-underline'
			});
		}

		if(Komento.options.config.bbcode_bold == 1 || Komento.options.config.bbcode_italic == 1 || Komento.options.config.bbcode_underline == 1) {
			settings.markupSet.push({separator:'---------------' });
		}

		if(Komento.options.config.bbcode_link == 1) {
			settings.markupSet.push({
				name: $.language('COM_KOMENTO_BBCODE_LINK'),
				key:'L',
				openWith:'[url="[![Link:!:http://]!]"(!( title="[![Title]!]")!)]', closeWith:'[/url]',
				placeHolder: $.language('COM_KOMENTO_BBCODE_LINK_TEXT'),
				className:'kmt-markitup-link'
			});
		}

		if(Komento.options.config.bbcode_picture == 1) {
			settings.markupSet.push({
				name: $.language('COM_KOMENTO_BBCODE_PICTURE'),
				key:'P',
				replaceWith:'[img][![Url]!][/img]',
				className:'kmt-markitup-picture'
			});
		}

		if(Komento.options.config.bbcode_video == 1) {
			settings.markupSet.push({
				name: $.language('COM_KOMENTO_BBCODE_VIDEO'),
				// replaceWith: '[video][![' + $.language('COM_KOMENTO_SUPPORTED_VIDEOS') + ']!][/video]',
				replaceWith: function(h) {
					Komento.ajax('site.views.komento.showVideoDialog', {
						caretPosition: h.caretPosition,
						element: $(h.textarea).attr('id')
					}, {
						success: function(html) {
							Komento.require().library('dialog').done(function() {
								$.dialog({
									content: html,
									title: $.language( 'COM_KOMENTO_INSERT_VIDEO' ),
									width: 400,
									afterShow: function() {
										$('.foundryDialog').find('.videoUrl').focus();
									}
								});
							});
						}
					});
				},
				className: 'kmt-markitup-video'
			});
		}

		if(Komento.options.config.bbcode_link == 1 || Komento.options.config.bbcode_picture == 1) {
			settings.markupSet.push({separator:'---------------' });
		}

		if(Komento.options.config.bbcode_bulletlist == 1) {
			settings.markupSet.push({
				name: $.language('COM_KOMENTO_BBCODE_BULLETLIST'),
				openWith:'[list]\n',
				closeWith:'\n[/list]',
				className:'kmt-markitup-bullet'
			});
		}

		if(Komento.options.config.bbcode_numericlist == 1) {
			settings.markupSet.push({
				name: $.language('COM_KOMENTO_BBCODE_NUMERICLIST'),
				openWith:'[list=[![Starting number]!]]\n',
				closeWith:'\n[/list]',
				className:'kmt-markitup-numeric'
			});
		}

		if(Komento.options.config.bbcode_bullet == 1) {
			settings.markupSet.push({
				name: $.language('COM_KOMENTO_BBCODE_BULLET'),
				openWith:'[*]',
				closeWith:'[/*]',
				className:'kmt-markitup-list'
			});
		}

		if(Komento.options.config.bbcode_bulletlist == 1 || Komento.options.config.bbcode_numericlist == 1 || Komento.options.config.bbcode_bullet == 1) {
			settings.markupSet.push({separator:'---------------' });
		}

		if(Komento.options.config.bbcode_quote == 1) {
			settings.markupSet.push({
				name: $.language('COM_KOMENTO_BBCODE_QUOTE'),
				openWith:'[quote]',
				closeWith:'[/quote]',
				className:'kmt-markitup-quote'
			});
		}

		if(Komento.options.config.bbcode_code == 1) {
			settings.markupSet.push({
				name: $.language('COM_KOMENTO_BBCODE_CODE'),
				openWith:'[code type="xml"]',
				closeWith:'[/code]',
				className:'kmt-markitup-code'
			});
		}

		if(Komento.options.config.bbcode_clean == 1) {
			settings.markupSet.push({
				name: $.language('COM_KOMENTO_BBCODE_CLEAN'),
				className:"clean", replaceWith:function(markitup) { return markitup.selection.replace(/\[(.*?)\]/g, "") },
				className:'kmt-markitup-clean'
			});
		}

		if(Komento.options.config.bbcode_quote == 1 || Komento.options.config.bbcode_code || Komento.options.config.bbcode_clean == 1) {
			settings.markupSet.push({separator:'---------------' });
		}

		if(Komento.options.config.bbcode_smile == 1) {
			settings.markupSet.push({
				name: $.language('COM_KOMENTO_BBCODE_SMILE'),
				openWith:':)',
				className:'kmt-markitup-smile'
			});
		}

		if(Komento.options.config.bbcode_happy == 1) {
			settings.markupSet.push({
				name: $.language('COM_KOMENTO_BBCODE_HAPPY'),
				openWith:':D',
				className:'kmt-markitup-happy'
			});
		}

		if(Komento.options.config.bbcode_surprised == 1) {
			settings.markupSet.push({
				name: $.language('COM_KOMENTO_BBCODE_SURPRISED'),
				openWith:':o',
				className:'kmt-markitup-surprised'
			});
		}

		if(Komento.options.config.bbcode_tongue == 1) {
			settings.markupSet.push({
				name: $.language('COM_KOMENTO_BBCODE_TONGUE'),
				openWith:':p',
				className:'kmt-markitup-tongue'
			});
		}

		if(Komento.options.config.bbcode_unhappy == 1) {
			settings.markupSet.push({
				name: $.language('COM_KOMENTO_BBCODE_UNHAPPY'),
				openWith:':(',
				className:'kmt-markitup-unhappy'
			});
		}

		if(Komento.options.config.bbcode_wink == 1) {
			settings.markupSet.push({
				name: $.language('COM_KOMENTO_BBCODE_WINK'),
				openWith:';)',
				className:'kmt-markitup-wink'
			});
		}

		if($.isArray(Komento.options.config.smileycode)) {
			$.each(Komento.options.config.smileycode, function(index, code) {
				settings.markupSet.push({
					name: code,
					openWith: code,
					className: 'kmt-markitup-custom-' + index
				});
			});
		}

		return settings;
	};

	module.resolve();
});

Komento.module('komento.commentform', function($) {
var module = this;

var req = Komento.require();
var scriptsToLoad = new Array();

scriptsToLoad.push('komento.common');
scriptsToLoad.push('komento.upload');

if(Komento.options.config.enable_bbcode == 1) {
	// scriptsToLoad.push('markitup');
	scriptsToLoad.push('komento.bbcode');
}

if(Komento.options.config.show_location == 1) {
	scriptsToLoad.push('location');
}

// dynamic scripts loading
req.script.apply(req, scriptsToLoad).image(Komento.options.spinner).library('dialog', 'markitup').done(function() {
Komento.Controller(
	'CommentForm',
	{
		defaults: {
			'{addCommentButton}'		: '.addCommentButton',
			'{formArea}'				: '.formArea',
			'{commentInput}'			: '#commentInput',
			'{markItUpButtons}'			: '.markItUpButton a',
			'{submitButton}'			: '.submitButton',
			'{parentId}'				: 'input[name="parent"]',
			'{formAuthor}'				: '.formAuthor',
			'{usernameInput}'			: '#register-username',
			'{nameInput}'				: '#register-name',
			'{emailInput}'				: '#register-email',
			'{websiteInput}'			: '#register-website',
			'{registerCheckbox}'		: '#register-checkbox',
			'{tncCheckbox}'				: '.tncCheckbox',
			'{ratings}'					: '[data-rating-value]',
			'{tncRead}'					: '.tncRead',
			'{subscribeForm}'			: '.subscribeForm',
			'{subscribeCheckbox}'		: '.subscribeCheckbox',
			'{unsubscribeButton}'		: '.unsubscribeButton',
			'{locationForm}'			: '.locationForm',
			'{formAlert}'				: '.formAlert',
			'{recaptchaResponse}'		: '#g-recaptcha-response',
			'{captchaImage}'			: '#captcha-image',
			'{captchaResponse}'			: '#captcha-response',
			'{captchaId}'				: '#captcha-id',
			'{captchaReload}'			: '.kmt-captcha-reload',
			'{locationInput}'			: '.locationInput',
			'{locationLatitude}'		: '.locationLatitude',
			'{locationLongitude}'		: '.locationLongitude',
			'{parentLink}'				: '.parentLink',
			'{parentContainer}'			: '.parentContainer',
			'{cancelStaticReplyButton}'	: '.cancelStaticReply',
			'{commentLength}'			: '.commentLength',
			'{commentLengthCount}'		: '.commentLengthCount',
			'{uploaderWrap}'			: '.uploaderWrap',
			'{pageItemId}'				: '.pageItemId',
		}
	},
	function(self)
	{ return {
		init: function() {
			// initialise parent id = 0
			//self.parentId().val(0);
			self.parentid = 0;

			// initialise comment input to empty
			self.commentInput().val('');

			// initialise bbcode
			if(Komento.options.config.enable_bbcode == 1) {
				self.commentInput().markItUp($.getBBcodeSettings());

				if($.isArray(Komento.options.config.smileycode)) {
					$.each(Komento.options.config.smileycode, function(index, code) {
						var selector = '.kmt-markitup-custom-' + index + ' a',
							path = Komento.options.config.smileypath[index];

						$.cssRule(selector, {
							"background-image": "url('" + path + "') !important"
						});
					});
				}
			}

			// initialise location map
			if(Komento.options.config.show_location == 1 && self.locationForm().exists()) {
				self.locationForm().implement('Komento.Controller.Location.Form.Simple');
			}

			// initialise plupload
			if(Komento.options.config.upload_enable == 1 && Komento.options.acl.upload_attachment == 1 && self.uploaderWrap().exists()) {
				Komento.options.element.commentupload = $('.uploaderWrap').addController('Komento.Controller.UploadForm');
				Komento.options.element.commentupload.kmt = Komento.options.element;
			}
		},

		"{commentInput} textChange" :function(el) {
			self.commentLengthCheck();
			self.experimentalValidateComment();
		},

		"{commentInput} keyup" :function(el) {
			self.commentLengthCheck();
			self.experimentalValidateComment();
		},

		"{nameInput} keyup": function() {
			self.experimentalValidateComment();
		},

		"{emailInput} keyup": function() {
			self.experimentalValidateComment();
		},

		"{websiteInput} keyup": function() {
			self.experimentalValidateComment();
		},

		"{subscribeCheckbox} click": function() {
			self.experimentalValidateComment();
		},

		"{tncCheckbox} click": function() {
			self.experimentalValidateComment();
		},

		"{captchaResponse} keyup": function() {
			self.experimentalValidateComment();
		},

		"{recaptchaResponse} keyup": function() {
			self.experimentalValidateComment();
		},

		"{tncRead} click": function() {
			var content = '<p>' + Komento.options.config.tnc_text.replace(/\n/g, "<br />") + '</p>';

			$.dialog({
				title: $.language( 'COM_KOMENTO_FORM_TNC' ),
				customClass: 'kmt-dialog',
				width: 470,
				showOverlay: false,
				content: content
			});
		},

		"{submitButton} click": function(el) {
			if(el.checkClick()) {
				el.html('<img src="' + Komento.options.spinner + '" />');
			}

			self.validateComment();
		},

		"{unsubscribeButton} click": function(el) {
			if(el.checkClick()) {
				el.html('<img src="' + Komento.options.spinner + '" />');

				self.unsubscribe();
			}
		},

		"{captchaReload} click": function() {
			self.reloadCaptcha();
		},

		"{parentLink} mouseover": function() {
			self.parentContainer().show();
		},

		"{parentLink} mouseout": function() {
			self.parentContainer().hide();
		},

		"{cancelStaticReplyButton} click": function() {
			self.cancelStaticReply();
		},

		"{addCommentButton} click": function() {
			self.loadForm();
		},

		loadForm: function()
		{
			if( Komento.options.config.form_toggle_button == 1 )
			{
				self.addCommentButton().hide();
				self.formArea().show();

				// Only refresh when upload is enabled.
				if( Komento.options.config.enable_upload == 1 && self.kmt.commentupload )
				{
					// manual refresh because element was not rendered before, hence causing upload button to not work on IE
					self.kmt.commentupload.plupload.refresh();
				}
			}
		},

		hideForm: function()
		{
			if( Komento.options.config.form_toggle_button == 1 )
			{
				self.addCommentButton().show();
				self.formArea().hide();
			}
		},

		/*wysiwygSave: function(strip, trim) {
			if(strip === undefined) strip = true;
			if(trim === undefined) trim = true;

			if(Komento.options.config.form_editor != 'bbcode' && Komento.options.config.form_editor != 'none') {
				window.saveContent();
				var text = window.getContent();

				if(strip) {
					text = $('<div>').html(text).text();
				}

				if( trim ) {
					text = $.trim(text);
				}

				self.commentInput().val(text);
			}
		},*/

		commentLengthCheck: function() {

			// self.wysiwygSave();

			if(Komento.options.config.antispam_max_length_enable == 1 && self.commentInput().val().length > Komento.options.config.antispam_max_length) {
				self.commentInput().val(self.commentInput().val().slice(0, Komento.options.config.antispam_max_length));
			}

			self.commentLengthCount().text(self.commentInput().val().length);
		},

		validateComment: function() {
			self.clearNotification();

			if( Komento.options.konfig.enable_js_form_validation == 0 ) {
				if(self.kmt.commentupload) {
					self.kmt.commentupload.startUpload();
				} else {
					self.postComment();
				}
				return;
			}

			// perform a save if editor is wysiwyg
			// self.wysiwygSave();

			// set and trim all fields
			var name = $.trim(self.nameInput().val());
			var email = $.trim(self.emailInput().val());
			var website = $.trim(self.websiteInput().val());
			var comment = $.trim(self.commentInput().val());
			var captcha = $.trim(self.captchaResponse().val());
			var recaptcha = $.trim(self.recaptchaResponse().val());

			var validation = [];

			// validate comment input
			if(comment.length == 0) {
				self.errorNotification($.language('COM_KOMENTO_FORM_NOTIFICATION_COMMENT_REQUIRED'));
				validation.push( '1' );
			}
			else {
				if(Komento.options.config.antispam_min_length_enable == 1 && Komento.options.config.antispam_min_length > 0 && comment.length < Komento.options.config.antispam_min_length)
				{
					self.errorNotification($.language('COM_KOMENTO_FORM_NOTIFICATION_COMMENT_TOO_SHORT'));
					validation.push( '1' );
				}
			}

			// validate captcha input
			if((self.captchaResponse().exists() && captcha.length == 0) || (self.recaptchaResponse().exists() && recaptcha.length == 0)) {
				self.errorNotification($.language('COM_KOMENTO_FORM_NOTIFICATION_CAPTCHA_REQUIRED'));
				validation.push( '1' );
			}

			// validate name field
			if(name.length == 0 && ((Komento.options.config.show_name == 2 && Komento.options.config.require_name == 2) || (Komento.options.guest == 1 && Komento.options.config.show_name > 0 && Komento.options.config.require_name == 1))) {
				if(self.nameInput().siblings('span').length == 0) {
					self.nameInput().after('<span class="help-inline">' + $.language('COM_KOMENTO_FORM_NOTIFICATION_NAME_REQUIRED') + '</span>');
				} else {
					self.nameInput().siblings('span').show();
				}

				self.nameInput().parents('li').addClass('error');
				validation.push( '1' );
			} else {
				self.nameInput().siblings('span').hide();
				self.nameInput().parents('li').removeClass('error');
			}

			// validate email field + subscription checkbox
			self.emailInput().siblings('span').remove();
			if(email.length == 0 && ((Komento.options.config.show_email == 2 && (Komento.options.config.require_email == 2 || self.subscribeCheckbox().prop('checked'))) || (Komento.options.guest == 1 && Komento.options.config.show_email > 0 && (Komento.options.config.require_email == 1 || self.subscribeCheckbox().prop('checked'))))) {
				self.emailInput().after('<span class="help-inline">' + $.language('COM_KOMENTO_FORM_NOTIFICATION_EMAIL_REQUIRED') + '</span>');
				self.emailInput().parents('li').addClass('error');
				validation.push( '1' );
			} else {

				// regex test email
				if(email.length > 0 && !self.validateEmail(email)) {
					self.emailInput().after('<span class="help-inline">' + $.language('COM_KOMENTO_FORM_NOTIFICATION_EMAIL_INVALID') + '</span>');
					self.emailInput().parents('li').addClass('error');
					validation.push( '1' );
				} else {
					self.emailInput().parents('li').removeClass('error');
				}
			}

			// validate website field
			self.websiteInput().siblings('span').remove();
			if(website.length == 0 && ((Komento.options.config.show_website == 2 && Komento.options.config.require_website == 2) || (Komento.options.guest == 1 && Komento.options.config.show_website > 0 && Komento.options.config.require_website == 1))) {
				self.websiteInput().after('<span class="help-inline">' + $.language('COM_KOMENTO_FORM_NOTIFICATION_WEBSITE_REQUIRED') + '</span>');
				self.websiteInput().parents('li').addClass('error');
				validation.push( '1' );
			} else {
				// regex test website
				if(website.length > 0 && !self.validateWebsite(website)) {
					self.websiteInput().after('<span class="help-inline">' + $.language('COM_KOMENTO_FORM_NOTIFICATION_WEBSITE_INVALID') + '</span>');
					self.websiteInput().parents('li').addClass('error');
					validation.push( '1' );
				} else {
					self.websiteInput().parents('li').removeClass('error');
				}
			}

			// validate tnc checkbox
			if(Komento.options.config.show_tnc == 1 && self.tncCheckbox().prop('checked') == 0) {
				self.errorNotification($.language('COM_KOMENTO_FORM_NOTIFICATION_TNC_REQUIRED'));
				validation.push( '1' );
			}

			// validate location field
			if( self.locationLongitude().val() == '' || self.locationLatitude().val() == '' || self.locationInput().val() == '' ) {
				self.locationInput().val('');
			}

			if(validation.length == 0) {
				if(self.kmt.commentupload) {
					self.kmt.commentupload.startUpload();
				} else {
					self.postComment();
				}
			} else {
				if(self.parentid == 0) {
					self.submitButton().text($.language('COM_KOMENTO_FORM_SUBMIT'));
				} else {
					self.submitButton().text($.language('COM_KOMENTO_FORM_REPLY'));
				}
			}
		},

		experimentalValidateComment: function() {
			if( Komento.options.konfig.enable_live_form_validation == 0 ) {
				self.submitButton().enable();
				return true;
			}

			// perform a save if editor is wysiwyg
			// self.wysiwygSave();

			var name = $.trim(self.nameInput().val());
			var email = $.trim(self.emailInput().val());
			var website = $.trim(self.websiteInput().val());
			var comment = $.trim(self.commentInput().val());
			var captcha = $.trim(self.captchaResponse().val());
			var recaptcha = $.trim(self.recaptchaResponse().val());

			var validation = [];

			// validate comment input
			if(self.commentInput().val().length == 0) {
				validation.push( '1' );
			}
			else {
				if(Komento.options.config.antispam_min_length_enable == 1 && Komento.options.config.antispam_min_length > 0 && comment.length < Komento.options.config.antispam_min_comment_length)
				{
					validation.push( '1' );
				}
			}

			// validate captcha field
			if((self.captchaResponse().exists() && captcha.length == 0) || (self.recaptchaResponse().exists() && recaptcha.length == 0)) {
				validation.push( '1' );
			}

			// validate name field
			if(name.length == 0 && ((Komento.options.config.show_name == 2 && Komento.options.config.require_name == 2) || (Komento.options.guest == 1 && Komento.options.config.show_name > 0 && Komento.options.config.require_name > 0))) {
				validation.push( '1' );
			}

			// validate email field + subscription checkbox
			if(email.length == 0 && ((Komento.options.config.show_email == 2 && (Komento.options.config.require_email == 2 || self.subscribeCheckbox().prop('checked'))) || (Komento.options.guest == 1 && Komento.options.config.show_email > 0 && (Komento.options.config.require_email > 0 || self.subscribeCheckbox().prop('checked'))))) {
				validation.push( '1' );
			}

			// validate website field
			if(website.length == 0 && ((Komento.options.config.show_website == 2 && Komento.options.config.require_website == 2) || (Komento.options.guest == 1 && Komento.options.config.show_website > 0 && Komento.options.config.require_website > 0))) {
				validation.push( '1' );
			}

			// validate tnc checkbox
			if(Komento.options.config.show_tnc == 1 && self.tncCheckbox().prop('checked') == 0) {
				validation.push( '1' );
			}

			if(validation.length == 0) {
				self.submitButton().enable();
			}
			else {
				self.submitButton().disable();
			}
		},

		validateEmail: function(email) {
			if( Komento.options.config.enable_email_regex == 1 ) {
				var syntax = Komento.options.config.email_regex;

				if($.isArray(Komento.options.config.email_regex)) {
					syntax = decodeURIComponent(Komento.options.config.email_regex[0]);
				}

				var regex = new RegExp( syntax );
				return regex.test(email);
			} else {
				return true;
			}

		},

		validateWebsite: function(website) {
			if( Komento.options.config.enable_website_regex == 1 ) {
				var syntax = Komento.options.config.website_regex;

				if($.isArray(Komento.options.config.website_regex)) {
					syntax = decodeURIComponent(Komento.options.config.website_regex[0]);
				}

				var regex = new RegExp( syntax );
				return regex.test(website);
			} else {
				return true;
			}

		},

		postComment: function() {
			self.submitButton().disable();

			var attachments = [];

			if(self.kmt.commentupload) {
				attachments = self.kmt.commentupload.options.uploadedId;
			}

			Komento.ajax('site.views.komento.addcomment', {
				component: Komento.component,
				cid: Komento.cid,
				comment: self.commentInput().val(),
				parent_id: self.parentid,
				depth: self.depth,
				username: self.usernameInput().val(),
				name: self.nameInput().val(),
				email: self.emailInput().val(),
				website: self.websiteInput().val(),
				subscribe: self.subscribeCheckbox().prop('checked'),
				register: self.registerCheckbox().prop('checked'),
				tnc: self.tncCheckbox().prop('checked'),
				recaptchaResponse: self.recaptchaResponse().val(),
				captchaResponse: self.captchaResponse().val(),
				captchaId: self.captchaId().val(),
				latitude: self.locationLatitude().val(),
				longitude: self.locationLongitude().val(),
				address: self.locationInput().val(),
				contentLink: Komento.contentLink,
				attachments: attachments,
				pageItemId: self.pageItemId().val(),
				ratings: self.ratings().val()
			},
			{
				success: function(nodeId, html, publishStatus) {
					var commentHtml = $.buildHTML(html);

					if(publishStatus == 1) {
						if(Komento.options.acl.read_comment == 1) {
							self.kmt.commentlist.addComment(nodeId, commentHtml);
						}
						self.successNotification($.language('COM_KOMENTO_FORM_NOTIFICATION_SUBMITTED'));
					}
					else {
						self.successNotification($.language('COM_KOMENTO_FORM_NOTIFICATION_PENDING'));
					}

					// Reset everything

					// Reset recaptcha
					if(self.captchaId().length != 0 || $('#recaptcha_table').length != 0) {
						//self.reloadCaptcha();
					}

					// Reset reply
					if(self.parentid != 0) {
						self.kmt.commentlist.cancelReply();
					}

					// Reset comment text
					self.commentInput().val('');

					// Reset comment length count
					self.commentLengthCount().text('0');

					// Reset location form
					if( self.locationForm().length > 0 ) {
						self.locationForm().controller().removeLocation();
					}

					// Reset submit button text
					if(self.parentid == 0) {
						self.submitButton().text($.language('COM_KOMENTO_FORM_SUBMIT'));
					} else {
						self.submitButton().text($.language('COM_KOMENTO_FORM_REPLY'));
					}

					// Reset attachments
					if(self.kmt.commentupload) {
						self.kmt.commentupload.options.uploadedId = [];
					}

					self.ratings().trigger('clear');
				},

				fail: function(data) {
					self.errorNotification(data);
					self.submitButton().text($.language('COM_KOMENTO_ERROR'));
				},

				captcha: function(data) {
					if(Komento.options.config.antispam_captcha_type == 1) {
						Recaptcha.reload();
					} else {
						self.captchaImage().attr('src', data.image);
						self.captchaId().val(data.id);
						self.captchaResponse().val('');
					}

					if(self.parentid == 0) {
						self.submitButton().text($.language('COM_KOMENTO_FORM_SUBMIT'));
					} else {
						self.submitButton().text($.language('COM_KOMENTO_FORM_REPLY'));
					}
				},

				subscribe: function() {
					self.subscribeForm().text($.language('COM_KOMENTO_FORM_NOTIFICATION_SUBSCRIBED')).addClass('subscribed');
				},

				confirmSubscribe: function() {
					self.subscribeForm().text($.language('COM_KOMENTO_FORM_NOTIFICATION_SUBSCRIBE_CONFIRMATION_REQUIRED')).addClass('subscribed');
				},

				subscribeError: function() {
					self.subscribeForm().text($.language('COM_KOMENTO_FORM_NOTIFICATION_SUBSCRIBE_ERROR'));
				},

				notification: function(message) {
					self.notification(message);
				},

				error: function(xhr) {
					self.errorNotification(xhr.statusText);

					$.bugReport(xhr);

					self.submitButton().text($.language('COM_KOMENTO_ERROR'));
				}
			});
		},

		reloadCaptcha: function() {
			if(Komento.options.config.antispam_captcha_type == 1) {
				if(Recaptcha !== undefined) {
					Recaptcha.reload();
				}
			} else {
				Komento.ajax('site.views.komento.reloadCaptcha',
				{
					component: Komento.component
				},
				{
					success: function(data)
					{
						self.captchaImage().attr('src', data.image);
						self.captchaId().val(data.id);
						self.captchaResponse().val('');
					}
				});
			}
		},

		staticReply: function(item) {
			var id = item.parentid.split('-')[1];
			self.parentid = id;
			self.depth = parseInt(item.depth) + 1;

			var parent = $('#' + parentid);

			var avatar = parent.find('.kmt-avatar:not(.parentContainer > .kmt-avatar)').clone();
			var author = parent.find('.kmt-author:not(.parentContainer > .kmt-author)').clone();
			var time = parent.find('.kmt-time:not(.parentContainer > .kmt-time)').clone();
			var text = parent.find('.commentText:not(.parentContainer > .commentText)').clone();
			var title = '<a href="javascript:void(0);" class="cancelStaticReply">x</a>' + $.language('COM_KOMENTO_FORM_IN_REPLY_TO') + '<a href="' + Komento.contentLink + '#' + parentid + '" class="parentLink kmt-parent-link">' + '#' + id + '</a>';

			var parentContainer = $('<span class="parentContainer hidden"></span>');
			parentContainer.html('').append(avatar).append(author).append(time).append(text);

			self.element.find('h3.kmt-title').html('').append(title).append(parentContainer);
			self.submitButton().text($.language('COM_KOMENTO_FORM_REPLY'));

			self.element.scroll();
		},

		cancelStaticReply: function() {
			self.parentid = 0;
			self.depth = 0;
			self.element.find('h3.kmt-title').text($.language('COM_KOMENTO_FORM_LEAVE_YOUR_COMMENTS'));
		},

		reply: function(item) {
			self.loadForm();

			self.parentid = item.id;

			self.depth = parseInt( item.depth ) + 1;

			self.element.find('h3.kmt-title').text($.language('COM_KOMENTO_FORM_REPLY'));
			self.element.find('.submitButton').text($.language('COM_KOMENTO_FORM_REPLY'));

			item.mine.append(self.element);
			self.element.scroll();
		},

		cancelReply: function() {
			self.hideForm();

			self.element.find('h3.kmt-title').text($.language('COM_KOMENTO_FORM_LEAVE_YOUR_COMMENTS'));
			self.parentid = 0;
			self.depth = 0;
			self.element.find('.submitButton').text($.language('COM_KOMENTO_FORM_SUBMIT'));

			if(Komento.options.config.form_position == 0) {
				if(Komento.options.config.tabbed_comments == 0 ) {
					$('.commentTools').before(self.element);
				} else {
					$('.fameList').before(self.element);
				}
			} else {
				if(Komento.options.config.tabbed_comments == 0 ) {
					$('.commentList').after(self.element);
				} else {
					$('.fameList').after(self.element);
				}
			}
		},

		unsubscribe: function() {
			Komento.ajax('site.views.komento.unsubscribe', {
				component: Komento.component,
				cid: Komento.cid
			},
			{
				success: function() {
					self.subscribeForm().text($.language('COM_KOMENTO_FORM_NOTIFICATION_UNSUBSCRIBED')).removeClass('subscribed');
				},

				fail: function() {
					self.subscribeForm().text($.language('COM_KOMENTO_ERROR'));
				}
			});
		},

		errorNotification: function(message) {
			self.formAlert().removeClass('success').addClass('error');
			self.notification(message);
		},

		successNotification: function(message) {
			self.formAlert().removeClass('error').addClass('success');

			var autohide = parseInt(Komento.options.config.autohide_form_notification);
			self.notification(message, autohide);
		},

		notification: function(message, autohide) {
			// todo: add js effects
			self.formAlert().show();
			self.formAlert().append('<li>' + message + '</li>');

			if(autohide == 1) {
				setTimeout(function() {
					self.closeNotification();
				}, 5000);
			}
		},

		closeNotification: function() {
			self.formAlert().hide();
		},

		clearNotification: function() {
			self.formAlert().html('').removeClass('error').hide();
		}

	} }
);

module.resolve();
});
});

Komento.module('komento.commentitem', function($) {
var module = this;

$.fn.itemset = function(options) {
	var el = $(this);
	var data = $(el).parents('.kmt-item').data();

	if(!data.item) {
		var item = {};

		item.mine = $(el).parents('.kmt-item');
		item.commentid = item.mine.attr('id');
		item.both = $('.' + item.commentid);
		item.id = item.commentid.split('-')[1];
		item.parentid = item.mine.attr('parentid');
		item.depth = item.mine.attr('depth');
		item.childs = item.mine.attr('childs');
		item.published = item.mine.attr('published');

		// declare object
		item.element = {};
		item.element.mine = {};
		item.element.both = {};

		// affects single self
		item.element.mine.commentText = item.mine.find(options['{commentText}']);
		item.element.mine.commentInfo = item.mine.find(options['{commentInfo}']);
		item.element.mine.commentForm = item.mine.find(options['{commentForm}']);
		item.element.mine.stickButton = item.mine.find(options['{stickButton}']);
		item.element.mine.replyButton = item.mine.find(options['{replyButton}']);
		item.element.mine.reportButton = item.mine.find(options['{reportButton}']);
		item.element.mine.likeButton = item.mine.find(options['{likeButton}']);
		item.element.mine.likesCounter = item.mine.find(options['{likesCounter}']);
		item.element.mine.editButton = item.mine.find(options['{editButton}']);
		item.element.mine.saveEditButton = item.mine.find(options['{saveEditButton}']);
		// item.element.mine.editForm = item.mine.find(options['{editForm}']);
		// item.element.mine.editInput = item.mine.find(options['{editInput}']);
		item.element.mine.deleteButton = item.mine.find(options['{deleteButton}']);
		item.element.mine.publishButton = item.mine.find(options['{publishButton}']);
		item.element.mine.unpublishButton = item.mine.find(options['{unpublishButton}']);
		item.element.mine.parentLink = item.mine.find(options['{parentLink}']);
		item.element.mine.parentContainer = item.mine.find(options['{parentContainer}']);
		item.element.mine.attachmentWrap = item.mine.find(options['{attachmentWrap}']);
		item.element.mine.attachmentFile = item.mine.find(options['{attachmentFile}']);

		// affects all
		item.element.both.commentText = item.both.find(options['{commentText}']);
		item.element.both.commentInfo = item.both.find(options['{commentInfo}']);
		item.element.both.stickButton = item.both.find(options['{stickButton}']);
		item.element.both.replyButton = item.both.find(options['{replyButton}']);
		item.element.both.reportButton = item.both.find(options['{reportButton}']);
		item.element.both.likeButton = item.both.find(options['{likeButton}']);
		item.element.both.likesCounter = item.both.find(options['{likesCounter}']);
		item.element.both.parentLink = item.both.find(options['{parentLink}']);
		item.element.both.parentContainer = item.both.find(options['{parentContainer}']);
		item.element.both.attachmentWrap = item.both.find(options['{attachmentWrap}']);
		item.element.both.attachmentFile = item.both.find(options['{attachmentFile}']);


		item.mine.data('item', item);
		data.item = item;
	}

	return data.item;
};

module.resolve();
});

Komento.module('komento.commentlist', function($) {
var module = this;

Komento.require()
	.library('ui/effect', 'fancybox', 'dialog')
	.image(Komento.options.spinner)
	.script('sharelinks', 'markitup', 'komento.common', 'komento.commentitem', 'komento.bbcode', 'komento.language')
	.view(
		'dialogs/delete.single',
		'dialogs/delete.affectchild',
		'dialogs/unpublish.affectchild',
		'comment/item/edit.form',
		'dialogs/delete.attachment'
	)
	.done(function() {
		Komento.Controller(
			'CommentList',
			{
				defaults: {
					'{commentList}': '.kmt-list',
					'{commentItem}': '.kmt-item',
					'{noComment}': '.kmt-empty-comment',
					'{loadMore}': '.loadMore',
					// Comment Item
					'{commentText}'			: '.commentText',
					'{commentInfo}'			: '.commentInfo',
					'{commentForm}'			: '.commentForm',
					'{deleteButton}'		: '.deleteButton',
					'{editButton}'			: '.editButton',
					'{saveEditButton}'		: '.saveEditButton',
					'{cancelEditButton}'	: '.cancelEditButton',
					'{editForm}'			: '.editForm',
					'{editInput}'			: '.editInput',
					'{replyButton}'			: '.replyButton',
					'{shareBox}'			: '.shareBox',
					'{reportButton}'		: '.reportButton',
					'{statusButton}'		: '.statusButton',
					'{statusOptions}'		: '.statusOptions',
					'{publishButton}'		: '.publishButton',
					'{unpublishButton}'		: '.unpublishButton',
					'{stickButton}'			: '.stickButton',
					'{likeButton}'			: '.likeButton',
					'{likesCounter}'		: '.likesCounter',
					'{parentLink}'			: '.parentLink',
					'{parentContainer}'		: '.parentContainer',
					'{socialButton}'		: '.socialButton',
					'{attachmentWrap}'		: '.attachmentWrap',
					'{attachmentList}'		: '.attachmentList',
					'{attachmentFile}'		: '.attachmentFile',
					'{attachmentDelete}'	: '.attachmentDelete',
					'{attachmentImage}'		: '.attachmentImage',
					'{attachmentImageLink}'	: '.attachmentImageLink',

					view: {
						editForm: 'comment/item/edit.form',
						deleteSingle: 'dialogs/delete.single',
						deleteChilds: 'dialogs/delete.affectchild',
						publishDialog: 'dialogs/publish.affectchild',
						unpublishDialog: 'dialogs/unpublish.affectchild',
						deleteAttachment: 'dialogs/delete.attachment'
					}
				}
			},
			function(self)
			{ return {
				init: function() {
					if(self.noComment().length == 0) {

						// initialise all sharelinks
						self.generateSharelinks();

						// try to get if permalink is parsed in
						if(Komento.options.konfig.enable_ajax_permalink == 1) {
							var url = document.location.href.split('#');
							var commentid;
							for(var i = 0; i < url.length; i++) {
								if(url[i].substring(0, 4) == 'kmt-') {
									commentid = url[i];
									break;
								}
							}

							if(commentid) {
								self.finditem(commentid);
							}

							if(Komento.options.config.upload_image_fancybox == 1) {
								self.attachmentImageLink().fancybox({
									type: 'image',
									helpers: {
										overlay: Komento.options.config.upload_image_overlay == 1 ? true : false
									}
								});
							}
						}

						// execute SH
						if(Komento.options.config.enable_syntax_highlighting == 1) {
							$.loadSHBrushes();
						}
					}
				},

				generateSharelinks: function() {
					var callback = function() {
						self.socialButton().each(function(i, el) {
							$(el).sharelinks();
						});
					};

					self.generateShortLinks(callback);
				},

				generateShortLinks: function(callback) {
					var replaceUrl = function(url) {
						self.socialButton().each(function(index, element) {
							if( !$(element).attr('loaded') ) {
								var commentid = $(element).attr('commentid');
								var permalink = url + '#kmt-' + commentid;

								$(element).attr('url', permalink);
								$(element).parents('.kmt-share-balloon').find('.short-url').val(permalink);
							}
						});

						callback && callback();
					};

					if(!Komento.shortenLink) {
						$.shortenlink(Komento.contentLink, replaceUrl);
					} else {
						replaceUrl(Komento.shortenLink);
					}
				},

				finditem: function(commentid)
				{
					if($('#' + commentid).length == 0) {
						if(!(self.loadMore().is(':hidden') || self.loadMore().length == 0)) {
							self.loadMoreComments(function() {
								self.finditem(commentid)
							});
						}
					} else {
						$('#' + commentid).scroll();
						$('#' + commentid).highlight();
					}
				},

				'{loadMore} click': function(el) {
					if(el.checkClick()) {
						el.loading();

						self.loadMoreComments();
					}
				},

				addComment: function(nodeId, commentHtml, contentLink) {
					var commentId = commentHtml.filter('li').attr('id');
					var implement = 0;

					if(self.noComment().length != 0) {
						self.noComment().remove();
						self.commentList().append(commentHtml);
						implement = 1;
					} else {
						if(nodeId == 0 || Komento.options.config.enable_threaded == 0) {
							if(Komento.options.config.load_previous == 1) {
								if(Komento.sort == 'oldest') {
									self.commentList().append(commentHtml);
									implement = 1;
								}

								if(Komento.sort == 'latest' && (self.loadMore().length == 0 || self.loadMore().is(':hidden'))) {
									self.commentList().prepend(commentHtml);
									implement = 1;
								}
							} else {
								if(Komento.sort == 'oldest' && (self.loadMore().length == 0 || self.loadMore().is(':hidden'))) {
									self.commentList().append(commentHtml);
									implement = 1;
								}

								if(Komento.sort == 'latest') {
									self.commentList().prepend(commentHtml);
									implement = 1;
								}
							}
						} else {
							if(Komento.sort == 'oldest') {
								if(self.commentList().find('#kmt-' + nodeId).next().attr('id') == 'kmt-form') {
									self.commentList().find('#kmt-' + nodeId).next().after(commentHtml);
									implement = 1;
								} else {
									self.commentList().find('#kmt-' + nodeId).after(commentHtml);
									implement = 1;
								}
							}

							if(Komento.sort == 'latest') {
								var parent = self.commentList().find('#' + commentHtml.attr('parentid'));

								if(parent.attr('id') === 'kmt-' + nodeId) {
									if(self.commentList().find('#kmt-' + nodeId).next().attr('id') == 'kmt-form') {
										self.commentList().find('#kmt-' + nodeId).next().after(commentHtml);
									} else {
										self.commentList().find('#kmt-' + nodeId).after(commentHtml);
									}
								} else {
									self.commentList().find('#kmt-' + nodeId).before(commentHtml);
								}

								implement = 1;
							}
						}
					}

					if(implement) {
						Komento.loadedCount++;

						if(nodeId != 0) {
							self.addChildCount($('#' + commentId).attr('parentid'));
						}

						self.generateSharelinks();

						if(Komento.options.config.scroll_to_comment == 1) {
							var comment = $('#' + commentId);

							comment.highlight();
							comment.scroll();
						}

						if(Komento.options.config.enable_syntax_highlighting == 1) {
							$.loadSHBrushes();
						}
					}

					self.kmt.tools.addCommentCounter();

					Komento.totalCount++;
				},

				addChildCount: function(parentid) {
					var count = parseInt($('.' + parentid).attr('childs'));
					$('.' + parentid).attr('childs', count + 1);

					if($('.' + parentid).exists() && parseInt($('li.' + parentid).attr('parentid').split('-')[1]) > 0) {
						self.addChildCount($('.' + parentid).attr('parentid'));
					}
				},

				loadMoreComments: function(callback) {
					var startCount;
					var limit = parseInt(Komento.options.config.max_comments_per_page);

					if(Komento.options.config.load_previous == 1) {
						startCount = Komento.totalCount - Komento.loadedCount - limit;

						if(startCount < 0) {
							limit = limit + startCount;
							startCount = 0;
						}
					} else {
						startCount = Komento.loadedCount;
					}

					Komento.ajax('site.views.komento.loadmorecomments', {
						component: Komento.component,
						cid: Komento.cid,
						start: startCount,
						limit: limit,
						sort: Komento.sort,
						contentLink: Komento.contentLink,
					},
					{
						success: function(html, loadedCount, loadMore) {
							var list = $(html).filter('li');

							if(Komento.options.config.load_previous == 1) {
								self.commentList().prepend(list);
							}
							else {
								self.commentList().append(list);
							}

							list.highlight();

							Komento.loadedCount = loadedCount;

							self.loadMore().removeClass('loading');

							if(loadMore == 1) {
								self.loadMore().removeClass('disabled');

								var nextStartCount;
								if(Komento.options.config.load_previous == 1) {
									nextStartCount = Komento.totalCount - Komento.loadedCount - limit;

									if(nextStartCount < 0) {
										limit = limit + nextStartCount;
										nextStartCount = 0;
									}
								} else {
									nextStartCount = Komento.loadedCount;
								}
								self.loadMore().attr('href', '#!kmt-start=' + nextStartCount);
							} else {
								self.loadMore().hide();
							}

							callback && callback();
						},

						fail: function(limit, limitstart, sort) {
						}
					});
				},

				/************** Comment Items **************/

				set: function(el) {
					self.item = el.itemset(self.options);
				},

				'{deleteButton} click': function(el, event) {
					// propagation hack to solve reply form issues sharing the same function name with famelist.js
					event.stopPropagation();

					self.set(el);
					self.showDeleteDialog();
				},

				'{editButton} click': function(el, event) {
					// propagation hack to solve reply form issues sharing the same function name with famelist.js
					event.stopPropagation();

					self.set(el);
					if(el.checkClick()) {

						if(el.checkSwitch()) {
							self.edit(el);
						} else {
							self.cancelEdit(el);
						}
					}
				},

				'{saveEditButton} click': function(el, event) {
					// propagation hack to solve reply form issues sharing the same function name with famelist.js
					event.stopPropagation();

					self.set(el);
					self.saveEdit(el);
				},

				'{cancelEditButton} click' : function(el, event) {
					// propagation hack to solve reply form issues sharing the same function name with famelist.js
					event.stopPropagation();

					self.set(el);
					self.cancelEdit(el);
				},

				'{replyButton} click': function(el, event) {
					// propagation hack to solve reply form issues sharing the same function name with famelist.js
					event.stopPropagation();

					self.set(el);
					if(Komento.options.konfig.enable_inline_reply == 1) {
						if(el.checkSwitch()) {
							self.reply();
						} else {
							self.cancelReply();
						}
					} else {
						self.kmt.form.staticReply(self.item);
					}
				},

				'{reportButton} click': function(el, event) {
					// propagation hack to solve reply form issues sharing the same function name with famelist.js
					event.stopPropagation();

					self.set(el);
					if(el.checkClick()) {
						if(el.checkSwitch()) {
							self.reportComment(el);
						} else {
							self.cancelreportComment(el);
						}
					}
				},

				'{publishButton} click': function(el, event) {
					// propagation hack to solve reply form issues sharing the same function name with famelist.js
					event.stopPropagation();

					self.set(el);
					if(self.item.childs > 0) {
						self.showPublishDialog(el);
					} else {
						self.publishComment(el);
					}
				},

				'{unpublishButton} click': function(el, event) {
					// propagation hack to solve reply form issues sharing the same function name with famelist.js
					event.stopPropagation();

					self.set(el);
					if(self.item.childs > 0) {
						self.showUnpublishDialog(el);
					} else {
						self.unpublishComment(el);
					}
				},

				'{stickButton} click': function(el, event) {
					// propagation hack to solve reply form issues sharing the same function name with famelist.js
					event.stopPropagation();

					self.set(el);
					if(el.checkClick()) {
						if(el.checkSwitch()) {
							self.stick(el);
						} else {
							self.unstick(el);
						}
					}
				},

				'{likesCounter} click': function(el, event) {
					// propagation hack to solve reply form issues sharing the same function name with famelist.js
					event.stopPropagation();

					self.set(el);

					self.showLikesDialog(el);
				},

				'{likeButton} click': function(el, event) {
					// propagation hack to solve reply form issues sharing the same function name with famelist.js
					event.stopPropagation();

					self.set(el);
					if(el.checkClick()) {
						if(el.checkSwitch()) {
							self.like(el);
						} else {
							self.unlike(el);
						}
					}
				},

				'{parentLink} mouseover': function(el) {
					self.set(el);
					if(Komento.options.config.enable_threaded == 1) {
						$('#' + self.item.parentid).addClass('kmt-highlight');
					} else {
						self.item.element.mine.parentContainer.show();
						if(self.item.element.mine.parentContainer.attr('loaded') == 0) {
							self.item.element.mine.parentContainer.html('<img src="' + Komento.options.spinner + '" />');
							self.loadParent();
						}
					}
				},

				'{parentLink} mouseout': function(el) {
					self.set(el);
					if(Komento.options.config.enable_threaded == 1) {
						$('#' + self.item.parentid).removeClass('kmt-highlight');
					} else {
						self.item.element.mine.parentContainer.hide();
					}
				},

				'{parentLink} click': function(el) {
					self.set(el);
					var parent = $('.' + self.item.parentid);
					parent.highlight();
				},

				'{attachmentDelete} click': function(el, event) {
					// propagation hack to solve reply form issues sharing the same function name with famelist.js
					event.stopPropagation();

					self.set(el);

					self.showAttachmentDeleteDialog(el);
				},

				'{attachmentImage} click': function(el, event) {

				},

				closeDialog: function() {
					if($('.foundryDialog').length > 0) {
						$('.foundryDialog').controller().close();
					}
				},

				edit: function() {
					var editWrap = self.item.mine.find('#' + self.item.commentid + '-edit');
					if(editWrap.length == 0) {
						Komento.ajax('site.views.komento.getcommentraw', {
							id: self.item.id
						},
						{
							success: function(comment) {
								self.item.element.mine.editButton
									.text($.language('COM_KOMENTO_COMMENT_EDIT_CANCEL'))
									.switchOff()
									.enable();

								var editForm = self.view.editForm({commentId: self.item.commentid, commentText: comment});
								self.item.element.mine.commentText.after(editForm);

								// set again for newly generated editform and editinput because previously set, dom for editform and editinput not yet created
								self.item.element.mine.editForm = $(editForm);
								self.item.element.mine.editInput = self.item.element.mine.editForm.find('.editInput');
								self.item.mine.data('item', self.item);

								if(Komento.options.config.enable_bbcode == 1) {
									self.item.element.mine.editInput.markItUp($.getBBcodeSettings());
								}
							},

							fail: function(message) {
								self.item.element.mine.editButton
									.text($.language('COM_KOMENTO_ERROR'));
							}
						});
					} else {
						editWrap.show();

						self.item.element.mine.editButton
							.text($.language('COM_KOMENTO_COMMENT_EDIT_CANCEL'))
							.switchOff()
							.enable();
					}

					// bugged
					// .after insert doesnt register at first
					// $('#' + commentId + '-edit').find('textarea').focus();
				},

				cancelEdit: function() {
					self.item.element.mine.editButton
						.text($.language('COM_KOMENTO_COMMENT_EDIT'))
						.switchOn()
						.enable();
					self.item.element.mine.editForm.hide();
				},

				saveEdit: function() {
					Komento.ajax('site.views.komento.editcomment', {
						id: self.item.id,
						edittedComment: self.item.element.mine.editInput.val()
					},
					{
						success: function(modified_html, modified_by, modified) {
							self.item.element.both.commentText.html(modified_html);

							if(Komento.options.config.enable_info == 1) {
								self.item.element.both.commentInfo.text($.language('COM_KOMENTO_COMMENT_EDITTED_BY', modified_by, modified)).show().css('display', '');
							}

							if(Komento.options.config.enable_syntax_highlighting == 1) {
								$.loadSHBrushes();
							}

							self.cancelEdit();
						},

						fail: function(message) {
							$.dialog({
								content: message
							});
						}
					});
				},

				reply: function() {
					$('.formAlert').hide().text('');;

					// only allow 1 reply at a time
					// revert all other cancelReplyButton upon clicking reply
					$(self.options['{replyButton}'])
						.switchOn()
						.find('span')
						.text($.language('COM_KOMENTO_COMMENT_REPLY'));

					self.item.element.mine.replyButton
						.switchOff()
						.find('span')
						.text($.language('COM_KOMENTO_COMMENT_REPLY_CANCEL'));

					self.kmt.form.reply(self.item);
				},

				cancelReply: function() {
					$(self.options['{replyButton}'])
						.switchOn()
						.find('span')
						.text($.language('COM_KOMENTO_COMMENT_REPLY'));

					self.kmt.form && self.kmt.form.cancelReply();
				},

				showDeleteDialog: function() {
					if(self.item.childs > 0) {
						$.dialog({
							height: 150,
							content: self.view.deleteChilds(true, {childs: self.item.childs}),
							afterShow: function() {
								$('.foundryDialog').find('.delete-affectChild').click(function() {
									self.deleteComment(1);
								});

								$('.foundryDialog').find('.delete-moveChild').click(function() {
									self.deleteComment(0);
								});
							}
						});
					} else {
						$.dialog({
							height: 150,
							content: self.view.deleteSingle(true),
							afterShow: function() {
								$('.foundryDialog').find('.delete-affectChild').click(function() {
									self.deleteComment(1);
								});
							}
						});
					}
				},

				deleteComment: function(affectChild) {
					$('.foundryDialog').find('.kmt-delete-status').show();

					Komento.ajax('site.views.komento.deletecomment', {
						id: self.item.id,
						affectChild: affectChild
					},
					{
						success: function() {
							self.closeDialog();

							if(affectChild == 1) {
								self.deleteChild(self.item.commentid);
							} else {
								self.moveChildUp(self.item.commentid, self.item.parentid);
							}

							self.item.both.hide('fade', function() {
								self.item.both.remove();
							});

							self.kmt.tools.deductCommentCounter();
						},

						fail: function() {
							$('.foundryDialog').find('.kmt-delete-status').text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				deleteChild: function(id) {
					$('li[parentid="' + id + '"]').each(function() {

						// recursive function to delete other childs
						self.deleteChild($(this).attr('id'));
					}).hide('fade', function() {
						$(this).remove();
					});

					var count = $('li[parentid="' + id + '"]').length;
					self.kmt.tools.deductCommentCounter(count);
				},

				moveChildUp: function(id, parentid) {
					$('li[parentid="' + id + '"]').attr('parentid', parentid).each(function() {

						// move child class up 1 level
						$(this).removeClass('kmt-child-' + $(this).attr('depth')).addClass('kmt-child-' + ($(this).attr('depth') - 1));

						// move depth up 1 level
						$(this).attr('depth', ($(this).attr('depth') - 1));

						// recursive function to move other childs
						self.moveChildUp($(this).attr('id'));
					});
				},

				reportComment: function() {
					Komento.ajax('site.views.komento.action', {
						id: self.item.id,
						type: 'report',
						action: 'add'
					},
					{
						success: function() {
							self.item.element.both.reportButton
								.text($.language('COM_KOMENTO_COMMENT_REPORTED'))
								.switchOff()
								.enable();
						},

						fail: function() {
							self.item.element.both.reportButton
								.text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				cancelreportComment: function() {
					Komento.ajax('site.views.komento.action', {
						id: self.item.id,
						type: 'report',
						action: 'remove'
					},
					{
						success: function() {
							self.item.element.both.reportButton
								.text($.language('COM_KOMENTO_COMMENT_REPORT'))
								.switchOn()
								.enable();
						},

						fail: function() {
							self.item.element.both.reportButton
								.text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				showUnpublishDialog: function() {
					$.dialog({
						height: 150,
						content: self.view.unpublishDialog(true, {childs: self.item.childs}),
						afterShow: function() {
							$('.foundryDialog').find('.unpublish-affectChild').click(function() {
								self.unpublishComment();
							});
						}
					});
				},

				unpublishComment: function() {
					Komento.ajax('site.views.komento.unpublish', {
						id: self.item.id
					},
					{
						success: function() {
							self.closeDialog();
							self.unpublishChild(self.item.commentid);
							self.item.both.hide('fade', function() {
								self.item.both.remove();
							});
							self.kmt.tools.deductCommentCounter();
						},

						fail: function() {

						}
					});
				},

				unpublishChild: function(id) {
					$('li[parentid="' + id + '"]').each(function() {
						self.unpublishChild($(this).attr('id'));
					}).hide('fade', function() {
						$(this).remove();
					});

					var count = $('li[parentid="' + id + '"]').length;
					self.kmt.tools.deductCommentCounter(count);
				},

				showPublishDialog: function() {

				},

				publishComment: function() {

				},

				publishChild: function(id) {

				},

				stick: function(el) {
					Komento.ajax('site.views.komento.stick', {
						id: self.item.id
					},
					{
						success: function() {
							self.item.element.mine.stickButton
								.text($.language('COM_KOMENTO_COMMENT_UNSTICK'))
								.switchOff()
								.enable();

							self.item.mine.addClass('kmt-sticked');

							// append comment into stick list
							self.kmt.famelist.stickComment(self.item.mine.clone())
						},

						fail: function() {
							self.item.element.mine.stickButton
								.text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				unstick: function() {
					Komento.ajax('site.views.komento.unstick', {
						id: self.item.id
					},
					{
						success: function() {
							self.item.element.both.stickButton
								.text($.language('COM_KOMENTO_COMMENT_STICK'))
								.switchOn()
								.enable();

							self.item.both.removeClass('kmt-sticked');

							// remove comment from stick list
							self.kmt.famelist.unstickComment(self.item.commentid);
						},

						fail: function() {
							self.stickButton
								.text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				like: function() {
					Komento.ajax('site.views.komento.action', {
						id: self.item.id,
						type: 'likes',
						action: 'add'
					},
					{
						success: function() {
							self.item.element.both.likeButton
								.switchOff()
								.enable()
								.find('span')
								.text($.language('COM_KOMENTO_COMMENT_UNLIKE'));

							var likes = parseInt(self.item.element.mine.likesCounter.find('span').text()) + 1;
							self.item.element.both.likesCounter.find('span')
								.text(likes);
						},

						fail: function( message ) {
							self.item.element.both.likesCounter.find('span')
								.text( message );
								// .text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				unlike: function() {
					Komento.ajax('site.views.komento.action', {
						id: self.item.id,
						type: 'likes',
						action: 'remove'
					},
					{
						success: function() {
							self.item.element.both.likeButton
								.switchOn()
								.enable()
								.find('span')
								.text($.language('COM_KOMENTO_COMMENT_LIKE'));

							var likes = parseInt(self.item.element.mine.likesCounter.find('span').text()) - 1;
							self.item.element.both.likesCounter.find('span')
								.text(likes);
						},

						fail: function() {
							self.item.element.both.likesCounter.find('span')
								.text($.language('COM_KOMENTO_ERROR'))
						}
					});
				},

				showLikesDialog: function(el) {
					Komento.ajax('site.views.komento.getLikedUsers', {
						id: self.item.id
					},
					{
						success: function(html) {
							$.dialog({
								title: $.language('COM_KOMENTO_COMMENT_PEOPLE_WHO_LIKED_THIS'),
								content: html
							});
						}
					});
				},

				loadParent: function() {
					var parent = $('#' + self.item.parentid);
					if(parent.length != 0) {
						var avatar = parent.find('.kmt-avatar:not(.parentContainer > .kmt-avatar)').clone();
						var author = parent.find('.kmt-author:not(.parentContainer > .kmt-author)').clone();
						var time = parent.find('.kmt-time:not(.parentContainer > .kmt-time)').clone();
						var text = parent.find('.commentText:not(.parentContainer > .commentText)').clone();

						// todo: configurable
						self.item.element.both.parentContainer.html('')
							.append(avatar)
							.append(author)
							.append(time)
							.append(text);
					} else {
						var parentid = self.item.parentid.split('-')[1];

						Komento.ajax('site.views.komento.getcomment', {
							id: parentid
						},
						{
							success: function(html) {
								self.item.element.both.parentContainer.html(html);
							},

							fail: function() {

							}
						})
					}
					self.item.element.both.parentContainer.attr('loaded', 1);
				},

				showAttachmentDeleteDialog: function(el) {
					var attachmentid = $(el).parents('.attachmentFile').attr('attachmentid');
					var attachmentname = $(el).parents('.attachmentFile').attr('attachmentname');

					$.dialog({
						content: self.view.deleteAttachment(true, {attachmentname: attachmentname}),
						afterShow: function() {
							$('.foundryDialog').find('.delete-attachment').click(function() {
								self.closeDialog();
								self.deleteFile(attachmentid);
							});

							$('.foundryDialog').find('.delete-attachment-cancel').click(function() {
								self.closeDialog();
							});
						}
					});
				},

				deleteFile: function(attachmentid) {
					var commentid = self.item.id;

					Komento.ajax('site.views.komento.deleteAttachment', {
						id: commentid,
						attachmentid: attachmentid
					},
					{
						success: function() {
							self.item.element.both.attachmentFile.filter('.file-' + attachmentid).remove();

							if(self.item.mine.find('.attachmentFile').length == 0) {
								self.item.element.both.attachmentWrap.remove();
							}
						},

						fail: function(error) {
							error = '<p class="error">' + error + '</p>';
							self.item.element.both.attachmentFile.filter('.file-' + attachmentid).append(error);
						}
					})
				}
			} }
		);
		module.resolve();
	});
});

Komento.module('sharelinks', function($) {
	var module = this;

	$.sharelinks = function(elem, type, options, callback) {
		var node = this[type].call($(elem), type, options, callback);
	}

	$.fn.sharelinks = function(type, options, callback) {
		/*
		options{
			url
			content
			image
		}

		type {
			facebook
			twitter
			googleplus
			linkedin
			pinterest
			tumblr
			digg
			delicious
			reddit
			stumbleupon
			identica
			stumpedia
			technorati
			blogmarks
		}
		*/

		var node = this;

		if(!node.attr('loaded')) {
			node.attr('loaded', 1);

			if(!type) {
				var type = node.attr('type');
			}

			if(!options) {
				var url, title, content;

				if(node.attr('url')) {
					url = encodeURIComponent(node.attr('url'));
				}
				if(node.attr('title')) {
					title = encodeURIComponent(node.attr('title'));
				}
				if(node.attr('content')) {
					content = encodeURIComponent(node.attr('content'));
				}

				var options = {
					'url': url,
					'title': title,
					'content': content
				}
			}

			$(document).ready(function() {
				var callback = function(node, link, type) {
					$.sharelinks['cleanup'].call(node);

					node.bind('click', function(event) {
						$.sharelinks['popup'].call(node, type, link);
					});
				}

				$.sharelinks[type].call(node, type, options, callback);
			});
		}
	};

	$.sharelinks.facebook = function(type, options, callback) {
		// p[url]
		// p[title]
		// p[summary]
		// p[images]

		var node = this,
			link = 'http://www.facebook.com/sharer.php?s=100',
			url,
			title,
			summary,
			images;

		if(options.url) {
			url = options.url;
		}

		if(options.title) {
			title = options.title;

			if(options.content) {
				summary = options.content;
			}
		} else {
			if(options.content) {
				title = 'Comments';
				summary = options.content;
			}
		}

		if(options.image) {
			images = options.image;
		}

		link += '&p[url]=' + url + '&p[title]=' + title + '&p[summary]=' + summary + '&p[images]=' + images;

		callback(node, link, type);
	};

	$.sharelinks.twitter = function(type, options, callback) {
		// url
		// text

		var node = this,
			link = 'http://twitter.com/intent/tweet',
			url,
			text;

		if(options.url) {
			url = options.url;
		}

		if(options.title) {
			text = options.title;
		} else {
			if(options.content) {
				text = options.content;
			}
		}

		link += '?url=' + url + '&text=' + text;

		callback(node, link, type);
	};

	$.sharelinks.googleplus = function(type, options, callback) {
		// url

		var node = this,
			link = 'http://plus.google.com/share',
			url;

		if(options.url) {
			url = options.url;
		}

		link += '?url=' + url;

		callback(node, link, type);
	};

	$.sharelinks.linkedin = function(type, options, callback) {
		// url
		// title
		// summary

		var node = this,
			link = 'http://linkedin.com/shareArticle?mini=true',
			url,
			title,
			summary;

		if(options.url) {
			url = options.url;
		}

		if(options.title) {
			title = options.title;

			if(options.content) {
				summary = options.content;
			}
		} else {
			if(options.content) {
				title = options.content;
			}
		}

		link += '&url=' + url + '&title=' + title + '&summary=' + summary;

		callback(node, link, type);
	};

	$.sharelinks.pinterest = function(type, options, callback) {
		// url
		// description
		// media (mandatory)

		var node = this,
			link = 'http://pinterest.com/pin/create/button/',
			url,
			description,
			media = options.image;

		if(options.url) {
			url = options.url;
		}

		if(options.title) {
			description = options.title;
		} else {
			if(options.content) {
				description = options.content;
			}
		}

		link += '?media=' + media + '&url=' + url + '&description=' + description;

		callback(node, link, type);
	};

	$.sharelinks.tumblr = function(type, options, callback) {
		// url
		// name
		// description

		var node = this,
			link = 'http://www.tumblr.com/share/link',
			url,
			name,
			description;

		if(options.url) {
			url = options.url;
		}

		if(options.title) {
			name = options.title;

			if(options.content) {
				description = options.content;
			}
		} else {
			if(options.content) {
				name = options.content;
			}
		}

		link += '?url=' + url + '&name=' + name + '&description=' + description;

		callback(node, link, type);
	};

	$.sharelinks.digg = function(type, options, callback) {
		// url
		// title
		var node = this,
			link = 'http://digg.com/submit',
			url,
			title;

		if(options.url) {
			url = options.url;
		}

		if(options.title) {
			title = options.title;
		} else {
			if(options.content) {
				title = options.content;
			}
		}

		link += '?url=' + url + '&title=' + title;

		callback(node, link, type);
	};

	$.sharelinks.delicious = function(type, options, callback) {
		// url
		// title
		var node = this,
			link = 'http://delicious.com/post',
			url,
			title;

		if(options.url) {
			url = options.url;
		}

		if(options.title) {
			title = options.title;
		} else {
			if(options.content) {
				title = options.content;
			}
		}

		link += '?url=' + url + '&title=' + title;

		callback(node, link, type);
	};

	$.sharelinks.reddit = function(type, options, callback) {
		// url
		// title
		var node = this,
			link = 'http://reddit.com/submit',
			url,
			title;

		if(options.url) {
			url = options.url;
		}

		if(options.title) {
			title = options.title;
		} else {
			if(options.content) {
				title = options.content;
			}
		}

		link += '?url=' + url + '&title=' + title;

		callback(node, link, type);
	};

	$.sharelinks.stumbleupon = function(type, options, callback) {
		// url
		// title
		var node = this,
			link = 'http://www.stumbleupon.com/submit',
			url,
			title;

		if(options.url) {
			url = options.url;
		}

		if(options.title) {
			title = options.title;
		} else {
			if(options.content) {
				title = options.content;
			}
		}

		link += '?url=' + url + '&title=' + title;

		callback(node, link, type);
	};

	$.sharelinks.indentica = function(type, options, callback) {
		// url
		// title

		var node = this,
			link = 'http://identi.ca/index.php?action=bookmarkpopup',
			url,
			title;

		if(options.url) {
			url = options.url;
		}

		if(options.title) {
			title = options.title;
		} else {
			if(options.content) {
				title = options.content;
			}
		}

		link += '&url=' + url + '&title=' + title;

		callback(node, link, type);
	};

	$.sharelinks.stumpedia = function(type, options, callback) {
		// url

		var node = this,
			link = 'http://www.stumpedia.com/submit',
			url;

		if(options.url) {
			url = options.url;
		}

		link += '?url=' + url;

		callback(node, link, type);
	};

	$.sharelinks.technorati = function(type, options, callback) {
		// add (url)

		var node = this,
			link = 'http://technorati.com/faves';
			add;

		if(options.url) {
			add = options.url;
		}

		link += '?add=' + url;

		callback(node, link, type);
	};

	$.sharelinks.blogmarks = function(type, options, callback) {
		// url
		// title

		var node = this,
			link = 'http://blogmarks.net/my/new.php?mini=1',
			url,
			title;

		if(options.url) {
			url = options.url;
		}

		if(options.title) {
			title = options.title;
		} else {
			if(options.content) {
				title = options.content;
			}
		}

		link += '&url=' + url + '&title=' + title;

		callback(node, link, type);
	};

	$.sharelinks.dialog = function(url) {
		Komento.require().library('dialog').done(function() {
			$.dialog(url);
		});
	};

	$.sharelinks.cleanup = function() {
		var node = this;
		node.removeAttr('url')
			.removeAttr('type')
			.removeAttr('title')
			.removeAttr('content')
			.removeAttr('image')
			.removeAttr('commentid')
			.attr('loaded', 1);
	};

	$.sharelinks.popup = function(type, url) {
		var optionString = 'menubar=0,resizable=0,scrollbars=0,';

		/*switch(type) {
			case 'facebook':
				optionString += 'width=640,height=320';
				break;
			case 'twitter':
				optionString += 'width=640,height=320';
				break;
			case 'googleplus':
				optionString += 'width=640,height=320';
				break;
			case 'linkedin':
				optionString += 'width=640,height=320';
				break;
		}*/

		optionString += 'width=660,height=320';

		window.open(url, '', optionString);
	}

	module.resolve();
});

// ----------------------------------------------------------------------------
// markItUp! Universal MarkUp Engine, JQuery plugin
// v 1.1.7
// Dual licensed under the MIT and GPL licenses.
// ----------------------------------------------------------------------------
// Copyright (C) 2007-2010 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
//
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THE SOFTWARE.
// ----------------------------------------------------------------------------


/**
 * @package		Komento
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

Komento.module('markitup', function($) {
	var module = this;

	$.fn.markItUp = function(settings, extraSettings) {
		var options, ctrlKey, shiftKey, altKey;
		ctrlKey = shiftKey = altKey = false;

		options = {	id:						'',
					nameSpace:				'',
					root:					'',
					previewInWindow:		'', // 'width=800, height=600, resizable=yes, scrollbars=yes'
					previewAutoRefresh:		true,
					previewPosition:		'after',
					previewTemplatePath:	'~/templates/preview.html',
					previewParserPath:		'',
					previewParserVar:		'data',
					resizeHandle:			true,
					beforeInsert:			'',
					afterInsert:			'',
					onEnter:				{},
					onShiftEnter:			{},
					onCtrlEnter:			{},
					onTab:					{},
					markupSet:			[	{ /* set */ } ]
				};
		$.extend(options, settings, extraSettings);

		// compute markItUp! path
		if (!options.root) {
			$('script').each(function(a, tag) {
				miuScript = $(tag).get(0).src.match(/(.*)jquery\.markitup(\.pack)?\.js$/);
				if (miuScript !== null) {
					options.root = miuScript[1];
				}
			});
		}

		return this.each(function() {
			var $$, textarea, levels, scrollPosition, caretPosition, caretOffset,
				clicked, hash, header, footer, previewWindow, template, iFrame, abort;
			$$ = $(this);
			textarea = this;
			levels = [];
			abort = false;
			scrollPosition = caretPosition = 0;
			caretOffset = -1;

			options.previewParserPath = localize(options.previewParserPath);
			options.previewTemplatePath = localize(options.previewTemplatePath);

			// apply the computed path to ~/
			function localize(data, inText) {
				if (inText) {
					return 	data.replace(/("|')~\//g, "$1"+options.root);
				}
				return 	data.replace(/^~\//, options.root);
			}

			// init and build editor
			function init() {
				id = ''; nameSpace = '';
				if (options.id) {
					id = 'id="'+options.id+'"';
				} else if ($$.attr("id")) {
					id = 'id="markItUp'+($$.attr("id").substr(0, 1).toUpperCase())+($$.attr("id").substr(1))+'"';

				}
				if (options.nameSpace) {
					nameSpace = 'class="'+options.nameSpace+'"';
				}
				$$.wrap('<div '+nameSpace+'></div>');
				$$.wrap('<div '+id+' class="markItUp"></div>');
				$$.wrap('<div class="markItUpContainer"></div>');
				$$.addClass("markItUpEditor");

				// add the header before the textarea
				header = $('<div class="markItUpHeader"></div>').insertBefore($$);
				$(dropMenus(options.markupSet)).appendTo(header);

				// add the footer after the textarea
				footer = $('<div class="markItUpFooter"></div>').insertAfter($$);

				// add the resize handle after textarea
				if (options.resizeHandle === true && $.browser.safari !== true) {
					resizeHandle = $('<div class="markItUpResizeHandle"></div>')
						.insertAfter($$)
						.bind("mousedown", function(e) {
							var h = $$.height(), y = e.clientY, mouseMove, mouseUp;
							mouseMove = function(e) {
								$$.css("height", Math.max(20, e.clientY+h-y)+"px");
								return false;
							};
							mouseUp = function(e) {
								$("html").unbind("mousemove", mouseMove).unbind("mouseup", mouseUp);
								return false;
							};
							$("html").bind("mousemove", mouseMove).bind("mouseup", mouseUp);
					});
					footer.append(resizeHandle);
				}

				// listen key events
				$$.keydown(keyPressed).keyup(keyPressed);

				// bind an event to catch external calls
				$$.bind("insertion", function(e, settings) {
					if (settings.target !== false) {
						get();
					}
					if (textarea === $.markItUp.focused) {
						markup(settings);
					}
				});

				// remember the last focus
				$$.focus(function() {
					$.markItUp.focused = this;
				});
			}

			// recursively build header with dropMenus from markupset
			function dropMenus(markupSet) {
				var ul = $('<ul></ul>'), i = 0;
				$('li:hover > ul', ul).css('display', 'block');
				$.each(markupSet, function() {
					var button = this, t = '', title, li, j;
					title = (button.key) ? (button.name||'')+' [Ctrl+'+button.key+']' : (button.name||'');
					key   = (button.key) ? 'accesskey="'+button.key+'"' : '';
					if (button.separator) {
						li = $('<li class="markItUpSeparator">'+(button.separator||'')+'</li>').appendTo(ul);
					} else {
						i++;
						for (j = levels.length -1; j >= 0; j--) {
							t += levels[j]+"-";
						}
						li = $('<li class="markItUpButton markItUpButton'+t+(i)+' '+(button.className||'')+'"><a href="" '+key+' title="'+title+'">'+(button.name||'')+'</a></li>')
						.bind("contextmenu", function() { // prevent contextmenu on mac and allow ctrl+click
							return false;
						}).click(function() {
							return false;
						}).mousedown(function() {
							if (button.call) {
								eval(button.call)();
							}
							setTimeout(function() { markup(button) },1);
							return false;
						}).hover(function() {
								$('> ul', this).show();
								$(document).one('click', function() { // close dropmenu if click outside
										$('ul ul', header).hide();
									}
								);
							}, function() {
								$('> ul', this).hide();
							}
						).appendTo(ul);
						if (button.dropMenu) {
							levels.push(i);
							$(li).addClass('markItUpDropMenu').append(dropMenus(button.dropMenu));
						}
					}
				});
				levels.pop();
				return ul;
			}

			// markItUp! markups
			function magicMarkups(string) {
				if (string) {
					string = string.toString();
					string = string.replace(/\(\!\(([\s\S]*?)\)\!\)/g,
						function(x, a) {
							var b = a.split('|!|');
							if (altKey === true) {
								return (b[1] !== undefined) ? b[1] : b[0];
							} else {
								return (b[1] === undefined) ? "" : b[0];
							}
						}
					);
					// [![prompt]!], [![prompt:!:value]!]
					string = string.replace(/\[\!\[([\s\S]*?)\]\!\]/g,
						function(x, a) {
							var b = a.split(':!:');
							if (abort === true) {
								return false;
							}
							value = prompt(b[0], (b[1]) ? b[1] : '');
							if (value === null) {
								abort = true;
							}
							return value;
						}
					);
					return string;
				}
				return "";
			}

			// prepare action
			function prepare(action) {
				if ($.isFunction(action)) {
					action = action(hash);
				}
				return magicMarkups(action);
			}

			// build block to insert
			function build(string) {
				openWith 	= prepare(clicked.openWith);
				placeHolder = prepare(clicked.placeHolder);
				replaceWith = prepare(clicked.replaceWith);
				closeWith 	= prepare(clicked.closeWith);
				if (replaceWith !== "") {
					block = openWith + replaceWith + closeWith;
				} else if (selection === '' && placeHolder !== '') {
					block = openWith + placeHolder + closeWith;
				} else {
					block = openWith + (string||selection) + closeWith;
				}
				return {	block:block,
							openWith:openWith,
							replaceWith:replaceWith,
							placeHolder:placeHolder,
							closeWith:closeWith
					};
			}

			// define markup to insert
			function markup(button) {
				var len, j, n, i;
				hash = clicked = button;
				get();

				$.extend(hash, {	line:"",
						 			root:options.root,
									textarea:textarea,
									selection:(selection||''),
									caretPosition:caretPosition,
									ctrlKey:ctrlKey,
									shiftKey:shiftKey,
									altKey:altKey
								}
							);
				// callbacks before insertion
				prepare(options.beforeInsert);
				prepare(clicked.beforeInsert);
				if (ctrlKey === true && shiftKey === true) {
					prepare(clicked.beforeMultiInsert);
				}
				$.extend(hash, { line:1 });

				if (ctrlKey === true && shiftKey === true) {
					lines = selection.split(/\r?\n/);
					for (j = 0, n = lines.length, i = 0; i < n; i++) {
						if ($.trim(lines[i]) !== '') {
							$.extend(hash, { line:++j, selection:lines[i] } );
							lines[i] = build(lines[i]).block;
						} else {
							lines[i] = "";
						}
					}
					string = { block:lines.join('\n')};
					start = caretPosition;
					len = string.block.length + (($.browser.opera) ? n : 0);
				} else if (ctrlKey === true) {
					string = build(selection);
					start = caretPosition + string.openWith.length;
					len = string.block.length - string.openWith.length - string.closeWith.length;
					len -= fixIeBug(string.block);
				} else if (shiftKey === true) {
					string = build(selection);
					start = caretPosition;
					len = string.block.length;
					len -= fixIeBug(string.block);
				} else {
					string = build(selection);
					start = caretPosition + string.block.length ;
					len = 0;
					start -= fixIeBug(string.block);
				}
				if ((selection === '' && string.replaceWith === '')) {
					caretOffset += fixOperaBug(string.block);

					start = caretPosition + string.openWith.length;
					len = string.block.length - string.openWith.length - string.closeWith.length;

					caretOffset = $$.val().substring(caretPosition,  $$.val().length).length;
					caretOffset -= fixOperaBug($$.val().substring(0, caretPosition));
				}
				$.extend(hash, { caretPosition:caretPosition, scrollPosition:scrollPosition } );

				if (string.block !== selection && abort === false) {
					insert(string.block);
					set(start, len);
				} else {
					caretOffset = -1;
				}
				get();

				$.extend(hash, { line:'', selection:selection });

				// callbacks after insertion
				if (ctrlKey === true && shiftKey === true) {
					prepare(clicked.afterMultiInsert);
				}
				prepare(clicked.afterInsert);
				prepare(options.afterInsert);

				// refresh preview if opened
				if (previewWindow && options.previewAutoRefresh) {
					refreshPreview();
				}

				// reinit keyevent
				shiftKey = altKey = ctrlKey = abort = false;
			}

			// Substract linefeed in Opera
			function fixOperaBug(string) {
				if ($.browser.opera) {
					return string.length - string.replace(/\n*/g, '').length;
				}
				return 0;
			}
			// Substract linefeed in IE
			function fixIeBug(string) {
				if ($.browser.msie) {
					return string.length - string.replace(/\r*/g, '').length;
				}
				return 0;
			}

			// add markup
			function insert(block) {
				if (document.selection) {
					var newSelection = document.selection.createRange();
					newSelection.text = block;
				} else {
					$$.val($$.val().substring(0, caretPosition)	+ block + $$.val().substring(caretPosition + selection.length, $$.val().length));
				}
				$$.trigger('textChange', $$.val());
			}

			// set a selection
			function set(start, len) {
				if (textarea.createTextRange){
					// quick fix to make it work on Opera 9.5
					if ($.browser.opera && $.browser.version >= 9.5 && len == 0) {
						return false;
					}
					range = textarea.createTextRange();
					range.collapse(true);
					range.moveStart('character', start);
					range.moveEnd('character', len);
					range.select();
				} else if (textarea.setSelectionRange ){
					textarea.setSelectionRange(start, start + len);
				}
				textarea.scrollTop = scrollPosition;
				textarea.focus();
			}

			// get the selection
			function get() {
				textarea.focus();

				scrollPosition = textarea.scrollTop;
				if (document.selection) {
					selection = document.selection.createRange().text;
					if ($.browser.msie) { // ie
						var range = document.selection.createRange(), rangeCopy = range.duplicate();
						rangeCopy.moveToElementText(textarea);
						caretPosition = -1;
						while(rangeCopy.inRange(range)) { // fix most of the ie bugs with linefeeds...
							rangeCopy.moveStart('character');
							caretPosition ++;
						}
					} else { // opera
						caretPosition = textarea.selectionStart;
					}
				} else { // gecko & webkit
					caretPosition = textarea.selectionStart;
					selection = $$.val().substring(caretPosition, textarea.selectionEnd);
				}
				return selection;
			}

			// open preview window
			function preview() {
				if (!previewWindow || previewWindow.closed) {
					if (options.previewInWindow) {
						previewWindow = window.open('', 'preview', options.previewInWindow);
					} else {
						iFrame = $('<iframe class="markItUpPreviewFrame"></iframe>');
						if (options.previewPosition == 'after') {
							iFrame.insertAfter(footer);
						} else {
							iFrame.insertBefore(header);
						}
						previewWindow = iFrame[iFrame.length - 1].contentWindow || frame[iFrame.length - 1];
					}
				} else if (altKey === true) {
					// Thx Stephen M. Redd for the IE8 fix
					if (iFrame) {
						iFrame.remove();
					} else {
						previewWindow.close();
					}
					previewWindow = iFrame = false;
				}
				if (!options.previewAutoRefresh) {
					refreshPreview();
				}
			}

			// refresh Preview window
			function refreshPreview() {
 				renderPreview();
			}

			function renderPreview() {
				var phtml;
				if (options.previewParserPath !== '') {
					$.ajax( {
						type: 'POST',
						url: options.previewParserPath,
						data: options.previewParserVar+'='+encodeURIComponent($$.val()),
						success: function(data) {
							writeInPreview( localize(data, 1) );
						}
					} );
				} else {
					if (!template) {
						$.ajax( {
							url: options.previewTemplatePath,
							success: function(data) {
								writeInPreview( localize(data, 1).replace(/<!-- content -->/g, $$.val()) );
							}
						} );
					}
				}
				return false;
			}

			function writeInPreview(data) {
				if (previewWindow.document) {
					try {
						sp = previewWindow.document.documentElement.scrollTop
					} catch(e) {
						sp = 0;
					}
					previewWindow.document.open();
					previewWindow.document.write(data);
					previewWindow.document.close();
					previewWindow.document.documentElement.scrollTop = sp;
				}
				if (options.previewInWindow) {
					previewWindow.focus();
				}
			}

			// set keys pressed
			function keyPressed(e) {
				shiftKey = e.shiftKey;
				altKey = e.altKey;
				ctrlKey = (!(e.altKey && e.ctrlKey)) ? e.ctrlKey : false;

				// ctrlKey fix
				ctrlKey = false;

				if (e.type === 'keydown') {
					if (ctrlKey === true) {
						li = $("a[accesskey="+String.fromCharCode(e.keyCode)+"]", header).parent('li');
						if (li.length !== 0) {
							ctrlKey = false;
							setTimeout(function() {
								li.triggerHandler('mousedown');
							},1);
							return false;
						}
					}
					if (e.keyCode === 13 || e.keyCode === 10) { // Enter key
						if (ctrlKey === true) {  // Enter + Ctrl
							ctrlKey = false;
							markup(options.onCtrlEnter);
							return options.onCtrlEnter.keepDefault;
						} else if (shiftKey === true) { // Enter + Shift
							shiftKey = false;
							markup(options.onShiftEnter);
							return options.onShiftEnter.keepDefault;
						} else { // only Enter
							markup(options.onEnter);
							return options.onEnter.keepDefault;
						}
					}
					if (e.keyCode === 9) { // Tab key
						if (shiftKey == true || ctrlKey == true || altKey == true) { // Thx Dr Floob.
							return false;
						}
						if (caretOffset !== -1) {
							get();
							caretOffset = $$.val().length - caretOffset;
							set(caretOffset, 0);
							caretOffset = -1;
							return false;
						} else {
							markup(options.onTab);
							return options.onTab.keepDefault;
						}
					}
				}
			}

			init();
		});
	};

	$.fn.markItUpRemove = function() {
		return this.each(function() {
				var $$ = $(this).unbind().removeClass('markItUpEditor');
				$$.parent('div').parent('div.markItUp').parent('div').replaceWith($$);
			}
		);
	};

	$.markItUp = function(settings) {
		var options = { target:false };
		$.extend(options, settings);
		if (options.target) {
			return $(options.target).each(function() {
				$(this).focus();
				$(this).trigger('insertion', [options]);
			});
		} else {
			$('textarea').trigger('insertion', [options]);
		}
	};

	module.resolve();
});

Komento.module('komento.commenttools', function($){
var module = this;

Komento.require()
	.script('komento.commentlist', 'komento.common', 'komento.language')
	.library('dialog')
	.view('notifications/new.comment')
	.done(function(){
		Komento.Controller(
			'CommentTools',
			{
				defaults: {
					'{commentCounter}': '.commentCounter',
					'{sortOldest}': '.sortOldest a',
					'{sortLatest}': '.sortLatest a',
					'{sortButton}': '.kmt-sorting a',
					'{adminButton}': '.adminMode a',
					'{subscribeEmail}': '.subscribeEmail a',
					'{addYours}': '.kmt-addyours',
					view: {
						newComment: 'notifications/new.comment'
					}
				}
			},
			function(self)
			{ return {
				init: function(){
					if(Komento.options.acl.read_comment == 1 && Komento.options.konfig.enable_ajax_load_list == 1){
						self.loadCommentList();
					}

					if(Komento.options.config.enable_live_notification == 1){
						self.hasNew = 0;
						self.newCount = 0;
						self.checkNewComment();
					}
				},

				loadCommentList: function(){
					self.reloadComments(Komento.sort);
				},

				'{addYours} click': function() {
					if(self.kmt.form) {
						self.kmt.form.loadForm();
					}
				},

				'{sortOldest} click': function(el){
					if(self.sortButton().checkClick())
					{
						self.sortButton().removeClass('selected');
						el.loading().addClass('selected');
						self.reloadComments('oldest');
					}
				},

				'{sortLatest} click': function(el){
					if(self.sortButton().checkClick()){
						self.sortButton().removeClass('selected');
						el.loading().addClass('selected');
						self.reloadComments('latest');
					}
				},

				'{sortLikes} click': function(el){
					if(self.sortButton().checkClick()){
						self.sortButton().removeClass('selected');
						el.loading().addClass('selected');
						self.reloadComments('likes');
					}
				},

				'{adminButton} click': function(el){
					self.reloadComments(Komento.sort, 'all');
				},

				'{subscribeEmail} click': function(el){
					$.dialog({
						content: self.getSubscribeContent(),
						afterShow: function(){
							var dialog = $('.foundryDialog'),
								submit = dialog.find('.subscribe'),
								unsubscribe = dialog.find('.unsubscribe'),
								name = dialog.find('.subscribeName'),
								email = dialog.find('.subscribeEmail'),
								user = dialog.find('.subscribeUser'),
								error = dialog.find('.subscribeError');

							submit.click(function(){
								if(user.length > 0) {
									Komento.ajax('site.views.komento.subscribeuser', {
										user: user.val(),
										component: Komento.component,
										cid: Komento.cid
									}, {
										success: function() {
											if(self.kmt.form) {
												self.kmt.form.subscribeForm().text($.language('COM_KOMENTO_FORM_NOTIFICATION_SUBSCRIBED')).addClass('subscribed');
											}

											$('.foundryDialog').controller().close();
										}
									})
								} else {
									if(name.val() == '' || email.val() == '') {
										error.show();
										return false;
									}

									Komento.ajax('site.views.komento.subscribeguest', {
										name: name.val(),
										email: email.val(),
										component: Komento.component,
										cid: Komento.cid
									}, {
										success: function() {
											$('.foundryDialog').controller().close();

											el.remove();
										}
									})
								}
							});

							unsubscribe.click(function() {
								Komento.ajax('site.views.komento.unsubscribeuser', {
									user: user.val(),
									component: Komento.component,
									cid: Komento.cid
								}, {
									success: function() {
										if(self.kmt.form) {
											self.kmt.form.subscribeForm().text($.language('COM_KOMENTO_FORM_NOTIFICATION_UNSUBSCRIBED')).removeClass('subscribed');
										}

										$('.foundryDialog').controller().close();
									}
								});
							});
						}
					});
				},

				getSubscribeContent: function(){
					var dfd = $.Deferred();

					Komento.ajax('site.views.komento.getsubscribecontent', {
						component: Komento.component,
						cid: Komento.cid
					}, {
						success: function(html){
							dfd.resolve(html);
						}
					});

					return dfd;
				},

				reloadComments: function(sort, published){
					// cancel all replies first
					// There are cases that self.kmt is not ready yet
					if(self.kmt){
						self.kmt.commentlist.cancelReply();
					}

					$('.commentList').html('<img src="' + Komento.options.spinner + '" />' + $.language('COM_KOMENTO_COMMENTS_LOADING'));
					Komento.ajax('site.views.komento.reloadcomments', {
						component: Komento.component,
						cid: Komento.cid,
						contentLink: Komento.contentLink,
						options: {
							sort: sort,
							published: published
						}
					},
					{
						success: function(html, loadedCount, totalCount)
						{
							Komento.sort = sort;
							Komento.loadedCount = loadedCount;
							Komento.totalCount = totalCount;

							self.setCommentCounter(loadedCount);

							var newCommentList = $(html);

							$('.commentList').html(newCommentList).controller().init();

							self.sortButton()
								.enable()
								.doneLoading();

							$('.kmt-notification').remove();
						},

						fail: function()
						{
							$('.commentList').text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				checkNewComment: function(){
					var timer = Komento.options.config.live_notification_interval ? parseInt(Komento.options.config.live_notification_interval) * 1000 : 30000;
					setTimeout(function(){
						Komento.ajax('site.views.komento.checknewcomment', {
							component: Komento.component,
							cid: Komento.cid,
							total: Komento.totalCount
						},
						{
							success: function(hasNew, newCount){
								if( hasNew == 1 && newCount > self.newCount ){
									$('.kmt-notification').remove();

									var notification = self.view.newComment({count: newCount});

									notification.find('.reloadComments').bind('click', function(){
										self.kmt.tools.reloadComments(Komento.sort);
									});

									$('body').append(notification);
								}

								self.newCount = newCount;
								self.hasNew = hasNew;
							},

							fail: function(){

							},

							complete: function(){
								self.kmt.tools.checkNewComment();
							}
						})
					}, timer);
				},

				addCommentCounter: function(){
					var current = self.commentCounter().text();
					self.commentCounter().text(parseInt(current) + 1);
				},

				deductCommentCounter: function(count){
					if( count === undefined ){
						count = 1;
					}
					var current = self.commentCounter().text();
					self.commentCounter().text(parseInt(current) - count);

					Komento.loadedCount -= count;
					Komento.totalCount -= count;
				},

				setCommentCounter: function(count){
					if( count === undefined ){
						count = 0;
					}

					self.commentCounter().text(count);
				}
			} }
		);

		module.resolve();
	});
});

Komento.module('komento.famelist', function($) {
var module = this;

Komento.require()
	.library('ui/effect', 'dialog')
	.image(Komento.options.spinner)
	.script('sharelinks', 'markitup', 'komento.common', 'komento.commentitem', 'komento.language')
	.view(
		'dialogs/delete.single',
		'dialogs/delete.affectchild',
		'dialogs/unpublish.affectchild',
		'comment/item/edit.form',
		'dialogs/delete.attachment'
	)
	.done(function() {
		Komento.Controller(
			'FameList',
			{
				defaults: {
					'{commentList}': '.kmt-list',
					'{stickList}': '.stickList .kmt-list',
					'{loveList}': '.loveList .kmt-list',
					'{commentItem}': '.kmt-item',
					'{stickItem}': '.stickList .kmt-list .kmt-item',
					'{loveItem}': '.loveList .kmt-list .kmt-item',
					'{noComment}': '.kmt-empty-comment',
					'{loadMore}': '.loadMore',
					// Tabs
					'{navs}': '.navs',
					'{tabs}': '.kmt-tabs',
					// Comment Item
					'{commentText}'			: '.commentText',
					'{commentInfo}'			: '.commentInfo',
					'{commentForm}'			: '.commentForm',
					'{deleteButton}'		: '.deleteButton',
					'{editButton}'			: '.editButton',
					'{saveEditButton}'		: '.saveEditButton',
					'{editForm}'			: '.editForm',
					'{editInput}'			: '.editInput',
					'{replyButton}'			: '.replyButton',
					'{shareBox}'			: '.shareBox',
					'{reportButton}'		: '.reportButton',
					'{statusButton}'		: '.statusButton',
					'{statusOptions}'		: '.statusOptions',
					'{publishButton}'		: '.publishButton',
					'{unpublishButton}'		: '.unpublishButton',
					'{stickButton}'			: '.stickButton',
					'{likeButton}'			: '.likeButton',
					'{likesCounter}'		: '.likesCounter',
					'{parentLink}'			: '.parentLink',
					'{parentContainer}'		: '.parentContainer',
					'{socialButton}'		: '.socialButton',
					view: {
						editForm: 'comment/item/edit.form',
						deleteDialog: 'dialogs/delete.affectchild',
						publishDialog: 'dialogs/publish.affectchild',
						unpublishDialog: 'dialogs/unpublish.affectchild',
						deleteAttachment: 'dialogs/delete.attachment'
					}
				}
			},
			function(self)
			{ return {
				init: function() {
					if(self.navs().length > 0) {
						self.navs().eq(0).trigger('click');
					}
				},

				// initialise for ajax calls
				ajaxinit: function() {
					self.generateSharelinks();
				},

				generateSharelinks: function() {
					var callback = function() {
						self.socialButton().each(function(i, el) {
							$(el).sharelinks();
						});
					};

					self.generateShortLinks(callback);
				},

				generateShortLinks: function(callback) {
					var replaceUrl = function(url) {
						self.socialButton().each(function(index, element) {
							if( !$(element).attr('loaded') ) {
								var commentid = $(element).attr('commentid');
								var permalink = url + '#kmt-' + commentid;

								$(element).attr('url', permalink);
								$(element).parents('.kmt-share-balloon').find('.short-url').val(permalink);
							}
						});

						callback && callback();
					};

					if(!Komento.shortenLink) {
						$.shortenlink(Komento.contentLink, replaceUrl);
					} else {
						replaceUrl(Komento.shortenLink);
					}
				},

				/************** Tabs **************/
				'{navs} click': function(el) {
					var funcName = $(el).attr('func');
					var tabName = $(el).attr('tab');
					var tab = $('.' + tabName);

					if(!tab.attr('loaded'))
					{
						self[funcName]();

						if((tabName == 'stickList' && Komento.options.konfig.enforce_live_stickies != '1') || (tabName == 'loveList' && Komento.options.konfig.enforce_live_lovies != '1'))
						{
							tab.attr('loaded', 1);
						}
					}

					self.tabs().hide();
					tab.show();

					self.navs().removeClass('active');
					el.addClass('active');
				},

				loadMainList: function() {
					self.loadComments('main');
				},

				loadStickList: function() {
					if(Komento.options.acl.read_stickies == 1 && Komento.options.config.enable_stickies == 1 && Komento.options.konfig.enable_ajax_load_stickies == 1) {
						self.loadComments('stickies');
					}
				},

				loadLoveList: function() {
					if(Komento.options.acl.read_lovies == 1 && Komento.options.config.enable_lovies == 1 && Komento.options.konfig.enable_ajax_load_lovies == 1) {
						self.loadComments('lovies');
					}
				},

				loadComments: function(type) {
					var classname, sticked, threaded, sort;

					threaded = 0;

					switch(type) {
						case 'stickies':
							classname = '.stickList';
							sticked = 1;
							sort = 'default';
							limit = parseInt(Komento.options.config.max_stickies);
							break;
						case 'lovies':
							classname = '.loveList';
							sticked = 'all';
							sort = 'love';
							limit = parseInt(Komento.options.config.max_lovies);
							break;
						default:
							classname = '.mainList';
							sticked = 'all';
							sort = 'default';
							limit = parseInt(Komento.options.config.max_comments_per_page);
					}

					$(classname).html('<div class="loading"><img src="' + Komento.options.spinner + '" />' + $.language('COM_KOMENTO_COMMENTS_LOADING') + '</div>');

					Komento.ajax('site.views.komento.loadcomments', {
						type: type,
						component: Komento.component,
						cid: Komento.cid,
						sticked: sticked,
						threaded: threaded,
						sort: sort,
						limit: limit,
						contentLink: Komento.contentLink,
					},
					{
						success: function(html) {
							var newCommentList = $(html);

							$(classname).html(newCommentList);
							self.ajaxinit();
						},

						fail: function() {
							$(classname).text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				/************** List Actions **************/

				stickComment: function(item) {
					item = $(item);

					// remove depth indentation
					item.children('.kmt-wrap').attr('style', 'left-margin: 0px !important');

					var itemid = item.attr('id').split('-')[1];
					var inserted = 0;

					if(self.stickItem().length == 0) {
						self.loadStickList();
					} else {

						if(self.stickItem().length < Komento.options.config.max_stickies)
						{
							self.stickItem().each(function(i, el) {
								if($(el).attr('id').split('-')[1] > itemid) {
									$(el).before(item);
									inserted = 1;
									return;
								}
							});

							if(inserted == 0) {
								self.stickList().append($(item));
							}
						}
					}
				},

				unstickComment: function(commentid) {
					self.stickList().find('#' + commentid).remove();
				},

				/************** Comment Items **************/

				set: function(el) {
					self.item = el.itemset(self.options);
				},

				'{deleteButton} click': function(el) {
					self.set(el);
					self.showDeleteDialog();
				},

				'{editButton} click': function(el) {
					self.set(el);
					if(el.checkClick()) {
						if(el.checkSwitch()) {
							self.edit(el);
						} else {
							self.cancelEdit(el);
						}
					}
				},

				'{saveEditButton} click': function(el) {
					self.set(el);
					self.saveEdit(el);
				},

				'{replyButton} click': function(el) {
					self.set(el);
					if(Komento.options.konfig.enable_inline_reply == 1) {
						if(el.checkSwitch()) {
							self.reply(el);
						} else {
							self.cancelReply(el);
						}
					} else {
						self.kmt.form.staticReply(self.item.parentid);
					}
				},

				'{reportButton} click': function(el) {
					self.set(el);
					if(el.checkClick()) {
						if(el.checkSwitch()) {
							self.reportComment(el);
						} else {
							self.cancelreportComment(el);
						}
					}
				},

				'{unpublishButton} click': function() {
					self.set(el);
					if(self.childs > 0) {
						self.showUnpublishDialog(el);
					} else {
						self.unpublishComment(el);
					}
				},

				'{stickButton} click': function(el) {
					self.set(el);
					if(el.checkClick()) {
						if(el.checkSwitch()) {
							self.stick(el);
						} else {
							self.unstick(el);
						}
					}
				},

				'{likesCounter} click': function(el) {
					self.set(el);

					self.showLikesDialog();
				},

				'{likeButton} click': function(el) {
					self.set(el);
					if(el.checkClick()) {
						if(el.checkSwitch()) {
							self.like(el);
						} else {
							self.unlike(el);
						}
					}
				},

				'{parentLink} mouseover': function(el) {
					self.set(el);
					if(Komento.options.config.enable_threaded == 1) {
						$('#' + self.item.parentid).addClass('kmt-highlight');
					} else {
						self.item.element.mine.parentContainer.show();
						if(self.item.element.mine.parentContainer.attr('loaded') == 0) {
							self.item.element.mine.parentContainer.html('<img src="' + Komento.options.spinner + '" />');
							self.loadParent();
						}
					}
				},

				'{parentLink} mouseout': function(el) {
					self.set(el);
					if(Komento.options.config.enable_threaded == 1) {
						$('#' + self.item.parentid).removeClass('kmt-highlight');
					} else {
						self.item.element.mine.parentContainer.hide();
					}
				},

				'{parentLink} click': function(el) {
					self.set(el);
					var parent = $('.' + self.item.parentid);
					parent.highlight();
				},

				'{attachmentDelete} click': function(el) {
					self.set(el);

					self.showAttachmentDeleteDialog(el);
				},

				closeDialog: function() {
					if($('.foundryDialog').length > 0) {
						$('.foundryDialog').controller().close();
					}
				},

				edit: function() {
					var editWrap = self.item.mine.find('#' + self.item.commentid + '-edit');
					if(editWrap.length == 0) {
						Komento.ajax('site.views.komento.getcommentraw', {
							id: self.item.id
						},
						{
							success: function(comment) {
								self.item.element.mine.editButton
									.text($.language('COM_KOMENTO_COMMENT_EDIT_CANCEL'))
									.switchOff()
									.enable();

								var editForm = self.view.editForm({commentId: self.item.commentid, commentText: comment});
								self.item.element.mine.commentText.after(editForm);

								// set again for newly generated editform and editinput because previously set, dom for editform and editinput not yet created
								self.item.element.mine.editForm = $(editForm);
								self.item.element.mine.editInput = self.item.element.mine.editForm.find('.editInput');
								self.item.mine.data('item', self.item);

								if(Komento.options.config.enable_bbcode == 1) {
									self.item.element.mine.editInput.markItUp($.getBBcodeSettings());
								}
							},

							fail: function() {
								self.item.element.mine.editButton
									.text($.language('COM_KOMENTO_ERROR'));
							}
						});
					} else {
						editWrap.show();

						self.item.element.mine.editButton
							.text($.language('COM_KOMENTO_COMMENT_EDIT_CANCEL'))
							.switchOff()
							.enable();
					}

					// bugged
					// .after insert doesnt register at first
					// $('#' + commentId + '-edit').find('textarea').focus();
				},

				cancelEdit: function() {
					self.item.element.mine.editButton
						.text($.language('COM_KOMENTO_COMMENT_EDIT'))
						.switchOn()
						.enable();
					self.item.element.mine.editForm.hide();
				},

				saveEdit: function() {
					Komento.ajax('site.views.komento.editcomment', {
						id: self.item.id,
						edittedComment: self.item.element.mine.editInput.val()
					},
					{
						success: function(modified_html, modified_by, modified) {
							self.item.element.both.commentText.html(modified_html);
							self.item.element.both.commentInfo.text($.language('COM_KOMENTO_COMMENT_EDITTED_BY', modified_by, modified)).show();

							if(Komento.options.config.enable_syntax_highlighting == 1) {
								$.loadSHBrushes();
							}

							self.cancelEdit();
						},

						fail: function(data) {
							$.dialog({
								content: message
							});
						}
					});
				},

				reply: function() {
					var commentForm = $('.commentForm');

					$('.formAlert').hide().text('');;

					// only allow 1 reply at a time
					// revert all other cancelReplyButton upon clicking reply
					$(self.options['{replyButton}'])
						.switchOn()
						.find('span')
						.text($.language('COM_KOMENTO_COMMENT_REPLY'));

					self.item.element.mine.replyButton
						.switchOff()
						.find('span')
						.text($.language('COM_KOMENTO_COMMENT_REPLY_CANCEL'));

					self.kmt.form.reply(self.item);
				},

				cancelReply: function() {
					$(self.options['{replyButton}'])
						.switchOn()
						.find('span')
						.text($.language('COM_KOMENTO_COMMENT_REPLY'));

					self.kmt.form.cancelReply();
				},

				showDeleteDialog: function() {
					$.dialog({
						height: 150,
						content: self.view.deleteDialog(true, {childs: self.childs}),
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

				deleteComment: function(affectChild) {
					$('.foundryDialog').find('.kmt-delete-status').show();

					Komento.ajax('site.views.komento.deletecomment', {
						id: self.item.id,
						affectChild: affectChild
					},
					{
						success: function() {
							self.closeDialog();

							if(affectChild) {
								self.deleteChild(self.item.id);
							} else {
								self.moveChildUp(self.item.id, self.item.parentid);
							}

							self.item.both.hide('fade', function() {
								self.item.both.remove();
							});
						},

						fail: function() {
							$('.foundryDialog').find('.kmt-delete-status').text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				deleteChild: function(id) {
					$('li[parentid="' + id + '"]').each(function() {

						// recursive function to delete other childs
						self.deleteChild($(this).attr('id'));
					}).hide('fade', function() {
						$(this).remove();
					});
				},

				moveChildUp: function(id, parentid) {
					$('li[parentid="' + id + '"]').attr('parentid', parentid).each(function() {

						// move child class up 1 level
						$(this).removeClass('kmt-child-' + $(this).attr('depth')).addClass('kmt-child-' + ($(this).attr('depth') - 1));

						// move depth up 1 level
						$(this).attr('depth', ($(this).attr('depth') - 1));

						// recursive function to move other childs
						self.moveChildUp($(this).attr('id'));
					});
				},

				reportComment: function() {
					Komento.ajax('site.views.komento.action', {
						id: self.item.id,
						type: 'report',
						action: 'add'
					},
					{
						success: function() {
							self.item.element.both.reportButton
								.text($.language('COM_KOMENTO_COMMENT_REPORTED'))
								.switchOff()
								.enable();
						},

						fail: function() {
							self.item.element.both.reportButton
								.text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				cancelreportComment: function() {
					Komento.ajax('site.views.komento.action', {
						id: self.item.id,
						type: 'report',
						action: 'remove'
					},
					{
						success: function() {
							self.item.element.both.reportButton
								.text($.language('COM_KOMENTO_COMMENT_REPORT'))
								.switchOn()
								.enable();
						},

						fail: function() {
							self.item.element.both.reportButton
								.text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				showUnpublishDialog: function() {
					$.dialog({
						height: 150,
						content: self.view.unpublishDialog(true, {childs: self.childs}),
						afterShow: function() {
							$('.foundryDialog').find('.unpublish-affectChild').click(function() {
								self.unpublishComment();
							});
						}
					});
				},

				unpublishComment: function() {
					Komento.ajax('site.views.komento.unpublish', {
						id: self.item.id
					},
					{
						success: function() {
							self.closeDialog();
							self.unpublishChild(self.item.id);
							self.item.both.hide('fade', function() {
								self.item.both.remove();
							});
						},

						fail: function() {

						}
					});
				},

				unpublishChild: function(id) {
					$('li[parentid="' + id + '"]').each(function() {
						self.unpublishChild($(this).attr('id'));
					}).hide('fade', function() {
						$(this).remove();
					});
				},

				stick: function(el) {
					Komento.ajax('site.views.komento.stick', {
						id: self.item.id
					},
					{
						success: function() {
							self.item.element.mine.stickButton
								.text($.language('COM_KOMENTO_COMMENT_UNSTICK'))
								.switchOff()
								.enable();

							self.item.mine.addClass('kmt-sticked');

							// append comment into stick list
							self.kmt.famelist.stickComment(self.item.mine.clone())
						},

						fail: function() {
							self.item.element.mine.stickButton
								.text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				unstick: function() {
					Komento.ajax('site.views.komento.unstick', {
						id: self.item.id
					},
					{
						success: function() {
							self.item.element.both.stickButton
								.text($.language('COM_KOMENTO_COMMENT_STICK'))
								.switchOn()
								.enable();

							self.item.both.removeClass('kmt-sticked');

							// remove comment from stick list
							self.unstickComment(self.item.commentid);
						},

						fail: function() {
							self.stickButton
								.text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				like: function() {
					Komento.ajax('site.views.komento.action', {
						id: self.item.id,
						type: 'likes',
						action: 'add'
					},
					{
						success: function() {
							self.item.element.both.likeButton
								.switchOff()
								.enable()
								.find('span')
								.text($.language('COM_KOMENTO_COMMENT_UNLIKE'));

							var likes = parseInt(self.item.element.mine.likesCounter.find('span').text()) + 1;
							self.item.element.both.likesCounter.find('span')
								.text(likes);
						},

						fail: function( message ) {
							self.item.element.both.likesCounter.find('span')
								.text( message );
								// .text($.language('COM_KOMENTO_ERROR'));
						}
					});
				},

				unlike: function() {
					Komento.ajax('site.views.komento.action', {
						id: self.item.id,
						type: 'likes',
						action: 'remove'
					},
					{
						success: function() {
							self.item.element.both.likeButton
								.switchOn()
								.enable()
								.find('span')
								.text($.language('COM_KOMENTO_COMMENT_LIKE'));

							var likes = parseInt(self.item.element.mine.likesCounter.find('span').text()) - 1;
							self.item.element.both.likesCounter.find('span')
								.text(likes);
						},

						fail: function() {
							self.item.element.both.likesCounter.find('span')
								.text($.language('COM_KOMENTO_ERROR'))
						}
					});
				},

				showLikesDialog: function(el) {
					Komento.ajax('site.views.komento.getLikedUsers', {
						id: self.item.id
					},
					{
						success: function(html) {
							$.dialog({
								title: $.language('COM_KOMENTO_COMMENT_PEOPLE_WHO_LIKED_THIS'),
								content: html
							});
						}
					});
				},

				loadParent: function() {
					var parent = $('#' + self.item.parentid);
					if(parent.length != 0) {
						var avatar = parent.find('.kmt-avatar:not(.parentContainer > .kmt-avatar)').clone();
						var author = parent.find('.kmt-author:not(.parentContainer > .kmt-author)').clone();
						var time = parent.find('.kmt-time:not(.parentContainer > .kmt-time)').clone();
						var text = parent.find('.commentText:not(.parentContainer > .commentText)').clone();

						// todo: configurable
						self.item.element.both.parentContainer.html('')
							.append(avatar)
							.append(author)
							.append(time)
							.append(text);
					} else {
						var parentid = self.item.parentid.split('-')[1];

						Komento.ajax('site.views.komento.getcomment', {
							id: parentid
						},
						{
							success: function(html) {
								self.item.element.both.parentContainer.html(html);
							},

							fail: function() {

							}
						})
					}
					self.item.element.both.parentContainer.attr('loaded', 1)
				},

				showAttachmentDeleteDialog: function(el) {
					var attachmentid = $(el).parents('.attachmentFile').attr('attachmentid');
					var attachmentname = $(el).parents('.attachmentFile').attr('attachmentname');

					$.dialog({
						height: 150,
						content: self.view.deleteAttachment(true, {attachmentname: attachmentname}),
						afterShow: function() {
							$('.foundryDialog').find('.delete-attachment').click(function() {
								self.closeDialog();
								self.deleteFile(attachmentid);
							});

							$('.foundryDialog').find('.delete-attachment-cancel').click(function() {
								self.closeDialog();
							});
						}
					});
				},

				deleteFile: function(attachmentid) {
					var commentid = self.item.id;

					Komento.ajax('site.views.komento.deleteAttachment', {
						id: commentid,
						attachmentid: attachmentid
					},
					{
						success: function() {
							self.item.element.both.attachmentFile.filter('.file-' + attachmentid).remove();

							if(self.item.mine.find('.attachmentFile').length == 0) {
								self.item.element.both.attachmentWrap.remove();
							}
						},

						fail: function(error) {
							error = '<p class="error">' + error + '</p>';
							self.item.element.both.attachmentFile.filter('.file-' + attachmentid).append(error);
						}
					})
				}
			} }
		);
		module.resolve();
	});
});

Komento.module('komento.insertvideo', function($) {
	var module = this;

	Komento.Controller(
		'InsertVideo',
	{
		defaults: {
			caretPosition: '',
			element: '',
			'{videoUrl}': '.videoUrl',
			'{insertVideo}': '.insertVideo',
			'{cancelVideo}': '.cancelVideo'
		},
	},
	function(self) { return {
		init: function() {
		},

		'{insertVideo} click': function() {
			self.addVideoLink();
		},

		'{cancelVideo} click': function() {
			self.closeDialog();
		},

		'{videoUrl} keypress': function(element, event) {
			if(event.keyCode == 13) {
				event.preventDefault();
				self.addVideoLink();
			}
		},

		addVideoLink: function() {
			var videoUrl = self.videoUrl().val();

			if(videoUrl === '') {
				return;
			}

			var textarea = $('#' + self.options.element);
			var tag = '[video]' + videoUrl + '[/video]';

			var contents = textarea.val();

			if(self.options.caretPosition == 0) {
				textarea.val(tag + contents);

				$('.commentInput').focus();
				$('.commentForm').controller().commentLengthCheck();

				self.closeDialog();
				return true;
			}

			var contents = textarea.val();

			textarea.val(contents.substring(0, self.options.caretPosition) + tag + contents.substring(self.options.caretPosition, contents.length));

			$('.commentInput').focus();
			$('.commentForm').controller().commentLengthCheck();

			self.closeDialog();
		},

		closeDialog: function() {
			$('.foundryDialog').controller().close();
		}
	} });

	module.resolve();
});

Komento.ready(function($) {
	if (Komento.options.responsive)
	{
		$(function() {
			$('#section-kmt').responsive([
				{at: 600, switchTo: 'w600'},
				{at: 400, switchTo: 'w600 w320'}
			]);
		});
	}
});

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

Komento.module('komento.ratings', function($) {
var module = this;

Komento.require()
	.library('ui/stars')
	.done(function() {

		Komento.Controller(
			'Ratings.Item',
			{
				defaults: {
					"{input}"	: "[data-rating-value]"
				}
			},
			function(self)
			{
				return {

					init: function() {
						self.element.stars(
						{
							split			: 2,
							disabled		: true,
							showTitles		: true
						});
					},
				}
			}
		);

		Komento.Controller(
			'Ratings.Form',
			{
				defaults: {
					"{input}"	: "[data-rating-value]",
					'{cancel}'	: '.ui-stars-cancel'
				}
			},
			function(self)
			{
				return {

					init: function() {

						self.element.stars(
						{
							split			: 2,
							disabled		: false,
							callback: function(element, className, value) {
								self.input().val(value);
							}
						});
					},

					'{self} clear': function() {
						self.cancel().trigger('click.stars');
					}
				}
			}
		);

		module.resolve();
	});
});

Komento.module('komento.upload', function($) {
	var module = this;

	Komento.require()
	.view('comment/form/uploadrow')
	.library('plupload')
	.done(function() {
		Komento.Controller(
			'UploadForm',
		{
			defaults: {
				uploadUrl: $.indexUrl + '?option=com_komento&controller=file&task=upload&component=' + Komento.component,
				uploadedId: [],
				'{uploader}': '.uploaderForm',
				'{uploadButton}': '.uploadButton',
				'{uploadArea}': '.uploadArea',
				'{uploadQueue}': '.uploadQueue',
				'{queueHeader}': '.queueHeader',
				'{queueRow}': '.queueRow',
				'{dragDrop}': '.dragDrop',
				'{removeFile}': '.removeFile',
				'{fileCounter}': '.fileCounter',
				view: {
					uploadrow: 'comment/form/uploadrow'
				}
			}
		},
		function(self) { return {
			init: function() {
				self.uploader().implement(
					'plupload',
					{
						settings: {
							url: self.options.uploadUrl,
							drop_element: 'uploadArea',
							max_file_size: Komento.options.config.upload_max_size + 'mb',
							filters: [
								{title: 'Allowed File Type', extensions: Komento.options.config.upload_allowed_extension}
							]
						},
						'{uploader}': '.uploaderForm',
						'{uploadButton}': '.uploadButton'
					},
					function() {
						self.plupload = this.plupload;

						if(self.plupload.runtime == 'html5') {
							self.enableDragDrop();
						}
					}
				);
			},

			"{uploader} FilesAdded": function(el, event, uploader, file) {
				self.addFiles(file);
			},

			'{uploader} UploadComplete': function(el, event, uploader, files) {
				$('.queueRow').each(function(index, item) {
					self.removeRow($(item).attr('id'));
				});
				self.kmt.form.postComment();
			},

			'{uploader} FileUploaded': function(el, event, uploader, file, response) {
				if( response.status == 1 ) {
					self.options.uploadedId.push(response.id);
				}

				if( response.status == 'notallowed' ) {
					self.plupload.stop();
					self.kmt.form.errorNotification($.language('COM_KOMENTO_FORM_NOTIFICATION_UPLOAD_NOT_ALLOWED'));
					return;
				}

				if( response.status == 'exceedfilesize' ) {
					self.plupload.stop();
					self.kmt.form.errorNotification($.language('COM_KOMENTO_FORM_NOTIFICATION_MAX_FILE_SIZE', Komento.options.config.upload_max_size + 'mb'));
					return;
				}
			},

			'{uploader} QueueChanged': function(el, event, uploader) {
				self.fileCounter().text(uploader.files.length);
			},

			'{uploader} Error': function(el, event, uploader, error) {
				self.kmt.form.clearNotification();
				switch(error.code) {
					case -600:
						self.kmt.form.errorNotification($.language('COM_KOMENTO_FORM_NOTIFICATION_MAX_FILE_SIZE', Komento.options.config.upload_max_size + 'mb'));
						break;
					case -601:
						self.kmt.form.errorNotification($.language('COM_KOMENTO_FORM_NOTIFICATION_FILE_EXTENSION', Komento.options.config.upload_allowed_extension));
						break;
				}
			},

			'{removeFile} click': function(el) {
				var id = el.parents('.queueRow').attr('id');
				self.removeRow(id);
			},

			enableDragDrop: function() {
				self.dragDrop().show();
			},

			startUpload: function() {
				if(self.plupload.files.length > 0) {
					self.plupload.start();
				} else {
					self.kmt.form.postComment();
				}
			},

			addFiles: function(file) {
				/* file = [{
					name,
					type,
					tmp_name,
					size
				}]*/

				$.each(file, function(index, item) {

					self.kmt.form.clearNotification();

					// check for file count before proceeding
					if(self.plupload.files.length > Komento.options.config.upload_max_file) {
						self.plupload.removeFile(item);
						self.kmt.form.errorNotification($.language('COM_KOMENTO_FORM_NOTIFICATION_MAX_FILE_ITEM', Komento.options.config.upload_max_file));
						return true;
					}

					// check for file status before proceeding
					if(item.status != 1) {
						return true;
					}

					var size = parseInt(item.size / 1024);

					var html = self.view.uploadrow({
						id: item.id,
						filename: item.name,
						size: size
					});

					html.data('fileitem', item);

					$('.uploadQueue').removeClass("hide");

					$('.uploadQueue').append(html);
				});
			},

			removeRow: function(id) {
				$('#' + id).remove();

				var file = self.plupload.getFile(id);
				self.plupload.removeFile(file);
			}
		} });
		module.resolve();
	});
});

Komento.module("location", function($) {

var module = this;

// require: start
Komento.require()
	.library(
		"ui/autocomplete"
	)
	.done(function(){

// controller: start

Komento.Controller(

	"Location.Form.Simple",

	{
		defaultOptions: {

			language: 'en',

			initialLocation: null,

			"{locationInput}": ".locationInput",

			"{locationLatitude}": ".locationLatitude",

			"{locationLongitude}": ".locationLongitude",

			"{autoDetectButton}": ".autoDetectButton"
		}
	},

	function(self) { return {
		init: function() {
			// Reset values
			self.locationInput().val($.language('COM_KOMENTO_COMMENT_WHERE_ARE_YOU'));
			self.locationLongitude().val('');
			self.locationLatitude().val('');

			// Bind one time focus on locationInput
			self.locationInput().one('focus', function() {
				self.locationInput().val('');
			});

			var mapReady = $.uid("ext");

			window[mapReady] = function() {
				$.___GoogleMaps.resolve();
			}

			if (!$.___GoogleMaps) {

				$.___GoogleMaps = $.Deferred();

				if(window.google === undefined || window.google.maps === undefined) {
					Komento.require()
						.script(
							{prefetch: false},
							"https://maps.googleapis.com/maps/api/js?sensor=true&language=" + self.options.language + "&callback=" + mapReady
						);
				} else {
					$.___GoogleMaps.resolve();
				}
			}

			// Defer instantiation of controller until Google Maps library is loaded.
			$.___GoogleMaps.done(function() {
				self._init();
			});
		},

		_init: function() {

			self.geocoder = new google.maps.Geocoder();

			self.hasGeolocation = navigator.geolocation!==undefined;

			if (!self.hasGeolocation) {
				self.autoDetectButton().remove();
			} else {
				self.autoDetectButton().show();
			}

			self.locationInput()
				.autocomplete({

					delay: 300,

					minLength: 0,

					source: self.retrieveSuggestions,

					select: function(event, ui) {

						self.locationInput()
							.autocomplete("close");

						self.setLocation(ui.item.location);
					}
				})
				.prop("disabled", false);

			// self.autocomplete = self.locationInput().autocomplete("widget");

			// self.autocomplete.addClass("location-suggestion");

			self.locationInput().addClass("location-suggestion");

			var initialLocation = $.trim(self.options.initialLocation);

			if (initialLocation) {

				self.getLocationByAddress(

					initialLocation,

					function(location) {

						self.setLocation(location[0]);
					}
				);

			};

			self.busy(false);
		},

		busy: function(isBusy) {
			self.locationInput().toggleClass("loading", isBusy);
		},

		getUserLocations: function(callback) {
			self.getLocationAutomatically(
				function(locations) {
					self.userLocations = self.buildDataset(locations);
					callback && callback(locations);
				}
			);
		},

		getLocationByAddress: function(address, callback) {

			self.geocoder.geocode(
				{
					address: address
				},
				callback);
		},

		getLocationByCoords: function(latitude, longitude, callback) {

			self.geocoder.geocode(
				{
					location: new google.maps.LatLng(latitude, longitude)
				},
				callback);
		},

		getLocationAutomatically: function(success, fail) {

			if (!navigator.geolocation) {
				return fail("ERRCODE", "Browser does not support geolocation or do not have permission to retrieve location data.")
			}

			navigator.geolocation.getCurrentPosition(
				// Success
				function(position) {
					self.getLocationByCoords(position.coords.latitude, position.coords.longitude, success)
				},
				// Fail
				fail
			);
		},

		setLocation: function(location) {

			if (!location) return;

			self.locationResolved = true;

			self.lastResolvedLocation = location;

			self.locationInput()
				.val(location.formatted_address);

			self.locationLatitude()
				.val(location.geometry.location.lat());

			self.locationLongitude()
				.val(location.geometry.location.lng());
		},

		removeLocation: function() {

			self.locationResolved = false;

			self.locationInput()
				.val('');

			self.locationLatitude()
				.val('');

			self.locationLongitude()
				.val('');
		},

		buildDataset: function(locations) {

			var dataset = $.map(locations, function(location){
				return {
					label: location.formatted_address,
					value: location.formatted_address,
					location: location
				};
			});

			return dataset;
		},

		retrieveSuggestions: function(request, response) {

			self.busy(true);

			var address = request.term,

				respondWith = function(locations) {
					response(locations);
					self.busy(false);
				};

			// User location
			if (address=="") {

				respondWith(self.userLocations || []);

			// Keyword search
			} else {

				self.getLocationByAddress(address, function(locations) {

					respondWith(self.buildDataset(locations));
				});
			}
		},

		suggestUserLocations: function() {

			if (self.hasGeolocation && self.userLocations) {

				self.removeLocation();

				self.locationInput()
					.autocomplete("search", "");
			}

			self.autoDetectButton().text($.language('COM_KOMENTO_FORM_LOCATION_AUTODETECT'));

			self.busy(false);
		},

		"{locationInput} blur": function() {

			// Give way to autocomplete
			setTimeout(function(){

				var address = $.trim(self.locationInput().val());

				// Location removal
				if (address=="") {

					self.removeLocation();

				// Unresolved location, reset to last resolved location
				} else if (self.locationResolved) {

					if (address != self.lastResolvedLocation.formatted_address) {

						self.setLocation(self.lastResolvedLocation);
					}
				} else {
					self.removeLocation();
				}

			}, 250);
		},

		"{autoDetectButton} click": function() {
			self.busy(true);

			self.autoDetectButton().text($.language('COM_KOMENTO_FORM_LOCATION_DETECTING'));

			if (self.hasGeolocation && !self.userLocations) {

				self.getUserLocations(self.suggestUserLocations);

			} else {

				self.suggestUserLocations();
			}
		},

		"{locationInput} keypress": function(el) {
			el.keypress(function(e) {
				if(e.which == 13) return false;
			});
		}
	}}
);

module.resolve();

// controller: end

	});
// require: end
});

Komento.module('migrator.actions', function($) {
var module = this;
Komento.require().script('admin.language','komento.common').done(function() {
Komento.Controller(
	'Migrator.Actions',
	{
		defaults: {
			'{migrateButton}': '.migrateButton',
			'{deleteButton}': '.deleteButton',
			'{migrateSettingsButton}': '.migrateSettingsButton'
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

		'{migrateSettingsButton} click': function(el) {
			
			Komento.ajax('admin.views.integrations.migrateSettings', {
				component: self.element.find('#componentSettings').val(),
				currentComponent: el.data("current-component")
			}, {
				success: function() {
					self.element.find('#SettingsMigratorProgress').removeClass("hide");
				}
			});
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

Komento.module('migrator.progress', function($) {
var module = this;
Komento.require().script('admin.language','komento.common').done(function() {
Komento.Controller(
	'Migrator.Progress',
	{
		defaults: {
			'{progressBar}': '.progressBar',
			'{progressStatus}': '.progressStatus',
			'{progressPercentage}': '.progressPercentage',
			'{logList}': '.logList',
			'{clearLog}': '.clearLog',
			'{totalComments}': '.totalComments',
			'{totalPosts}': '.totalPosts',
			'{migratedComments}': '.migratedComments'
		}
	},
	function(self)
	{ return {
		init: function() {
		},

		'{clearLog} click': function() {
			self.logList().html('');
		},

		setTotalPosts: function(data) {
			self.totalPosts().text(data);
		},

		setTotalComments: function(data) {
			self.totalComments().text(data);
		},

		updateMigratedComments: function(data) {
			var current = self.migratedComments().text();
			var newcount = parseInt(current) + parseInt(data);

			self.migratedComments().text( newcount );

			var totalComments = parseInt(self.totalComments().eq(0).text());
			var progress = Math.ceil((newcount / totalComments) * 100);

			self.progressBar().animate({
				width: progress.toString() + '%'
			});
			self.progressPercentage().text( progress );
		},

		log: function(data) {
			var time = new Date();
			var hour = time.getHours() > 9 ? time.getHours() : '0' + time.getHours();
			var minute = time.getMinutes() > 9 ? time.getMinutes() : '0' + time.getMinutes();
			var seconds = time.getSeconds() > 9 ? time.getSeconds() : '0' + time.getSeconds();
			var now = '[' + hour + ':' + minute + ':' + seconds + ']';

			var html = '<li>' + now + ' ' + data + '</li>';

			var height = self.logList()[0].scrollHeight;
			self.logList().append(html).scrollTop(height);
		}
	}}
);
module.resolve();
});
});
});
