RSFormPro.Backup = {
	forms: [],
	
	submissionsCount: {},
	
	// Request limiting variables
	formsPerRequest: 5,
	submissionsPerRequest: 100,
	
	// Progress variables
	progressUnit: 0,
	progressCount: 0,
	
	// Request key
	key: '',
	
	requestTimeOut: {
		Seconds : 0,
		Milliseconds: function(){
			return RSFormPro.Backup.requestTimeOut.Seconds * 1000;
		}
	},
	
	parseJSON: function(data) {
		if (typeof data != 'object') {
			// parse invalid data:
			var match = data.match(/{.*}/);
			if (match) {
				return jQuery.parseJSON(match[0]);
			} else {
				var serverError = '';
				var matchedError = data.match(/error.*/);
				if (matchedError) {
					serverError = matchedError[0].replace(/(<([^>]+)>)/ig, '').substring(0, 255);
				}
				RSFormPro.Backup.showError(Joomla.JText._('RSFP_JSON_DECODING_ERROR').replace('%s', serverError));
				return false;
			}
		}
		
		return jQuery.parseJSON(data);
	},
	
	// Shows an error message if something goes wrong and stops the backup process.
	showError: function(message) {
		RSFormPro.Backup.clearError();
		jQuery('.progressBar').css('background', '#b94a48');
		return jQuery('.progressWrapper').after('<div class="alert alert-error"><strong>' + Joomla.JText._('RSFP_ERROR') + '</strong> ' + message + '</div>');
	},
	
	clearError: function() {
		jQuery('.alert').remove();
	},
	
	// Shows the current status (text) of the backup progress
	showStatus: function(message) {
		jQuery('.alert').remove();
		return jQuery('.progressWrapper').after('<div class="alert alert-info"><strong>' + Joomla.JText._('RSFP_STATUS') + '</strong> ' + message + '</div>');
	},
	
	// Helper to parse the JSON response based on several conditions
	parseResponse: function(data, textStatus, jqXHR){
		if (data.status == 'error') {
			RSFormPro.Backup.showError(data.message);
		} else if (data.status == 'ok') {
			RSFormPro.Backup.progress();
			
			// If key has been supplied, store it.
			if (data.key) {
				RSFormPro.Backup.key = data.key;
			}
			
			switch (data.step)
			{
				// Continue requesting form structure to be saved.
				case 'forms':
					RSFormPro.Backup.sendRequest('storeForms', {
						'forms': RSFormPro.Backup.forms.splice(0, RSFormPro.Backup.formsPerRequest), // Keep splicing the array until there's nothing left
					});
					
					RSFormPro.Backup.showStatus(Joomla.JText._('RSFP_STATUS_BACKING_UP_FORM_STRUCTURE_LEFT').replace('%d', RSFormPro.Backup.forms.length));
				break;
				
				// Continue requesting submissions to be saved.
				case 'prepare-submissions':
				case 'next-form-submissions':
				case 'submissions':
					if (jQuery('#jform_submissions1').prop('checked') ? 1 : 0) {
						// This is done as a first step so that we can populate the forms array once.
						// Submissions are done per form at this point.
						if (data.step == 'prepare-submissions') {
							RSFormPro.Backup.grabSelectedForms();
						}
						
						// Grab the next (or first) form
						var form;
						if (data.step == 'next-form-submissions' || data.step == 'prepare-submissions') {
							var nextForm = RSFormPro.Backup.forms.splice(0, 1);
							if (nextForm.length > 0) {
								form = nextForm[0];
							}
						} else {
							// Still working on current form
							form = data.form;
						}
						
						if (form) {
							RSFormPro.Backup.sendRequest('storeSubmissions', {
								'limit': 		RSFormPro.Backup.submissionsPerRequest,
								'start': 		data.start ? data.start : 0,
								'header':		data.header ? data.header : 0,
								'form':  		form
							});
							
							var submissionsLeft = RSFormPro.Backup.submissionsCount[form] - parseFloat(data.start ? data.start : 0);
							if (submissionsLeft < 0) {
								submissionsLeft = 0;
							}
							
							if (submissionsLeft) {
								RSFormPro.Backup.showStatus(Joomla.JText._('RSFP_STATUS_BACKING_UP_FORM_SUBMISSIONS_LEFT').replace('%d', form).replace('%d', submissionsLeft));
							} else {
								RSFormPro.Backup.showStatus(Joomla.JText._('RSFP_STATUS_FINISHING_UP_SUBMISSIONS_FOR_FORM').replace('%d', form));
							}
						} else {
							RSFormPro.Backup.sendRequest('prepareGzip');
						}
					} else {
						RSFormPro.Backup.sendRequest('prepareGzip');
					}
				break;
				
				// Continue adding files to the GZIP archive.
				case 'prepare-gzip':
				case 'compress-gzip':
					RSFormPro.Backup.showStatus(Joomla.JText._('RSFP_STATUS_COMPRESSING_FILES'));
					
					if (data.step == 'prepare-gzip') {
						// Let's see how many steps are needed
						RSFormPro.Backup.progressUnit = 100 / parseFloat(data.chunks);
						
						// Reset to 0%
						RSFormPro.Backup.progress(true);
					}
					
					RSFormPro.Backup.sendRequest('compressGzip', {
						'seek': data.seek ? data.seek : 0,
					});
				break;
				
				case 'done':
					RSFormPro.Backup.toggle();
					Joomla.submitbutton('backup.download');
				break;
			}
		}
	},
	
	// Helper - sends a task request to index.php?option=com_rsform&controller=backup and passes the response to the parseResponse() function.
	sendRequest: function(task, data) {
		if (!data) {
			data = {};
		}
		
		data['option'] 		= 'com_rsform';
		data['controller'] 	= 'backup';
		data['task']		= task;
		
		if (RSFormPro.Backup.key.length > 0) {
			data['key'] = RSFormPro.Backup.key;
		}
		
		if (RSFormPro.Backup.requestTimeOut.Seconds != 0 && task !='start') {
			setTimeout(function(){RSFormPro.Backup.ajaxRequest(data)}, RSFormPro.Backup.requestTimeOut.Milliseconds());
		} else {
			RSFormPro.Backup.ajaxRequest(data);
		}
	},
	
	ajaxRequest : function(data) {
		jQuery.ajax({
			converters: {
				"text json": RSFormPro.Backup.parseJSON
			},
			type: "POST",
			url: 'index.php?option=com_rsform',
			data: data,
			success: RSFormPro.Backup.parseResponse,
			dataType: 'json'
		});
	},
	
	// Helper function to adjust the progress by a unit.
	progress: function(reset) {		
		var bar 	= jQuery('.progressBar');
		var current = RSFormPro.Backup.progressCount += RSFormPro.Backup.progressUnit;
		
		if (reset) {
			current = 0;
			RSFormPro.Backup.progressCount = 0;
		}
		
		if (current > 100) {
			current = 100;
		}
		
		bar.animate({'width': current + '%'}, {
			'duration': 'fast',
			'step': function(now, tween) {
				bar.html(Math.round(now) + '%');
			}
		});
	},
	
	// Helper function that populates the this.forms array
	grabSelectedForms: function() {
		RSFormPro.Backup.forms = [];
		jQuery(document.getElementsByName('cid[]')).each(function(index, el){
			if (jQuery(el).prop('checked')) {
				RSFormPro.Backup.forms.push(jQuery(el).val());
			}
		});
	},
	
	// Starts the backup process
	start: function() {
		RSFormPro.Backup.toggle();
		
		RSFormPro.Backup.grabSelectedForms();
		
		// Time to calculate the number of steps so that we can show a nice progress bar.
		var steps = 0;
		
		// +1 from the start() request.
		steps++;
		
		// + number of forms
		steps += Math.ceil(RSFormPro.Backup.forms.length / RSFormPro.Backup.formsPerRequest);
		
		// +1 from the empty forms AJAX request (when the forms array is empty)
		steps++;
		
		if (jQuery('#jform_submissions1').prop('checked')) {
			for (var i = 0; i < RSFormPro.Backup.forms.length; i++) {
				steps += Math.ceil(RSFormPro.Backup.submissionsCount[RSFormPro.Backup.forms[i]] / RSFormPro.Backup.submissionsPerRequest);
				
				// +1 from the "next-form-submissions" step (when there are no more submissions to load from the db)
				steps++;
			}
		}
		
		RSFormPro.Backup.progressUnit = 100 / steps;
		
		RSFormPro.Backup.sendRequest('start', {
			'forms':		RSFormPro.Backup.forms,
			'submissions':  jQuery('#jform_submissions1').prop('checked') ? 1 : 0,
		});
	},
	
	// Toggles the display and availability of controls
	toggle: function() {
		var progress 	= jQuery('.progressWrapper');
		var button		= jQuery('#backupButton');
		var forms		= jQuery('#formsList');
		
		// If the progress wrapper is shown, then we have to hide it and re-enable elements
		var shown = progress.is(':visible');
		
		shown ? progress.fadeOut()  : progress.fadeIn();
		shown ? button.show()		: button.hide();
		shown ? forms.show()		: forms.hide();
		
		// If we're re-enabling elements we must clear the error as well.
		if (shown) {
			RSFormPro.Backup.clearError();
			
			RSFormPro.Backup.forms 			= [];
			RSFormPro.Backup.progressUnit 	= 0;
			RSFormPro.Backup.progressCount 	= 0;
			
			RSFormPro.Backup.progress(true);
		}
	}
}