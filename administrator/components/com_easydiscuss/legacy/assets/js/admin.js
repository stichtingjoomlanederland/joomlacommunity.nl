EasyDiscuss.require()
.library("uniform")
.language(
	'COM_EASYDISCUSS_YES_OPTION',
	'COM_EASYDISCUSS_NO_OPTION'
)
.done(function($) {

	// Apply uniform on checkboxes.
	$('.check, .check :checkbox, input:radio').uniform();

	// Initialize yes/no buttons.
	$(document).on( 'click.button.data-fd-api', '[data-fd-toggle-value]', function() {

		var parent = $(this).parents('[data-foundry-toggle="buttons-radio"]');

		if(parent.hasClass('disabled')) {
			return;
		}

		// This means that this toggle value belongs to a radio button
		if (parent.length > 0) {

			// Get the current button that's clicked.
			var value = $(this).data( 'fd-toggle-value' );

			// Set the value here.
			// Have to manually trigger the change event on the input
			parent.find( 'input[type=hidden]' ).val( value ).trigger('change');
			return;
		}
	});
 });


EasyDiscuss.ready(function($) {

	$(document).ready(function() {

		// Apply generic checkAll feature
		$('.discussCheckAll').bind('change' , function() {
			$('#adminForm').find('input[name=cid\\[\\]]').prop('checked' , $(this).is(':checked'));

			var total = $(this).is(':checked') ? $('#adminForm').find('input[name=cid\\[\\]]').length : 0;

			$('#adminForm').find('input[name=boxchecked]').val(total);
		});

		// insert span tag into submenu item for more flexibility
		$('#submenu li a').each(function() {
			$(this).wrapInner('<span></span>');
		});

		$('body .admintable tr:odd').addClass('tr-odd');

		$('.admintable tr').hover(function() {
			$(this).addClass('tr-hover');
		},
		function() {
			$(this).removeClass('tr-hover');
		});

		// move version notice to header
    // 		$('#versionTracker').appendTo('.icon-48-home').show();
    // 		$('.icon-48-home').css({ position: 'relative' });


		$('.icon-item').click(function(event) {
			window.location.href = $('a', this).attr('href');
		});

		$('.icon-item').hover(function(event) {
			$(this).addClass('hover');
		}, function() {
			$(this).removeClass('hover');
		});

		// The new admin.checkbox.init(); as the old one replaced by iButton.js
		$('.yes_no').click(function(event) {

			// Toogle the value. Basic stuff.
			var value = $(this).find(':checked');
			$(this).find(':checkbox', ':radio').val(value.length);

			// Update parent meta also
			var parent = $(this).parent();
			$(parent).attr('value', value.length);

			// Workaround as unchecked radios and checkboxes aren't gonna pass the form post
			$(this).find(':hidden').val(value.length);
		});

	});


	function toggleSettings()
	{

	}

	var admin = window.admin = {

		rank: {

			checktitle: function(ele ) {
				var val = $(ele).val();

				$(ele).removeClass('input-error');

				if (val == '')
				{
					setTimeout(function()
					{
						ele.focus();
						ele.select();
					},200);

					$(ele).addClass('input-error');
					$('#sys-msg').html(COM_EASYDISCUSS_RANKING_ERR_ENTER_TITLE);
					return;
				}
			},

			checkvalue: function(ele ) {
				var val = $(ele).val();
				var intRegex = /^\d+$/;

				if (!intRegex.test(val))
				{
					setTimeout(function()
					{
						ele.focus();
						ele.select();
					},200);

					$('#sys-msg').html(COM_EASYDISCUSS_RANKING_ERR_ONLY_NUMBER);
					$(ele).addClass('input-error');

					return;
				}

				if (parseInt(val, 10) <= 0)
				{
					setTimeout(function()
					{
						ele.focus();
						ele.select();
					},200);

					$('#sys-msg').html(COM_EASYDISCUSS_RANKING_ERR_GREATER_THAN_ZERO);
					$(ele).addClass('input-error');

					return;
				}

				// now we check if all the pair value entered are valid or not.
				admin.rank.validate();
			},

			validate: function() {

				//clear error styling first
				$('input[name="start[]"]').removeClass('input-error');
				$('input[name="end[]"]').removeClass('input-error');

				startItems	= $('input[name="start[]"]');
				endItems	= $('input[name="end[]"]');
				errorMessage	= '';

				if (startItems.length > 0)
				{
					for (i = 0; i < startItems.length; i++)
					{

						var curStart	= startItems[i];
						var curEnd	= endItems[i];

						var curStartVal	= parseInt($(curStart).val(), 10);
						var curEndVal	= parseInt($(curEnd).val(), 10);

						if (curStartVal >= curEndVal)
						{

							$('#sys-msg').html(COM_EASYDISCUSS_RANKING_ERR_END_CANNOT_SMALLER_THAN_START);
							$(curEnd).addClass('input-error');
							return;
						}

						if (i != 0)
						{
							var prevStart	= startItems[i - 1];
							var prevEnd	= endItems[i - 1];

							var prevEndVal	= parseInt($(prevEnd).val() , 10);

							if ((prevEndVal + 1) != curStartVal)
							{
								$('#sys-msg').html(COM_EASYDISCUSS_RANKING_ERR_CANNOT_HAVE_GAPS);
								$(curStart).addClass('input-error');
								$(prevEnd).addClass('input-error');
								return;
							}
						}
					}
				}//end if

				//clear all errors
				admin.rank.clearerrors();
			},

			remove: function(eleId ) {
				var rankId = $('#rank-' + eleId).children().first().children('input[name="id[]"]').val();

				if (rankId != '0')
				{
					if ($('#itemRemove').val() == '')
					{
						$('#itemRemove').val(rankId);
					}
					else
					{
						var rankIds = $('#itemRemove').val() + ',' + rankId;
						$('#itemRemove').val(rankIds);
					}
				}

				$('#rank-' + eleId).remove();

				admin.rank.sort();
			},

			sort: function() {

				startItems	= $('input[name="start[]"]');
				endItems	= $('input[name="end[]"]');
				errorMessage	= '';

				if (startItems.length > 0)
				{
					for (i = 0; i < startItems.length; i++)
					{
						if ((i + 1) <= startItems.length)
						{
							var nextStart	= startItems[i + 1];
							var curStart	= startItems[i];

							var nextEnd	= endItems[i + 1];
							var curEnd	= endItems[i];

							var nextStartVal	= parseInt($(nextStart).val() , 10);
							var nextEndVal	= parseInt($(nextEnd).val() , 10);

							var curEndVal	= parseInt($(curEnd).val(), 10);

							if ((curEndVal + 1) != nextStartVal)
							{
								var interval = nextEndVal - nextStartVal;

								var newNextStartVal = curEndVal + 1;
								var newNextEndVal = newNextStartVal + interval;

								$(nextStart).val(newNextStartVal);
								$(nextEnd).val(newNextEndVal);
							}
						}//end if
					}
				}//end if

			},

			clearerrors: function() {
				$('input.input-error').removeClass('input-error');
				$('#sys-msg').html('');
			},

			add: function() {
				var newtitle = $('#newtitle').val();
				if (newtitle.length == 0)
				{
					$('#newtitle').addClass('input-error');
					$('#newtitle').focus();
					$('#sys-msg').html(COM_EASYDISCUSS_RANKING_ERR_ENTER_TITLE);
					return;
				}

				var itemCnt	= $('#itemCnt').val();
				var itemCnt	= parseInt(itemCnt, 10);

				var items	= $('input[name="id[]"]');
				var endVal	= '';

				if (items.length > 0)
				{
					endVal = $('input[name="end[]"]').last().val();

					if (endVal == '')
					{
						$('#sys-msg').html(COM_EASYDISCUSS_RANKING_ERR_ALL_VALUE_IS_CORRECT);
						$('input[name="end[]"]').last().focus();

						return;
					}

					endVal = parseInt(endVal, 10);
				}


				var newStartValue = 1 + endVal;

				var input = '<tr id="rank-' + itemCnt + '">';
					input += '	<td>' + (items.length + 1) + '<input type="hidden" name="id[]" value="0" /></td>';
					input += '	<td style="text-align: center;"><input onchange="admin.rank.checktitle(this)" type="text" name="title[]" value="' + newtitle + '" class="input-full inputbox"/></td>';
					input += '	<td style="text-align: center;"><input onchange="admin.rank.checkvalue(this)" style="text-align: center;" type="text" name="start[]" value="' + newStartValue + '" class="input-full inputbox"/></td>';
					input += '	<td style="text-align: center;"><input onchange="admin.rank.checkvalue(this)" style="text-align: center;" type="text" name="end[]" value="" class="input-full inputbox"/></td>';
					input += '	<td style="text-align: center;"><a href="javascript:void(0);" onclick="admin.rank.remove(' + itemCnt + ')">' + COM_EASYDISCUSS_RANKING_DELETE + '</a></td>';
					input += '</tr>';

					$('#rank-list')
						.append(
							input
						);

				//set the focus here.
				$('input[name="end[]"]').last().focus();

				// update the counter
				$('#itemCnt').val(itemCnt + 1);

				//clear text box
				$('#newtitle').val('');

				admin.rank.clearerrors();
			}
		},

		category: {
			acl: {
				showpanel: function(action , id ) {
					$("input[name='cid[]']").each(function() {
						var rule	= this.value;
						$('#arrow_category_acl_' + rule).hide();
					});

					$('#arrow_category_acl_' + action).show();
					$('#activerule').val(action);

					// Hide all edit links
					$('.edit-link').show();

					$('#acl-edit-' + id).hide();

					$('.left-panel').removeClass('active');
					$('.left-panel.' + action).toggleClass('active');

					//$('.left-panel ' + action).toggleClass(action);

					// now we add in a action class into righ panel for later processing.
					$('#panel-wraper').removeClass();
					$('#panel-wraper').addClass(action);

				},

				assign: function(type ) {

					var action	= $('#activerule').val();

					var items = $(":input[name='panel_" + type + "[]']:checked");
					if (items != null)
					{
						for (i = 0; i < items.length; i++)
						{
							var ele	= items[i];
							var id	= $(ele).val();
							var text	= $('#panel_' + type + '_' + id).val();

							var doinsert	= true;
							var curProcessItem = $(":input[name='acl_" + type + '_' + action + "[]']");

							if (curProcessItem.length > 0)
							{
								for (c = 0; c < curProcessItem.length; c++)
								{
									var cele = curProcessItem[c];
									if (cele.value == id)
									{
										doinsert = false;
										break;
									}
								}
							}

							if (doinsert)
							{

								var input = '<li id="acl_' + type + '_' + action + '_' + id + '">';
								input += '<span><a href="javascript: admin.category.acl.remove(\'acl_' + type + '_' + action + '_' + id + '\');">Delete</a></span>';
								input += ' - ' + text;
								input += '<input type="hidden" name="acl_' + type + '_' + action + '[]" value="' + id + '" />';
								input += '</li>';


								$('#category_acl_' + type + '_' + action)
									.append(
										input
									);
							}
						}//end for i
					}//end if group is null

				},

				remove: function(id ) {
					$('#' + id).remove();
				},

				addpaneluser: function(id, name, prefix) {

					var users	= $(":input[name='" + prefix + "_panel_user[]']");
					var doinsert	= true;

					if (users.length > 0)
					{
						for (c = 0; c < users.length; c++)
						{
							var	ele	= users[c];
							var cid	= $(ele).val();

							if (cid == id)
							{
								doinsert = false;
								break;
							}
						}
					}

					if (doinsert)
					{
						var input = '<li id="user-li-' + id + '">';
						input += '<input type="checkbox" name="' + prefix + '_panel_user[]" value="' + id + '" checked="checked" />';
						input += '<input type="hidden" id="' + prefix + '_panel_user_' + id + '" value="' + name + '" />';
						input += name;
						input += '</li>';

						$('#cat-' + prefix + '-panel-user-ul')
							.append(input);
					}
					//end addpaneluser
				}
			}
		},

		customFields: {
			acl: {
				showpanel: function(action , id ) {
					$("input[name='cid[]']").each(function() {
						var rule	= this.value;
						$('#arrow_customFields_acl_' + rule).hide();
					});

					$('#arrow_customFields_acl_' + action).show();
					$('#activerule').val(action);

					// Hide all edit links
					$('.edit-link').show();

					$('#acl-edit-' + id).hide();

					$('.left-panel').removeClass('active');
					$('.left-panel.' + action).toggleClass('active');

					//$('.left-panel ' + action).toggleClass(action);

					// now we add in a action class into righ panel for later processing.
					$('#panel-wraper').removeClass();
					$('#panel-wraper').addClass(action);

				},

				assign: function(type ) {

					var action	= $('#activerule').val();

					var items = $(":input[name='panel_" + type + "[]']:checked");
					if (items != null)
					{
						for (i = 0; i < items.length; i++)
						{
							var ele	= items[i];
							var id	= $(ele).val();
							var text	= $('#panel_' + type + '_' + id).val();

							var doinsert	= true;
							var curProcessItem = $(":input[name='acl_" + type + '_' + action + "[]']");

							if (curProcessItem.length > 0)
							{
								for (c = 0; c < curProcessItem.length; c++)
								{
									var cele = curProcessItem[c];
									if (cele.value == id)
									{
										doinsert = false;
										break;
									}
								}
							}

							if (doinsert)
							{

								var input = '<li id="acl_' + type + '_' + action + '_' + id + '">';
								input += '<span><a href="javascript: admin.customFields.acl.remove(\'acl_' + type + '_' + action + '_' + id + '\');">Delete</a></span>';
								input += ' - ' + text;
								input += '<input type="hidden" name="acl_' + type + '_' + action + '[]" value="' + id + '" />';
								input += '</li>';


								$('#customFields_acl_' + type + '_' + action)
									.append(
										input
									);
							}
						}//end for i
					}//end if group is null

				},

				remove: function(id ) {
					$('#' + id).remove();
				},

				addpaneluser: function(id, name, prefix) {

					var users	= $(":input[name='" + prefix + "_panel_user[]']");
					var doinsert	= true;

					if (users.length > 0)
					{
						for (c = 0; c < users.length; c++)
						{
							var	ele	= users[c];
							var cid	= $(ele).val();

							if (cid == id)
							{
								doinsert = false;
								break;
							}
						}
					}

					if (doinsert)
					{
						var input = '<li id="user-li-' + id + '">';
						input += '<input type="checkbox" name="' + prefix + '_panel_user[]" value="' + id + '" checked="checked" />';
						input += '<input type="hidden" id="' + prefix + '_panel_user_' + id + '" value="' + name + '" />';
						input += name;
						input += '</li>';

						$('#customFields-' + prefix + '-panel-user-ul')
							.append(input);
					}
					//end addpaneluser
				}
			}
		},

		post: {
			moderate: {
				dialog: function(blogId) {
					disjax.loadingDialog();
					disjax.load('Posts' , 'showApproveDialog' , blogId);
				},
				publish: function() {
					$('#moderate-task').val('publish');
					$('#moderate-form').submit();
				},
				unpublish: function() {
					$('#moderate-task').val('unpublish');
					$('#moderate-form').submit();
				}
			},
			submit: function() {
				if (admin.post.validate()) {
					$('#adminForm').submit();
				}
				return false;
			},
			cancel: function() {
				$('#task').val('cancelSubmit');
				$('#adminForm').submit();
			},
			// validate all required fields
			validate: function(notitle, submitType ) {

				if (!notitle) {
					// if the title is empty
					if ($('#title').val() == '')
					{
						// do something here
						$('#dc_post_notification .msg_in').html(langEmptyTitle);
						$('#dc_post_notification .msg_in').addClass('dc_error');
						return false;
					}
				}

				// if the content is empty
				if ($('#dc_reply_content').val() == '')
				{
					// do something here
					$('#dc_post_notification .msg_in').html(langEmptyContent);
					$('#dc_post_notification .msg_in').addClass('dc_error');
					return false;
				}

				// if the category is empty
				if ($('#category_id').val() == '0')
				{
					$('#dc_post_notification .msg_in').html(langEmptyCategory);
					$('#dc_post_notification .msg_in').addClass('dc_error');
					return false;
				}

				return true;
			},

			tags: {

				add: function() {
					var tags = $('#new_tags').val();

					if (tags == langTagSepartor || tags == '')
					{
						$('#dc_tag_notification .msg_in').html(langEmptyTag);
						$('#dc_tag_notification .msg_in').addClass('dc_error');
						return false;
					}

					$('#dc_tag_notification .msg_in').html('');
					$('#dc_tag_notification .msg_in').removeClass('dc_error');

					var tagArr = tags.split(',');

					if (tagArr.length > 0)
					{
						$(tagArr).each(function(key , value ) {

							value	= $.trim(value);
							idValue	= value.replace(/ /g, '-');

							if ($('#tag_' + idValue).html() == null)
							{
								if (idValue != '')
								{
									var strItem = '<li class="tag_item" id="tag_' + idValue + '">';
										strItem += '	<a class="remove_tag" href="javascript:void(0);" onclick="admin.post.tags.remove(\'' + idValue + '\');"><span>X</span></a>';
										strItem += '	<span class="tag_caption">' + value + '</span>';
										strItem += '	<input type="hidden" name="tags[]" value="' + value + '" />';
										strItem += '</li>';

									$('#tag_items').append(strItem);
								}
							}
						});
					}
					$('#new_tags').val('');

					var addedTags = $(':hidden[name="tags[]"]');
					if (addedTags.length >= 1)
					{
						$('#tag_required_msg').hide();
						$('div.tag_selected').removeClass('alert');
					}


				},

				addexisting: function(value) {
					if (value.length > 0)
					{
						value	= $.trim(value);
						idValue	= value.replace(/ /g, '-');

						if ($('#tag_' + idValue).html() == null)
						{
							var strItem = '<li class="tag_item" id="tag_' + idValue + '">';
								strItem += '	<a class="remove_tag" href="javascript:void(0);" onclick="admin.post.tags.remove(\'' + idValue + '\');"><span>X</span></a>';
								strItem += '	<span class="tag_caption">' + value + '</span>';
								strItem += '	<input type="hidden" name="tags[]" value="' + value + '" />';
								strItem += '</li>';

							$('#tag_items').append(strItem);
						}
						else
						{
							//disjax.dialog(discuss.system.getString('TAG EXISTS'), '', admin.system.getString('WARNING'));
						}
					}
					var addedTags = $(':hidden[name="tags[]"]');
					if (addedTags.length >= 1)
					{
						$('#tag_required_msg').hide();
						$('div.tag_selected').removeClass('alert');
					}
				},

				load: function(tag ) {

					var tags	= tag;
					var tagArr	= tags.split(',');

					if (tagArr.length > 0)
					{
						$(tagArr).each(function(key , value ) {

							value	= $.trim(value);
							idValue	= value.replace(/\s\s/g	, '-');
							idValue	= value.replace(/\s/g	, '-');

							if ($('#tag_' + idValue).html() == null)
							{
								if (idValue != '')
								{
									var strItem = '<li class="tag_item" id="tag_' + idValue + '">';
									strItem += '	<a class="remove_tag" href="javascript:void(0);" onclick="admin.post.tags.remove(\'' + idValue + '\');"><span>X</span></a>';
									strItem += '	<span class="tag_caption">' + value + '</span>';
									strItem += '	<input type="hidden" name="tags[]" value="' + value + '" />';
									strItem += '</li>';
									$('#tag_items').append(strItem);
								}
							}

						});
					}
					$('#tags').val('');
				},

				remove: function(key) {
					$('#tag_' + key).remove();

					var addedTags = $(':hidden[name="tags[]"]');
					if (addedTags.length <= 0)
					{
						$('#tag_required_msg').show();
					}
				}

			}

		},
		reports: {
			change: function(id) {
					var actionType = $('#report-action-' + id).val();
					$('#email-text-' + id).val('');

					if (actionType == 'E')
					{
						$('#email-container-' + id).show();
					}
					else
					{
						$('#email-container-' + id).hide();
					}
			},

			revertEmailForm: function(id) {
				$('#email-container-' + id).hide();
				$('#report-action-' + id).children(':first').prop('selected' , true);
			}
		},

		system: {
			redirect: function(url) {
				window.location = url;
			},

			refresh: function() {
				window.location.reload();
			},

			loader: function(show) {

				if (show)
				{
					if ($('img#discuss-loader').length > 0)
					{
						$('img#discuss-loader').remove();
					}

					var img	= new Image;
					img.src	= '/components/com_easydiscuss/assets/images/login-loading.gif';
					img.name	= 'discuss-loader';
					img.id	= 'discuss-loader';


					var divBody	= $('div#discuss-wrapper');
					var divWidth	= divBody.width();

					//divHeight		= window.innerHeight || self.innerHeight || (de&&de.clientHeight) || window.parent.document.body.clientHeight;
					divHeight	= window.innerHeight || self.innerHeight || window.parent.document.body.clientHeight;

					divBody.prepend(img);
					$('img#discuss-loader').css('marginTop', (divHeight / 2));
					$('img#discuss-loader').css('marginLeft', (divWidth / 2));
					$('img#discuss-loader').css('position', 'absolute');
					$('img#discuss-loader').css('z-index', 10);
				}
				else
				{
					if ($('img#discuss-loader').length > 0)
					{
						$('img#discuss-loader').remove();
					}
				}
			}
		},
		files: {
			add: function() {
				$('#file_contents div').before('<input type="file" name="filedata[]" id="filedata" size="50" />');
			},
			remove: function(attachment_id) {
				$('#button-delete-att-' + attachment_id).prop('disabled', true);
				disjax.load('post', 'deleteAttachment', attachment_id);
			}
		},
		checkbox: {
			init: function() {
				// Transform checkboxes.
				$('.option-enable').click(function() {
					var parent = $(this).parent();
					$('.option-disable' , parent).removeClass('selected');
					$(this).addClass('selected');
					$('.radiobox' , parent).attr('value' , 1);
				});

				$('.option-disable').click(function() {
					var parent = $(this).parent();
					$('.option-enable' , parent).removeClass('selected');
					$(this).addClass('selected');
					$('.radiobox' , parent).attr('value' , 0);
				});
			}
		}
	};

	var effect = {

		highlight: function(element) {
			setTimeout(function() {
				$(element).animate({ backgroundColor: '#ffff66' }, 300).animate({ backgroundColor: 'transparent' }, 1500);
			}, 500);
		}
	};

});
