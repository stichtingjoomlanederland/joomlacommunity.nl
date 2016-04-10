jQuery.noConflict();

var RSFirewall = {};

RSFirewall.$ = jQuery;

RSFirewall.parseJSON = function(data) {
	if (typeof data != 'object') {
		// parse invalid data:
		var match = data.match(/{.*}/);

		return RSFirewall.$.parseJSON(match[0]);
	}

	return RSFirewall.$.parseJSON(data);
};

RSFirewall.requestTimeOut = {};
RSFirewall.requestTimeOut.Seconds = 0;
RSFirewall.requestTimeOut.Milliseconds = function(){
	return parseFloat(RSFirewall.requestTimeOut.Seconds) * 1000;
};

/* loading helper */
RSFirewall.removeLoading = function() {
	RSFirewall.$('.com-rsfirewall-icon-16-loading').remove();
};
RSFirewall.addLoading = function(where, type) {
	if (typeof type == 'undefined') {
		RSFirewall.$(where).append('<span class="com-rsfirewall-icon-16-loading"></span>');
	} else {
		RSFirewall.$(where)[type]('<span class="com-rsfirewall-icon-16-loading"></span>');
	}
};
RSFirewall.removeArrow = function() {
	RSFirewall.$('.com-rsfirewall-current-item').removeClass('com-rsfirewall-current-item');
};
RSFirewall.addArrow = function(item) {
	RSFirewall.$(item).addClass('com-rsfirewall-current-item');
};

/* Database */
RSFirewall.Database = {};
RSFirewall.Database.Check = {
	unhide: function(item) {
		return RSFirewall.$(item).removeClass('com-rsfirewall-hidden');
	},
	ignored: false,
	tables: [],
	tablesNum: 0,
	table: '',
	content: '',
	prefix: '',
	startCheck: function() {
		this.table 	 = RSFirewall.$('#' + this.prefix + '-table');
		this.content = RSFirewall.$('#' + this.prefix);
		if (!this.tables.length) {
			return false;
		}

		this.unhide(this.content);
		this.content.hide().show('fast', function() {
			RSFirewall.Database.Check.stepCheck(0);
		});
	},
	stopCheck: function() {

	},
	setProgress: function(index) {
		if (RSFirewall.$('#' + this.prefix + '-progress .com-rsfirewall-bar').length > 0) {
			var currentProgress = (100 / this.tablesNum) * index;
			RSFirewall.$('#' + this.prefix + '-progress .com-rsfirewall-bar').css('width', currentProgress + '%').text(parseInt(currentProgress) + '%');
		}
	},
	stepCheck: function(index) {
		this.setProgress(index);
		if (!this.tables || !this.tables.length) {
			this.stopCheck();
			return false;
		}

		this.unhide(this.table.find('tr')[index+1]);

		var table = this.tables.pop();
		RSFirewall.$.ajax({
			type: 'POST',
			url: 'index.php?option=com_rsfirewall',
			data: {
				task: 'dbcheck.optimize',
				table: table,
				sid: Math.random()
			},
			beforeSend: function() {
				RSFirewall.addLoading(RSFirewall.$('#result' + index));
			},
			success: function(data) {
				RSFirewall.removeLoading();
				RSFirewall.$('#result' + index).html(data);
				if (RSFirewall.requestTimeOut.Seconds != 0) {
					setTimeout(function(){RSFirewall.Database.Check.stepCheck(index+1)}, RSFirewall.requestTimeOut.Milliseconds());
				}
				else {
					RSFirewall.Database.Check.stepCheck(index+1);
				}
			}
		});
	}
};

/* System */
RSFirewall.System = {};
RSFirewall.System.Check = {
	unhide: function(item) {
		return RSFirewall.$(item).removeClass('com-rsfirewall-hidden');
	},
	content: null,
	table: null,
	steps: [],
	currentStep: '',
	prefix: '',
	fix: function(step, currentButton) {
		var parent = RSFirewall.$(currentButton).parents('td');

		var data = {
			task: 'fix.' + step,
			sid: Math.random()
		};

		if (parent.find('.com-rsfirewall-loader-wrapper').length > 0) {
			var loaderWrapper = parent.find('.com-rsfirewall-loader-wrapper');
		} else {
			var loaderWrapper = RSFirewall.$('<span class="com-rsfirewall-loader-wrapper"></span>');
			loaderWrapper.insertAfter(currentButton);
		}

		loaderWrapper.removeClass('com-rsfirewall-ok com-rsfirewall-not-ok com-rsfirewall-error').empty();

		if (step == 'fixAdminUser') {
			data.username = RSFirewall.$('#com-rsfirewall-new-username').val();
		} else if (step == 'fixHashes') {
			data.files = [];
			RSFirewall.$('input[name="hashes[]"]:checked').each(function() {
				data.files.push(RSFirewall.$(this).val());
			});
			data.flags = [];
			RSFirewall.$('input[name="hash_flags[]"]').each(function() {
				data.flags.push(RSFirewall.$(this).val());
			});
		} else if (step == 'ignoreFiles') {
			data.files = [];
			RSFirewall.$('input[name="ignorefiles[]"]:checked').each(function() {
				data.files.push(RSFirewall.$(this).val());
			});
		} else if (step == 'fixFolderPermissions') {
			data.folders = [];
			// adjust the limit
			var limit = this.limit;
			if (RSFirewall.$('input[name="folders[]"]:checked').length < this.limit) {
				limit = RSFirewall.$('input[name="folders[]"]:checked').length;
			}
			// add the folders to the POST array
			for (var i=0; i<limit; i++) {
				data.folders.push(RSFirewall.$(RSFirewall.$('input[name="folders[]"]:checked')[i]).val());
			}

			// how many items are left?
			loaderWrapper.text(Joomla.JText._('COM_RSFIREWALL_ITEMS_LEFT').replace('%d', RSFirewall.$('input[name="folders[]"]:checked').length));

			// stop if there are no folders to process
			if (data.folders.length == 0) {
				// show the message
				loaderWrapper.html(Joomla.JText._('COM_RSFIREWALL_FIX_FOLDER_PERMISSIONS_DONE'));
				loaderWrapper.addClass('com-rsfirewall-ok');
				RSFirewall.$(currentButton).remove();
				return;
			}
		} else if (step == 'fixFilePermissions') {
			data.files = [];
			// adjust the limit
			var limit = this.limit;
			if (RSFirewall.$('input[name="files[]"]:checked').length < this.limit) {
				limit = RSFirewall.$('input[name="files[]"]:checked').length;
			}
			// add the files to the POST array
			for (var i=0; i<limit; i++) {
				data.files.push(RSFirewall.$(RSFirewall.$('input[name="files[]"]:checked')[i]).val());
			}

			// how many items are left?
			loaderWrapper.text(Joomla.JText._('COM_RSFIREWALL_ITEMS_LEFT').replace('%d', RSFirewall.$('input[name="files[]"]:checked').length));

			// stop if there are no files to process
			if (data.files.length == 0) {
				// show the message
				loaderWrapper.html(Joomla.JText._('COM_RSFIREWALL_FIX_FILE_PERMISSIONS_DONE'));
				loaderWrapper.addClass('com-rsfirewall-ok');
				RSFirewall.$(currentButton).remove();
				return;
			}
		}

		RSFirewall.$.ajax({
			converters: {
				"text json": RSFirewall.parseJSON
			},
			dataType: 'json',
			type: 'POST',
			url: 'index.php?option=com_rsfirewall',
			data: data,
			beforeSend: function() {
				RSFirewall.addLoading(loaderWrapper);
				RSFirewall.$(currentButton).hide();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				RSFirewall.removeLoading();
				RSFirewall.$(currentButton).show();

				loaderWrapper.addClass('com-rsfirewall-error');
				loaderWrapper.html(Joomla.JText._('COM_RSFIREWALL_ERROR_FIX') + jqXHR.status + ' ' + errorThrown);
			},
			success: function(json) {
				RSFirewall.removeLoading();
				RSFirewall.$(currentButton).show();

				if (json.success == true) {
					if (RSFirewall.System.Check.parseFixDetails(step, json, loaderWrapper, currentButton)) {
						// returning true means that we need to skip what's below
						return;
					}

					if (typeof json.data.result != 'undefined') {
						if (json.data.result == true) {
							loaderWrapper.addClass('com-rsfirewall-ok');
							RSFirewall.$(currentButton).remove();
						} else {
							loaderWrapper.addClass('com-rsfirewall-not-ok');
						}
					}
					if (typeof json.data.message != 'undefined') {
						loaderWrapper.html(json.data.message);
					}

				} else {
					loaderWrapper.addClass('com-rsfirewall-error');
					if (typeof json.data.message != 'undefined') {
						loaderWrapper.html(json.data.message);
					}
				}
			}
		});
	},
	setProgress: function(index) {
		if (RSFirewall.$('#' + this.prefix + '-progress .com-rsfirewall-bar').length > 0) {
			var currentProgress = (100 / this.steps.length) * index;
			RSFirewall.$('#' + this.prefix + '-progress .com-rsfirewall-bar').css('width', currentProgress + '%').text(parseInt(currentProgress) + '%');
		}
	},
	stopCheck: function() {
		// overwritten
	},
	stepCheck: function(index, more_data) {
		this.setProgress(index);
		if (typeof(this.steps[index]) == 'undefined') {
			if (typeof this.stopCheck == 'function') {
				this.stopCheck();
			}
			return;
		}

		trindex = index > 0 ? index*2 : 0;

		var currentRow  			= RSFirewall.$(this.table.find('tbody tr.com-rsfirewall-table-row')[trindex]);
		var currentText 			= RSFirewall.$(currentRow.find('td span')[0]);
		var currentResult 			= RSFirewall.$(currentRow.find('td span')[1]);
		var currentDetailsRow 		= RSFirewall.$(this.table.find('tbody tr.com-rsfirewall-table-row')[trindex+1]);
		var currentDetails			= RSFirewall.$(currentDetailsRow.children('td')[0]);
		var currentDetailsButton 	= RSFirewall.$(this.table.find('.com-rsfirewall-details-button')[index]);
		var currentStep 			= this.steps[index];
		this.currentStep			= currentStep;

		this.unhide(currentRow);

		default_data = {
			task: 'check.' + currentStep,
			sid: Math.random()
		};
		if (more_data) {
			for (var key in more_data)
				default_data[key] = more_data[key];
		}

		RSFirewall.$.ajax({
			converters: {
				"text json": RSFirewall.parseJSON
			},
			dataType: 'json',
			type: 'POST',
			url: 'index.php?option=com_rsfirewall',
			data: default_data,
			beforeSend: function() {
				RSFirewall.addArrow(currentText);
				RSFirewall.addLoading(currentResult);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				currentResult.addClass('com-rsfirewall-error');
				currentResult.html(Joomla.JText._('COM_RSFIREWALL_ERROR_CHECK') + jqXHR.status + ' ' + errorThrown);

				RSFirewall.removeArrow();
				if (RSFirewall.requestTimeOut.Seconds != 0) {
					setTimeout(function(){RSFirewall.System.Check.stepCheck(index+1)}, RSFirewall.requestTimeOut.Milliseconds());
				}
				else {
					RSFirewall.System.Check.stepCheck(index+1);
				}
			},
			success: function(json) {
				RSFirewall.removeArrow();
				if (json.success == true) {
					if (typeof json.data.message != 'undefined') {
						currentResult.html(json.data.message);
					}
					if (typeof json.data.result != 'undefined') {
						currentResult.addClass(json.data.result == true ? 'com-rsfirewall-ok' : 'com-rsfirewall-not-ok');
						// show the button if we need to provide details
						if (json.data.result == false) {
							RSFirewall.System.Check.unhide(currentDetailsButton);
						}
					}

					// a little hack to stop going to the next step
					// if this step requires extra ajax calls
					if (RSFirewall.System.Check.parseCheckDetails(currentStep, json, currentDetails, currentResult, currentDetailsButton)) {
						return;
					}
				} else {
					if (typeof json.data.message != 'undefined') {
						if (currentStep == 'checkCoreFilesIntegrity') {
							currentResult.addClass('com-rsfirewall-not-ok');
						} else {
							currentResult.addClass('com-rsfirewall-error');
						}
						currentResult.html(Joomla.JText._('COM_RSFIREWALL_ERROR_CHECK') + json.data.message);
					}
				}
				if (RSFirewall.requestTimeOut.Seconds != 0) {
					setTimeout(function(){RSFirewall.System.Check.stepCheck(index+1)}, RSFirewall.requestTimeOut.Milliseconds());
				}
				else {
					RSFirewall.System.Check.stepCheck(index+1);
				}
			}
		});
	},
	startCheck: function() {
		this.table 	 = RSFirewall.$('#' + this.prefix + '-table');
		this.content = RSFirewall.$('#' + this.prefix);

		var currentTable = this.table;

		// make buttons clickable
		this.table.find('.com-rsfirewall-details-button').each(function(i, el) {
			RSFirewall.$(el).click(function() {
				var row = currentTable.find('tbody tr.com-rsfirewall-table-row')[i*2+1];
				if (RSFirewall.$(row).hasClass('com-rsfirewall-hidden')) {
					RSFirewall.System.Check.unhide(row);
					RSFirewall.$(row).hide();
				}
				RSFirewall.$(row).toggle();
				RSFirewall.$(this).children('span').toggleClass(function(j, theClass) {
					RSFirewall.$(this).removeAttr('class');
					if (theClass == 'expand')
						return 'shrink';
					return 'expand';
				});
			});
		});

		this.unhide(this.content);
		this.content.hide().show('fast', function() {
			RSFirewall.System.Check.stepCheck(0);
		});
	},
	/* custom details parsing rules */
	parseFixDetails: function(step, json, wrapper, button) {
		if (step == 'fixPHP') {
			if (json.data.contents) {
				RSFirewall.$('#com-rsfirewall-php-ini').text(json.data.contents);
				this.unhide('#com-rsfirewall-php-ini-wrapper');
				RSFirewall.$('#com-rsfirewall-php-ini-wrapper').hide().fadeIn('slow');
			}
		} else if (step == 'fixFolderPermissions') {
			if (json.data.results) {
				for (var i=0; i<json.data.results.length; i++) {
					var result = RSFirewall.$('<span>', {'class': json.data.results[i] == 1 ? 'com-rsfirewall-ok' : 'com-rsfirewall-not-ok'});
					RSFirewall.$(RSFirewall.$('input[name="folders[]"]:checked')[0]).replaceWith(result);
				}
				RSFirewall.System.Check.fix(step, button);
				return true;
			}
		} else if (step == 'fixFilePermissions') {
			if (json.data.results) {
				for (var i=0; i<json.data.results.length; i++) {
					var result = RSFirewall.$('<span>', {'class': json.data.results[i] == 1 ? 'com-rsfirewall-ok' : 'com-rsfirewall-not-ok'});
					RSFirewall.$(RSFirewall.$('input[name="files[]"]:checked')[0]).replaceWith(result);
				}
				RSFirewall.System.Check.fix(step, button);
				return true;
			}
		}
	},
	parseCheckDetails: function(step, json, details, result, detailsButton) {

		if (step == 'checkCoreFilesIntegrity') {
			if (RSFirewall.$('#com-rsfirewall-hashes').length == 0) {
				// let's create the table
				var table = RSFirewall.$('<table>', {'class': 'com-rsfirewall-colored-table', 'id': 'com-rsfirewall-hashes'});
				details.append(table);
			}

			var table = RSFirewall.$('#com-rsfirewall-hashes');

			if (json.data.ignored) {
				this.ignored = true;
			}

			if (json.data.files && json.data.files.length > 0) {
				var j = table.find('tr').length;

				for (var i=0; i<json.data.files.length; i++) {
					var file = json.data.files[i];

					var tr = RSFirewall.$('<tr>', {
						'class': j % 2 ? 'blue' : 'yellow',
						'id' : 'hash' + j

					});
					var td_checkbox = RSFirewall.$('<td>', {
						width: '1%',
						nowrap: 'nowrap'
					});
					var checkbox = RSFirewall.$('<input />', {
						type: 'checkbox',
						checked: true,
						name: 'hashes[]',
						value: file.path,
						id: 'checkboxHash' + j
					});
					var hidden = RSFirewall.$('<input />', {
						type: 'hidden',
						name: 'hash_flags[]'
					});
					var label = RSFirewall.$('<label>', {'for': 'checkboxHash' + j}).text(file.path);
					var $hid = 'hash' + j;

					var downloadBtn = RSFirewall.$('<button type="button" style="margin-left:10px; margin-right:10px;" class="rsfirewall-download-original com-rsfirewall-button" onclick="RSFirewall.diffs.download(\''+ file.path +'\', \''+$hid +'\' , window.document)"></button>')
						.text(Joomla.JText._('COM_RSFIREWALL_DOWNLOAD_ORIGINAL'));

					if (file.type == 'wrong') {
						var td_type = RSFirewall.$('<td>').text(Joomla.JText._('COM_RSFIREWALL_FILE_HAS_BEEN_MODIFIED'));
						var btn = RSFirewall.$('<button type="button" id="diff'+$hid+'" class="com-rsfirewall-button"></button>')
							.attr('href', 'index.php?option=com_rsfirewall&view=diff&tmpl=component&hid=hash'+ j +'&file=' + encodeURIComponent(file.path))
							.text(Joomla.JText._('COM_RSFIREWALL_VIEW_DIFF'))
							.click(function(){
								window.open(RSFirewall.$(this).attr('href'), 'diffWindow', 'width=800, height=600, left=50%, location=0, menubar=0, resizable=1, scrollbars=1, toolbar=0, titlebar=1');
							});

						td_type.append(btn);
						td_type.append(downloadBtn);

					} else if (file.type == 'missing') {
						hidden.val('M');
						var td_type = RSFirewall.$('<td>').text(Joomla.JText._('COM_RSFIREWALL_FILE_IS_MISSING'));
						td_type.append(downloadBtn);
					}

					var td_path = RSFirewall.$('<td>').append(label);

					table.append(tr.append(td_checkbox.append(checkbox, hidden), td_path, td_type));
					j++;
				}

			}

			// we haven't reached the end of the file so do another ajax call
			if (json.data.fstart) {
				var stepIndex = this.steps.indexOf(this.currentStep);

				if (RSFirewall.requestTimeOut.Seconds != 0) {
					setTimeout(function(){RSFirewall.System.Check.stepCheck(stepIndex, {'fstart': json.data.fstart})}, RSFirewall.requestTimeOut.Milliseconds());
				}
				else {
					RSFirewall.System.Check.stepCheck(stepIndex, {'fstart': json.data.fstart});
				}
				// returning true means that this step hasn't finished and we don't need to go to the next step
				return true;
			} else {
				if (table.find('tr').length > 0) {
					var $html =  Joomla.JText._('COM_RSFIREWALL_HASHES_INCORRECT').replace('%d', '<span id="hashCount">' + table.find('tr').length + '</span> ');
					result.html($html).addClass('com-rsfirewall-not-ok');
					RSFirewall.System.Check.unhide(detailsButton);
				} else {
					result.text(Joomla.JText._('COM_RSFIREWALL_HASHES_CORRECT')).addClass('com-rsfirewall-ok');
				}
				// returning false means that this step has finished and we need to go to the next step

				if (this.ignored) {
					RSFirewall.$('#com-rsfirewall-ignore-files-button').removeClass('com-rsfirewall-hidden');
				}
				return false;
			}

		} else if (step == 'checkConfigurationIntegrity') {
			// let's create the table
			var table = RSFirewall.$('<table>', {'class': 'com-rsfirewall-colored-table'});

			// and populate it
			if (json.data.details) {
				for (var i=0; i<json.data.details.length; i++) {
					var detail = json.data.details[i];
					var tr = RSFirewall.$('<tr>', {'class': i % 2 ? 'blue' : 'yellow'});
					var td_line = RSFirewall.$('<td>').html(Joomla.JText._('COM_RSFIREWALL_CONFIGURATION_LINE').replace('%d', detail.line));
					var td_code = RSFirewall.$('<td>').text(detail.code);

					table.append(tr.append(td_line, td_code));
				}
			}

			details.append(table);
		} else if (step == 'checkAdminPasswords') {
			// let's create the table
			var table = RSFirewall.$('<table>', {'class': 'com-rsfirewall-colored-table'});

			var header = RSFirewall.$('<tr>');
			var td_user = RSFirewall.$('<th>').text(Joomla.JText._('COM_RSFIREWALL_USERNAME'));
			var td_pass = RSFirewall.$('<th>').text(Joomla.JText._('COM_RSFIREWALL_PASSWORD'));
			table.append(header.append(td_user, td_pass));

			// and populate it
			if (json.data.details) {
				for (var i=0; i<json.data.details.length; i++) {
					var detail = json.data.details[i];
					var tr = RSFirewall.$('<tr>', {'class': i % 2 ? 'blue' : 'yellow'});
					var td_user = RSFirewall.$('<td>').html(detail.username);
					var td_pass = RSFirewall.$('<td>').text(detail.password);

					table.append(tr.append(td_user, td_pass));
				}
			}

			details.append(table);
		} else if (step == 'checkTemporaryFiles') {
			if (json.data.details) {
				var p = RSFirewall.$('<p>');
				details.append(p.append(json.data.details.message));

				// let's create the table
				var table = RSFirewall.$('<table>', {'class': 'com-rsfirewall-colored-table'});
				var j = 0;
				var limit = 10;

				json.data.details.folders = RSFirewall.$.map(json.data.details.folders, function(el) { return el } );
				json.data.details.files = RSFirewall.$.map(json.data.details.files, function(el) { return el } );

				for (var i=0; i < json.data.details.folders.length; i++) {
					var folder = json.data.details.folders[i];
					var tr = RSFirewall.$('<tr>', {'class': j % 2 ? 'blue' : 'yellow'});
					var td = RSFirewall.$('<td>').text('[' + folder + ']');

					table.append(tr.append(td));
					j++;
					if (i >= limit) {
						var tr = RSFirewall.$('<tr>', {'class': j % 2 ? 'blue' : 'yellow'});
						var td = RSFirewall.$('<td>').append(RSFirewall.$('<em>').text(Joomla.JText._('COM_RSFIREWALL_MORE_FOLDERS').replace('%d', (json.data.details.folders.length - limit))));
						table.append(tr.append(td));
						j++;
						break;
					}
				}
				for (var i=0; i < json.data.details.files.length; i++) {
					var file = json.data.details.files[i];
					var tr = RSFirewall.$('<tr>', {'class': j % 2 ? 'blue' : 'yellow'});
					var td = RSFirewall.$('<td>').text(file);

					table.append(tr.append(td));
					j++;
					if (i >= limit) {
						var tr = RSFirewall.$('<tr>', {'class': j % 2 ? 'blue' : 'yellow'});
						var td = RSFirewall.$('<td>').append(RSFirewall.$('<em>').text(Joomla.JText._('COM_RSFIREWALL_MORE_FILES').replace('%d', (json.data.details.files.length - limit))));
						table.append(tr.append(td));
						j++;
						break;
					}
				}

				details.append(table);
			}
		} else if (step == 'checkDisableFunctions') {
			if (json.data.details) {
				var p = RSFirewall.$('<p>');
				details.append(p.append(json.data.details));
			}
			if (this.table.find('.com-rsfirewall-not-ok').length > 0) {
				this.unhide(RSFirewall.$('#com-rsfirewall-server-configuration-fix'));
			}
		} else if (step == 'checkFolderPermissions') {
			if (RSFirewall.$('#com-rsfirewall-folders').length == 0) {
				// let's create the table
				var table = RSFirewall.$('<table>', {'class': 'com-rsfirewall-colored-table', 'id': 'com-rsfirewall-folders'});
				details.append(table);
			}

			var table = RSFirewall.$('#com-rsfirewall-folders');

			if (json.data.stop) {
				// stop scanning
				if (table.find('tr').length > 0) {
					result.text(Joomla.JText._('COM_RSFIREWALL_FOLDER_PERMISSIONS_INCORRECT').replace('%d', table.find('tr').length)).addClass('com-rsfirewall-not-ok');
					RSFirewall.System.Check.unhide(detailsButton);
				} else {
					result.text(Joomla.JText._('COM_RSFIREWALL_FOLDER_PERMISSIONS_CORRECT')).addClass('com-rsfirewall-ok');
				}

				// finished
				return false;
			} else {
				if (json.data.folders && json.data.folders.length > 0) {
					var j = table.find('tr').length;
					for (var i=0; i<json.data.folders.length; i++) {
						var folder = json.data.folders[i];

						var tr = RSFirewall.$('<tr>', {'class': j % 2 ? 'blue' : 'yellow'});
						var td_checkbox = RSFirewall.$('<td>', {
							width: '1%',
							nowrap: 'nowrap'
						});
						var checkbox = RSFirewall.$('<input />', {
							type: 'checkbox',
							checked: true,
							name: 'folders[]',
							value: folder.path,
							id: 'checkboxFolder' + j
						});
						var label = RSFirewall.$('<label>', {'for': 'checkboxFolder' + j}).text(folder.path);
						var td_path = RSFirewall.$('<td>').append(label);
						var td_perms = RSFirewall.$('<td>').text(folder.perms);

						table.append(tr.append(td_checkbox.append(checkbox), td_path, td_perms));
						j++;
					}
				}

				if (json.data.next_folder) {
					var stepIndex = this.steps.indexOf(this.currentStep);

					var next_folder = json.data.next_folder;
					var next_folder_stripped = json.data.next_folder_stripped;
					result.text(Joomla.JText._('COM_RSFIREWALL_PLEASE_WAIT_WHILE_BUILDING_DIRECTORY_STRUCTURE').replace('%s', next_folder_stripped));

					if (RSFirewall.requestTimeOut.Seconds != 0) {
						setTimeout(function(){RSFirewall.System.Check.stepCheck(stepIndex, {'folder': next_folder})}, RSFirewall.requestTimeOut.Milliseconds());
					}
					else {
						RSFirewall.System.Check.stepCheck(stepIndex, {'folder': next_folder});
					}

					// not finished
					return true;
				}
			}
		} else if (step == 'checkFilePermissions') {
			if (RSFirewall.$('#com-rsfirewall-files').length == 0) {
				// let's create the table
				var table = RSFirewall.$('<table>', {'class': 'com-rsfirewall-colored-table', 'id': 'com-rsfirewall-files'});
				details.append(table);
			}

			var table = RSFirewall.$('#com-rsfirewall-files');

			if (json.data.files && json.data.files.length > 0) {
				var j = table.find('tr').length;
				for (var i=0; i<json.data.files.length; i++) {
					var file = json.data.files[i];

					var tr = RSFirewall.$('<tr>', {'class': j % 2 ? 'blue' : 'yellow'});
					var td_checkbox = RSFirewall.$('<td>', {
						width: '1%',
						nowrap: 'nowrap'
					});
					var checkbox = RSFirewall.$('<input />', {
						type: 'checkbox',
						checked: true,
						name: 'files[]',
						value: file.path,
						id: 'checkboxFile' + j
					});
					var label = RSFirewall.$('<label>', {'for': 'checkboxFile' + j}).text(file.path);
					var td_path = RSFirewall.$('<td>').append(label);
					var td_perms = RSFirewall.$('<td>').text(file.perms);

					table.append(tr.append(td_checkbox.append(checkbox), td_path, td_perms));
					j++;
				}
			}

			if (json.data.next_file) {
				var stepIndex = this.steps.indexOf(this.currentStep);

				var next_file = json.data.next_file;
				var next_file_stripped = json.data.next_file_stripped;
				result.text(Joomla.JText._('COM_RSFIREWALL_PLEASE_WAIT_WHILE_BUILDING_FILE_STRUCTURE').replace('%s', next_file_stripped));

				if (RSFirewall.requestTimeOut.Seconds != 0) {
					setTimeout(function(){RSFirewall.System.Check.stepCheck(stepIndex, {'file': next_file})}, RSFirewall.requestTimeOut.Milliseconds());
				}
				else {
					RSFirewall.System.Check.stepCheck(stepIndex, {'file': next_file});
				}

				// not finished
				return true;
			} else {
				if (json.data.stop) {
					// stop scanning
					if (table.find('tr').length > 0) {
						result.text(Joomla.JText._('COM_RSFIREWALL_FILE_PERMISSIONS_INCORRECT').replace('%d', table.find('tr').length)).addClass('com-rsfirewall-not-ok');
						RSFirewall.System.Check.unhide(detailsButton);
					} else {
						result.text(Joomla.JText._('COM_RSFIREWALL_FILE_PERMISSIONS_CORRECT')).addClass('com-rsfirewall-ok');
					}

					// finished
					return false;
				}
			}
		} else if (step == 'checkSignatures') {
			if (RSFirewall.$('#com-rsfirewall-signatures').length == 0) {
				// let's create the table
				var table = RSFirewall.$('<table>', {'class': 'com-rsfirewall-colored-table', 'id': 'com-rsfirewall-signatures'});
				details.append(table);
			}

			var table = RSFirewall.$('#com-rsfirewall-signatures');

			if (json.data.files && json.data.files.length > 0) {
				var j = table.find('tr').length;
				for (var i=0; i<json.data.files.length; i++) {
					var file = json.data.files[i];

					var tr = RSFirewall.$('<tr>', {'class': j % 2 ? 'blue' : 'yellow'});
					var td_checkbox = RSFirewall.$('<td>', {
						valign: 'top',
						width: '1%',
						nowrap: 'nowrap'
					});
					var checkbox = RSFirewall.$('<input />', {
						valign: 'top',
						width: '19%',
						type: 'checkbox',
						checked: true,
						name: 'ignorefiles[]',
						value: file.path,
						id: 'checkboxFile' + j
					});
					var td_path = RSFirewall.$('<td valign="top" width="20%">').text(file.path);
					var td_reason = RSFirewall.$('<td valign="top" width="20%">', {'nowrap': 'nowrap'}).text(file.reason);
					var td_match = RSFirewall.$('<td valign="top" width="40%">').addClass('broken-word');
					if (file.match) {
						var td_match = td_match.text(file.match.substring(0, 355));
					}

					var btn = RSFirewall.$('<button type="button" class="com-rsfirewall-button pull-right"></button>')
						.attr('href', 'index.php?option=com_rsfirewall&view=file&tmpl=component&file=' + encodeURIComponent(file.path))
						.text(Joomla.JText._('COM_RSFIREWALL_VIEW_FILE'))
						.click(function(){
							window.open(RSFirewall.$(this).attr('href'), 'fileWindow', 'width=800, height=600, left=50%, location=0, menubar=0, resizable=1, scrollbars=1, toolbar=0, titlebar=1');
						});

					td_match.append(btn);

					table.append(tr.append(td_checkbox.append(checkbox), td_path, td_reason, td_match));
					j++;
				}
			}

			if (json.data.next_file) {
				var stepIndex = this.steps.indexOf(this.currentStep);

				var next_file = json.data.next_file;
				var next_file_stripped = json.data.next_file_stripped;
				result.text(Joomla.JText._('COM_RSFIREWALL_PLEASE_WAIT_WHILE_BUILDING_FILE_STRUCTURE').replace('%s', next_file_stripped));

				if (RSFirewall.requestTimeOut.Seconds != 0) {
					setTimeout(function(){RSFirewall.System.Check.stepCheck(stepIndex, {'file': next_file})}, RSFirewall.requestTimeOut.Milliseconds());
				}
				else {
					RSFirewall.System.Check.stepCheck(stepIndex, {'file': next_file});
				}

				// not finished
				return true;
			} else {
				if (json.data.stop) {
					// stop scanning
					if (table.find('tr').length > 0) {
						result.text(Joomla.JText._('COM_RSFIREWALL_MALWARE_PLEASE_REVIEW_FILES').replace('%d', table.find('tr').length)).addClass('com-rsfirewall-not-ok');
						RSFirewall.System.Check.unhide(detailsButton);
					} else {
						result.text(Joomla.JText._('COM_RSFIREWALL_NO_MALWARE_FOUND')).addClass('com-rsfirewall-ok');
					}

					// finished
					return false;
				}
			}
		} else {
			if (json.data.details) {
				var p = RSFirewall.$('<p>');
				details.append(p.append(json.data.details));
			}
		}
	}
};

RSFirewall.Grade = {
	create: function() {
		// compute the grade value
		// each failed step removes 2 from the total grade
		var grade = 100 - RSFirewall.$('.com-rsfirewall-count span.com-rsfirewall-not-ok').length * 2;

		var hasErrors = RSFirewall.$('.com-rsfirewall-error').length > 0;

		// If errors occured, grade is 0 and change the text.
		if (hasErrors) {
			grade = 0;
			RSFirewall.$('#com-rsfirewall-grade p').html('<strong class="com-rsfirewall-error">' + Joomla.JText._('COM_RSFIREWALL_GRADE_NOT_FINISHED') + '</strong><br />' + Joomla.JText._('COM_RSFIREWALL_GRADE_NOT_FINISHED_DESC'));
		}

		RSFirewall.$('#com-rsfirewall-grade input').val(grade);

		// green
		RSFirewall.$("#com-rsfirewall-grade input").knob({
			'min':0,
			'max':100,
			'readOnly': true,
			'width': 90,
			'height': 90,
			'inputColor': '#000000',
			'dynamicDraw': true,
			'thickness': 0.3,
			'tickColorizeValues': true,
			'change': function(v) {
				var grade = v;
				if (grade <= 75) {
					color = '#ED7A53';
				} else if (grade <= 90) {
					color = '#88BBC8';
				} else if (grade <= 100) {
					color = '#9FC569';
				}
				this.fgColor = color;
			}
		});
		RSFirewall.$("#com-rsfirewall-grade").fadeIn('slow');

		// Save if no errors encountered.
		if (!hasErrors) {
			this.save();
		}
	},
	save: function() {
		RSFirewall.$.ajax({
			type: 'POST',
			url: 'index.php?option=com_rsfirewall',
			data: {
				task: 'check.saveGrade',
				grade: RSFirewall.$('#com-rsfirewall-grade input').val(),
				sid: Math.random()
			}
		});
	}
};

RSFirewall.Status = {
	errorContainer: '',

	Error: function(display, type, error) {
		this.errorContainer.empty();
		if (display && typeof error != 'undefined') {
			this.errorContainer.addClass('com-rsfirewall-log-'+type);
			var heading = RSFirewall.$('<h3>').append(Joomla.JText._('COM_RSFIREWALL_LOG_'+type.toUpperCase()));
			this.errorContainer.append(heading);
			this.errorContainer.append(error);
			this.errorContainer.show();
		} else {
			this.errorContainer.removeClass('com-rsfirewall-log-error', 'com-rsfirewall-log-warning');
			this.errorContainer.hide();
		}
	},

	Change: function(id, listId, type, element) {
		var data = {
			task: 'logs.'+type,
			id: id
		};
		if (listId != null) {
			data.listId = listId;
		}

		RSFirewall.$.ajax({
			converters: {
				"text json": RSFirewall.parseJSON
			},
			dataType: 'json',
			type: 'POST',
			url: 'index.php?option=com_rsfirewall&task',
			data: data,
			beforeSend: function() {
				RSFirewall.addLoading(element, 'after');
				RSFirewall.$(element).hide();

				// remove previous errors
				RSFirewall.Status.Error(false);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				RSFirewall.removeLoading();
				RSFirewall.$(element).show();

				// set the error
				RSFirewall.Status.Error(true, 'error', jqXHR.status + ' ' + errorThrown);
			},
			success: function(json) {
				RSFirewall.removeLoading();
				if (json.success) {
					if (json.data.result) {
						// all ok
						if (json.data.type) {
							// change the button from where we triggered the action
							RSFirewall.Status.MakeButton(element, id, null, 0);

							// change the buttons for all other rows that contains the same ip
							RSFirewall.Status.ChangeSameIp(id, null, 0);
						} else {
							// change the button from where we triggered the action
							RSFirewall.Status.MakeButton(element, id, json.data.listId, 1);

							// change the buttons for all other rows that contains the same ip
							RSFirewall.Status.ChangeSameIp(id, json.data.listId, 1);
						}
						RSFirewall.$(element).remove();
					} else {
						// errors
						RSFirewall.$(element).show();

						// set the warning
						RSFirewall.Status.Error(true, 'warning', json.data.error);
					}
				}
			}
		});
	},

	MakeButton: function(element, id, listId, type) {
		var button;
		if (type) {
			button = '<button type="button" onclick="RSFirewall.Status.Change('+id+', '+listId+', \'unblockajax\', this)" class="btn btn-small">'+Joomla.JText._('COM_RSFIREWALL_UNBLOCK')+'</button>';
		}
		else {
			button = '<button type="button" onclick="RSFirewall.Status.Change('+id+', null, \'blockajax\', this)" class="btn  btn-danger btn-small">'+Joomla.JText._('COM_RSFIREWALL_BLOCK')+'</button>';
		}
		RSFirewall.$(element).after(button);
	},

	ChangeSameIp: function (id, listId, type) {
		// get the ip address that we need to change the button
		var ip = RSFirewall.$('#rsf-log-'+id).find('.rsf-ip-address').html().trim();

		// parse the table to find the same ip entries
		RSFirewall.$('.adminlist .rsf-entry').each(function(){
			var ipFound;
			if (ipFound = RSFirewall.$(this).find('.rsf-ip-address').html().trim()) {
				if (ipFound == ip) {
					RSFirewall.$(this).find('.rsf-status').empty();

					if (type) {
						button = '<button type="button" onclick="RSFirewall.Status.Change('+id+', '+listId+', \'unblockajax\', this)" class="btn btn-small">'+Joomla.JText._('COM_RSFIREWALL_UNBLOCK')+'</button>'
					} else {
						button = '<button type="button" onclick="RSFirewall.Status.Change('+id+', null, \'blockajax\', this)" class="btn  btn-danger btn-small">'+Joomla.JText._('COM_RSFIREWALL_BLOCK')+'</button>';
					}
					RSFirewall.$(this).find('.rsf-status').append(button);
				}
			}
		});

	}
};

/**
 * Function to download the original file from the remote server
 * and overwrite the one stored locally.
 *
 * @type {{download: RSFirewall.diffs.download}}
 */
RSFirewall.diffs = {
	download: function ($local, $hid, $window) {

		if (!confirm(Joomla.JText._('COM_RSFIREWALL_CONFIRM_OVERWRITE_LOCAL_FILE'))) {
			return false;
		}

		RSFirewall.$.ajax({
			type      : 'POST',
			dataType  : 'JSON',
			url       : 'index.php?option=com_rsfirewall',
			data      : {
				task      : 'diff.download',
				localFile : $local
			},
			beforeSend: function () {
				var $buttons = [];
				var $counter = RSFirewall.$('#' + $hid, $window).find('td').last();
				var $button = $counter.find('.rsfirewall-download-original');
				var $optional = RSFirewall.$('#replace-original');

				$buttons.push($button);
				if ($optional.length) {
					$buttons.push($optional);
				}

				RSFirewall.$.each($buttons, function(){
					RSFirewall.$(this).attr('disabled', 'true').addClass('btn-processing');
					RSFirewall.$(this).html('<span class="icon-refresh"></span> ' + Joomla.JText._("COM_RSFIREWALL_BUTTON_PROCESSING"));
				});
			},
			success: function (result) {
				var $hashCount = RSFirewall.$('#hashCount', $window);
				var $parent = $hashCount.parents('.com-rsfirewall-table-row.alt-row');
				var $counter = RSFirewall.$('#' + $hid, $window).find('td').last();
				var $oldValue = parseInt(RSFirewall.$('#hashCount', $window).html());
				var $button = $counter.find('.rsfirewall-download-original');
				var $optional = RSFirewall.$('#replace-original');
				var $diffButton = RSFirewall.$('#diff' + $hid, $window);

				var $buttons = [];

				$buttons.push($button);
				if ($optional.length) {
					$buttons.push($optional);
				}

				if (result.status == true) {
					$diffButton.remove();
					RSFirewall.$.each($buttons, function(){
						RSFirewall.$(this).removeClass('btn-processing').addClass('btn-success');
						RSFirewall.$(this).html('<span class="icon-checkmark-2"></span> ' + Joomla.JText._("COM_RSFIREWALL_BUTTON_SUCCESS"));
					});


					if ($oldValue == 1) {
						$parent.find('.com-rsfirewall-not-ok').removeClass('com-rsfirewall-not-ok').addClass('com-rsfirewall-ok');
						$parent.find('.com-rsfirewall-ok').empty().append('<span>' + Joomla.JText._('COM_RSFIREWALL_HASHES_CORRECT') + '</span>');
					} else {
						$hashCount.html($oldValue - 1);
					}

				} else {
					RSFirewall.$.each($buttons, function(){
						RSFirewall.$(this).removeClass('btn-processing').addClass('btn-failed');
						RSFirewall.$(this).html('<span class="icon-cancel-circle"></span> ' + Joomla.JText._("COM_RSFIREWALL_BUTTON_FAILED"));
					});
				}

				if ($optional.length) {
					RSFirewall.$('.rsfirewall-replace-original').append('<div class="alert alert-info">' + result.message + '</div>');
				}

				if($counter.find('#'+$hid+'-message', $window).length){
					RSFirewall.$('#'+$hid+'-message', $window).remove();
				}

				$counter.append('<span id="'+$hid+'-message">'+result.message+'</span>');
			}
		});
	}
};

RSFirewall.ignore = {
	remove: function ($id) {
		if (!confirm(Joomla.JText._('COM_RSFIREWALL_CONFIRM_UNIGNORE'))) {
			return false;
		}
		RSFirewall.$.ajax({
			type      : 'POST',
			dataType  : 'JSON',
			url       : 'index.php?option=com_rsfirewall',
			data      : {
				task         : 'ignored.removeFromIgnored',
				ignoredFileId: $id
			},
			beforeSend: function () {
				$button = RSFirewall.$('#removeIgnored' + $id);
				$button.attr('disabled', 'true').addClass('btn-processing');
				$button.html('<span class="icon-refresh"></span> ' + Joomla.JText._("COM_RSFIREWALL_BUTTON_PROCESSING"));
			},
			success   : function (result) {
				$button = RSFirewall.$('#removeIgnored' + $id);

				if (result.status == true) {
					$button.removeClass('btn-processing').addClass('btn-success');
					$button.html('<span class="icon-checkmark-2"></span> ' + Joomla.JText._("COM_RSFIREWALL_BUTTON_SUCCESS"));
					$button.parents('tr').hide('fast');

				} else {
					$button.removeClass('btn-processing').addClass('btn-failed');
					$button.html('<span class="icon-cancel-circle"></span> ' + Joomla.JText._("COM_RSFIREWALL_BUTTON_FAILED"));
				}
			}
		})
	},
};