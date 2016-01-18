jQuery.noConflict();
jQuery(document).ready(function($){
	
	var RSFilesProgress = {
		_formatFileSize: function (bytes) {
			if (typeof bytes !== 'number') {
				return '';
			}
			if (bytes >= 1000000000) {
				return (bytes / 1000000000).toFixed(2) + ' GB';
			}
			if (bytes >= 1000000) {
				return (bytes / 1000000).toFixed(2) + ' MB';
			}
			return (bytes / 1000).toFixed(2) + ' KB';
		},
		
		_formatBitrate: function (bits) {
			if (typeof bits !== 'number') {
				return '';
			}
			if (bits >= 1000000000) {
				return (bits / 1000000000).toFixed(2) + ' Gbit/s';
			}
			if (bits >= 1000000) {
				return (bits / 1000000).toFixed(2) + ' Mbit/s';
			}
			if (bits >= 1000) {
				return (bits / 1000).toFixed(2) + ' kbit/s';
			}
			return bits.toFixed(2) + ' bit/s';
		},
		
		_formatTime: function (seconds) {
			var date = new Date(seconds * 1000),
				days = Math.floor(seconds / 86400);
			days = days ? days + 'd ' : '';
			return days +
				('0' + date.getUTCHours()).slice(-2) + ':' +
				('0' + date.getUTCMinutes()).slice(-2) + ':' +
				('0' + date.getUTCSeconds()).slice(-2);
		},
		
		_formatPercentage: function (floatValue) {
			return (floatValue * 100).toFixed(2) + ' %';
		},
		
		renderExtendedProgress: function (data) {
			return this._formatBitrate(data.bitrate) + ' | ' +
				this._formatTime(
					(data.total - data.loaded) * 8 / data.bitrate
				) + ' | ' +
				this._formatPercentage(
					data.loaded / data.total
				) + ' | ' +
				this._formatFileSize(data.loaded) + ' / ' +
				this._formatFileSize(data.total);
		}
	};
	
	// Create the upload form
	var uploadForm = $('#rsfl_upload_form').fileupload({
		url: $('#siteroot').text() + 'index.php?option=com_rsfiles&task=rsfiles.upload&from=' +  $('input[name="from"]').val()  + '&Itemid=' + $('#itemid').text(),
		autoUpload: false,
		sequentialUploads: true,
		dataType: 'json',
		maxChunkSize: parseInt($('#chunk').val()),
		add: function (e, data) {
			if (e.isDefaultPrevented()) {
				return false;
			}
			
			// Cancel uploads
			$('#com-rsfiles-cancel-upload').on('click', function(e) {
				data.abort();
				data.files = [];
				document.getElementById('rsfl_upload_form').reset();
				$('#com-rsfiles-add-files').css('display','');
				$('#com-rsfiles-no-files').css('display','none');
				$('#com-rsfiles-progress').css('display', 'none');
				$('#com-rsfiles-progress-info').css('display', 'none');
			});
			
			var $this = $(this);
			data.process(function () {
				return $this.fileupload('process', data);
			}).done(function () {
				$('#com-rsfiles-upload-files').click(function (e) {
					if (data.files.length > 0) {
						data.submit().success(function (result, textStatus, jqXHR) {
							if (result && typeof result == 'object') {
								$('#com-rsfiles-upload-results').prepend($('<li>', {'class': result.success ? 'rs_success' : 'rs_error'}).html('<span class="rsicon-cancel com-rsfiles-close-message"></span>' + result.data.message));

								$('.com-rsfiles-close-message').on('click',function() {
									$(this).parent('li').hide('fast');
								});
								
								var totalFiles = $('#com-rsfiles-no-files > span').text();
								var remainingFiles = totalFiles - 1;
								if (remainingFiles > 0) {
									$('#com-rsfiles-no-files > span').text(remainingFiles);
								} else {
									$('#com-rsfiles-add-files').css('display','');
									$('#com-rsfiles-no-files').css('display','none');
								}
							}
							
							data.files.splice(0,1);
						}).error(function (jqXHR, textStatus, errorThrown) {
							if (errorThrown === 'abort') {
								$(data.files).each(function(index) {
									$.ajax({
										url: $('#siteroot').text() + 'index.php?option=com_rsfiles&task=rsfiles.cancelupload',
										method: 'POST',
										dataType: 'json',
										data: {
											'file'	: data.files[index].filename,
											'from'	: $('input[name="from"]').val(),
											'folder': $('input[name="folder"]').val(),
											'Itemid': $('#itemid').text()
										}
									});
								});
							}
						});
					}
				});
				
			}).fail(function () {
				if (data.files.error) {
					$(data.files).each(function(index) {
						var error = data.files[index].error;
						if (error) {
							$('#com-rsfiles-upload-results').prepend($('<li>', {'class': 'rs_error'}).html('<span class="rsicon-cancel com-rsfiles-close-message"></span>' + error));
							$('.com-rsfiles-close-message').on('click',function() {
								$(this).parent('li').hide('fast');
							});
							
							var totalFiles = $('#com-rsfiles-no-files > span').text();
							var remainingFiles = totalFiles - 1;
							if (remainingFiles > 0) {
								$('#com-rsfiles-no-files > span').text(remainingFiles);
							} else {
								$('#com-rsfiles-add-files').css('display','');
								$('#com-rsfiles-no-files').css('display','none');
							}
						}
					})
				}
			});
		},
		progressall: function (e, data) {
			var current = parseInt($('#com-rsfiles-bar').text());
			var progress = Math.floor(data.loaded / data.total * 100);
			
			if (progress > current) {			
				$('#com-rsfiles-progress').css('display', 'block');
				$('#com-rsfiles-progress .com-rsfiles-bar').css(
					'width',
					progress + '%'
				).html(progress + '%');

				$('#com-rsfiles-progress-info').css('display', '');
				$('#com-rsfiles-progress-info').html(RSFilesProgress.renderExtendedProgress(data));
			}
		},
		start: function(e) {
			$('#com-rsfiles-upload-files').prop('disabled', true);
		},
		stop: function (e) {
			$('#com-rsfiles-progress .com-rsfiles-bar').css('width','100%').html('100%');
			$('#com-rsfiles-upload-files').prop('disabled', false);
		}
	}).on('fileuploadprocessstart', function () {
        $('#com-rsfiles-upload-text').text(Joomla.JText._('COM_RSFILES_PROCESS_START'));
		$('#com-rsfiles-upload-files').prop('disabled', true);
    }).on('fileuploadprocessstop', function () {
        $('#com-rsfiles-upload-text').text(Joomla.JText._('COM_RSFILES_START_UPLOAD'));
		$('#com-rsfiles-upload-files').prop('disabled', false);
    });
	
	// Set the total numer of files in queue
	uploadForm.bind('fileuploadchange', function (e, data) {
		var $el = $('#com-rsfiles-upload-field');
        $el.wrap('<form>').closest('form').get(0).reset();
        $el.unwrap();
		
		$('#com-rsfiles-no-files > span').text(data.files.length);
		$('#com-rsfiles-add-files').css('display','none');
		$('#com-rsfiles-no-files').css('display','');
		$('#com-rsfiles-progress .com-rsfiles-bar').css('width','0%').html('0%');
		$('#com-rsfiles-progress-info').text('');
		$('#com-rsfiles-upload-results li').remove();
	});
	
	// Submit files
	uploadForm.bind('fileuploadsubmit', function (e, data) {
		data.formData = {
			'overwrite'	: $('#overwrite').is(':checked') ? 1 : 0,
			'exists'	: data.files[0].exists,
			'filename'	: data.files[0].filename,
			'folder'	: $('input[name="folder"]').val()
		};
	});
});