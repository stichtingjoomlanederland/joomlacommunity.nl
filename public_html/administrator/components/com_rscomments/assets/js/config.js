jQuery(document).ready(function() {
	if (jQuery('#jform_template').parent().parent().hasClass('control-group')) {
		jQuery('#jform_template').parent().parent().css('display','none');
	}
	
	// Upload fields
	if (jQuery('#jform_enable_upload1').is(':checked')) {
		if (jQuery('#jform_max_size').parent().parent().hasClass('control-group')) {
			jQuery('#jform_max_size').parent().parent().css('display','');
		} else {
			jQuery('#jform_max_size').parent().css('display','');
		}
		
		if (jQuery('#jform_allowed_extensions').parent().parent().hasClass('control-group')) {
			jQuery('#jform_allowed_extensions').parent().parent().css('display','');
		} else {
			jQuery('#jform_allowed_extensions').parent().style.display = '';
		}
	} else if (jQuery('#jform_enable_upload0').is(':checked')) {
		if (jQuery('#jform_max_size').parent().parent().hasClass('control-group')) {
			jQuery('#jform_max_size').parent().parent().css('display','none');
		} else {
			jQuery('#jform_max_size').parent().css('display','none');
		}
		
		if (jQuery('#jform_allowed_extensions').parent().parent().hasClass('control-group')) {
			jQuery('#jform_allowed_extensions').parent().parent().css('display','none');
		} else {
			jQuery('#jform_allowed_extensions').parent().css('display','none');
		}
	}
	
	jQuery('#jform_enable_upload1').on('click', function () {
		if (jQuery('#jform_max_size').parent().parent().hasClass('control-group')) {
			jQuery('#jform_max_size').parent().parent().css('display','');
		} else {
			jQuery('#jform_max_size').parent().css('display','');
		}
		
		if (jQuery('#jform_allowed_extensions').parent().parent().hasClass('control-group')) {
			jQuery('#jform_allowed_extensions').parent().parent().css('display','');
		} else {
			jQuery('#jform_allowed_extensions').parent().css('display','');
		}
	});
	
	jQuery('#jform_enable_upload0').on('click', function () {
		if (jQuery('#jform_max_size').parent().parent().hasClass('control-group')) {
			jQuery('#jform_max_size').parent().parent().css('display','none');
		} else {
			jQuery('#jform_max_size').parent().css('display','none');
		}
		
		if (jQuery('#jform_allowed_extensions').parent().parent().hasClass('control-group')) {
			jQuery('#jform_allowed_extensions').parent().parent().css('display','none');
		} else {
			jQuery('#jform_allowed_extensions').parent().css('display','none');
		}
	});
	
	// Accordion fields
	if (jQuery('#jform_form_accordion1').is(':checked')) {
		if (jQuery('#jform_show_form0').parent().parent().parent().hasClass('control-group')) {
			jQuery('#jform_show_form0').parent().parent().parent().css('display','');
		} else {
			jQuery('#jform_show_form0').parent().parent().css('display','');
		}
	} else if (jQuery('#jform_form_accordion0').is(':checked')) {
		if (jQuery('#jform_show_form0').parent().parent().parent().hasClass('control-group')) {
			jQuery('#jform_show_form0').parent().parent().parent().css('display','none');
		} else {
			jQuery('#jform_show_form0').parent().parent().css('display','none');
		}
	}
	
	jQuery('#jform_form_accordion1').on('click', function () {
		if (jQuery('#jform_show_form0').parent().parent().parent().hasClass('control-group')) {
			jQuery('#jform_show_form0').parent().parent().parent().css('display','');
		} else {
			jQuery('#jform_show_form0').parent().parent().css('display','');
		}
	});
	
	jQuery('#jform_form_accordion0').on('click', function () {
		if (jQuery('#jform_show_form0').parent().parent().parent().hasClass('control-group')) {
			jQuery('#jform_show_form0').parent().parent().parent().css('display','none');
		} else {
			jQuery('#jform_show_form0').parent().parent().css('display','none');
		}
	});
	
	// Email notification fields
	if (jQuery('#jform_email_notification1').is(':checked')) {
		if (jQuery('#jform_notification_emails').parent().parent().hasClass('control-group')) {
			jQuery('#jform_notification_emails').parent().parent().css('display','');
		} else {
			jQuery('#jform_notification_emails').parent().css('display','');
		}
	} else if (jQuery('#jform_email_notification0').is(':checked')) {
		if (jQuery('#jform_notification_emails').parent().parent().hasClass('control-group')) {
			jQuery('#jform_notification_emails').parent().parent().css('display','none');
		} else {
			jQuery('#jform_notification_emails').parent().css('display','none');
		}
	}
	
	jQuery('#jform_email_notification1').on('click', function () {
		if (jQuery('#jform_notification_emails').parent().parent().hasClass('control-group')) {
			jQuery('#jform_notification_emails').parent().parent().css('display','');
		} else {
			jQuery('#jform_notification_emails').parent().css('display','');
		}
	});
	
	jQuery('#jform_email_notification0').on('click', function () {
		if (jQuery('#jform_notification_emails').parent().parent().hasClass('control-group')) {
			jQuery('#jform_notification_emails').parent().parent().css('display','none');
		} else {
			jQuery('#jform_notification_emails').parent().css('display','none');
		}
	});
	
	// Report emails
	if (jQuery('#jform_enable_email_reports1').is(':checked')) {
		if (jQuery('#jform_report_emails').parent().parent().hasClass('control-group')) {
			jQuery('#jform_report_emails').parent().parent().css('display','');
		} else {
			jQuery('#jform_report_emails').parent().css('display','');
		}
	} else if (jQuery('#jform_enable_email_reports0').is(':checked')) {
		if (jQuery('#jform_report_emails').parent().parent().hasClass('control-group')) {
			jQuery('#jform_report_emails').parent().parent().css('display','none');
		} else {
			jQuery('#jform_report_emails').parent().css('display','none');
		}
	}
	
	jQuery('#jform_enable_email_reports1').on('click', function () {
		if (jQuery('#jform_report_emails').parent().parent().hasClass('control-group')) {
			jQuery('#jform_report_emails').parent().parent().css('display','');
		} else {
			jQuery('#jform_report_emails').parent().css('display','');
		}
	});
	
	jQuery('#jform_enable_email_reports0').on('click', function () {
		if (jQuery('#jform_report_emails').parent().parent().hasClass('control-group')) {
			jQuery('#jform_report_emails').parent().parent().css('display','none');
		} else {
			jQuery('#jform_report_emails').parent().css('display','none');
		}
	});
});