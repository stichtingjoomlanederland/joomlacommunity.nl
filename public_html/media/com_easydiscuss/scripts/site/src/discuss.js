ed.define('site/src/discuss', ['edq'], function($) {

	// Scoping issues
	jQuery = $;

  var discuss = {

	location: {
		remove: function(id )
		{
			disjax.loadingDialog();
			disjax.load('location' , 'removeLocation' , id.toString());
		},
		removeHTML: function(id )
		{
			$('#location-' + id).remove();
		}
	},

	reply: {
		clear: function(element) {

			// Empty contents
			$('textarea[name=dc_reply_content]').val('');

			// Clear out CKEditor's content
			if (window.CKEDITOR) {
				try {
					window.CKEDITOR.instances['dc_reply_content'].setData('');
				} catch (e) {}
			}

			// Clear off attachments
			discuss.attachments.clear();

			// Clear off references
			discuss.references.clear(form);

			// Maps
			$(form).find('.showCoords')
				.removeClass('showCoords');

			$(form).find('.locationMap')
				.remove();

			$(form).find('[name=latitude]').val('');
			$(form).find('[name=longitude]').val('');

			// Polls
			var pollController = $(form).find('.polls-tab').controller();

			if (pollController) {
				$(form).find('.polls-tab').controller().resetPollForm();
			}
		},
		submit: function(id) {
			var token	= $('.easydiscuss-token').val();
			action_url = discuss_site + '&view=post&layout=ajaxSubmitReply&format=ajax&tmpl=component&' + token + '=1';

			form = $('.' + id).find('[name=dc_submit]')[0];

			var replyNotification = $('.' + id).find('.replyNotification');

			var iframe = document.createElement('iframe');
				iframe.setAttribute('id', 'upload_iframe');
				iframe.setAttribute('name', 'upload_iframe');
				iframe.setAttribute('width', '0');
				iframe.setAttribute('height', '0');
				iframe.setAttribute('border', '0');
				iframe.setAttribute('style', 'width: 0; height: 0; border: none;');

			form.parentNode.appendChild(iframe);
			window.frames['upload_iframe'].name = 'upload_iframe';
			var iframeId = document.getElementById('upload_iframe');

			// Temporarily disable the submit button.
			$('.submit-reply').prop('disabled' , true);

			// Add event...
			var eventHandler = function()  {

				var content;

				if (iframeId.detachEvent)
				{
					iframeId.detachEvent('onload', eventHandler);
				}
				else
				{
					iframeId.removeEventListener('load', eventHandler, false);
				}

				// Message from server...
				if (iframeId.contentDocument)
				{
					content = iframeId.contentDocument;
				}
				else if (iframeId.contentWindow)
				{
					content = iframeId.contentWindow.document;
				}
				else if (iframeId.document)
				{
					content = iframeId.document;
				}

				content = $(content).find('script#ajaxResponse').html();

				var result = $.parseJSON(content);

				switch (result.type)
				{
					case 'success.captcha':
						Recaptcha.reload();
					case 'success':
						discuss.spinner.hide('reply_loading');

						replyNotification
							.html(result.message)
							.removeClass('alert-error')
							.addClass('alert-success');

						// Clear any empty replies
						$('.discussionReplies .empty').hide();

						// Update the counter.
						var count = $('.replyCount').html();
							count = parseInt(count) + 1;

						$('.replyCount').html(count);

						// Append the result to the list.

						// var item = $(result.html);

						if ($('.discussionReplies').length > 0) {
							var controller = $('.discussionReplies').controller();

							controller.addItem(result.html, true);
						}

						// Reload the lightbox for new contents
						discuss.attachments.initGallery({
							type: 'image',
							helpers: {
								overlay: null
							}
						});

						if (EasyDiscuss.main_syntax_highlighter) {
							Prism.highlightAll();
						}


						// Reload the syntax highlighter.
						if (result.script != 'undefined') {
							eval(result.script);
						}

						form.reset();

						// Clear the form.
						discuss.reply.clear(form);
					break;
					case 'error':
						discuss.spinner.hide('reply_loading');
						$('.submit-reply').removeAttr('disabled');
						replyNotification
							.html(result.message)
							.removeClass('alert-success').addClass('alert-error');
					break;
					case 'error.captcha':
						Recaptcha.reload();
						discuss.spinner.hide('reply_loading');
						$('.submit-reply').removeAttr('disabled');

						replyNotification
							.html(result.message)
							.removeClass('alert-success')
							.addClass('alert-error');
					break;
				}

				replyNotification.show();

				$('.submit-reply').removeAttr('disabled');

				// Del the iframe...
				setTimeout(function()
				{
					$(iframeId).remove();
				}, 250);
			};

			$(iframeId).load(eventHandler);

			// Set properties of form...
			form.setAttribute('target', 'upload_iframe');
			form.setAttribute('action', action_url);
			form.setAttribute('method', 'post');
			form.setAttribute('enctype', 'multipart/form-data');
			form.setAttribute('encoding', 'multipart/form-data');

			// Submit the form...
			form.submit();

			// update reply count by 1
			EasyDiscuss.repliesCount = EasyDiscuss.repliesCount === undefined ? 1 : EasyDiscuss.repliesCount + 1;
		},
		verify: function() {

			$('#dc_notification .msg_in').html('');
			$('#dc_notification .msg_in').removeClass('dc_error dc_success dc_alert');
			// discuss.spinner.show('reply_loading');
			disjax.loadingDialog();
			disjax.load('post', 'checklogin');
		},
		post: function() {
			$('.submit-reply').prop('disabled' , true);
			$('#dc_notification .msg_in').html('');
			$('#dc_notification .msg_in').removeClass('dc_error dc_success dc_alert');
			discuss.spinner.show('reply_loading');
			if (discuss.post.validate(true, 'reply')) {
				disjax.load('post' , 'ajaxSubmitReply' , disjax.getFormVal('#dc_submit'));
			} else {
				discuss.spinner.hide('reply_loading');
				$('.submit-reply').removeAttr('disabled');
			}
		},
		minimize: function(id) {
			$('#dc_reply_' + id).hide();
			$('#reply_minimize_msg_' + id).show();
		},
		maximize: function(id) {
			$('#dc_reply_' + id).removeClass('is-minimized');
			$('#reply_minimize_msg_' + id).hide();
		},
		addURL: function(element ) {

			var data	= $(element).siblings('ul.attach-list').children(':first').clone();

			var remove = $('.remove-url').clone();
			remove.css('display' , 'block');

			// Clear up the value of the url.
			$(data).find('input').val('');

			// Show the remove link for new items.
			$(data).find('a').show();

			$(element).siblings('ul.attach-list').append(data);
		},
		removeURL: function(element ) {
			// Detect if this is the only url item left.
			var items = $('ul.attach-list').children();

			if (items.length == 1)
			{
				return false;
			}

			$(element).parents('li').remove();
		},
		accept: function(id ) {

			// Show dialog
			disjax.loadingDialog();
			disjax.load('post' , 'confirmAccept' , id);
			//disjax.load( 'post' , 'acceptReply' , id );
		},
		reject: function(id ) {
			disjax.loadingDialog();
			disjax.load('post' , 'confirmReject' , id);
			// disjax.load( 'post' , 'rejectReply' , id );
		}
	},

	composer: {

		init: function(element, options ) {

			EasyDiscuss.require()
				.script('composer')
				.done(function() {
					$(element).implement(EasyDiscuss.Controller.Composer, options);
				});
		}
	},
	/*
	 * Filter items
	 */
	filter: function(type , categoryId ) {
		EasyDiscuss.ajax('site.views.index.filter' , { args: [type, categoryId] } ,
		{
			beforeSend: function() {

				// Show loading
				discuss.spinner.show('index-loading');

				// Hide the main list item
				$('#dc_list').hide();

				// Hide all paginations during filter
				$('#dc_pagination').hide();

				// Remove all active classes from the child filters.
				$('#filter-links').children().removeClass('active');
			},
			success: function(showFeaturedList , content , sorting , type , nextLimit , paginationContent ) {

				// Show only if necessary
				if (showFeaturedList)
				{
					$('#dc_featured_list').show();
				}

				// Assign the new content
				$('#dc_list').html(content);

				// Update the sorting content
				$('#sort-wrapper').html();

				// Update the pagination type.
				$('#pagination-filter').val(type);

				// Update the pagination limit
				$('#pagination-start').val(nextLimit);

				// Update pagination content
				$('#dc_pagination').html(paginationContent);
			},
			complete: function() {

				// Hide loading once the process is complete
				discuss.spinner.hide('index-loading');

				// Since we hid it earlier, show the list content
				$('#dc_list').show();

				// Show the pagination since we hid it earlier.
				$('#dc_pagination').show();

				// Add active class for the child filter
				$('#filter-links').find('.' + type).addClass('active');
			}
		});
	},
	sort: function(type , filter , categoryId ) {
		discuss.spinner.show('index-loading');

		if (discuss_featured_style == '2' && filter != 'allposts')
		{
			$('#dc_featured_list').hide();
		}

		$('#dc_list').hide();

		// Hide all paginations during filter
		$('#dc_pagination').hide();

		// Remove all active classes from the child sorts.
		$('#sort-links').children().removeClass('active');

		// Add active class for the current sort type
		$('#sort-links').find('.' + type).addClass('active');

		disjax.load('index' , 'sort' , type, filter, categoryId);
	},
	references: {
		clear: function(element ) {

			var list = $(element).find('.field-references ul.attach-list');

			var data	= list.children(':first').clone();

			list.empty()
				.append(data);
		}
	},
	attachments: {
		initGallery: function(options ) {

			$('.attachment-image-link').fancybox(options);
		},
		clear: function() {
			$('.uploadQueue').empty();
		}
	},
	map: {
		render: function(title , latitude , longitude , elementId ) {

			var latLng = new google.maps.LatLng(latitude, longitude);

			var map	= new google.maps.Map(document.getElementById(elementId) ,
				{
					zoom: 12,
					center: latLng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
			);

			var marker	= new google.maps.Marker(
				{
					position: latLng,
					center: latLng,
					title: title,
					map: map
				}
			);
		}
	},
	/**
	 * Widgets that can be toggled
	 */
	widget: {
		init: function() {
			$('.widget-toggle').click(function() {
				$(this).parents('.discuss-widget').find('.widget-body').toggle();
				$(this).parents('.discuss-widget').toggleClass('is-hidden');
			});
		}
	},
	/**
	 * Submit posts
	 */
	post: {
		move: function(postId )
		{
			disjax.loadingDialog();
			disjax.load('post' , 'movePost' , postId);
		},
		branch: function( postId )
		{
			disjax.loadingDialog();

			disjax.load( 'post' , 'branchForm' , postId );
		},
		mergeForm: function(postId )
		{
			disjax.loadingDialog();
			disjax.load('post' , 'mergeForm' , postId);
		},
		feature: function(postId )
		{
			disjax.loadingDialog();
			disjax.load('post' , 'confirmFeature' , postId);
		},
		unfeature: function(postId )
		{
			disjax.loadingDialog();
			disjax.load('post' , 'confirmUnfeature' , postId);
		},
		// deprecated since 3.0
		// submit new post
		submit: function() {
			if ($('#dc_submit #category_id').val() == '0')
			{
				disjax.loadingDialog();
				disjax.load('index', 'getTemplate', 'ajax.selectcategory.php');
				return false;
			}

			$('#createpost').prop('disabled', true);
			document.dc_submit.submit();
		},
		qqSubmit: function() {

			if ($('#category_id').val() == '0')
			{
				disjax.loadingDialog();
				disjax.load('index', 'getTemplate', 'ajax.selectcategory.php');
				return false;
			}

			$('#createpost').prop('disabled', true);
			document.mod_edqq.submit();
		},
		postTopicSubmit: function() {
			// Module post topic

			if ($('#mod_post_topic_category_id').val() == '0')
			{
				disjax.loadingDialog();
				disjax.load('index', 'getTemplate', 'ajax.selectcategory.php');
				return false;
			}

			$('#createpost').prop('disabled', true);
			document.mod_post_topic.submit();
		},
		// reply to post
		reply: function() {

			if (discuss.post.validate(true, 'reply')) {
				finalData	= disjax.getFormVal('#dc_submit');
				disjax.load('Post', 'ajaxSubmitReply', finalData);
			}
			return false;
		},
		// validate all required fields
		validate: function(notitle, submitType ) {

			if (!notitle) {
				// if the title is empty
				if ($('#ez-title').val() == '' || $('#ez-title').val() == langPostTitle)
				{
					// do something here
					if (submitType == 'reply')
					{
						$('#dc_notification .msg_in').html(langEmptyTitle);
						$('#dc_notification .msg_in').addClass('dc_error');
					}
					else
					{
						$('#dc_post_notification .msg_in').html(langEmptyTitle);
						$('#dc_post_notification .msg_in').addClass('dc_error');
					}
					return false;
				}
			}
			return true;
		},
		del: function(id, oType , url ) {
			disjax.loadingDialog();
			disjax.load('post' , 'deletePostForm' , id, oType, url);
		},

		vote: {

			add: function(post_id, value, vtype) {
				disjax.load('Post', 'ajaxAddVote', post_id, value);
			},

			check: function(post_id) {
				// this function is for debug purposes.
				disjax.load('Post', 'ajaxSumVote', post_id);
			},

			view: function(post_id) {
				disjax.load('Post', 'ajaxViewVote', post_id, '');
			}

		},

		lock: function(post_id) {
			disjax.load('Post', 'ajaxLockPost', post_id);
		},

		unlock: function(post_id) {
			disjax.load('Post', 'ajaxUnlockPost', post_id);
		},

		resolve: function(post_id) {
			disjax.load('Post', 'resolve', post_id);
		},

		unresolve: function(post_id) {
			disjax.load('Post', 'unresolve', post_id);
		},

		likes: function(contentId, status, likeId) {
			disjax.load('Post', 'ajaxLikes', contentId, status, likeId);
		},

		replyLikes: function(contentId, status, likeId) {
			disjax.load('Post', 'ajaxReplyLikes', contentId, status, likeId);
		},

		onhold: function(post_id) {
			disjax.load('Post', 'ajaxOnHoldPost', post_id);
		},

		accepted: function(post_id) {
			disjax.load('Post', 'ajaxAcceptedPost', post_id);
		},

		workingon: function(post_id) {
			disjax.load('Post', 'ajaxWorkingOnPost', post_id);
		},

		reject: function(post_id) {
			disjax.load('Post', 'ajaxRejectPost', post_id);
		},

		nostatus: function(post_id) {
			disjax.load('Post', 'ajaxNoStatusPost', post_id);
		},

		/**
		 * Deprecated since 3.0
		 */
		featured: function(contentId, status ) {
			disjax.load('Post', 'ajaxFeatured', contentId, status);
		},

		toggleTools: function(show, id, showDelete)
		{
			if (show)
			{
				$('.post_delete_link').show();
				$('.likes').show();
				$('.comments').show();
				$('.vote_up').show();
				$('.vote_down').show();
				$('#dc_main_reply_form').show();
			}
			else
			{
				//revert comment form if currently visible
				discuss.comment.cancel();

				if (showDelete == '1')
				{
					$('.post_delete_link').show();
				}
				else
				{
					$('.post_delete_link').hide();
				}
				$('.likes').hide();
				$('.comments').hide();
				$('.vote_up').hide();
				$('.vote_down').hide();
				$('#dc_main_reply_form').hide();
			}
		},

		attachment: {
			remove: function(attachment_id) {
				$('#button-delete-att-' + attachment_id).prop('disabled', true);
				disjax.load('post', 'deleteAttachment', attachment_id);
			}
		}

	},
	login: {
		verify: function() {
			// if the content is empty
			if (discuss.post.validate(false, 'reply')) {
				$('#submit').prop('disabled', true);
				disjax.loadingDialog();
				disjax.load('post', 'checklogin');
			} else {
				return false;
			}
		},
		token: '',
		showpane: function(usertype) {

			$('#usertype_pane_right').children().hide();
			$('#usertype_pane_left').children();

			$('#usertype_member').removeClass('active');
			$('#usertype_guest').removeClass('active');
			$('#discuss_register').removeClass('active');
			$('#usertype_twitter').removeClass('active');

			$('#usertype_status .msg_in').html('');
			$('#usertype_status .msg_in').removeClass('dc_error');


			switch (usertype)
			{
				case 'guest':
					$('#usertype_guest').addClass('active');
					$('#usertype_guest_pane_wrapper').show();
					break;
				case 'register':
					$('#discuss_register').addClass('active');
					$('#discuss_register_pane_wrapper').show();
					break;
				case 'twitter':
					$('#usertype_twitter').addClass('active');
					$('#usertype_twitter_pane_wrapper').show();
					break;
				case 'member':
				default:
					$('#usertype_member').addClass('active');
					$('#usertype_member_pane_wrapper').show();
			}
		},
		submit: {
			reply: function(usertype) {
				switch (usertype)
				{
					case 'guest':
						$('#edialog-guest-reply').prop('disabled', true);
						var email	= $('#discuss_usertype_guest_email').val();
						var name	= $('#discuss_usertype_guest_name').val();
						disjax.load('post', 'ajaxGuestReply', email, name);
						break;
					case 'member':
						$('#edialog-member-reply').prop('disabled', true);
						var username	= $('#discuss_usertype_member_username').val();
						var password	= $('#discuss_usertype_member_password').val();
						var token	= discuss.login.token;
						disjax.load('post', 'ajaxMemberReply', username, password, token);
						break;
					case 'twitter':
						$('#edialog-twitter-reply').prop('disabled', true);
						disjax.load('post', 'ajaxTwitterReply');
						break;
					default:
						break;
				}
			}
		},

		getGuestDefaultName: function() {
			var email = $('#discuss_usertype_guest_email').val();
			$('#discuss_usertype_guest_name').val(email.split('@', 1));
		},

		twitter: {
			signin: function(status, msg) {
				if (status) {
					disjax.load('post', 'ajaxRefreshTwitter');
				} else {
					alert('failed');
				}
			},

			signout: function() {
				disjax.load('post', 'ajaxSignOutTwitter');
			}
		}
	},
	files: {
		add: function() {
			jQuery('#file_contents div').before('<input type="file" name="filedata[]" id="filedata" size="50" />');
		}
	},
	pagination: {
		more: function(type ) {

			if (type == 'questions')
				disjax.load('index' , 'ajaxReadmore' , $('#pagination-start').val() , $('#pagination-sorting').val() , type, $('#discuss_parent').val(), $('#pagination-filter').val(), $('#pagination-category').val(), $('#pagination-query').val());
			else
				disjax.load('index' , 'ajaxReadmore' , $('#pagination-start').val() , $('#pagination-sorting').val() , type, $('#discuss_parent').val(), $('#pagination-filter').val(), $('#pagination-category').val());
		},
		addButton: function(type, label ) {
			html = '<a href="javascript:void(0);" onclick="discuss.pagination.more( \'' + type + '\' );"><span>' + label + '</span></a>';

			if ($('#dc_pagination a').length < 1)
				$('#dc_pagination').prepend(html);
		}
	},
	comment: {

		/**
		 * Deprecated since 3.0
		 */
		save: function(id) {
			discuss.spinner.show('discussSubmitWait');

			finalData	= disjax.getFormVal('#frmComment' + id);
			disjax.load('Post', 'ajaxSubmitComment', finalData);
		},
		/**
		 * Deprecated since 3.0
		 */
		add: function(id , commentBtn ) {

			// Add active state to the comment form.
			$(commentBtn).toggleClass('active');
			$('#comment-action-container-' + id).toggle();

			// Clear existing error messages.
			$('#err-msg .msg_in').html('');

			$('#comment-notification-' + id + ' .msg_in').html('');
			$('#comment-notification-' + id + ' .msg_in').removeClass('alert alert-error success');
		},

		clearForm: function(id )
		{
			$('#comment-err-msg .msg_in').html('');
			$('#comment-err-msg .msg_in').removeClass('alert alert-error success');

			$('#comment').val('');
			$('#comment-action-container-' + id).hide();
		},

		cancel: function(id) {

			$('#comment-err-msg .msg_in').html('');
			$('#comment-err-msg .msg_in').removeClass('dc_alert dc_error dc_success');

			$('#comment-notification-' + id + ' .msg_in').html('');
			$('#comment-notification-' + id + ' .msg_in').removeClass('alert alert-error success');

			$('#comment').val('');
			$('#comment-action-container-' + id).hide();

			//toggle toolbar button
			$('#comments-button-' + id).show();
		},

		remove: function(id) {
			var message = 'Are you sure?';
			// var title 	= langConfirmDeleteCommentTitle;

			if (window.confirm(message))
			{
				disjax.load('Post', 'ajaxCommentDelete', id);

				// Deduct a comment count
				EasyDiscuss.commentsCount = EasyDiscuss.commentsCount === undefined ? 0 : EasyDiscuss.commentsCount - 1;
			} else {
				return false;
			}
		},

		removeEntry: function(id) {
			$('#comment-' + id).remove();
		}
	},

	reports: {
		add: function(id) {
			disjax.loadingDialog();
			disjax.load('post' , 'reportForm' , id);
		},
		cancel: function() {
			disjax.closedlg();
		},
		submit: function() {
			disjax.load('post' , 'ajaxSubmitReport' , disjax.getFormVal('#frmReport'));
		},
		revertForm: function(id) {
			effect.highlight('#post_content_layout_' + id);

			setTimeout(function() {
				discuss.reports.cancel();
			}, 4000);
		}
	},

	/**
	 * Elements
	 */
	element: {

		focus: function(element) {
			ele	= '#' + element;
			$(ele).focus();
		}
	},


	/**
	 * Spinner
	 */
	spinner: {
		// toggle btw the spinner and save button
		show: function(id ) {
			var loading	= new Image;
			loading.src	= spinnerPath;
			loading.name	= 'discuss-loading';
			loading.id	= 'discuss-loading';

			$('#' + id).html(loading);
			$('#' + id).show();
		},
		// toggle btw the spinner and save button
		hide: function(id) {
			$('#' + id).hide();
		}
	},
	system: {
		redirect: function(url) {
			window.location = url;
		},

		refresh: function() {
			window.location.reload();
		}
	},
	subscribe: {
		post: function(post_id) {
			var type	= 'post';
			var email	= $('#subscribe_email').val();
			var name	= $('#subscribe_name').val();
			var interval	= 'instant';
			discuss.spinner.show('dialog_loading');
			disjax.load('post', 'ajaxAddSubscription', type, email, name, interval, post_id + '');
		},
		site: function() {
			var type	= 'site';
			var email	= $('#subscribe_email').val();
			var name	= $('#subscribe_name').val();
			var interval	= $('input:radio[name=subscription_interval]:checked').val();
			var post_id	= '0';

			discuss.spinner.show('dialog_loading');
			disjax.load('index', 'ajaxAddSubscription', type, email, name, interval, post_id + '');
		},

		tag: function(tag_id) {
			var type	= 'tag';
			var email	= $('#subscribe_email').val();
			var name	= $('#subscribe_name').val();
			var interval	= $('input:radio[name=subscription_interval]:checked').val();
			discuss.spinner.show('dialog_loading');
			disjax.load('index', 'ajaxAddSubscription', type, email, name, interval, tag_id + '');
		},

		category: function(cat_id) {
			var type	= 'category';
			var email	= $('#subscribe_email').val();
			var name	= $('#subscribe_name').val();
			var interval	= $('input:radio[name=subscription_interval]:checked').val();
			discuss.spinner.show('dialog_loading');

			disjax.load('index', 'ajaxAddSubscription', type, email, name, interval, cat_id + '');
		},

		user: function(user_id) {
			var type	= 'user';
			var email	= $('#subscribe_email').val();
			var name	= $('#subscribe_name').val();
			var interval	= $('input:radio[name=subscription_interval]:checked').val();
			discuss.spinner.show('dialog_loading');
			disjax.load('index', 'ajaxAddSubscription', type, email, name, interval, user_id + '');
		}
	},
	user: {
		tabs: {
			show: function(element , tabClass , ajax )
			{
				discuss.spinner.show('profile-loading');

				// Reset all tabs to non active.
				$('.user-tabs ul li').removeClass('active');

				// Set the current item as active
				$(element).parent().addClass('active');

				// Hide all tab contents first.
				$('#dc_profile .tab-item').hide();

				// Hide all paginations during filter
				$('#dc_pagination').hide();

				var pid	= $('#profile-id').val();

				if (ajax)
				{
					disjax.load('profile' , 'filter' , tabClass, pid);
				}
				else
				{
					$('#dc_profile .' + tabClass).show();
				}
			}
		},
		checkAlias: function() {
			var	alias	= $('#profile-alias').val();

			if (alias != '')
			{
				disjax.load('profile', 'ajaxCheckAlias', alias);
			}
		}
	},
	tooltips: {
		init: function() {},
		execute: function(id, type) {}
	},
	notifications: {
		interval: 3000,

		monitor: null,

		count: null,

		// Initializes the notification checks
		startMonitor: function()
		{
			var self = discuss.notifications;

			self.monitor = setTimeout(self.update, self.interval);
		},

		stopMonitor: function()
		{
			clearTimeout(discuss.notifications.monitor);
		},

		update: function()
		{
			var self = discuss.notifications;

			self.stopMonitor();

			var params	= {};

			params[$('.easydiscuss-token').val()]	= 1;

			EasyDiscuss.ajax('site.views.notifications.count', params,
			{
				type: 'jsonp',

				success: function(count)
				{
					if (count == 0)
					{
						$('#notification-count').hide();
						$('#mod-notification-count').hide();
					}

					if (count == 0 || !count) return;

					if (self.count != count)
					{
						$('#notification-count').html(count);
						$('#mod-notification-count').html(count);
					}

					if (count > 0)
					{
						$('#notification-count').show();
						$('#mod-notification-count').show();
					}
					// Update the count
					self.count = count;
				},

				complete: function()
				{
					self.startMonitor();
				}
			});
		}
	},
	conversation: {
		interval: 3000,

		monitor: null,

		count: null,

		// Initializes the notification checks
		startMonitor: function()
		{
			var self = discuss.conversation;

			self.monitor = setTimeout(self.update, self.interval);
		},

		stopMonitor: function()
		{
			clearTimeout(discuss.conversation.monitor);
		},

		update: function()
		{
			var self = discuss.conversation;

			self.stopMonitor();

			var params	= {};

			params[$('.easydiscuss-token').val()]	= 1;

			EasyDiscuss.ajax('site.views.conversation.count', params,
			{
				type: 'jsonp',

				success: function(count)
				{
					if (count == 0)
					{
						$('#conversation-count').hide();
					}

					if (count == 0 || !count) return;

					if (self.count != count)
					{
						$('#conversation-count').html(count);
					}

					if (count > 0)
					{
						$('#conversation-count').show();
					}


					// Update the count
					self.count = count;
				},

				complete: function()
				{
					self.startMonitor();
				}
			});
		},

		reply: function(recipientId ) {
			EasyDiscuss.ajax('site.views.conversation.reply' , {
				id: recipientId
			}).done(function() {

			});
		},

		write: function(userId ) {

			disjax.load('conversation' , 'write' , userId);
		},

		send: function()
		{
			var contents = $('#conversationMessage').val(),
				recipientId	= $('#recipientId').val();

			if (!contents)
			{
				$('#conversationEmptyMessage').show();
				return false;
			}

			EasyDiscuss.ajax('site.views.conversation.save' ,
			{
				'contents'	: contents,
				'recipient'	: recipientId
			})
			.done(function(options )
			{

				disjax.dialog(options);
			});
			// disjax.load('conversation' , 'save' , contents, recipientId);
		}
	},
	polls: {
		show: function() {
			$('#discuss-polls').toggle();
			$('#discuss-multiple-polls').toggle();
			$('#discuss-multiple-polls-title').toggle();

		},
		vote: function(element ) {

			var id	= $(element).val();
			disjax.load('polls' , 'vote' , id);
		},
		unvote: function(postId ) {
			disjax.load('polls' , 'unvote' , postId);
		},
		showVoters: function(pollId ) {
			disjax.load('polls' , 'getvoters' , pollId);
		},
		lock: function(postId) {
			//disjax.load('polls' , 'lockPolls' , postId);
			EasyDiscuss.ajax('site.views.polls.lockPolls' ,
			{
				'postId'	: postId
			})
			.done(function(msg, isQuestion, id, pollsId )
			{
				if (isQuestion)
				{
					$('.discussQuestion').addClass('is-poll-lock');
				}
				else
				{
					$('#dc_reply_' + id).addClass('is-poll-lock');
				}

				var length = pollsId.length,
				element = null;

				for (var i = 0; i < length; i++)
				{
					element = pollsId[i];

					$('#poll-count-' + element).attr('disabled', 'disabled');
					$('#poll-count-' + element).prop('disabled', 'disabled');
				}

				$('#poll_notice_' + id).html(msg);
				$('#poll_notice_' + id).show();
				$('#poll_notice_' + id).parent().addClass('alert');

			});
		},
		unlock: function(postId) {
			//disjax.load('polls' , 'unlockPolls' , postId);
			EasyDiscuss.ajax('site.views.polls.unlockPolls' ,
			{
				'postId'	: postId
			})
			.done(function(isQuestion, id, pollsId )
			{
				if (isQuestion)
				{
					$('.discussQuestion').removeClass('is-poll-lock');
				}
				else
				{
					$('#dc_reply_' + id).removeClass('is-poll-lock');
				}

				var length = pollsId.length,
				element = null;

				for (var i = 0; i < length; i++) {
					element = pollsId[i];
					$('#poll-count-' + element).removeAttr('disabled', 'disabled');
					$('#poll-count-' + element).removeProp('disabled', 'disabled');
				}

				$('#poll_notice_' + id).html('');
				$('#poll_notice_' + id).parent().removeClass('alert');

			});
		}

	},
	tabs: {
		show: function(element , className )
		{
			// Hide all tabs
			$('.form-tab-item').hide();

			// Remove active class
			$(element).parent().siblings().removeClass('active');

			$(element).parent().addClass('active');

			// Show the responsible tab
			$('.tab-' + className).show();


		}
	},
	toolbar: {
		login: function() {
			$('#dc_toolbar .to_login div.toolbar-note').toggle();
		}
	}
  };

 //  window.effect = effect = {
	// highlight: function(element) {
	// 	setTimeout(function() {
	// 		$(element).animate({ backgroundColor: '#ffff66' }, 300).animate({ backgroundColor: 'transparent' }, 1500);
	// 	}, 500);
	// }
 //  };


	return discuss;
});
