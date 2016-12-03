var RSFiles = {};
RSFiles.Sync = {
	steps: [],
	currentStep: '',
	type: 'sync',
	startFolder: '',
	color: '#FFFFFF',
	setProgress: function() {
		jQuery('#com-rsfiles-sync-progress').css('display','block');
	},
	
	sync : function (index, more_data) {
		this.setProgress();
		if (typeof(this.steps[index]) == 'undefined') {
			this.stop();
			return;
		}
		
		var currentStep 	= this.steps[index];
		this.currentStep	= currentStep;
		
		default_data = {
			task: 'files.' + currentStep,
			sid: Math.random()
		}
		
		if (this.type == 'extend') {
			if (jQuery('#extendCanCreate').prop('checked')) {
				var cancreate = [];
				jQuery('#jform_CanCreate :selected').each(function() {
					cancreate.push(jQuery(this).val());
				});
				
				default_data.cancreate = cancreate.join(',');
			}
			
			if (jQuery('#extendCanUpload').prop('checked')) {
				var canupload = [];
				jQuery('#jform_CanUpload :selected').each(function() {
					canupload.push(jQuery(this).val());
				});
				
				default_data.canupload = canupload.join(',');
			}
			
			if (jQuery('#extendCanDelete').prop('checked')) {
				var candelete = [];
				jQuery('#jform_CanDelete :selected').each(function() {
					candelete.push(jQuery(this).val());
				});
				
				default_data.candelete = candelete.join(',');
			}
			
			if (jQuery('#extendCanEdit').prop('checked')) {
				var canedit = [];
				jQuery('#jform_CanEdit :selected').each(function() {
					canedit.push(jQuery(this).val());
				});
				
				default_data.canedit = canedit.join(',');
			}
			
			if (jQuery('#extendView').prop('checked')) {
				var view = [];
				jQuery('#jform_CanView :selected').each(function() {
					view.push(jQuery(this).val());
				});
				
				default_data.view = view.join(',');
			}
			
			if (jQuery('#extendDownload').prop('checked')) {
				var download = [];
				jQuery('#jform_CanDownload :selected').each(function() {
					download.push(jQuery(this).val());
				});
				
				default_data.download = download.join(',');
			}
		}
		
		if (more_data) {
			for (var key in more_data) {
				default_data[key] = more_data[key];
			}
		} else {
			if (this.type == 'extend') {
				if (currentStep == 'checkExtendFolders') {
					default_data.folder = this.startFolder;
					default_data.stop = this.startFolder;
				} else if (currentStep == 'checkExtendFiles') {
					default_data.file = this.startFolder;
					default_data.stop = this.startFolder;
				} else {
					default_data.folder = this.startFolder;
				}
			}
		}
		
		
		jQuery.ajax({
			type: 'POST',
			url: 'index.php?option=com_rsfiles',
			data: default_data,
			dataType: 'json',
			success: function(response) {
				if (response.success == true) {
					if (RSFiles.Sync.parseCheckDetails(currentStep, response)) {
						return;
					}
					
					RSFiles.Sync.sync(index+1);
				} else {
					if (typeof response.data.message != 'undefined') {
						alert(response.data.message);
					}
				}
			}
		});
	},
	
	start: function() {
		RSFiles.Sync.sync(0);
	},
	
	stop: function() {
		window.setTimeout(function () {
			jQuery('#com-rsfiles-sync-progress').css('display','none');
			jQuery('#com-rsfiles-sync-text').html('');
			
			document.location.reload();
		}, 3000);
	},
	
	parseCheckDetails: function(step, json) {
		
		if (typeof json.data.text != 'undefined') {
			jQuery('#com-rsfiles-sync-text').html(json.data.text);
		}
		
		if (step == 'checkFolders') {
			if (json.data.stop) {
				// finished
				return false;
			} else {
				if (json.data.next_folder) {
					var stepIndex = this.steps.indexOf(this.currentStep);
					var next_folder = json.data.next_folder;
					RSFiles.Sync.sync(stepIndex, {'folder': next_folder});
					
					// not finished
					return true;
				}
			}
		} else if (step == 'checkFiles') {
			if (json.data.next_file) {
				var stepIndex = this.steps.indexOf(this.currentStep);
				var next_file = json.data.next_file;
				RSFiles.Sync.sync(stepIndex, {'file': next_file});
				
				// not finished
				return true;
			} else {
				if (json.data.stop) {
					// finished
					return false;
				}
			}
		} else if (step == 'checkDatabase') {
			// Nothing to do here
		} else if (step == 'checkExtendFolders') {
			if (json.data.stop) {
				// finished
				return false;
			} else {
				if (json.data.next_folder) {
					var stepIndex = this.steps.indexOf(this.currentStep);
					var next_folder = json.data.next_folder;
					RSFiles.Sync.sync(stepIndex, {'folder': next_folder, 'stop' : this.startFolder});
					
					// not finished
					return true;
				}
			}
		} else if (step == 'checkExtendFiles') {
			if (json.data.next_file) {
				var stepIndex = this.steps.indexOf(this.currentStep);
				var next_file = json.data.next_file;
				RSFiles.Sync.sync(stepIndex, {'file': next_file, 'stop' : this.startFolder});
				
				// not finished
				return true;
			} else {
				if (json.data.stop) {
					// finished
					return false;
				}
			}
		} else if (step == 'checkExtendExternal') {
			if (json.data.next_file) {
				var stepIndex = this.steps.indexOf(this.currentStep);
				var next_file = json.data.next_file;
				RSFiles.Sync.sync(stepIndex, {'file': next_file, 'stop' : this.startFolder});
				
				// not finished
				return true;
			} else {
				if (json.data.stop) {
					// finished
					return false;
				}
			}
		}
	}
};

function rsf_create() {
	if (jQuery('#newfolder').val().trim() == '') {
		return;
	}
	
	jQuery('#com-rsfiles-sync-progress').css('display','block');
	jQuery.ajax({
		type: 'POST',
		url: 'index.php?option=com_rsfiles',
		data: 'task=files.create&folder='+ jQuery('#newfolder').val().trim() + '&path=' + encodeURIComponent(jQuery('#path').val().trim()) + '&tmpl=component',
		dataType: 'json',
		success: function(response) {
			jQuery('#com-rsfiles-sync-progress').css('display','none');
			
			if (typeof response.data.message != 'undefined') {
				jQuery('#com-rsfiles-alert').html(response.data.message);
				jQuery('#com-rsfiles-alert').css('display','');
				setTimeout(function() {
					jQuery('#com-rsfiles-alert').animate({
						opacity: 0
					}, 500, function() {
						jQuery(this).css('display','none');
						jQuery(this).css('opacity','1');
						jQuery(this).empty();
					});
				},1500);
			}
			
			if (response.success == true) {
				setTimeout(function() {
					document.location.reload();
				},2000);
			}
		}
	});
}

function rsf_mirror(root, id) {
	if (id) {
		if (jQuery('#mname'+id).val().trim() == '' || jQuery('#murl'+id).val().trim() == '') {
			return;
		}
		
		var params = 'task=file.mirror&name='+ jQuery('#mname'+id).val().trim() + '&url=' + encodeURIComponent(jQuery('#murl'+id).val().trim()) + '&id=' + id + '&type=update&tmpl=component';
	} else {
		if (jQuery('#mname').val().trim() == '' || jQuery('#murl').val().trim() == '') {
			return;
		}
		
		var params = 'task=file.mirror&name='+ jQuery('#mname').val().trim() + '&url=' + encodeURIComponent(jQuery('#murl').val().trim()) + '&id=' + jQuery('#jform_IdFile').val().trim() + '&tmpl=component';
	}
	
	jQuery.ajax({
		type: 'POST',
		url: 'index.php?option=com_rsfiles',
		data: params,
		dataType: 'json',
		success: function(response) {
			if (response.success) {
				if (id) {
					jQuery('#sname'+id).html(jQuery('#mname'+id).val());
					jQuery('#surl'+id).html(jQuery('#murl'+id).val());
					jQuery('#actions'+id).html('<a href="javascript:void(0)" onclick="rsf_edit_mirror(\''+ root +'\','+ id +')"><img src="'+ root +'administrator/components/com_rsfiles/assets/images/icons/edit.png" alt="" /></a><a href="javascript:void(0)" onclick="rsf_delete_mirror('+ id +')"><img src="'+ root +'administrator/components/com_rsfiles/assets/images/icons/delete.png" alt="" /></a>');
				} else {
					tr = jQuery('<tr>', { 'id' : 'mirror'+response.data.id, 'class': 'row' + (jQuery('#file_mirror > tr').length % 2) });
					
					a1 = jQuery('<a>', { 'href': 'javascript:void(0)' }).on('click', function() {
						rsf_edit_mirror(root,response.data.id);
					});
					
					a2 = jQuery('<a>', { 'href': 'javascript:void(0)' }).on('click', function() {
						rsf_delete_mirror(response.data.id);
					});
					
					img1 = jQuery('<img>', { 'alt': '', 'src': root + 'administrator/components/com_rsfiles/assets/images/icons/edit.png' });
					img2 = jQuery('<img>', { 'alt': '', 'src': root + 'administrator/components/com_rsfiles/assets/images/icons/delete.png'	});
					
					a1.append(img1);
					a2.append(img2);
					
					span1 = jQuery('<span>', { 'id' : 'sname'+response.data.id, 'text' : jQuery('#mname').val() });
					span2 = jQuery('<span>', { 'id' : 'surl'+response.data.id, 'text' : jQuery('#murl').val() });
					span3 = jQuery('<span>', { 'id' : 'actions'+response.data.id });
					
					span3.append(a1);
					span3.append(a2);
					
					td1 = jQuery('<td>');
					td2 = jQuery('<td>');
					td3 = jQuery('<td>', { 'class' : 'center', 'align' : 'center' });
					
					td1.append(span1);
					td2.append(span2);
					
					td3.append(span3);
					tr.append(td1);
					tr.append(td2);
					tr.append(td3);
					
					jQuery('#file_mirror').append(tr);
					
					jQuery('#mname').val('');
					jQuery('#murl').val('');
				}
			}
		}
	});
}

function rsf_delete_mirror(id) {
	jQuery.ajax({
		type: 'POST',
		url: 'index.php?option=com_rsfiles',
		data: 'task=file.deletemirror&id=' + id + '&tmpl=component',
		dataType: 'json',
		success: function(response) {
			if (response.success) {
				jQuery('#mirror'+id).remove();
			}
		}
	});
}

function rsf_edit_mirror(root,id) {
	jQuery('#sname'+id).html('<input type="text" id="mname' + id + '" name="name" value="' + jQuery('#sname'+id).html() + '" class="input-large" size="20" />');
	jQuery('#surl'+id).html('<input type="text" id="murl' + id + '" name="url" value="' + jQuery('#surl'+id).html() + '" class="input-xxlarge" size="50" />');
	jQuery('#actions'+id).html('<button type="button" class="btn btn-primary button" onclick="rsf_mirror(\''+root+'\','+id+')">'+ Joomla.JText._('COM_RSFILES_SAVE') +'</button> <button type="button" class="btn button" onclick="rsf_mirror_cancel(\''+root+'\','+ id +')">'+ Joomla.JText._('COM_RSFILES_CANCEL') +'</button>');
}

function rsf_mirror_cancel(root, id) {
	jQuery.ajax({
		type: 'POST',
		url: 'index.php?option=com_rsfiles',
		data: 'task=file.getmirror&id=' + id + '&tmpl=component',
		dataType: 'json',
		success: function(response) {
			if (response.success) {
				jQuery('#sname'+id).html(response.data.MirrorName);
				jQuery('#surl'+id).html(response.data.MirrorURL);
				jQuery('#actions'+id).html('<a href="javascript:void(0)" onclick="rsf_edit_mirror(\''+ root +'\','+ id +')"><img src="'+ root +'administrator/components/com_rsfiles/assets/images/icons/edit.png" alt="" /></a><a href="javascript:void(0)" onclick="rsf_delete_mirror('+ id +')"><img src="'+ root +'administrator/components/com_rsfiles/assets/images/icons/delete.png" alt="" /></a>');
			}
		}
	});
}

function rsf_add_files() {
	var input = jQuery('<input>', {
		'type' : 'file',
		'name' : 'screenshot[]',
		'size' : '40',
		'class': 'input-large'
	});
	
	jQuery('#rsf_files').append(jQuery('<br>'));
	jQuery('#rsf_files').append(input);
}

function rsf_delete_screenshot(id) {
	jQuery.ajax({
		type: 'POST',
		url: 'index.php?option=com_rsfiles',
		data: 'task=file.deletescreenshot&id=' + id + '&tmpl=component',
		dataType: 'json',
		success: function(response) {
			if (response.success) {
				jQuery('#screenshot'+id).remove();
			} else {
				if (response.data.message != 'undefined') {
					alert(response.data.message);
				}
			}
		}
	});
}

function statistics(id) {
	jQuery('#cb'+id).prop('checked', true);
	Joomla.submitbutton('files.statistics');
}

// Create briefcase folder
function jSelectUser_addusers(id,name) {
	if (id == '') { 
		window.parent.SqueezeBox.close();
		return;
	}
	
	jQuery.ajax({
		type: 'POST',
		url: 'index.php?option=com_rsfiles',
		data: 'task=files.briefcase&id=' + id + '&tmpl=component',
		dataType: 'json',
		success: function(response) {
			if (response.data.message != 'undefined') {
				alert(response.data.message);
			}
			
			if (response.success) {
				window.parent.location.reload();
			}
		}
	});
}

function rsf_rsmail(id,nameSelect) {
	if (id == 0) {
		if (jQuery('#jform_rsmail_field_name').parent().hasClass('controls')) {
			jQuery('#jform_rsmail_field_name').parent().parent().css('display','none');
		} else {
			jQuery('#jform_rsmail_field_name').parent().css('display','none');
		}
		return;
	}
	
	jQuery.ajax({
		type: 'POST',
		url: 'index.php?option=com_rsfiles',
		data: 'task=settings.fields&id=' + id + '&tmpl=component',
		dataType: 'json',
		success: function(response) {
			if (jQuery('#jform_rsmail_field_name').parent().hasClass('controls')) {
				jQuery('#jform_rsmail_field_name').parent().parent().css('display','');
			} else {
				jQuery('#jform_rsmail_field_name').parent().css('display','');
			}
			
			jQuery('#jform_rsmail_field_name').empty();
			jQuery(response).each(function (i,el) {
				jQuery('#jform_rsmail_field_name').append(jQuery('<option></option>').val(el).html(el));
			});
			
			if (nameSelect) {
				jQuery('#jform_rsmail_field_name [value="'+nameSelect+'"]').prop('selected',true);
			}
			
			if (typeof jQuery.fn.chosen == 'function') {
				jQuery("#jform_rsmail_field_name").trigger("liszt:updated");
			}
		}
	});
}

function rsf_single_upload() {
	window.location = jQuery('#singleupload').text();
}

jQuery(window).ready(function() {
	if (jQuery('#newfolder').length) {
		jQuery('#newfolder').on('keypress', function (e) {
			if (e.keyCode == 13) {
				rsf_create();
				return false;
			}
		});
	}
});