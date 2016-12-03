ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	$.Joomla( 'submitbutton' , function( action ){
		$.Joomla( 'submitform' , [action] );
	});

	$('#activerule').val("select");

	$('#accordion-toggle-select').click( function() {
		$('#activerule').val("select");
		groupCollapse('select');
	});

	$('#accordion-toggle-view').click( function() {
		$('#activerule').val("view");
		groupCollapse('view');
	});

	$('#accordion-toggle-reply').click( function() {
		$('#activerule').val("reply");
		groupCollapse('reply');
	});

	$('#accordion-toggle-viewreply').click( function() {
		$('#activerule').val("viewreply");
		groupCollapse('viewreply');
	});

	$('#accordion-toggle-moderate').click( function() {
		$('#activerule').val("moderate");
		groupCollapse('moderate');
	});

	$('#category-acl-assign-group').click( function() {
		categoryAclAssign('group');
	});

	$('#category-acl-assign-user').click( function() {
		categoryAclAssign('user');
	});


	$('[data-category-browse-user]').click( function() {

	    EasyDiscuss.dialog({
	        content: EasyDiscuss.ajax('admin/views/users/display')
	    });
    });


	window.selectUser = function(id , name, prefix)
	{
		addpaneluser(id, name, prefix);

		// Close dialog
		// $.Joomla('squeezebox').close();
	};

	function addpaneluser(id, name, prefix) {

		var users = $(":input[name='" + prefix + "_panel_user[]']");
		var doinsert = true;

		if (users.length > 0) {
			for (c = 0; c < users.length; c++) {
				var	ele	= users[c];
				var cid	= $(ele).val();

				if (cid == id) {
					doinsert = false;
					break;
				}
			}
		}

		if (doinsert) {
			var input = '<li id="user-li-' + id + '">';
			input += '<input type="checkbox" name="' + prefix + '_panel_user[]" value="' + id + '" checked="checked" />';
			input += '<input type="hidden" id="' + prefix + '_panel_user_' + id + '" value="' + name + '" />';
			input += name;
			input += '</li>';

			$('#cat-' + prefix + '-panel-user-ul')
				.append(input);
		}
	//end addpaneluser
	}

	function groupCollapse(type)
	{
		if ($('#collapse-'+type).hasClass("in")) {
			$('#collapse-'+type).removeClass("in");
			$('#collapse-'+type).addClass(" collapse");
		} else {
			$('#collapse-'+type).removeClass("collapse");
			$('#collapse-'+type).addClass(" in");
		}
	}


	function categoryAclAssign(type)
	{
		var action	= $('#activerule').val();
		var items = $(":input[name='acl_panel_"+ type + "[]']:checked");

		if( items != null )
		{
			for(i = 0; i < items.length; i++)
			{
				var ele = items[i];
				var id = $(ele).val();
				var text = $("#acl_panel_" + type + "_" + id).val();

				var doinsert = true;
				var curProcessItem = $(":input[name='acl_" + type + "_" + action + "[]']");

				if( curProcessItem.length > 0 )
				{
					for(c = 0; c < curProcessItem.length; c++)
					{
						var cele = curProcessItem[c];
						if( cele.value == id )
						{
							doinsert = false;
							break;
						}
					}
				}

				if( doinsert )
				{
					var input = '<li id="acl_' + type + '_' + action + '_' + id + '">';
					input += '<span><a href="javascript:void(0)" onclick="aclremove(\'acl_' + type + '_' + action + '_' + id + '\');">Delete</a></span>';
					input += ' - ' + text;
					input += '<input type="hidden" name="acl_'+ type + '_' + action + '[]" value="' + id + '" />';
					input += '</li>';

					$('#category_acl_' + type + '_' + action)
						.append(
							input
						);
				}
			}//end for i
		}//end if type is null
	}

	window.aclremove = function(id) {
		$('#' + id).remove();
	}
});