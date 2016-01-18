jQuery.noConflict();
jQuery(document).ready(function($){
	var uploadForm = $('#rsfl_upload_form').fileupload({
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
								$('#com-rsfiles-upload-results').prepend($('<li>', {'class': result.success ? 'rs_success' : 'rs_error'}).html('<span class="icon-delete com-rsfiles-close-message"></span>' + result.data.message));

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
										url: $('#siteroot').text() + 'administrator/index.php?option=com_rsfiles&task=files.cancelupload',
										method: 'POST',
										dataType: 'json',
										data: {
											'file'	: data.files[index].filename,
											'path': $('input[name="FilePath"]').val()
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
							$('#com-rsfiles-upload-results').prepend($('<li>', {'class': 'rs_error'}).html('<span class="icon-delete com-rsfiles-close-message"></span>' + error));
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
			}
		},
		start: function(e) {
			$('#com-rsfiles-upload-files').prop('disabled', true);
		},
		stop: function (e) {
			$('#com-rsfiles-progress .com-rsfiles-bar').css('width','100%').html('100%');
			$('#com-rsfiles-upload-files').prop('disabled', false);
			window.parent.document.location.reload();
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
		$('#com-rsfiles-upload-results li').remove();
	});
	
	// Submit files
	uploadForm.bind('fileuploadsubmit', function (e, data) {
		var CanDelete 	= $('select[name="CanDelete[]"]').val() || [];
		var CanEdit 	= $('select[name="CanEdit[]"]').val() || [];
		var CanView		= $('select[name="CanView[]"]').val() || [];
		var CanDownload = $('select[name="CanDownload[]"]').val() || [];
		CanDelete		= CanDelete.join(',');
		CanEdit			= CanEdit.join(',');
		CanView			= CanView.join(',');
		CanDownload		= CanDownload.join(',');
		
		data.formData = {
			'published': 				$('input[name="published"]:checked').val(),
			'DateAdded': 				$('input[name="DateAdded"]').val(),
			'FileStatistics': 			$('input[name="FileStatistics"]:checked').val(),
			'show_preview': 			$('input[name="show_preview"]:checked').val(),
			'FileVersion': 				$('input[name="FileVersion"]').val(),
			'IdLicense': 				$('select[name="IdLicense"]').val(),
			'DownloadMethod': 			$('select[name="DownloadMethod"]').val(),
			'DownloadLimit': 			$('input[name="DownloadLimit"]').val(),
			'overwrite': 				$('input[name="overwrite"]:checked').val(),
			'path': 					$('input[name="FilePath"]').val(),
			'filename':					data.files[0].filename,
			'CanEdit': 					CanEdit,
			'CanDelete': 				CanDelete,
			'CanView': 					CanView,
			'CanDownload': 				CanDownload
		};
	});
});