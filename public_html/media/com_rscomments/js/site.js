/*
 * RSComments! javascript functions
*/

var RSCommentsReCAPTCHAv2 = {
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

if (typeof jQuery !== 'undefined') {
	jQuery(document).ready(function($) {
		$(window).load(RSCommentsReCAPTCHAv2.onLoad);
	});
} else if (typeof MooTools !== 'undefined') {
	window.addEvent('domready', function(){
		window.addEvent('load', RSCommentsReCAPTCHAv2.onLoad);
	});
} else {
	rscAddEvent(window, 'load', function() {
		RSCommentsReCAPTCHAv2.onLoad();
	});
}

(function( jQuery, window, undefined ) {
	var matched, browser;

	jQuery.uaMatch = function( ua ) {
		ua = ua.toLowerCase();

		var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
			/(webkit)[ \/]([\w.]+)/.exec( ua ) ||
			/(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
			/(msie) ([\w.]+)/.exec( ua ) ||
			ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
			[];

		return {
			browser: match[ 1 ] || "",
			version: match[ 2 ] || "0"
		};
	};

	// Don't clobber any existing jQuery.browser in case it's different
	if ( !jQuery.browser ) {
		matched = jQuery.uaMatch( navigator.userAgent );
		browser = {};

		if ( matched.browser ) {
			browser[ matched.browser ] = true;
			browser.version = matched.version;
		}

		// Chrome is Webkit, but Webkit is also Safari.
		if ( browser.chrome ) {
			browser.webkit = true;
		} else if ( browser.webkit ) {
			browser.safari = true;
		}

		jQuery.browser = browser;
	}
})( jQuery, window );

jQuery(document).ready(function() {
	rsc_shuffle();
	
	jQuery('#rscomments-report').on('hide', function() {
		if (jQuery('#rscomments-form-recaptcha').length) {
			reload_form_recapthca();
		}
	});
	
	jQuery('.rscomments-accordion-title').click(function(e) {
		// Grab current anchor value
		var currentAttrValue = jQuery(this).attr('href');
		
		if (jQuery(e.target).is('.active')) {
			rscomments_close_accordion();
		} else {
			rscomments_close_accordion();

			// Add active class to section title
			jQuery(this).addClass('active');
			// Set the close message
			jQuery(this).html(Joomla.JText._('COM_RSCOMMENTS_HIDE_FORM'));
			// Open up the hidden content panel
			jQuery('.rscomments-accordion ' + currentAttrValue).slideDown(300).addClass('open'); 
		}

		e.preventDefault();
	});
});

function rscomments_close_accordion() {
	jQuery('.rscomments-accordion .rscomments-accordion-title').removeClass('active');
	jQuery('.rscomments-accordion .rscomments-accordion-title').html(Joomla.JText._('COM_RSCOMMENTS_SHOW_FORM'));
	jQuery('.rscomments-accordion .rscomments-accordion-content').slideUp(300).removeClass('open');
}

// Reset form
function rsc_reset_form() {
	var root = typeof rsc_root != 'undefined' ? rsc_root : '';
	
	jQuery.ajax({
		type: 'POST',
		url: root + 'index.php?option=com_rscomments',
		data : {
			'task': 'reset',
			'randomTime' : Math.random()
		},
		dataType: 'html',
		success: function(response) {
			jQuery('#rscommentsForm :input').removeClass('invalid');
			jQuery('#rscomments-form-message').removeClass('alert-danger');
			jQuery('#rscomments-form-message').css('display','none');
			jQuery('#rscomments-form-message').empty();
			if (jQuery('#rscommentsForm').length) document.getElementById('rscommentsForm').reset();
			jQuery('#rsc_comment').keyup();
			initTooltips();
		}
	});
}

// Show the report layout
function rscomments_show_report(id) {
	jQuery('#rscomments-report').find('#reportid').val(id);
	jQuery('#rscomments-report').modal('show');
}

// Subscribe the user
function rscomments_subscribe() {
	var option	= window.parent.jQuery('#rscomments-subscribe').find('#commentoption').val();
	var id		= window.parent.jQuery('#rscomments-subscribe').find('#commentid').val();
	var name	= jQuery('#subscriber-name').val();
	var email	= jQuery('#subscriber-email').val();
	var regex	= /^[a-zA-Z0-9.!#$%&’*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
	var root	= typeof rsc_root != 'undefined' ? rsc_root : '';
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
		url: root + 'index.php?option=com_rscomments&task=comments.subscribeuser',
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
					window.parent.jQuery('#rscomments-subscribe').modal('hide');
					window.parent.jQuery('#rscomments-subscribe').find('.modal-body').empty();
				}, 2000);
			} else {
				jQuery('#subscriber-message').addClass('alert-danger');
			}
			
			jQuery('#subscriber-message').css('display', '');
			jQuery('#subscriber-message').html('<p>' + response.message + '</p>');
		}
	});	
}

// Report comment
function rscomments_report() {
	var id		= jQuery('#commentid').val();
	var reason	= jQuery('#report-reason').val();
	var root	= typeof rsc_root != 'undefined' ? rsc_root : '';
	var errors	= new Array();
	var params	= {};
	
	var newrecaptcha   	= jQuery('.g-recaptcha-response').val();
	var recaptcha		= jQuery('#recaptcha_response_field').val();
	var builtincaptcha 	= jQuery('#report_submit_captcha').val();
	
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
	
	if (jQuery('#report_submit_captcha').length) {
		if (jQuery.trim(builtincaptcha) == '') {
			errors.push('<p>' + Joomla.JText._('COM_RSCOMMENTS_REPORT_INVALID_CAPTCHA') + '</p>');
		} else {
			params.captcha = builtincaptcha;
		}
	}
	
	if (jQuery('#recaptcha_response_field').length) {
		if (jQuery.trim(recaptcha) == '') {
			errors.push('<p>' + Joomla.JText._('COM_RSCOMMENTS_REPORT_INVALID_CAPTCHA') + '</p>');
		} else {
			params.recaptcha_challenge_field = jQuery('#recaptcha_challenge_field').val();
			params.recaptcha_response_field = recaptcha;
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
		url: root + 'index.php?option=com_rscomments&task=comments.report',
		data : params,
		dataType: 'json',
		success: function(response) {
			if (response.success) {
				jQuery('#report-message').removeClass('alert-danger');
				jQuery('#report-reason').val('');
				setTimeout(function() {
					window.parent.jQuery('#rscomments-report').modal('hide');
					window.parent.jQuery('#rscomments-report').find('.modal-body').empty();
				}, 2000);
			} else {
				jQuery('#report-message').addClass('alert-danger');
				
				if (jQuery('#report_submit_captcha').length) {
					jQuery('#report_submit_captcha').val('');
					jQuery('#rscomments-refresh-captcha').click();
				}
				
				if (jQuery('#recaptcha_response_field').length) {
					jQuery('#recaptcha_response_field').val('');
					Recaptcha.reload();
				}
				
				if (jQuery('.g-recaptcha-response').length) {
					grecaptcha.reset();
				}
			}
			
			jQuery('#report-message').css('display', '');
			jQuery('#report-message').html('<p>' + response.message + '</p>');
		}
	});	
}

function rscomments_form_fields() {
	var params		= new Array();
	
	jQuery('#rscommentsForm :input').each(function() {
		if (jQuery(this).prop('type') == 'button' || jQuery(this).prop('name') == '') {
			return;
		}
		
		var fname   = jQuery(this).prop('name');
		var fvalue  = jQuery(this).val();
		var fnameid = jQuery(this).prop('id');
		
		if (jQuery(this).prop('id') == 'rsc_subscribe_thread') {
			fvalue = jQuery('#rsc_subscribe_thread').is(':checked') ? 1 : 0;
		}
		
		if (fname.indexOf('rsc_terms') != -1 ) {
			ie_version = rsc_get_ie();
			if (ie_version > -1 && ie_version >= 9) {
				jQuery('.rokrsc_terms').each(function(i,el) {
					if (jQuery(el).prop('class').indexOf('rokchecks-active') > -1) {
						jQuery('#'+fnameid).prop('checked',true);
					} else {
						jQuery('#'+fnameid).prop('checked',false);
					}
				});
			}
			
			fvalue = jQuery('#'+fnameid).is(':checked') ? 1 : 0;
		}
		
		if (fname.indexOf('rsc_consent') != -1 ) {
			ie_version = rsc_get_ie();
			if (ie_version > -1 && ie_version >= 9) {
				jQuery('.rokrsc_consent').each(function(i,el) {
					if (jQuery(el).prop('class').indexOf('rokchecks-active') > -1) {
						jQuery('#'+fnameid).prop('checked',true);
					} else {
						jQuery('#'+fnameid).prop('checked',false);
					}
				});
			}
			
			fvalue = jQuery('#'+fnameid).is(':checked') ? 1 : 0;
		}
		
		params.push(fname + '=' + encodeURIComponent(fvalue));
	});
	
	return params;
}

// Validate form
function rsc_validate(upload,url_captcha) {
	var root	= typeof rsc_root != 'undefined' ? rsc_root : '';
	var params	= rscomments_form_fields();
	
	jQuery('#rscommentsForm :input').removeClass('invalid');
	jQuery('#rsc_loading_form').css('display','');
	jQuery('#rsc_submit').prop('disabled', true);
	
	jQuery.ajax({
		type: 'POST',
		url: root + 'index.php?option=com_rscomments',
		data: 'task=comments.validate&' + params.join('&') + '&randomTime='+Math.random(),
		dataType: 'json',
		success: function(response) {
			jQuery('#rsc_loading_form').css('display','none');
			jQuery('#rsc_submit').prop('disabled', false);
			jQuery('#rscomments-form-message').empty();
			
			if (response.success) {
				jQuery('#rscomments-form-message').removeClass('alert-danger');
				jQuery('#rscomments-form-message').css('display', 'none');
				
				if (upload == 1) {
					rsc_upload(url_captcha);
				} else {
					rsc_save(url_captcha);
				}
			} else {
				jQuery('#rscomments-form-message').addClass('alert-danger');
				jQuery(response.errors).each(function(i, el) {
					jQuery('#rscomments-form-message').append('<p>' + el + '</p>');
				});
				jQuery('#rscomments-form-message').css('display', '');
				
				jQuery(jQuery.browser.webkit ? "body": "html").animate({
					scrollTop: jQuery('#rscomments-form-message').offset().top
				});
				
				jQuery(response.fields).each(function(i, el) {
					jQuery('#rscommentsForm :input[name="jform[' + el + ']"]').addClass('invalid');
				});
				
				// Captcha reset
				if (jQuery('#rscommentsForm').find('#submit_captcha').length) {
					jQuery('#rscommentsForm').find('#submit_captcha').val('');
					jQuery('#rscommentsForm').find('#rscomments-refresh-captcha').click();
				}
				
				if (jQuery('#rscommentsForm').find('#recaptcha_response_field').length) {
					jQuery('#rscommentsForm').find('#recaptcha_response_field').val('');
					Recaptcha.reload();
				}
				
				if (jQuery('#rscommentsForm').find('.g-recaptcha-response').length) {
					grecaptcha.reset();
				}
			}
		}
	});
}

// Save comment
function rsc_save(url_captcha) {
	var root	= typeof rsc_root != 'undefined' ? rsc_root : '';
	var params	= rscomments_form_fields();
	
	jQuery('#rscommentsForm :input').removeClass('invalid');
	jQuery('#rsc_loading_form').css('display','');
	jQuery('#rsc_submit').prop('disabled',true);
	
	jQuery.ajax({
		type: 'POST',
		url: root + 'index.php?option=com_rscomments',
		data: 'task=comments.save&' + params.join('&') + '&randomTime='+Math.random(),
		dataType: 'json',
		success: function(response) {
			jQuery('#rsc_loading_form').css('display','none');
			jQuery('#rsc_submit').prop('disabled', false);
			jQuery('#rscomments-form-message').empty();
			
			if (response.success) {
				jQuery('#rscomments-form-message').removeClass('alert-danger');
				jQuery('#rscomments-form-message').html('<p>' + response.SuccessMessage + '</p>');
				jQuery('#rscomments-form-message').css('display', '');
				
				// Reset the fields from the comment form
				if (jQuery('#rscommentsForm').length) document.getElementById('rscommentsForm').reset();
				jQuery('#rsc_id_parent').val('0');
				jQuery('#rsc_cancel').css('display','none');
				jQuery('#rsc_IdComment').val('');
				
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
				jQuery('#rscomments-comment-form').append(jQuery('.rscomment-form'));
				
				// Scroll to comment
				if (response.IdComment) {
					jQuery(jQuery.browser.webkit ? "body": "html").animate({
						scrollTop: jQuery('#rscomment'+response.IdComment).offset().top
					});
				}
				
				// Remove the success message
				setTimeout(function() {
					jQuery('#rscomments-form-message').animate({
						opacity: 0
					}, 500, function() {
						jQuery(this).css('display','none');
						jQuery(this).css('opacity','1');
						jQuery(this).empty();
					});
				},3500);
				
				// Captcha reset
				if (jQuery('#rscommentsForm').find('#submit_captcha').length) {
					jQuery('#rscommentsForm').find('#submit_captcha').val('');
					jQuery('#rscommentsForm').find('#rscomments-refresh-captcha').click();
				}
				
				if (jQuery('#rscommentsForm').find('#recaptcha_response_field').length) {
					jQuery('#rscommentsForm').find('#recaptcha_response_field').val('');
					Recaptcha.reload();
				}
				
				if (jQuery('#rscommentsForm').find('.g-recaptcha-response').length) {
					grecaptcha.reset();
				}
				
				initTooltips();
			} else {
				jQuery('#rscomments-form-message').addClass('alert-danger');
				jQuery('#rscomments-form-message').html('<p>' + response.error + '</p>');
				jQuery('#rscomments-form-message').css('display', '');
				
				jQuery(jQuery.browser.webkit ? "body": "html").animate({
					scrollTop: jQuery('#rscomments-form-message').offset().top
				});
			}
		}
	});
}

// Reply to comment
function rsc_reply(id) {
	if (jQuery('#rscommentsForm').length) {
		// Clear errors
		jQuery('#rscomments-form-message').css('display','none');
		jQuery('#rscomments-form-message').html('');
		// Open the form if this is closed
		if (!jQuery('.rscomments-accordion-title').hasClass('active')) {
		  jQuery('.rscomments-accordion-title').click();
		}
		// Clear the form
		document.getElementById('rscommentsForm').reset();
		// Move form to the comment
		jQuery('#rscomments-reply-'+id).append(jQuery('.rscomment-form'));
		// Set parent ID
		jQuery('#rsc_id_parent').val(id);
		// Add the cancel button
		jQuery('#rsc_cancel').css('display','inline');
	}
}

// Cancel reply to comment
function rsc_cancel_reply() {
	jQuery('#rscomments-comment-form').append(jQuery('.rscomment-form'));

	jQuery('#rsc_id_parent').val('0');
	jQuery('#rsc_cancel').css('display','none');
	rsc_reset_form();
}

// Edit comment
function rsc_edit(id) {
	if (jQuery('#rscommentsForm').length) {
		var root = typeof rsc_root != 'undefined' ? rsc_root : '';
		jQuery('#rsc_loading_form').css('display','');
		
		jQuery.ajax({
			type: 'POST',
			url: root + 'index.php?option=com_rscomments',
			data: 'task=comments.edit&id=' + id + '&randomTime='+Math.random(),
			dataType: 'json',
			success: function(response) {
				jQuery('#rsc_loading_form').css('display','none');
				
				if (response.error) {
					alert(response.error);
				} else {
					jQuery('#rsc_IdComment').val(response.IdComment);
					jQuery('#rsc_id_parent').val(response.IdParent);
					jQuery('#rsc_name').val(response.name);
					jQuery('#rsc_email').val(response.email);
					jQuery('#rsc_comment').val(response.comment);
					
					if (jQuery('#rsc_subject').length) jQuery('#rsc_subject').val(response.subject);
					if (jQuery('#rsc_website').length) jQuery('#rsc_website').val(response.website);
					
					jQuery('#rsc_name').prop('disabled',false);
					jQuery('#rsc_email').prop('disabled',false);
					
					jQuery(jQuery.browser.webkit ? "body": "html").animate({
						scrollTop: jQuery('#rsc_comment').offset().top
					});
					
					initTooltips();
				}
			}
		});
	}
}

// Quote comment
function rsc_quote(name,id) {
	if (jQuery('#rscommentsForm').length) {
		var root = typeof rsc_root != 'undefined' ? rsc_root : '';
		jQuery('#rsc_loading_form').css('display','');
		
		jQuery.ajax({
			type: 'POST',
			url: root + 'index.php?option=com_rscomments',
			data: 'task=comments.quote&id=' + id + '&randomTime='+Math.random(),
			dataType: 'json',
			success: function(response) {
				jQuery('#rsc_loading_form').css('display','none');
				
				if (response.comment) {
					jQuery('#rsc_comment').val('[quote name="'+name+'"]' + response.comment + '[/quote]');
					jQuery('#rsc_comment').keyup();
					jQuery('#rsc_IdComment').val('');
					
					// Open the form if this is closed
					if (!jQuery('.rscomments-accordion-title').hasClass('active')) {
					  jQuery('.rscomments-accordion-title').click();
					}
					
					jQuery(jQuery.browser.webkit ? "body": "html").animate({
						scrollTop: jQuery('#rsc_comment').offset().top
					});
					
					initTooltips();
				}
			}
		});
	}
}

// Cast a positive vote
function rsc_pos(id) {
	rscomments_vote('voteup', id);
}

// Cast a negative vote
function rsc_neg(id) {
	rscomments_vote('votedown', id);
}

// Cast a vote
function rscomments_vote(type, id) {
	var root = typeof rsc_root != 'undefined' ? rsc_root : '';
	showLoader(id);
	
	jQuery.ajax({
		type: 'POST',
		url: root + 'index.php?option=com_rscomments',
		data: 'task=comments.' + type + '&id=' + id + '&randomTime='+Math.random(),
		dataType: 'json',
		success: function(response) {
			hideLoader(id);
			jQuery('#rsc_voting'+id).html(response.vote);
		}
	});
}

// Publish comment
function rsc_publish(id) {
	var root = typeof rsc_root != 'undefined' ? rsc_root : '';
	showLoader(id);
	
	jQuery.ajax({
		type: 'POST',
		url: root + 'index.php?option=com_rscomments',
		data: 'task=comments.publish&id=' + id + '&randomTime='+Math.random(),
		dataType: 'json',
		success: function(response) {
			hideLoader(id);
			jQuery('#rsc_publish'+id).html(response.message);
			jQuery('#rscomment'+id+' .rsc_buttons_container span').css('display','');
			jQuery('#rscomment'+id).animate({ opacity: 1 }, 500);
			initTooltips();
		}
	});
}

// Unpublish comment
function rsc_unpublish(id) {
	var root = typeof rsc_root != 'undefined' ? rsc_root : '';
	showLoader(id);
	
	jQuery.ajax({
		type: 'POST',
		url: root + 'index.php?option=com_rscomments',
		data: 'task=comments.unpublish&id=' + id + '&randomTime='+Math.random(),
		dataType: 'json',
		success: function(response) {
			hideLoader(id);
			jQuery('#rsc_publish'+id).html(response.message);
			jQuery('#rscomment'+id+' .rsc_buttons_container span').css('display','none');
			jQuery('#rscomment'+id).animate({ opacity: '0.6' }, 500);
			initTooltips();
		}
	});
}

// Subscribe function
function rsc_subscribe(id,option) {
	var root = typeof rsc_root != 'undefined' ? rsc_root : '';
	jQuery('#rscomments-top-loader').css('display','');
	
	jQuery.ajax({
		type: 'POST',
		url: root + 'index.php?option=com_rscomments',
		data: 'task=comments.subscribe&id=' + id + '&opt=' + option + '&randomTime='+Math.random(),
		dataType: 'json',
		success: function(response) {
			jQuery('#rscomments-top-loader').css('display','none');
			jQuery('#rsc_subscr').html(response.html);
			
			jQuery('#rscomments-top-alert').css('display','');
			jQuery('#rscomments-top-alert').html(response.message);
			
			setTimeout(function() {
				jQuery('#rscomments-top-alert').animate({
					opacity: 0
				}, 500, function() {
					jQuery(this).css('display','none');
					jQuery(this).css('opacity','1');
					jQuery(this).empty();
				});
			},3500);
			
			initTooltips();
		}
	});
}

// Unsubscribe function
function rsc_unsubscribe(id, option) {
	var root = typeof rsc_root != 'undefined' ? rsc_root : '';
	jQuery('#rscomments-top-loader').css('display','');
	
	jQuery.ajax({
		type: 'POST',
		url: root + 'index.php?option=com_rscomments',
		data: 'task=comments.unsubscribe&id=' + id + '&opt=' + option + '&randomTime='+Math.random(),
		dataType: 'json',
		success: function(response) {
			jQuery('#rscomments-top-loader').css('display','none');
			jQuery('#rsc_subscr').html(response.html);
			
			jQuery('#rscomments-top-alert').css('display','');
			jQuery('#rscomments-top-alert').html(response.message);
			
			setTimeout(function() {
				jQuery('#rscomments-top-alert').animate({
					opacity: 0
				}, 500, function() {
					jQuery(this).css('display','none');
					jQuery(this).css('opacity','1');
					jQuery(this).empty();
				});
			},3500);
			
			initTooltips();
		}
	});
}

// Open thread
function rsc_open(id,option,override) {
	jQuery('#rscomments-top-loader').css('display','');
	var root = typeof rsc_root != 'undefined' ? rsc_root : '';
	
	jQuery.ajax({
		type: 'POST',
		url: root + 'index.php?option=com_rscomments',
		data: 'task=comments.openthread&id=' + id + '&opt=' + option + '&override=' + override + '&randomTime='+Math.random(),
		dataType: 'json',
		success: function(response) {
			jQuery('#rscomments-top-loader').css('display','none');
			
			if (response.status) {
				jQuery('#rscomments-comment-form').html(response.form);
				jQuery('#rsc_thread').html(response.link);
				
				jQuery('div.rsc_buttons_container').css('display','');
				
				jQuery('#rscomments-top-alert').css('display','');
				jQuery('#rscomments-top-alert').html(response.message);
				
				rsc_reset_form();
				
				setTimeout(function() {
					jQuery('#rscomments-top-alert').animate({
						opacity: 0
					}, 500, function() {
						jQuery(this).css('display','none');
						jQuery(this).css('opacity','1');
						jQuery(this).empty();
					});
				},3500);
				
			} else {
				jQuery('#rsc_thread').html(response.link);
				jQuery('#rscomments-top-alert').css('display','');
				jQuery('#rscomments-top-alert').html(response.message);
				
				setTimeout(function() {
					jQuery('#rscomments-top-alert').animate({
						opacity: 0
					}, 500, function() {
						jQuery(this).css('display','none');
						jQuery(this).css('opacity','1');
						jQuery(this).empty();
					});
				},3500);
			}
			
			initTooltips();
		}
	});
}

// Close thread
function rsc_close(id,option,override) {
	jQuery('#rscomments-top-loader').css('display','');
	var root = typeof rsc_root != 'undefined' ? rsc_root : '';
	
	jQuery.ajax({
		type: 'POST',
		url: root + 'index.php?option=com_rscomments',
		data: 'task=comments.closethread&id=' + id + '&opt=' + option + '&override=' + override + '&randomTime='+Math.random(),
		dataType: 'json',
		success: function(response) {
			jQuery('#rscomments-top-loader').css('display','none');
			
			if (response.status) {
				jQuery('#rscomments-comment-form').html('');
				jQuery('#rsc_thread').html(response.link);
				
				jQuery('div.rsc_buttons_container').css('display','none');
				jQuery('#rscomments-top-alert').css('display','');
				jQuery('#rscomments-top-alert').html(response.message);
				
				setTimeout(function() {
					jQuery('#rscomments-top-alert').animate({
						opacity: 0
					}, 500, function() {
						jQuery(this).css('display','none');
						jQuery(this).css('opacity','1');
						jQuery(this).empty();
					});
				},3500);
			} else {
				jQuery('#rsc_thread').html(response.link);
				jQuery('#rscomments-top-alert').css('display','');
				jQuery('#rscomments-top-alert').html(response.message);
				
				setTimeout(function() {
					jQuery('#rscomments-top-alert').animate({
						opacity: 0
					}, 500, function() {
						jQuery(this).css('display','none');
						jQuery(this).css('opacity','1');
						jQuery(this).empty();
					});
				},3500);
			}
			
			initTooltips();
		}
	});
}

// Delete function 
function rsc_delete_fn(confirmText, id) {
	jQuery('#rscomment'+id).animate({ opacity: '0.5' }, 500);
	
	if (confirm(confirmText)) {
		rsc_delete(id);
	} else {
		jQuery('#rscomment'+id).animate({ opacity: '1' }, 500);
	}
}

// Delete comment
function rsc_delete(id) {
	var root = typeof rsc_root != 'undefined' ? rsc_root : '';
	showLoader(id);
	
	jQuery.ajax({
		type: 'POST',
		url: root + 'index.php?option=com_rscomments',
		data: 'task=comments.remove&id=' + id + '&randomTime='+Math.random(),
		dataType: 'json',
		success: function(response) {
			hideLoader(id);
			
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
			
			initTooltips();
		}
	});
}

function rsc_view(id) {
	if (jQuery('#c'+id).css('display') == 'none') {
		jQuery('#c'+id).css('display','block');
		jQuery('#c'+id).css('visibility','visible');
		jQuery('#comment-hidden-'+id).css('display','none');
	}
}

function initTooltips() {
	jQuery('.tooltip').hide(); 
	jQuery('.hasTooltip').tooltip('destroy');
	jQuery('.hasTooltip').tooltip({"html": true,"container": "body"});
}

function rsc_shuffle() {
	var hash = window.location.hash;
	if (hash.indexOf('#rscomment') != -1) {
		var cid = hash.replace('#','');
		if (!jQuery('#'+cid).length) {
			jQuery('#rscommentsPagination').click();
		} else {
			window.setTimeout(function() {
				jQuery(jQuery.browser.webkit ? "body": "html").animate({
					scrollTop: jQuery('#'+cid).offset().top
				});
			},1000);
		}
	}
}

// Pagination
function rsc_pagination(page, option, id, template, override) {
	var limitstart = 0;
	var total = parseInt(jQuery('#rsc_total').text());
	var root = typeof rsc_root != 'undefined' ? rsc_root : '';
	
	limitstart = jQuery('.rscomments-comments-list .rscomment').length;
	jQuery('#rsc_loading_pages').css('display','');
	
	jQuery.ajax({
		type: 'POST',
		url: root + 'index.php?option=com_rscomments',
		data: 'task=pagination&override=' + override + '&content=' + option + '&id=' + id + '&rsctemplate=' + template + '&limitstart=' + limitstart + '&pagination=0&randomTime='+Math.random(),
		dataType: 'html',
		success: function(response) {
			jQuery('#rsc_loading_pages').css('display','none');
			
			var htmlresponse = jQuery('<div>', { id : 'rsc_tmp_'+limitstart }).html(response);
			jQuery('.rscomments-comments-list').append(htmlresponse);
			initTooltips();
			
			jQuery(jQuery.browser.webkit ? "body": "html").animate({
				scrollTop: jQuery('#rsc_tmp_'+limitstart).offset().top
			});
			
			rsc_shuffle();
			
			if (jQuery('.rscomments-comments-list .rscomment').length >= total) {
				jQuery('#rsc_global_pagination').remove();
			}
		}
	});
}

// Refresh captcha
function rsc_refresh_captcha(root,url_captcha, prefix) {
	var img = typeof prefix != 'undefined' ? '#' + prefix + '_submit_captcha_image' : '#submit_captcha_image';
	var inp = typeof prefix != 'undefined' ? '#' + prefix + '_submit_captcha' : '#submit_captcha';
	
	sign = (url_captcha.indexOf('?') == -1) ? '?' : '&';
	jQuery(img).prop('src', root+url_captcha+sign+'sid=' + Math.random());
	jQuery(inp).val('');
}

function showLoader(id) {
	jQuery('#rscomment' + id + ' .rscomment-body').css('opacity','0.3');
	jQuery('#rscomment-comment-loader-' + id).css('display','');
}

function hideLoader(id) {
	jQuery('#rscomment' + id + ' .rscomment-body').css('opacity','1');
	jQuery('#rscomment-comment-loader-' + id).css('display','none');
}

function rsc_initTooltips(divid,isajaxcall) {
	initTooltips();
}

function rsc_get_ie() {
	var rv = -1;
	if (navigator.appName == 'Microsoft Internet Explorer') {
		var ua = navigator.userAgent;
		var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
		if (re.exec(ua) != null)
			rv = parseFloat( RegExp.$1 );
	}
	return rv;
}

function rsc_upload(root,url_captcha) {
	//set the id to the upload frame
	var rsframe 	= document.getElementById('rsc_frame');
	var innerDoc 	= rsframe.contentDocument || rsframe.contentDocument.document;

	// setting the functions variables to the frame hidden inputs
	innerDoc.getElementById('root').value 	= root;
	innerDoc.getElementById('url_captcha').value 	= url_captcha;

	innerDoc.getElementById('frameform').submit();
}

function rsc_comment_cnt(what,cntfield,maxlimit) {
	var field = jQuery('#'+what);	
	if (field.val().length > maxlimit) {
		field.val(field.val().substring(0, maxlimit));
	} else {
		jQuery('#'+cntfield).html(maxlimit - field.val().length);
	}
}

function rsc_show_emoticons(what) {
	jQuery('#rsc_emoticons').css('display','block');
}

function rsc_do_report() {
	jQuery('#rscomments-report iframe').contents().find('#commentid').val(jQuery('#rscomments-report').find('#reportid').val());
	jQuery('#rscomments-report iframe').contents().find('#rscomm_report').click();
}

function rsc_check(e) {
	var target = (e && e.target) || (event && event.srcElement);
	
	if (rsc_checkParent(target)) {
		jQuery('#rsc_emoticons').css('display','none');
	}
	
	if (jQuery(target).hasClass('rsc_emoti_on')) {
		jQuery('#rsc_emoticons').css('display','block');
	}
}

function rsc_checkParent(t) {
	while(t.parentNode) {
		if (t == document.getElementById('rsc_emoticons')) return false;
		t=t.parentNode;
	}
	return true
}

function rscomments_agree() {
	jQuery('#rsc_terms').prop('checked', true);
	jQuery('#rscomments-terms').modal('hide');
}

function hideMessage(id) {}
function rsc_initModal() {}

function rsc_createImage(obj,text) {
	var textarea = document.getElementById(obj);
	var url = prompt(text,'http://');
	var scrollTop = textarea.scrollTop;
	var scrollLeft = textarea.scrollLeft;

	if (url != '' && url != null) {
		if (document.selection) {
			textarea.focus();
			var sel = document.selection.createRange();
			sel.text = '[img]' + url + '[/img]';
		} else {
			var len = textarea.value.length;
			var start = textarea.selectionStart;
			var end = textarea.selectionEnd;
			
			var sel = textarea.value.substring(start, end);
			var rep = '[img]' + url + '[/img]';
			textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
			textarea.scrollTop = scrollTop;
			textarea.scrollLeft = scrollLeft;
		}
	}
}

function rsc_createUrl(obj,text) {
	var textarea = document.getElementById(obj);
	var url = prompt(text,'http://');
	var scrollTop = textarea.scrollTop;
	var scrollLeft = textarea.scrollLeft;

	if (url != '' && url != null) {
		if (document.selection) {
			textarea.focus();
			var sel = document.selection.createRange();
			
			if(sel.text=="")
				sel.text = '[url]'  + url + '[/url]';
			else 
				sel.text = '[url=' + url + ']' + sel.text + '[/url]';
		} else {
			var len = textarea.value.length;
			var start = textarea.selectionStart;
			var end = textarea.selectionEnd;
			
			var sel = textarea.value.substring(start, end);
			
			if(sel=="")
				var rep = '[url]' + url + '[/url]';
			else
				var rep = '[url=' + url + ']' + sel + '[/url]';
		
			textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);	
			textarea.scrollTop = scrollTop;
			textarea.scrollLeft = scrollLeft;
		}
	}
}

function rsc_addTags(tag1,tag2,obj) {
	var textarea = document.getElementById(obj);
	// Code for IE
	if (document.selection) {
		textarea.focus();
		var sel = document.selection.createRange();
		sel.text = tag1 + sel.text + tag2;
	} else {  
		// Code for Mozilla Firefox
		var len = textarea.value.length;
	    var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		var scrollTop = textarea.scrollTop;
		var scrollLeft = textarea.scrollLeft;

        var sel = textarea.value.substring(start, end);
		var rep = tag1 + sel + tag2;
        textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
		
		textarea.scrollTop = scrollTop;
		textarea.scrollLeft = scrollLeft;		
	}
}

function rsc_createList(tag1,tag2,obj) {
	var textarea = document.getElementById(obj);
	// Code for IE
	if (document.selection) {
		textarea.focus();
		var sel = document.selection.createRange();
		var list = sel.text.split('\n');
	
		for(i=0;i<list.length;i++)
			list[i] = '[*]' + list[i];

		sel.text = tag1 + '\n' + list.join("\n") + '\n' + tag2;
	} else {
		// Code for Firefox
		var len = textarea.value.length;
		var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		var i;
		
		var scrollTop = textarea.scrollTop;
		var scrollLeft = textarea.scrollLeft;		
		var sel = textarea.value.substring(start, end);
		
		var list = sel.split('\n');
		
		for(i=0;i<list.length;i++)
			list[i] = '[*]' + list[i];
		
		var rep = tag1 + '\n' + list.join("\n") + '\n' +tag2;
		textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
		
		textarea.scrollTop = scrollTop;
		textarea.scrollLeft = scrollLeft;
	}
}

function rsc_smiley(tag) {
	var textarea = document.getElementById('rsc_comment');
	var scrollTop = textarea.scrollTop;
	var scrollLeft = textarea.scrollLeft;

	if (document.selection) {
		textarea.focus();
		var sel = document.selection.createRange();
		sel.text = tag;
	} else {
		var len = textarea.value.length;
		var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		
		var sel = textarea.value.substring(start, end);
		var rep = tag;
		textarea.value =  textarea.value.substring(0,start) + rep + textarea.value.substring(end,len);
		textarea.scrollTop = scrollTop;
		textarea.scrollLeft = scrollLeft;
		textarea.focus();
	}
	
	var popup = document.getElementById('rsc_emoticons');
	popup.style.display = 'none';
}

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