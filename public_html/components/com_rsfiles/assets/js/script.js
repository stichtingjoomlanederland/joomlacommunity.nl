function validate_report() {
	if (jQuery('#report').val().trim() == '') {
		jQuery('#report').addClass('invalid');
		return false;
	}
	
	jQuery('#report').removeClass('invalid');
	return true;
}

function rsf_bookmark(root, path, briefcase, iid) {
	var Itemid = '&Itemid='+iid;
	
	jQuery.ajax({
		type: 'POST',
		url: root + 'index.php?option=com_rsfiles',
		data: 'task=rsfiles.bookmark&path=' + encodeURIComponent(path) + '&tmpl=component'+ Itemid + (briefcase == 1 ? '&from=briefcase' : ''),
		dataType: 'json',
		success: function(response) {
			window.parent.jQuery('#rsf_alert').css('display','');
			window.parent.jQuery('#rsf_message').html(response.data.message);
			
			if (response.success) {
				window.parent.jQuery('#rsf_alert').removeClass('alert-error');
			} else {
				window.parent.jQuery('#rsf_alert').addClass('alert-error');
			}
		}
	});
}

function rsf_download_bookmarks(all) {
	if (all) {
		if (jQuery('input[name="cid[]"]').length > 1) {
			jQuery('#rscheckbox').click();
		}
		
		return true;
	} else {
		if (jQuery('input[name="boxchecked"]').val() == 0) {
			alert(Joomla.JText._('COM_RSFILES_PLEASE_SELECT_FILES'));
			return false;
		}
		
		return true;
	}
}

function validate_new_folder() {
	if (jQuery('#folder').val().trim() == '' || jQuery('#folder').val().trim().length < 2) {
		jQuery('#folder').addClass('invalid');
		return false;
	}
	
	jQuery('#folder').removeClass('invalid');
	return true;
}

function rsf_add_external() {
	var newid = Math.round(Math.random() * 100000);
	
	var div1  = jQuery('<div>', { id : 'e'+newid }).addClass('control-group');
	var div2  = jQuery('<div>').addClass('controls');
	var div3  = jQuery('<div>').addClass('input-append');
	var input = jQuery('<input>', {	type : 'text', name : 'external[]'}).addClass('input-xxlarge rsf_reset_margin');
	var a 	  = jQuery('<a>').addClass('btn btn-info').on('click', function() { rsf_remove_external(newid); });
	var i	  = jQuery('<i>').addClass('rsicon-minus');
	
	a.append(i);
	div3.append(input);
	div3.append(a);
	div2.append(div3);
	div1.append(div2);
	jQuery('#external').append(div1);
}

function rsf_remove_external(id) {
	if (id != 1) {
		jQuery('#e'+id).remove(); 
	}
}

function rsf_create_briefcase(root, id) {
	jQuery.ajax({
		type: 'POST',
		url: root + 'index.php?option=com_rsfiles',
		data: 'task=rsfiles.newbriefcase&id=' + id + '&tmpl=component',
		dataType: 'json',
		success: function(response) {
			window.parent.jQuery('#rsf_alert').css('display','');
			window.parent.jQuery('#rsf_message').html(response.data.message);
			window.parent.SqueezeBox.close();
			
			if (response.success) {
				window.parent.jQuery('#rsf_alert').removeClass('alert-error');
				window.parent.setTimeout(function() {
					window.parent.location.reload();
				},1500);
			} else {
				window.parent.jQuery('#rsf_alert').addClass('alert-error');
			}
		}
	});
}

function rsf_validate() {
	var $return = true;
	if (jQuery('#submit_captcha').length) {
		if (jQuery('#submit_captcha').val().trim() == '') {
			jQuery('#submit_captcha').addClass('invalid');
			$return = false;
		} else {
			jQuery('#submit_captcha').removeClass('invalid'); 
		}
	} else if (jQuery('#recaptcha_response_field').length) {
		if (jQuery('#recaptcha_response_field').val().trim() == '') {
			jQuery('#recaptcha_response_field').addClass('invalid');
			$return = false;
		} else { 
			jQuery('#recaptcha_response_field').removeClass('invalid'); 
		}
	} else if (jQuery('#g-recaptcha-response').length) {
		if (jQuery('#g-recaptcha-response').val().trim() == '') {
			jQuery('#g-recaptcha-response').addClass('invalid');
			$return = false;
		} else { 
			jQuery('#g-recaptcha-response').removeClass('invalid'); 
		}
	}
	
	if (!$return) {
		jQuery('#rsf_alert').addClass('alert-error');
		jQuery('#rsf_alert').css('display','');
		jQuery('#rsf_message').html(Joomla.JText._('COM_RSFILES_NO_CAPTCHA'));
	} else {
		jQuery('#rsf_alert').removeClass('alert-error');
		jQuery('#rsf_alert').css('display','none');
	}
	
	return $return;
}

function rsf_validate_email() {
	var ret = true;
	var msg = new Array();
	
	if (jQuery('#jform_name').val().trim() == '') {
		jQuery('#jform_name').addClass('invalid');
		ret = false;
	} else {
		jQuery('#jform_name').removeClass('invalid');
	}
	
	if (jQuery('#jform_email').val().trim() == '') {
		jQuery('#jform_email').addClass('invalid');
		ret = false;
	} else {
		jQuery('#jform_email').removeClass('invalid');
	}
	
	if (jQuery('#agreement').length) {
		if (!jQuery('#agreement').prop('checked')) {
			msg.push(Joomla.JText._('COM_RSFILES_CHECK_AGREEMENT'));
			ret = false;
		}
	}
	
	if (jQuery('#submit_captcha').length) {
		if (jQuery('#submit_captcha').val().trim() == '') {
			jQuery('#submit_captcha').addClass('invalid');
			msg.push(Joomla.JText._('COM_RSFILES_NO_CAPTCHA'));
			ret = false;
		} else {
			jQuery('#submit_captcha').removeClass('invalid'); 
		}
	} else if (jQuery('#recaptcha_response_field').length) {
		if (jQuery('#recaptcha_response_field').val().trim() == '') {
			jQuery('#recaptcha_response_field').addClass('invalid');
			msg.push(Joomla.JText._('COM_RSFILES_NO_CAPTCHA'));
			ret = false;
		} else { 
			jQuery('#recaptcha_response_field').removeClass('invalid'); 
		}
	} else if (jQuery('#g-recaptcha-response').length) {
		if (jQuery('#g-recaptcha-response').val().trim() == '') {
			jQuery('#g-recaptcha-response').addClass('invalid');
			msg.push(Joomla.JText._('COM_RSFILES_NO_CAPTCHA'));
			ret = false;
		} else { 
			jQuery('#g-recaptcha-response').removeClass('invalid'); 
		}
	}
	
	if (!ret) {
		if (msg.length) {
			jQuery('#rsf_alert').addClass('alert-error');
			jQuery('#rsf_alert').css('display','');
			jQuery('#rsf_message').html(msg.join('<br />'));
		} else {
			jQuery('#rsf_alert').removeClass('alert-error');
			jQuery('#rsf_alert').css('display','none');
		}
	} else {
		jQuery('#rsf_alert').removeClass('alert-error');
		jQuery('#rsf_alert').css('display','none');
	}
	
	return ret;
}

function rsf_upload_external() {
	var external = [];
	
	jQuery('#externalfiles').css('display','none');
	jQuery('input[name="external[]"]').each(function() {
		if (jQuery.trim(jQuery(this).val()) != '') {
			external.push('external[]=' + jQuery(this).val());
		}
	});
	
	if (external.length) {
		jQuery('#rsf_loading').css('display', '');
		
		jQuery.ajax({
			type: 'POST',
			url: jQuery('#siteroot').text() + 'index.php?option=com_rsfiles',
			data: 'task=rsfiles.uploadexternal&folder=' + jQuery('input[name="folder"]').val() + '&' + external.join('&') +'&Itemid=' + jQuery('#itemid').text(),
			dataType: 'json',
			success: function(response) {
				jQuery('#rsf_loading').css('display', 'none');
				jQuery('#externalfiles').css('display','');
				jQuery('#externalfiles').html(response.data.message);
				response.success ? jQuery('#externalfiles').addClass('alert-success').removeClass('alert-error') : jQuery('#externalfiles').addClass('alert-error').removeClass('alert-success');
				
				if (response.success) {
					jQuery('input[name="external[]"]').val('');
					jQuery('#external a').click();
				}
			}
		});
	}
}

function rsf_single_upload() {
	window.location = jQuery('#singleupload').text();
}