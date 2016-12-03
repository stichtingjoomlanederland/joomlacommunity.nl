function rsc_add_emoticon(root) {
	jQuery.ajax({
		url: 'index.php?option=com_rscomments',
		type: 'post',
		data: 'task=emoticons.add',
		success: function(data){
            if (data) {
				var container	= document.getElementById('emoticons_container');
				var rand		= data;
				
				var tr  = document.createElement('tr');
				var td1 = document.createElement('td');
				var td2 = document.createElement('td');
				var td3 = document.createElement('td');
				var td4 = document.createElement('td');
				
				var rows = document.getElementById('emoticons_container').children.length;
				tr.id = 'row'+rand;
				tr.className = 'row'+(rows % 2);
				
				td2.className = 'center';
				td2.align = 'center';
				td3.className = 'center';
				td3.align = 'center';
				td4.className = 'center';
				td4.align = 'center';
				
				container.appendChild(tr);
				tr.appendChild(td1);
				tr.appendChild(td2);
				tr.appendChild(td3);
				tr.appendChild(td4);
				
				var input1 = document.createElement('input');
				input1.type = 'text';
				input1.id = 'symbol'+rand;
				input1.className = 'input-small';
				input1.size = '10';
				input1.name = 'symbol['+rand+']';
				input1.style.textAlign = 'center';
				
				var input2 = document.createElement('input');
				input2.type = 'text';
				input2.id = 'image'+rand;
				input2.className = 'input-xxlarge';
				input2.size = '50';
				input2.name = 'image['+rand+']';
				
				var image1 = document.createElement('img');
				image1.id = 'preview'+rand;
				image1.src = '';
				image1.alt = '';
				image1.className = 'rsc_emoticon_image';
				image1.style.display = 'none';
				
				var button1 = document.createElement('button');
				var text1	= document.createTextNode(Joomla.JText._('COM_RSCOMMENTS_EDIT'));
				var button2 = document.createElement('button');
				var text2	= document.createTextNode(Joomla.JText._('COM_RSCOMMENTS_DELETE'));
				var button3 = document.createElement('button');
				var text3	= document.createTextNode(Joomla.JText._('COM_RSCOMMENTS_SAVE'));
				var button4 = document.createElement('button');
				var text4	= document.createTextNode(Joomla.JText._('COM_RSCOMMENTS_CANCEL'));
				
				// Set button type
				button1.type = 'button';
				button2.type = 'button';
				button3.type = 'button';
				button4.type = 'button';
				
				// Set buttons class name
				button1.className = 'btn button';
				button2.className = 'btn button';
				button3.className = 'btn button';
				button4.className = 'btn button';
				
				// Set buttons id
				button1.id = 'edit'+rand;
				button2.id = 'delete'+rand;
				button3.id = 'save'+rand;
				button4.id = 'cancel'+rand;
				
				button1.style.display = 'none';
				button4.style.display = 'none';
				
				// Set events
				button1.onclick = function () {
					rsc_edit_emoticon(rand);
				}
				
				button2.onclick = function () {
					rsc_delete_emoticon(rand);
				}
				
				button3.onclick = function () {
					rsc_save_emoticon(rand);
				}
				
				button4.onclick = function () {
					rsc_cancel_emoticon(rand);
				}
				
				
				button1.appendChild(text1);
				button2.appendChild(text2);
				button3.appendChild(text3);
				button4.appendChild(text4);
				
				td1.appendChild(input1);
				td2.appendChild(image1);
				td2.appendChild(document.createTextNode('\u00A0'));
				td2.appendChild(input2);
				td3.appendChild(button1);
				td3.appendChild(document.createTextNode('\u00A0'));
				td3.appendChild(button3);
				td3.appendChild(document.createTextNode('\u00A0'));
				td3.appendChild(button2);
				td3.appendChild(document.createTextNode('\u00A0'));
				td3.appendChild(button4);
				
				var image2 = document.createElement('img');
				image2.id = 'loader'+rand;
				image2.src = root + 'components/com_rscomments/assets/images/loader.gif';
				image2.alt = '';
				image2.style.display = 'none';
				
				td4.appendChild(image2);
				
				var cont = jQuery('#emoticons_container');
				var scrollTo = jQuery('#row'+rand);
				window.scroll(0,scrollTo.offset().top - cont.offset().top + cont.scrollTop());
			}
		}
	});
}

function rsc_edit_emoticon(id) {
	jQuery('#symbol'+id).prop('disabled',false);
	jQuery('#image'+id).prop('disabled',false);
	
	jQuery('#edit'+id).css('display','none');
	jQuery('#delete'+id).css('display','none');
	
	jQuery('#save'+id).css('display','');
	jQuery('#cancel'+id).css('display','');
}

function rsc_delete_emoticon(id) {
	jQuery('#loader'+id).css('display','');
	
	jQuery.ajax({
		url: 'index.php?option=com_rscomments',
		type: 'post',
		data: 'task=emoticons.delete&id=' + id,
		success: function(data){
            if (data == 1) {
				jQuery('#row'+id).remove();
			}
		}
	});
}

function rsc_save_emoticon(id) {
	
	if (jQuery('#symbol'+id).val() == '' || jQuery('#image'+id).val() == '') {
		alert(Joomla.JText._('COM_RSCOMMENTS_EMOTICONS_EMPTY_VALUES'));
		return false;
	}
	
	jQuery('#loader'+id).css('display','');
	
	jQuery.ajax({
		url: 'index.php?option=com_rscomments',
		type: 'post',
		dataType : 'json',
		data: 'task=emoticons.save&replace=' + encodeURIComponent(jQuery('#symbol'+id).val()) + '&with=' + encodeURIComponent(jQuery('#image'+id).val()) + '&id=' + id,
		success: function(data) {
            if (data.success) {
				jQuery('#symbol'+id).prop('disabled', true);
				jQuery('#image'+id).prop('disabled', true);
				
				jQuery('#edit'+id).css('display','');
				jQuery('#delete'+id).css('display','');
				
				jQuery('#save'+id).css('display','none');
				jQuery('#cancel'+id).css('display','none');
				
				jQuery('#preview'+id).prop('src',data.image);
				jQuery('#preview'+id).css('display','');
				
			} else {
				if (typeof data.error != 'undefined') {
					alert(data.error);
				}
			}
			
			jQuery('#loader'+id).css('display','none');
		}
	});
}

function rsc_cancel_emoticon(id) {
	jQuery('#symbol'+id).prop('disabled',true);
	jQuery('#image'+id).prop('disabled',true);
	
	jQuery('#edit'+id).css('display','');
	jQuery('#delete'+id).css('display','');
	
	jQuery('#save'+id).css('display','none');
	jQuery('#cancel'+id).css('display','none');
}