RSFormPro.Restore = {
	// Progress variables
	progressUnit: 0,
	progressCount: 0,
	
	currentForm : 0,
	
	formsFound: 0,
	overwrite: 0,
	
	currentFormId: 0,
	keepId: 0,
	
	requestTimeOut: {
		Seconds : 0,
		Milliseconds: function(){
			return RSFormPro.Restore.requestTimeOut.Seconds * 1000;
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
	
	// Shows an error message if something goes wrong and stops the restore process.
	showError: function(message) {
		RSFormPro.Restore.clearError();
		jQuery('.progressBar').css('background', '#b94a48');
		return jQuery('.progressWrapper').after('<div class="alert alert-error"><strong>' + Joomla.JText._('RSFP_ERROR') + '</strong> ' + message + '</div>');
	},
	
	addToError: function(message) {
		jQuery('.alert-error').append('<br/>' + message);
	},
	
	clearError: function() {
		jQuery('.alert').remove();
	},
	
	// Shows the current status (text) of the restore progress
	showStatus: function(message) {
		jQuery('.alert-info').remove();
		return jQuery('.progressWrapper').after('<div class="alert alert-info"><strong>' + Joomla.JText._('RSFP_STATUS') + '</strong> ' + message + '</div>');
	},
	
	// Show the list of forms to be restored
	showList: function(metadata) {
		// build the metadata info
		var metaInfo = '<div class="alert alert-warning">';
		metaInfo += '<h3>' + Joomla.JText._('RSFP_BACKUP_INFORMATION') + '</h3>';
		metaInfo += '<dl class="dl-horizontal">';
		metaInfo += '<dt>RSForm! Pro:</dt><dd>' + metadata.metaInfo.version + '</dd>';
		metaInfo += '<dt>Joomla!:</dt><dd>' + metadata.metaInfo.cms + '</dd>';
		metaInfo += '<dt>PHP:</dt><dd>' + metadata.metaInfo.php + '</dd>';
		metaInfo += '<dt>' + Joomla.JText._('RSFP_BACKUP_OS') + ':</dt><dd>' + metadata.metaInfo.os + '</dd>';
		metaInfo += '<dt>' + Joomla.JText._('RSFP_BACKUP_WEBSITE') + ':</dt><dd>' + metadata.metaInfo.url + '</dd>';
		metaInfo += '<dt>' + Joomla.JText._('RSFP_BACKUP_AUTHOR') + ':</dt><dd>' + metadata.metaInfo.author + '</dd>';
		metaInfo += '<dt>' + Joomla.JText._('RSFP_BACKUP_DATE') + ':</dt><dd>' + metadata.metaInfo.date + '</dd>';
		metaInfo += '</dl>';
		metaInfo += '</div>';
		
		// build the Forms List
		var forms = metadata.info;
		var table = jQuery("<table>",{"class":"restoreForms table table-striped"});
		// add the table headers
		table.append('<th style="width:2%;">#</th><th>Form</th><th class="center" nowrap="nowrap" width="1%">Structure</th><th class="center" nowrap="nowrap" width="1%">Submissions</th>');
		for(var i = 0; i < forms.length; i++) {
			var tr 				= jQuery("<tr>");
			var title 			= jQuery("<td>");
			var statusForm 		= jQuery("<td>", {"class":"center", "id":'form-'+forms[i].id});
			var submissionsForm = jQuery("<td>", {"class":"center", "id":'submissions-'+ forms[i].id});
			
			// add the title of the form
			title.append(forms[i].title + ' (' + forms[i].submissions + ')');
			
			// fill with blank space the columns for now
			statusForm.append();
			
			// add the columns to the row
			tr.append('<td>'+(i+1)+'</td>');
			tr.append(title);
			tr.append(statusForm);
			tr.append(submissionsForm);
			
			// add the table row
			table.append(tr);
		}
		if (jQuery('.alert').length > 0) {
			jQuery('.alert-info').after(metaInfo);
			jQuery('.alert-warning').after(table);
		}
		else {
			jQuery('.progressWrapper').after(metaInfo);
			jQuery('.alert-warning').after(table);
		}
	},
	
	getFormSubmissions : function(FormId) {
		for (i=0; i < RSFormPro.Restore.formsFound.length; i++) {
			if (FormId == RSFormPro.Restore.formsFound[i].id) {
				return RSFormPro.Restore.formsFound[i].submissions;
			}
		}
	},
	
	// Helper to parse the JSON response based on several conditions
	parseResponse: function(data, textStatus, jqXHR){
		if (data.status == 'error') {
			if (data.message == 'tmp-removed') {
				RSFormPro.Restore.addToError(Joomla.JText._('RSFP_TMP_FOLDER_REMOVED'));
			} else {
				RSFormPro.Restore.showError(data.message);
				RSFormPro.Restore.sendRequest('deleteTemporaryFiles',{'onerror':1});
			}
		} else if (data.status == 'ok') {
			switch (data.step)
			{
				case 'next-xml-headers':
					RSFormPro.Restore.sendRequest('getInfo');
					RSFormPro.Restore.showStatus(Joomla.JText._('RSFP_READING_METADATA_INFORMATION'));
				break;
				
				case 'list-info': 
					RSFormPro.Restore.formsFound = data.metadata.info;
					// set the progress bar unit
					RSFormPro.Restore.progressUnit = 100 / RSFormPro.Restore.formsFound.length;
					
					// parse the first form
					if (RSFormPro.Restore.overwrite) {
						RSFormPro.Restore.sendRequest('overwriteForms');
						RSFormPro.Restore.showStatus(Joomla.JText._('RSFP_REMOVING_OLD_FORMS'));
					}
					else {
						RSFormPro.Restore.sendRequest('parseForm', {'form':data.metadata.info[0].id, 'submissions':data.metadata.info[0].submissions, 'keepid':RSFormPro.Restore.keepId});
						RSFormPro.Restore.showStatus(Joomla.JText._('RSFP_RESTORING_FORM_STRUCTURE').replace('%s', data.metadata.info[0].title));
						
						// Add loader
						jQuery('#form-'+data.metadata.info[0].id).append('<span class="loading-small"></span>');
						// increment the form counter
						RSFormPro.Restore.currentForm++;
					}
					
					RSFormPro.Restore.showList(data.metadata);
				break;
				
				case 'parse-form':
					if (RSFormPro.Restore.overwrite && RSFormPro.Restore.currentForm == 0) {
						RSFormPro.Restore.sendRequest('parseForm', {'form':RSFormPro.Restore.formsFound[0].id, 'submissions':RSFormPro.Restore.formsFound[0].submissions, 'keepid':RSFormPro.Restore.keepId});
						RSFormPro.Restore.showStatus(Joomla.JText._('RSFP_RESTORING_FORM_STRUCTURE').replace('%s', RSFormPro.Restore.formsFound[0].title));
						
						// Add loader
						jQuery('#form-'+RSFormPro.Restore.formsFound[0].id).append('<span class="loading-small"></span>');
						// increment the form counter
						RSFormPro.Restore.currentForm++;
					}
					else {
						if (RSFormPro.Restore.currentForm == RSFormPro.Restore.formsFound.length) {
							RSFormPro.Restore.sendRequest('deleteTemporaryFiles');
							RSFormPro.Restore.showStatus(Joomla.JText._('RSFP_DELETING_TEMPORARY_FOLDER'));
						}
						else {
							RSFormPro.Restore.sendRequest('parseForm', {'form':RSFormPro.Restore.formsFound[RSFormPro.Restore.currentForm].id, 'submissions':RSFormPro.Restore.formsFound[RSFormPro.Restore.currentForm].submissions, 'keepid':RSFormPro.Restore.keepId});
							RSFormPro.Restore.showStatus(Joomla.JText._('RSFP_RESTORING_FORM_STRUCTURE').replace('%s', RSFormPro.Restore.formsFound[RSFormPro.Restore.currentForm].title));
							
							// Add loader
							jQuery('#form-'+RSFormPro.Restore.formsFound[RSFormPro.Restore.currentForm].id).append('<span class="loading-small"></span>');
							// increment the form counter
							RSFormPro.Restore.currentForm++;
						}
					}
					
					// set the checkmark for the structure of the form if there are no submissions
					if (data.form) {
						if (RSFormPro.Restore.getFormSubmissions(data.form) == 0) {
							jQuery('#form-'+ data.form +' .loading-small').remove();
							jQuery('#form-'+ data.form).append('<span class="icon-checkmark-circle rsform_ok"></span>');
							jQuery('#submissions-'+ data.form).append('--');
							// move progress bar
							RSFormPro.Restore.progress();
						}
					}
					
					// set the checkmark for the form submissions if they are done
					if (data.finished) {
						jQuery('#submissions-'+ data.form +' .loading-small').remove();
						jQuery('#submissions-'+ data.form).append('<span class="icon-checkmark-circle rsform_ok"></span>');
						
						// move progress bar
						RSFormPro.Restore.progress();
					}
				break;
				
				case 'parse-submissions':
				case 'continue-submissions':
					if (data.formId) {
						RSFormPro.Restore.currentFormId = data.formId;
						// set the checkmark for the structure of the form if there are submissions
						jQuery('#form-'+ data.form +' .loading-small').remove();
						jQuery('#form-'+ data.form).append('<span class="icon-checkmark-circle rsform_ok"></span>');
						
						// put the loader for the submissions
						jQuery('#submissions-'+ data.form).append('<span class="loading-small"></span>');
					}
					RSFormPro.Restore.sendRequest('parseSubmissions', {'form':data.form, 'formId': RSFormPro.Restore.currentFormId, 'file':data.file});
					RSFormPro.Restore.showStatus(Joomla.JText._('RSFP_RESTORING_FORM_SUBMISSIONS').replace('%s', RSFormPro.Restore.formsFound[(RSFormPro.Restore.currentForm - 1)].title).replace('%d', data.file + 1));
				break;
				
				case 'restore-done':
					RSFormPro.Restore.showStatus(Joomla.JText._('RSFP_RESTORE_COMPLETE'));
					jQuery('#viewForms').show();
					jQuery('.progressWrapper').remove();
				break;
			}
		}
	},
	
	// Helper - sends a task request to index.php?option=com_rsform&controller=restore and passes the response to the parseResponse() function.
	sendRequest: function(task, data) {
		if (!data) {
			data = {};
		}
		
		data['option'] 		= 'com_rsform';
		data['controller'] 	= 'restore';
		data['task']		= task;
		data['key'] 		= jQuery('#restoreKey').val();
		data['overwrite'] 	= RSFormPro.Restore.overwrite;
		
		if (RSFormPro.Restore.requestTimeOut.Seconds != 0 && task !='decompress') {
			setTimeout(function(){RSFormPro.Restore.ajaxRequest(data)}, RSFormPro.Restore.requestTimeOut.Milliseconds());
		} else {
			RSFormPro.Restore.ajaxRequest(data);
		}
	},
	
	ajaxRequest : function(data) {
		jQuery.ajax({
			converters: {
				"text json": RSFormPro.Restore.parseJSON
			},
			type: "POST",
			url: 'index.php?option=com_rsform',
			data: data,
			success: RSFormPro.Restore.parseResponse,
			dataType: 'json'
		});
	},
	
	// Helper function to adjust the progress by a unit.
	progress: function(reset) {		
		var bar 	= jQuery('.progressBar');
		var current = RSFormPro.Restore.progressCount += RSFormPro.Restore.progressUnit;
		
		if (reset) {
			current = 0;
			RSFormPro.Restore.progressCount = 0;
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
	
	// Starts the restore process
	start: function() {
		RSFormPro.Restore.overwrite = parseInt(jQuery('#overwriteOption').val());
		RSFormPro.Restore.keepId    = parseInt(jQuery('#keepIdOption').val());
		RSFormPro.Restore.sendRequest('decompress');
		RSFormPro.Restore.showStatus(Joomla.JText._('RSFP_DECOMPRESSING_ARCHIVE'));
	},
}