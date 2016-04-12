RSFirewall.$(document).ready(function($) {
	$.ajax({
		converters: {
			"text json": RSFirewall.parseJSON
		},
		dataType: 'json',
		type: 'POST',
		url: 'index.php',
		data: {
			'option': 'com_rsfirewall',
			'task': 'getLatestJoomlaVersion'
		},
		error: function(jqXHR, textStatus, errorThrown) {
			RSFirewall.$('#mod-rsfirewall-joomla-version').html(textStatus).addClass('com-rsfirewall-error');
		},
		success: function(json) {
			if (json.success == true) {
				if (json.data.is_latest == true) {
					var message = Joomla.JText._('MOD_RSFIREWALL_YOU_ARE_RUNNING_LATEST_VERSION').replace('%s', json.data.current);
					RSFirewall.$('#mod-rsfirewall-joomla-version').html(message).addClass('com-rsfirewall-ok');
				} else {
					var message = Joomla.JText._('MOD_RSFIREWALL_UPDATE_IS_AVAILABLE_JOOMLA').replace('%s', json.data.latest);
					RSFirewall.$('#mod-rsfirewall-joomla-version').html(message).addClass('com-rsfirewall-notice');
				}
			} else {
				var message = json.data.message;
				RSFirewall.$('#mod-rsfirewall-joomla-version').html(message).addClass('com-rsfirewall-error');
			}
		}
	});
	
	$.ajax({
		converters: {
			"text json": RSFirewall.parseJSON
		},
		dataType: 'json',
		type: 'POST',
		url: 'index.php',
		data: {
			'option': 'com_rsfirewall',
			'task': 'getLatestFirewallVersion'
		},
		error: function(jqXHR, textStatus, errorThrown) {
			RSFirewall.$('#mod-rsfirewall-firewall-version').html(textStatus).addClass('com-rsfirewall-error');
		},
		success: function(json) {
			if (json.success == true) {
				if (json.data.is_latest == true) {
					var message = Joomla.JText._('MOD_RSFIREWALL_YOU_ARE_RUNNING_LATEST_VERSION').replace('%s', json.data.current);
					RSFirewall.$('#mod-rsfirewall-firewall-version').html(message).addClass('com-rsfirewall-ok');
				} else {
					var message = Joomla.JText._('MOD_RSFIREWALL_UPDATE_IS_AVAILABLE_RSFIREWALL').replace('%s', json.data.latest);
					RSFirewall.$('#mod-rsfirewall-firewall-version').html(message).addClass('com-rsfirewall-notice');
				}
			} else {
				var message = json.data.message;
				RSFirewall.$('#mod-rsfirewall-firewall-version').html(message).addClass('com-rsfirewall-error');
			}
		}
	});
});