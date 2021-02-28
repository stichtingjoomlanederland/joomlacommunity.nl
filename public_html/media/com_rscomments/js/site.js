if (typeof RSComments == 'undefined') {
	var RSComments = {};
}

RSComments = {
	
	init: function() {
		jQuery('[data-rsc-task]').off('click');
		jQuery('[data-rsc-task="reply"]').on('click', RSComments.reply);
		jQuery('[data-rsc-task="quote"]').on('click', RSComments.quote);
		jQuery('[data-rsc-task="open"]').on('click', RSComments.open);
		jQuery('[data-rsc-task="close"]').on('click', RSComments.close);
		jQuery('[data-rsc-task="bbcode"]').on('click', RSComments.addBBCode);
		jQuery('[data-rsc-task="emoticons"]').on('click', RSComments.showEmoticons);
		jQuery('[data-rsc-task="form"]').find('.rscomments-accordion-title').unbind('click');
		jQuery('[data-rsc-task="form"]').find('.rscomments-accordion-title').on('click', RSComments.accordion);
		jQuery('[data-rsc-task="commentform"]').on('keyup keydown', RSComments.countChars);
		jQuery('[data-rsc-task="publish"], [data-rsc-task="unpublish"]').on('click', RSComments.changeState);
		jQuery('[data-rsc-task="delete"]').on('click', RSComments.remove);
		jQuery('[data-rsc-task="voteup"], [data-rsc-task="votedown"]').on('click', RSComments.vote);
		jQuery('[data-rsc-task="showcomment"]').on('click', RSComments.showComment);
		jQuery('[data-rsc-task="validate"]').on('click', RSComments.validate);
		jQuery('[data-rsc-task="edit"]').on('click', RSComments.edit);
		jQuery('[data-rsc-task="reset"]').on('click', RSComments.reset);
		jQuery('[data-rsc-task="terms"]').on('click', RSComments.terms);
		jQuery('[data-rsc-task="report"]').on('click', RSComments.report);
		jQuery('[data-rsc-task="subscribe"], [data-rsc-task="unsubscribe"]').on('click', RSComments.changeSubscription);
		jQuery('[data-rsc-task="subscribeform"]').on('click', RSComments.subscribeForm);
		jQuery('[data-rsc-task="pagination"]').on('click', RSComments.pagination);
		jQuery('[data-rsc-task="detectaddress"]').on('click', RSComments.detectAddress);
		jQuery('[data-rsc-task="searchaddress"]').on('keyup', RSComments.searchAddress);
		jQuery('[data-rsc-task="preview"]').on('click', RSComments.previewComment);
		jQuery('[data-rsc-task="closepreview"]').on('click', RSComments.closePreview);
		
		RSComments.initTooltips();
		RSComments.initModal();
		RSComments.shuffle();
	},
	
	root: function() {
		return typeof rsc_root != 'undefined' ? rsc_root : '';
	},
	
	open: function() {
		var commentContainer = jQuery(this).parents('.rscomments');
		var id = commentContainer.data('rsc-id');
		var option = commentContainer.data('rsc-option');
		var override = jQuery(this).data('rsc-override');
		var object = this;
		
		commentContainer.find('.rscomments-top-loader').css('display','');
		jQuery.ajax({
			type: 'POST',
			url: RSComments.root() + 'index.php?option=com_rscomments',
			data: 'task=comments.openthread&id=' + id + '&opt=' + option + '&override=' + override + '&randomTime='+Math.random(),
			dataType: 'json',
			success: function(response) {
				commentContainer.find('.rscomments-top-loader').css('display','none');
				
				if (response.status) {
					commentContainer.find('.rscomments-comment-form').html(response.form);
					commentContainer.find('.rsc_thread').html(response.link);
					commentContainer.find('.rscomm-actions').css('display','');
					commentContainer.find('.rscomments-top-alert').css('display','');
					commentContainer.find('.rscomments-top-alert').html(response.message);
					
					RSComments.resetForm(commentContainer);
					
					setTimeout(function() {
						commentContainer.find('.rscomments-top-alert').animate({
							opacity: 0
						}, 500, function() {
							commentContainer.find(this).css('display','none');
							commentContainer.find(this).css('opacity','1');
							commentContainer.find(this).empty();
						});
					},3500);
				} else {
					commentContainer.find('.rsc_thread').html(response.link);
					commentContainer.find('.rscomments-top-alert').css('display','');
					commentContainer.find('.rscomments-top-alert').html(response.message);
					
					setTimeout(function() {
						commentContainer.find('.rscomments-top-alert').animate({
							opacity: 0
						}, 500, function() {
							commentContainer.find(this).css('display','none');
							commentContainer.find(this).css('opacity','1');
							commentContainer.find(this).empty();
						});
					},3500);
				}
			}
		});
	},
	
	close: function() {
		var commentContainer = jQuery(this).parents('.rscomments');
		var id = commentContainer.data('rsc-id');
		var option = commentContainer.data('rsc-option');
		var override = jQuery(this).data('rsc-override');
		
		commentContainer.find('.rscomments-top-loader').css('display','');
		jQuery.ajax({
			type: 'POST',
			url: RSComments.root() + 'index.php?option=com_rscomments',
			data: 'task=comments.closethread&id=' + id + '&opt=' + option + '&override=' + override + '&randomTime='+Math.random(),
			dataType: 'json',
			success: function(response) {
				commentContainer.find('.rscomments-top-loader').css('display','none');
				
				if (response.status) {
					commentContainer.find('.rscomments-comment-form').html('');
					commentContainer.find('.rsc_thread').html(response.link);
					
					commentContainer.find('.rscomm-actions').css('display','none');
					commentContainer.find('.rscomments-top-alert').css('display','');
					commentContainer.find('.rscomments-top-alert').html(response.message);
					
					setTimeout(function() {
						commentContainer.find('.rscomments-top-alert').animate({
							opacity: 0
						}, 500, function() {
							commentContainer.find(this).css('display','none');
							commentContainer.find(this).css('opacity','1');
							commentContainer.find(this).empty();
						});
					},3500);
				} else {
					commentContainer.find('.rsc_thread').html(response.link);
					commentContainer.find('.rscomments-top-alert').css('display','');
					commentContainer.find('.rscomments-top-alert').html(response.message);
					
					setTimeout(function() {
						commentContainer.find('.rscomments-top-alert').animate({
							opacity: 0
						}, 500, function() {
							commentContainer.find(this).css('display','none');
							commentContainer.find(this).css('opacity','1');
							commentContainer.find(this).empty();
						});
					},3500);
				}
			}
		});
	},
	
	quote: function() {
		var commentContainer = jQuery(this).parents('.rscomments');
		var id = jQuery(this).data('rsc-commentid');
		var name = jQuery(this).data('rsc-name');
		
		if (commentContainer.find('form').length) {
			commentContainer.find('.rsc_loading_form').css('display','');
			
			jQuery.ajax({
				type: 'POST',
				url: RSComments.root() + 'index.php?option=com_rscomments',
				data: 'task=comments.quote&id=' + id + '&randomTime='+Math.random(),
				dataType: 'json',
				success: function(response) {
					commentContainer.find('.rsc_loading_form').css('display','none');
					
					if (response.comment) {
						commentContainer.find('textarea[name="jform[comment]"]').val('[quote name="'+name+'"]' + response.comment + '[/quote]');
						commentContainer.find('textarea[name="jform[comment]"]').keyup();
						commentContainer.find('input[name="jform[IdComment]"]').val('');
						
						// Open the form if this is closed
						if (!commentContainer.find('.rscomments-accordion-title').hasClass('active')) {
							commentContainer.find('.rscomments-accordion-title').click();
						}
			
						RSComments.animate(commentContainer.find('textarea[name="jform[comment]"]'));
					}
				}
			});
		}
	},
	
	reply: function() {
		var commentContainer = jQuery(this).parents('.rscomments');
		var id = jQuery(this).data('rsc-commentid');
		
		if (commentContainer.find('form').length) {
			commentContainer.find('form')[0].reset();
			commentContainer.find('[data-rsc-task="closepreview"]').click();
			commentContainer.find('.rscomments-form-message').css('display','none');
			commentContainer.find('.rscomments-form-message').html('');
			
			// Open the form if this is closed
			if (!jQuery('.rscomments-accordion-title').hasClass('active')) {
				jQuery('.rscomments-accordion-title').click();
			}
			
			jQuery('#rscomment'+id).append(commentContainer.find('.rscomment-form'));
			commentContainer.find('input[name="jform[IdParent]"]').val(id);
			commentContainer.find('.rsc_cancel_btn').css('display','inline').on('click', function(){
				RSComments.cancelReply(commentContainer);
			});
		}
	},
	
	cancelReply: function(object) {
		object.find('.rscomments-comment-form').append(object.find('.rscomment-form'));
		object.find('input[name="jform[IdParent]"]').val(0);
		object.find('.rsc_cancel_btn').css('display','none').off('click');
		object.find('[data-rsc-task="closepreview"]').click();
		
		RSComments.resetForm(object);
	},
	
	resetForm: function(object) {
		jQuery.ajax({
			type: 'POST',
			url: RSComments.root() + 'index.php?option=com_rscomments',
			data : {
				'task': 'reset',
				'randomTime' : Math.random()
			},
			dataType: 'html',
			success: function(response) {
				object.find('form :input').removeClass('invalid');
				object.find('.rscomments-form-message').removeClass('alert-danger');
				object.find('.rscomments-form-message').css('display','none');
				object.find('.rscomments-form-message').empty();
				
				if (object.find('form').length) {
					object.find('form')[0].reset();
				}
				
				RSComments.initTooltips();
			}
		});
	},
	
	reset: function() {
		RSComments.resetForm(jQuery(this).parents('.rscomments'));
		jQuery(this).parents('.rscomments').find('[data-rsc-task="closepreview"]').click();
		jQuery(this).parents('form').find('.comment_length').html(parseInt(jQuery(this).parents('.rscomments').find('[data-rsc-task="commentform"]').prop('maxlength')));
	},
	
	addBBCode: function() {
		var currentCode = jQuery(this).data('rsc-code');
		var pText = jQuery(this).data('rsc-text');
		
		var bbcodes = {
			bold: ['[b]', '[/b]'],
			italic: ['[i]', '[/i]'],
			underline: ['[u]', '[/u]'],
			stroke: ['[s]', '[/s]'],
			quote: ['[quote]', '[/quote]'],
			code: ['[code]', '[/code]'],
			youtube: ['[youtube]', '[/youtube]'],
			vimeo: ['[vimeo]', '[/vimeo]'],
			list: function(selected) {
				var content = '';
				jQuery.each(selected.split(/\r?\n/), function() {
					content += (content ? '\n' : '') + '[*]' + this;
				});
				
				return '[LIST]\n' + content + '\n[/LIST]'
			},
			olist: function(selected) {
				var content = '';
				jQuery.each(selected.split(/\r?\n/), function() {
					content += (content ? '\n' : '') + '[*]' + this;
				});
				
				return '[LIST=1]\n' + content + '\n[/LIST]'
			},
			url: function(selected) {
				if (url = prompt(pText,'http://')) {
					return selected == '' ? '[url]' + url + '[/url]' : '[url=' + url + ']' + selected + '[/url]';
				}
				
				return '';
			},
			image: function(selected) {
				 if (url = prompt(pText,'http://')) {
					 return '[img]' + url + '[/img]';
				 }
				 
				 return '';
			}
		};
		
		if (jQuery(this).parents('.rscomments').find('[data-rsc-task="commentform"]').length) {
			var textarea	= jQuery(this).parents('.rscomments').find('[data-rsc-task="commentform"]')[0];
			var len			= textarea.value.length;
			var start		= textarea.selectionStart;
			var end			= textarea.selectionEnd;
			var scrollTop	= textarea.scrollTop;
			var scrollLeft	= textarea.scrollLeft;
			var selected	= textarea.value.substring(start, end);
			
			if (typeof(bbcodes[currentCode]) != 'undefined') {
				if (typeof(bbcodes[currentCode]) == 'object') {
					replaceWith = bbcodes[currentCode][0] + selected + bbcodes[currentCode][1];
				} else if (typeof(bbcodes[currentCode]) == 'function') {
					replaceWith = bbcodes[currentCode](selected);
				}
			} else {
				replaceWith = currentCode;
				jQuery(this).parents('.rscomments').find('.rsc_emoticons').css('display','none');
				jQuery(this).parents('.rscomments').find('[data-rsc-task="emoticons"]').removeClass('btn-success');
			}
			
			textarea.value =  textarea.value.substring(0,start) + replaceWith + textarea.value.substring(end,len);
			textarea.scrollTop = scrollTop;
			textarea.scrollLeft = scrollLeft;
		}
	},
	
	showEmoticons: function() {
		if (jQuery(this).parents('.rscomments').find('.rsc_emoticons').css('display') == 'none') {
			jQuery(this).parents('.rscomments').find('.rsc_emoticons').css('display','block');
			jQuery(this).addClass('btn-success');
		} else {
			jQuery(this).parents('.rscomments').find('.rsc_emoticons').css('display','none');
			jQuery(this).removeClass('btn-success');
		}
	},
	
	accordion: function(e) {
		var currentAttrValue = jQuery(this).attr('href');
		
		if (jQuery(e.target).is('.active')) {
			RSComments.closeAccordion(this);
		} else {
			RSComments.closeAccordion(this);

			jQuery(this).addClass('active');
			jQuery(this).html(Joomla.JText._('COM_RSCOMMENTS_HIDE_FORM'));
			jQuery(this).parents('form').find('.rscomments-accordion ' + currentAttrValue).slideDown(300).addClass('open'); 
		}

		e.preventDefault();
	},
	
	closeAccordion: function(object) {
		jQuery(object).parents('form').find('.rscomments-accordion .rscomments-accordion-title').removeClass('active');
		jQuery(object).parents('form').find('.rscomments-accordion .rscomments-accordion-title').html(Joomla.JText._('COM_RSCOMMENTS_SHOW_FORM'));
		jQuery(object).parents('form').find('.rscomments-accordion .rscomments-accordion-content').slideUp(300).removeClass('open');
	},
	
	initTooltips: function() {
		jQuery('.tooltip').hide(); 
		try {
			jQuery('.hasTooltip').tooltip('destroy');
			jQuery('.hasTooltip').tooltip('dispose');
		} catch (err) {}
		jQuery('.hasTooltip').tooltip({"html": true,"container": "body"});
	},
	
	initModal: function() {
		if (jQuery('.mycomments-modal').length) {
			jQuery('.mycomments-modal').magnificPopup({ type: 'iframe'});
		}
	},
	
	countChars: function() {
		var maxlimit = parseInt(jQuery(this).prop('maxlength'));
		
		if (jQuery(this).val().length > maxlimit) {
			jQuery(this).val(jQuery(this).val().substring(0, maxlimit));
		} else {
			jQuery(this).parents('form').find('.comment_length').html(maxlimit - jQuery(this).val().length);
		}
	},
	
	animate: function(el) {
		jQuery('html, body').animate({
			scrollTop: el.offset().top
		});
	},
	
	showLoader: function(id) {
		jQuery('#rscomment' + id + ' .rscomment-body').css('opacity','0.3');
		jQuery('#rscomment-comment-loader-' + id).css('display','');
	},
	
	hideLoader: function(id) {
		jQuery('#rscomment' + id + ' .rscomment-body').css('opacity','1');
		jQuery('#rscomment-comment-loader-' + id).css('display','none');
	},
	
	changeState: function() {
		var id = jQuery(this).parents('[data-rsc-cid]').data('rsc-cid');
		var task = jQuery(this).data('rsc-task');
		
		RSComments.showLoader(id);
		
		jQuery.ajax({
			type: 'POST',
			url: RSComments.root() + 'index.php?option=com_rscomments',
			data: 'task=comments.' + task + '&id=' + id + '&randomTime='+Math.random(),
			dataType: 'json',
			success: function(response) {
				RSComments.hideLoader(id);
				
				jQuery('#rsc_publish' + id).html(response.message);
				jQuery('#rscomment' + id + ' .rscomm-actions').css('display', (task == 'publish' ? '' : 'none'));
				jQuery('#rscomment' + id).animate({ opacity: task == 'publish' ? 1 : 0.6 }, 500);
			}
		});
	},
	
	remove: function() {
		var id = jQuery(this).parents('[data-rsc-cid]').data('rsc-cid');
		
		jQuery('#rscomment'+id).animate({ opacity: '0.5' }, 500);
		
		if (confirm(jQuery(this).data('rsc-text'))) {
			RSComments.showLoader(id);
			
			jQuery.ajax({
				type: 'POST',
				url: RSComments.root() + 'index.php?option=com_rscomments',
				data: 'task=comments.remove&id=' + id + '&randomTime='+Math.random(),
				dataType: 'json',
				success: function(response) {
					RSComments.hideLoader(id);
					
					if (response.error) {
						alert(response.error);
					} else {
						if (jQuery('#rscomment'+id).length) {
							jQuery('#rscomment'+id).animate({ opacity: 0 }, 500, function() {
								jQuery(this).empty();
								jQuery(this).remove();
							});
						}
						
						jQuery(response).each(function(i,el) {
							jQuery('#rscomment'+el).animate({ opacity: 0 }, 500, function() {
								jQuery(this).empty();
								jQuery(this).remove();
							});
						});
					}
				}
			});
		} else {
			jQuery('#rscomment'+id).animate({ opacity: '1' }, 500);
		}
	},
	
	vote: function() {
		var id = jQuery(this).parents('[data-rsc-cid]').data('rsc-cid');
		var task = jQuery(this).data('rsc-task');
		
		RSComments.showLoader(id);
		
		jQuery.ajax({
			type: 'POST',
			url: RSComments.root() + 'index.php?option=com_rscomments',
			data: 'task=comments.' + task + '&id=' + id + '&randomTime='+Math.random(),
			dataType: 'json',
			success: function(response) {
				RSComments.hideLoader(id);
				jQuery('#rsc_voting'+id).html(response.vote);
			}
		});
	},
	
	showComment: function() {
		var id = jQuery(this).parents('[data-rsc-cid]').data('rsc-cid');
		
		if (jQuery('#c' + id).css('display') == 'none') {
			jQuery('#c' + id).css('display','block');
			jQuery('#c' + id).css('visibility','visible');
			jQuery('#comment-hidden-' + id).css('display','none');
		}
	},
	
	formFields: function(object) {
		var params		= new Array();
	
		jQuery(object).parents('[data-rsc-task="form"]').find(':input').each(function() {
			if (jQuery(this).prop('type') == 'button' || jQuery(this).prop('name') == '') {
				return;
			}
			
			var fname   = jQuery(this).prop('name');
			var fvalue  = jQuery(this).val();
			var fnameid = jQuery(this).prop('id');
			
			if (jQuery(this).data('rsc-task') == 'subscribethread') {
				fvalue = jQuery(this).is(':checked') ? 1 : 0;
			}
			
			if (jQuery(this).hasClass('rsc_terms')) {
				fvalue = jQuery(this).is(':checked') ? 1 : 0;
			}
			
			if (jQuery(this).hasClass('rsc_consent')) {
				fvalue = jQuery(this).is(':checked') ? 1 : 0;
			}
			
			params.push(fname + '=' + encodeURIComponent(fvalue));
		});
		
		return params;
	},
	
	validate: function() {
		var params = RSComments.formFields(this);
		var upload = jQuery(this).data('rsc-upload');
		var object = this;
		
		jQuery(this).parents('[data-rsc-task="form"]').find(':input').removeClass('invalid');
		jQuery(this).parents('[data-rsc-task="form"]').find('.rsc_loading_form').css('display','');
		jQuery(this).prop('disabled', true);
		
		jQuery.ajax({
			type: 'POST',
			url: RSComments.root() + 'index.php?option=com_rscomments',
			data: 'task=comments.validate&' + params.join('&') + '&randomTime='+Math.random(),
			dataType: 'json',
			success: function(response) {
				jQuery(object).prop('disabled', false);
				jQuery(object).parents('[data-rsc-task="form"]').find('.rsc_loading_form').css('display','none');
				jQuery(object).parents('[data-rsc-task="form"]').find('.rscomments-form-message').empty();
				
				if (response.success) {
					jQuery(object).parents('[data-rsc-task="form"]').find('.rscomments-form-message').removeClass('alert-danger');
					jQuery(object).parents('[data-rsc-task="form"]').find('.rscomments-form-message').css('display', 'none');
					
					 if (upload == 1) {
						RSComments.upload(object);
					} else {
						RSComments.save(object);
					}
				} else {
					jQuery(object).parents('[data-rsc-task="form"]').find('.rscomments-form-message').addClass('alert-danger');
					
					jQuery(response.errors).each(function(i, el) {
						jQuery(object).parents('[data-rsc-task="form"]').find('.rscomments-form-message').append('<p>' + el + '</p>');
					});
					jQuery(object).parents('[data-rsc-task="form"]').find('.rscomments-form-message').css('display', '');
					
					RSComments.animate(jQuery(object).parents('[data-rsc-task="form"]').find('.rscomments-form-message'));
					
					jQuery(response.fields).each(function(i, el) {
						jQuery(object).parents('[data-rsc-task="form"]').find(':input[name="jform[' + el + ']"]').addClass('invalid');
					});
					
					// Reset captcha
					if (jQuery(object).parents('[data-rsc-task="form"]').find('.rscomments-refresh-captcha').length) {
						jQuery(object).parents('[data-rsc-task="form"]').find('.rscomments-refresh-captcha').click();
					}
					
					if (jQuery(object).parents('[data-rsc-task="form"]').find('.g-recaptcha-response').length) {
						var cid = jQuery(object).parents('[data-rsc-task="form"]').find('.g-recaptcha-response').parents('div[id^="rsc-g-recaptcha-"]').prop('id').replace('rsc-g-recaptcha-', '');
						grecaptcha.reset(RSCommentsReCAPTCHAv2.ids[cid]);
						if (RSCommentsReCAPTCHAv2.type[cid] == 'invisible') {
							grecaptcha.execute(RSCommentsReCAPTCHAv2.ids[cid]);
						}
					}
					
					jQuery(object).parents('[data-rsc-task="form"]').find('[data-rsc-task="closepreview"]').click();
				}
			}
		});
	},
	
	save: function(object) {
		var params = RSComments.formFields(object);
		
		jQuery(object).parents('[data-rsc-task="form"]').find(':input').removeClass('invalid');
		jQuery(object).parents('[data-rsc-task="form"]').find('.rsc_loading_form').css('display','');
		jQuery(object).prop('disabled', true);
		
		jQuery.ajax({
			type: 'POST',
			url: RSComments.root() + 'index.php?option=com_rscomments',
			data: 'task=comments.save&' + params.join('&') + '&randomTime='+Math.random(),
			dataType: 'json',
			success: function(response) {
				jQuery(object).prop('disabled', false);
				jQuery(object).parents('[data-rsc-task="form"]').find('.rsc_loading_form').css('display','none');
				jQuery(object).parents('[data-rsc-task="form"]').find('.rscomments-form-message').empty();
				
				if (response.success) {
					jQuery(object).parents('[data-rsc-task="form"]').find('.rscomments-form-message').removeClass('alert-danger');
					jQuery(object).parents('[data-rsc-task="form"]').find('.rscomments-form-message').html('<p>' + response.SuccessMessage + '</p>');
					jQuery(object).parents('[data-rsc-task="form"]').find('.rscomments-form-message').css('display', '');
					
					// Reset the fields from the comment form
					if (jQuery(object).parents('[data-rsc-task="form"]').length) {
						jQuery(object).parents('[data-rsc-task="form"]')[0].reset();
					}
					
					jQuery(object).parents('[data-rsc-task="form"]').find('input[name="jform[IdParent]"]').val('0');
					jQuery(object).parents('[data-rsc-task="form"]').find('input[name="jform[IdComment]"]').val('');
					jQuery(object).parents('[data-rsc-task="form"]').find('.rsc_cancel_btn').css('display','none');
					
					var responseComment = jQuery('<div>', { id : 'rscomment_tmp'+response.IdComment }).html(response.HtmlComment);
					
					if (jQuery('#'+responseComment.id).length) {
						jQuery('#'+responseComment.id).remove();
					} else {
						if (jQuery('#rscomment'+response.IdComment).length) {
							jQuery('#rscomment'+response.IdComment).remove();
						}
					}
					
					// Add this to the top of the list
					if (response.Referrer == 0) {
						responseComment.insertBefore(jQuery('.rscomments-comments-list'));
					} else {
						if (jQuery('#rscomment'+response.Referrer).length) {
							responseComment.insertAfter(jQuery('#rscomment'+response.Referrer));
						}
					}
					
					// Move form to it's original place
					jQuery(object).parents('.rscomments').find('.rscomments-comment-form').append(jQuery(object).parents('.rscomments').find('.rscomment-form'));
					
					// Scroll to comment
					if (response.IdComment) {
						RSComments.animate(jQuery('#rscomment'+response.IdComment));
					}
					
					// Remove the success message
					setTimeout(function() {
						jQuery(object).parents('[data-rsc-task="form"]').find('.rscomments-form-message').animate({
							opacity: 0
						}, 500, function() {
							jQuery(this).css('display','none');
							jQuery(this).css('opacity','1');
							jQuery(this).empty();
						});
					},3500);
					
					// Captcha reset
					if (jQuery(object).parents('[data-rsc-task="form"]').find('.rscomments-refresh-captcha').length) {
						jQuery(object).parents('[data-rsc-task="form"]').find('.rscomments-refresh-captcha').click();
					}
					
					if (jQuery(object).parents('[data-rsc-task="form"]').find('.g-recaptcha-response').length) {
						var cid = jQuery(object).parents('[data-rsc-task="form"]').find('.g-recaptcha-response').parents('div[id^="rsc-g-recaptcha-"]').prop('id').replace('rsc-g-recaptcha-', '');
						grecaptcha.reset(RSCommentsReCAPTCHAv2.ids[cid]);
						if (RSCommentsReCAPTCHAv2.type[cid] == 'invisible') {
							grecaptcha.execute(RSCommentsReCAPTCHAv2.ids[cid]);
						}
					}
					
					jQuery(object).parents('[data-rsc-task="form"]').find('[data-rsc-task="closepreview"]').click();
				} else {
					jQuery(object).parents('[data-rsc-task="form"]').find('.rscomments-form-message').addClass('alert-danger');
					jQuery(object).parents('[data-rsc-task="form"]').find('.rscomments-form-message').html('<p>' + response.error + '</p>');
					jQuery(object).parents('[data-rsc-task="form"]').find('.rscomments-form-message').css('display', '');
					
					RSComments.animate(jQuery(object).parents('[data-rsc-task="form"]').find('.rscomments-form-message'));
					jQuery(object).parents('[data-rsc-task="form"]').find('[data-rsc-task="closepreview"]').click();
				}
			}
		});
	},
	
	upload: function(object) {
		var rsframe 	= jQuery(object).parents('[data-rsc-task="form"]').find('iframe[name="rsc_frame"]')[0];
		var innerDoc 	= rsframe.contentDocument || rsframe.contentDocument.document;
		
		innerDoc.getElementById('rsc_id').value = jQuery(object).parents('.rscomments').data('rsc-id');
		innerDoc.getElementById('rsc_option').value = jQuery(object).parents('.rscomments').data('rsc-option');
		innerDoc.getElementById('frameform').submit();
	},
	
	edit: function() {
		var id = jQuery(this).parents('[data-rsc-cid]').data('rsc-cid');
		var object = this;
		
		if (jQuery(this).parents('.rscomments').find('[data-rsc-task="form"]').length) {
			jQuery(this).parents('.rscomments').find('[data-rsc-task="form"]').find('.rsc_loading_form').css('display','');
			
			jQuery.ajax({
				type: 'POST',
				url: RSComments.root() + 'index.php?option=com_rscomments',
				data: 'task=comments.edit&id=' + id + '&randomTime='+Math.random(),
				dataType: 'json',
				success: function(response) {
					jQuery(object).parents('.rscomments').find('[data-rsc-task="form"]').find('.rsc_loading_form').css('display','none');
					
					if (response.error) {
						alert(response.error);
					} else {
						jQuery(object).parents('.rscomments').find('[data-rsc-task="form"]').find('input[name="jform[IdComment]"]').val(response.IdComment);
						jQuery(object).parents('.rscomments').find('[data-rsc-task="form"]').find('input[name="jform[IdParent]"]').val(response.IdParent);
						jQuery(object).parents('.rscomments').find('[data-rsc-task="form"]').find('input[name="jform[name]"]').val(response.name);
						jQuery(object).parents('.rscomments').find('[data-rsc-task="form"]').find('input[name="jform[email]"]').val(response.email);
						jQuery(object).parents('.rscomments').find('[data-rsc-task="form"]').find('[data-rsc-task="commentform"]').val(response.comment);
						jQuery(object).parents('.rscomments').find('[data-rsc-task="form"]').find('input[name="jform[subject]"]').val(response.subject);
						jQuery(object).parents('.rscomments').find('[data-rsc-task="form"]').find('input[name="jform[website]"]').val(response.website);
						
						jQuery(object).parents('.rscomments').find('[data-rsc-task="form"]').find('input[name="jform[name]"]').prop('disabled',false);
						jQuery(object).parents('.rscomments').find('[data-rsc-task="form"]').find('input[name="jform[email]"]').prop('disabled',false);
						jQuery(object).parents('.rscomments').find('[data-rsc-task="closepreview"]').click();
						
						RSComments.animate(jQuery(object).parents('.rscomments').find('[data-rsc-task="form"]').find('[data-rsc-task="commentform"]'));
					}
				}
			});
		}
	},
	
	shuffle: function() {
		var hash = window.location.hash;
		if (hash.indexOf('#rscomment') != -1) {
			var cid = hash.replace('#','');
			if (jQuery('#'+cid).length) {
				window.setTimeout(function() {
					RSComments.animate(jQuery('#'+cid));
				},1000);
			}
		}
	},
	
	captcha: function(object, url) {
		jQuery(object).parents('.rsc-captcha-container').find('img').prop('src', url + (url.indexOf('?') == -1 ? '?' : '&') + 'sid=' + Math.random());
		jQuery(object).parents('.rsc-captcha-container').find('input').val('');
	},
	
	terms: function() {
		jQuery('input[name="rscomments_id"]').val(jQuery(this).parents('.rscomments').data('rsc-id'));
		jQuery('input[name="rscomments_option"]').val(jQuery(this).parents('.rscomments').data('rsc-option'));
		
		if (jQuery('#rscomments-terms').length) {
			jQuery('#rscomments-terms').modal('show');
		} else {
			jQuery.magnificPopup.open({
				items: {
					src: jQuery('input[name="rscomments_terms"]').val()
				},
				type: 'ajax'
			}, 0);
		}
	},
	
	agree: function() {
		if (jQuery('input[name="rscomments_id"]').val() != '' && jQuery('input[name="rscomments_option"]').val() != '') {
			jQuery('.rscomments[data-rsc-id="' + jQuery('input[name="rscomments_id"]').val() + '"][data-rsc-option="' + jQuery('input[name="rscomments_option"]').val() + '"]').find('input[name="jform[rsc_terms]"]').prop('checked', true);
			
			jQuery('input[name="rscomments_id"]').val('');
			jQuery('input[name="rscomments_option"]').val('');
		}
		
		if (jQuery('#rscomments-terms').length) {
			jQuery('#rscomments-terms').modal('hide');
		} else {
			jQuery.magnificPopup.close();
		}
	},
	
	report: function() {
		var id = jQuery(this).parents('[data-rsc-cid]').data('rsc-cid');
		jQuery('input[name="rscomments_cid"]').val(id);
		
		if (jQuery('#rscomments-report').length) {
			jQuery('#rscomments-report').modal('show');
		} else {
			jQuery.magnificPopup.open({
				items: {
					src: jQuery('input[name="rscomments_report"]').val()
				},
				type: 'iframe'
			}, 0);
		}
	},
	
	doReport: function() {
		var id		= window.parent.jQuery('input[name="rscomments_cid"]').val();
		var reason	= jQuery('#report-reason').val();
		var errors	= new Array();
		var params	= {};
		var newrecaptcha   	= jQuery('.g-recaptcha-response').val();
		var builtincaptcha 	= jQuery('.rsc-captcha-container input').val();
		
		if (jQuery.trim(reason) == '') {
			errors.push('<p>' + Joomla.JText._('COM_RSCOMMENTS_REPORT_NO_REASON') + '</p>');
			jQuery('#report-reason').addClass('invalid');
		} else {
			jQuery('#report-reason').removeClass('invalid');
		}
		
		if (jQuery('.g-recaptcha-response').length) {
			if (jQuery.trim(newrecaptcha) == '') {
				errors.push('<p>' + Joomla.JText._('COM_RSCOMMENTS_REPORT_INVALID_CAPTCHA') + '</p>');
			} else {
				params['g-recaptcha-response'] = newrecaptcha;
			}
		}
		
		if (jQuery('.rsc-captcha-container').find('.rscomments-refresh-captcha').length) {
			if (jQuery.trim(builtincaptcha) == '') {
				errors.push('<p>' + Joomla.JText._('COM_RSCOMMENTS_REPORT_INVALID_CAPTCHA') + '</p>');
			} else {
				params.captcha = builtincaptcha;
			}
		}
		
		if (jQuery('#consent').length && jQuery('#consent:checked').length == 0) {
			errors.push('<p>' + Joomla.JText._('COM_RSCOMMENTS_CONSENT_ERROR') + '</p>');
		}
		
		if (errors.length) {
			jQuery('#report-message').addClass('alert-danger');
			jQuery('#report-message').css('display','');
			jQuery('#report-message').html(errors.join(''));
			return;
		} else {
			jQuery('#report-message').empty();
			jQuery('#report-message').removeClass('alert-danger');
			jQuery('#report-message').css('display','none');
		}
		
		params.report = reason;
		params.id = id;
		
		jQuery.ajax({
			type: 'POST',
			url: RSComments.root() + 'index.php?option=com_rscomments&task=comments.report',
			data : params,
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					jQuery('#report-message').removeClass('alert-danger').addClass('alert-success');
					jQuery('#report-reason').val('');
					setTimeout(function() {
						if (window.parent.jQuery('#rscomments-report').length) {
							window.parent.jQuery('#rscomments-report').modal('hide');
							window.parent.jQuery('#rscomments-report').find('.modal-body').empty();
						} else {
							window.parent.jQuery.magnificPopup.close();
						}
						
					}, 2000);
				} else {
					jQuery('#report-message').addClass('alert-danger');
					
					if (jQuery('.rsc-captcha-container').find('.rscomments-refresh-captcha').length) {
						jQuery('.rscomments-refresh-captcha').click();
					}
					
					if (jQuery('.g-recaptcha-response').length) {
						grecaptcha.reset(RSCommentsReCAPTCHAv2.ids['report']);
						if (RSCommentsReCAPTCHAv2.type['report'] == 'invisible') {
							grecaptcha.execute(RSCommentsReCAPTCHAv2.ids['report']);
						}
					}
				}
				
				jQuery('#report-message').css('display', '');
				jQuery('#report-message').html('<p>' + response.message + '</p>');
			}
		});
	},
	
	changeSubscription: function() {
		var id = jQuery(this).parents('.rscomments').data('rsc-id');
		var option = jQuery(this).parents('.rscomments').data('rsc-option');
		var task = jQuery(this).data('rsc-task');
		var parent = jQuery(this).parents('.rscomments');
		
		jQuery(this).parents('.rscomments').find('.rscomments-top-loader').css('display','');
		
		jQuery.ajax({
			type: 'POST',
			url: RSComments.root() + 'index.php?option=com_rscomments',
			data: 'task=comments.' + task + '&id=' + id + '&opt=' + option + '&randomTime='+Math.random(),
			dataType: 'json',
			success: function(response) {
				parent.find('.rscomments-top-loader').css('display','none');
				parent.find('.rscomments-top-alert').html(response.message);
				parent.find('.rscomments-top-alert').css('display','');
				
				setTimeout(function() {
					parent.find('.rscomments-top-alert').animate({
						opacity: 0
					}, 500, function() {
						jQuery(this).css('display','none');
						jQuery(this).css('opacity','1');
						jQuery(this).empty();
					});
				},3500);
				
				parent.find('.rsc_subscr').html(response.html);
			}
		});
		
	},
	
	subscribeForm: function() {
		jQuery('input[name="rscomments_id"]').val(jQuery(this).parents('.rscomments').data('rsc-id'));
		jQuery('input[name="rscomments_option"]').val(jQuery(this).parents('.rscomments').data('rsc-option'));
		
		if (jQuery('#rscomments-subscribe').length) {
			jQuery('#rscomments-subscribe').modal('show');
		} else {
			jQuery.magnificPopup.open({
				items: {
					src: jQuery('input[name="rscomments_subscribe"]').val()
				},
				type: 'iframe'
			}, 0);
		}
	},
	
	doSubscribe: function() {
		var id		= window.parent.jQuery('input[name="rscomments_id"]').val();
		var option	= window.parent.jQuery('input[name="rscomments_option"]').val();
		var name	= jQuery('#subscriber-name').val();
		var email	= jQuery('#subscriber-email').val();
		var regex	= /^[a-zA-Z0-9.!#$%&’*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
		var errors	= new Array();
		
		if (jQuery.trim(name) == '') {
			errors.push('<p>' + Joomla.JText._('COM_RSCOMMENTS_NO_SUBSCRIBER_NAME') + '</p>');
			jQuery('#subscriber-name').addClass('invalid');
		} else {
			jQuery('#subscriber-name').removeClass('invalid');
		}
		
		if (jQuery.trim(email) == '') {
			errors.push('<p>' + Joomla.JText._('COM_RSCOMMENTS_NO_SUBSCRIBER_EMAIL') + '</p>');
			jQuery('#subscriber-email').addClass('invalid');
		} else {
			jQuery('#subscriber-email').removeClass('invalid');
		}
		
		if (jQuery.trim(email) != '') {
			if (!regex.test(jQuery.trim(email))) {
				errors.push('<p>' + Joomla.JText._('COM_RSCOMMENTS_INVALID_SUBSCRIBER_EMAIL') + '</p>');
				jQuery('#subscriber-email').addClass('invalid');
			} else {
				jQuery('#subscriber-email').removeClass('invalid');
			}
		}
		
		if (jQuery('#consent').length && jQuery('#consent:checked').length == 0) {
			errors.push('<p>' + Joomla.JText._('COM_RSCOMMENTS_CONSENT_ERROR') + '</p>');
		}
		
		if (errors.length) {
			jQuery('#subscriber-message').addClass('alert-danger');
			jQuery('#subscriber-message').css('display','');
			jQuery('#subscriber-message').html(errors.join(''));
			return;
		} else {
			jQuery('#subscriber-message').empty();
			jQuery('#subscriber-message').removeClass('alert-danger');
			jQuery('#subscriber-message').css('display','none');
		}
		
		jQuery.ajax({
			type: 'POST',
			url: RSComments.root() + 'index.php?option=com_rscomments&task=comments.subscribeuser',
			data : {
				'theoption'	: option,
				'theid'		: id,
				'name'		: name,
				'email'		: email
			},
			dataType: 'json',
			success: function(response) {
				if (response.status) {
					jQuery('#subscriber-message').removeClass('alert-danger');
					setTimeout(function() {
						if (window.parent.jQuery('#rscomments-subscribe').length) {
							window.parent.jQuery('#rscomments-subscribe').modal('hide');
							window.parent.jQuery('#rscomments-subscribe').find('.modal-body').empty();
						} else {
							window.parent.jQuery.magnificPopup.close();
						}
					}, 2000);
				} else {
					jQuery('#subscriber-message').addClass('alert-danger');
				}
				
				jQuery('#subscriber-message').css('display', '');
				jQuery('#subscriber-message').html('<p>' + response.message + '</p>');
			}
		});
	},
	
	pagination: function() {
		var total = parseInt(jQuery(this).parents('.rscomments').find('.rsc_total').text());
		var limitstart = jQuery(this).parents('.rscomments').find('.rscomment').length;
		var object = this;
		
		var id = jQuery(object).parents('.rscomments').data('rsc-id');
		var option = jQuery(object).parents('.rscomments').data('rsc-option');
		var override = jQuery(object).data('task-override');
		
		jQuery(object).parents('.rscomments').find('.rsc_loading_pages').css('display','');
		
		jQuery.ajax({
			type: 'POST',
			url: RSComments.root() + 'index.php?option=com_rscomments',
			data: 'task=pagination&override=' + override + '&content=' + option + '&id=' + id + '&limitstart=' + limitstart + '&pagination=0&randomTime='+Math.random(),
			dataType: 'html',
			success: function(response) {
				jQuery(object).parents('.rscomments').find('.rsc_loading_pages').css('display','none');
				
				var htmlresponse = jQuery('<div>', { id : 'rsc_tmp_'+limitstart }).html(response);
				jQuery(object).parents('.rscomments').find('.rscomments-comments-list').append(htmlresponse);
				
				RSComments.animate(jQuery('#rsc_tmp_'+limitstart));
				RSComments.shuffle();
				
				if (jQuery(object).parents('.rscomments').find('.rscomments-comments-list .rscomment').length >= total) {
					jQuery(object).parents('.rscomments').find('.rsc_pagination').remove();
				}
			}
		});
	},
	
	detectAddress: function() {
		var object = this;
		
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function(position) {
				try {
					var coordinates = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
					var geocoder = new google.maps.Geocoder();
					
					geocoder.geocode({'latLng': coordinates}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							var inputBtn = jQuery(object).parents('[data-rsc-task="form"]').find('input[name="jform[location]"]');
							var results_wrapper = jQuery('<div class="rsc-results-wrapper"><ul class="rsc-results"></ul></div>');
							inputBtn.after(results_wrapper);
							
							jQuery(results).each(function(index, item) {
								var li = jQuery('<li>' + item.formatted_address + '</li>').click(function() {
									inputBtn.val(item.formatted_address);
									jQuery(object).parents('[data-rsc-task="form"]').find('input[name="jform[coordinates]"]').val( item.geometry.location.lat().toFixed(7) + ',' + item.geometry.location.lng().toFixed(7) );
									results_wrapper.remove();
								});
								
								results_wrapper.find('ul').append(li);  
							});
							
							jQuery(document).click( function(event) {
								if (jQuery(event.target).parents().index(results_wrapper) == -1 ) {
									results_wrapper.remove();
								}
							});
						}
					});
				} catch(err) {}
			});
		}
	},
	
	searchAddress: function() {
		var inputBtn = jQuery(this);
		inputBtn.parent().find('.rsc-results-wrapper').remove();
		
		if (jQuery.trim(inputBtn.val())) {
			try {
				var geocoder = new google.maps.Geocoder();
				
				geocoder.geocode( {address: inputBtn.val()}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						var results_wrapper = jQuery('<div class="rsc-results-wrapper"><ul class="rsc-results"></ul></div>');
						inputBtn.after(results_wrapper);
						
						jQuery(results).each(function(index, item) {
							var li = jQuery('<li>' + item.formatted_address + '</li>').click(function() {
								inputBtn.val(item.formatted_address);
								inputBtn.parents('[data-rsc-task="form"]').find('input[name="jform[coordinates]"]').val( item.geometry.location.lat().toFixed(7) + ',' + item.geometry.location.lng().toFixed(7) );
								results_wrapper.remove();
							});
							
							results_wrapper.find('ul').append(li);  
						});
						
						jQuery(document).click( function(event) {
							if (jQuery(event.target).parents().index(results_wrapper) == -1 ) {
								results_wrapper.remove();
							}
						});
					}
				});
			} catch(err) {}
		}
	},
	
	previewComment: function() {
		var object  = jQuery(this);
		var comment = jQuery(this).parents('.rscomments').find('textarea[name="jform[comment]"]').val();
		
		if (comment.length) {
			object.parents('.rscomments').find('.rsc_loading_preview').css('display','');
			
			jQuery.ajax({
				type: 'POST',
				url: RSComments.root() + 'index.php?option=com_rscomments',
				data: 'task=preview&comment[]=' + encodeURIComponent(comment) + '&randomTime='+Math.random(),
				dataType: 'html',
				success: function(response) {
					object.parents('.rscomments').find('.rsc_loading_preview').css('display','none');
					object.parents('.rscomments').find('.rscomments-action-btns').css('display', 'none');
					object.parents('.rscomments').find('[data-rsc-task="emoticons"]').removeClass('btn-success');
					object.parents('.rscomments').find('.rscomments-close-preview').css('display', '');
					object.parents('.rscomments').find('textarea[name="jform[comment]"]').css('display', 'none');
					
					object.parents('.rscomments').find('.rscomments-preview-area').css('min-height', object.parents('.rscomments').find('textarea[name="jform[comment]"]').height());
					object.parents('.rscomments').find('.rscomments-preview-area').html(response);
					object.parents('.rscomments').find('.rscomments-preview-area').css('display', '');
				}
			});
		}
	},
	
	closePreview: function() {
		jQuery(this).parents('.rscomments').find('.rscomments-action-btns').css('display', '');
		jQuery(this).parents('.rscomments').find('.rsc_emoticons').css('display', 'none');
		jQuery(this).parents('.rscomments').find('.rscomments-close-preview').css('display', 'none');
		jQuery(this).parents('.rscomments').find('textarea[name="jform[comment]"]').css('display', '');
		jQuery(this).parents('.rscomments').find('.rscomments-preview-area').css('display', 'none');
	},
	
	isIE: function() {
		var rv = -1;
		if (navigator.appName == 'Microsoft Internet Explorer') {
			var ua = navigator.userAgent;
			var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
			if (re.exec(ua) != null)
				rv = parseFloat( RegExp.$1 );
		}
		
		return rv;
	}
}

jQuery(document).ready(function() {
	RSComments.init();
});

jQuery(document).ajaxSuccess(function() {
	RSComments.init();
});

var RSCommentsReCAPTCHAv2 = {
	ids: {},
	type: {},
	loaders: [],
	onLoad: function() {
		window.setTimeout(function(){
			for (var i = 0; i < RSCommentsReCAPTCHAv2.loaders.length; i++) {
				var func = RSCommentsReCAPTCHAv2.loaders[i];
				if (typeof func == "function") {
					func();
				}
			}
		}, 500)
	}
};

window.addEventListener('DOMContentLoaded', function() {
	RSCommentsReCAPTCHAv2.onLoad();
});

function rscAddEvent(obj, evType, fn) {
	if (obj.addEventListener) {
		obj.addEventListener(evType, fn, false);
		return true;
	} else if (obj.attachEvent){
		var r = obj.attachEvent("on"+evType, fn);
		return r;
	} else {
		return false;
	}
}

/* DEPRECATED Functions */
var deprecatedText = 'This function is not used anymore. If you have a template override, please update your files.';
function rsc_check(e) {}
function hideMessage(id) {}
function rsc_initModal() {}
function rsc_reply(id) { alert(deprecatedText); }
function rsc_cancel_reply(id) { alert(deprecatedText); }
function rsc_quote(name,id) { alert(deprecatedText); }
function rsc_open(id,option,override) { alert(deprecatedText); }
function rsc_close(id,option,override) { alert(deprecatedText); }
function rsc_closes(id,option,override) { alert(deprecatedText); }
function rsc_createImage(text) { alert(deprecatedText); }
function rsc_createUrl(text) { alert(deprecatedText); }
function rsc_addTags(tag1,tag2) { alert(deprecatedText); }
function rsc_createList(tag1,tag2) { alert(deprecatedText); }
function rsc_show_emoticons() { alert(deprecatedText); }
function rsc_smiley(tag) { alert(deprecatedText); }
function rsc_reset_form() { alert(deprecatedText); }
function rscomments_close_accordion() { alert(deprecatedText); }
function rsc_initTooltips(divid,isajaxcall) { alert(deprecatedText); }
function rsc_comment_cnt(what,cntfield,maxlimit) {}
function rsc_publish(id) { alert(deprecatedText); }
function rsc_unpublish(id) { alert(deprecatedText); }
function rsc_delete_fn(confirmText, id) { alert(deprecatedText); }
function rsc_delete(id) {}
function showLoader(id) {}
function hideLoader(id) {}
function rsc_pos(id) { alert(deprecatedText); }
function rsc_neg(id) { alert(deprecatedText); }
function rscomments_vote(type, id) {}
function rsc_view(id) { alert(deprecatedText); }
function initTooltips() {}
function rsc_get_ie() {}
function rscomments_form_fields() {}
function rsc_validate(upload,url_captcha) {}
function rsc_save(url_captcha) {}
function rsc_shuffle() {}
function rsc_upload(root,url_captcha) {}
function rsc_edit(id) { alert(deprecatedText); }
function rsc_refresh_captcha(root,url_captcha, prefix) {}
function rscomments_agree() {}
function rscomments_show_report(id) {}
function rsc_do_report() {}
function rsc_subscribe(id,option) { alert(deprecatedText); }
function rsc_unsubscribe(id, option) { alert(deprecatedText); }
function rscomments_subscribe() { alert(deprecatedText); }
function rscomments_report() { alert(deprecatedText); }
function rsc_pagination(page, option, id, template, override) {}
/* DEPRECATED Functions */