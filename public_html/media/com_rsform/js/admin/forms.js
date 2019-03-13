/* editModal functions */
RSFormPro.editModal = {
	selector: '#editModal',
	
	disableButton: function() {
		jQuery(this.selector).find('.btn-primary').prop('disabled', true);
	},
	
	enableButton: function() {
		jQuery(this.selector).find('.btn-primary').prop('disabled', false);
	},
	
	open: function() {
		jQuery(this.selector).modal('show');
		
		// Focus on textbox once
		var focused = false;
		jQuery(this.selector).on('shown.bs.modal', function() {
			if (!focused && jQuery('#NAME').length > 0) {
				focused = true;
				jQuery('#NAME').focus();
			}
		});
	},
	
	close: function() {
		jQuery(this.selector).modal('hide');
	},
	
	save: function() {
		var componentType = parseInt(jQuery('#COMPONENTTYPE').val());
		processComponent(componentType);
	},
	
	display: function(componentTypeId, componentId) {
		// Let's add a title to our modal
		var $component = jQuery('#rsfpc' + componentTypeId);
		
		if ($component.length > 0)
		{
			jQuery(this.selector).find('.modal-header h3').text($component.text());
			
			if (!componentId)
			{
				$component.addClass('rsform_loading_btn');
			}
		}
		
		var urlParams = {
			option: 'com_rsform',
			task: 'components.display',
			componentType: componentTypeId,
			formId: jQuery('#formId').val(),
			format: 'raw',
			randomTime: Math.random()
		};

		if (componentId > 0)
		{
			urlParams.componentId = componentId;
			document.getElementById('componentIdToEdit').value = componentId;
		}
		else
		{
			document.getElementById('componentIdToEdit').value = -1;
		}
		
		jQuery.ajax({
			url: 'index.php',
			data: urlParams,
			beforeSend: function() {
				stateLoading();
			},
			complete: function() {
				stateDone();
			},
			success: function(responseText) {
				$component.removeClass('rsform_loading_btn');
				var response = responseText.split('{rsfsep}');
				
				// Display tabs
				for (var r = 0; r < response.length; r++)
				{
					jQuery('[href="#rsfptab' + r + '"]').show();
					jQuery('#rsfptab' + r).html(response[r]);
					
					if (response[r].trim() == '')
					{
						jQuery('[href="#rsfptab' + r + '"]').hide();
					}
				}
				
				// Switch to 1st tab.
				jQuery('[href="#rsfptab0"]').click();

				changeValidation(document.getElementById('VALIDATIONRULE'));

				var $fields = jQuery('[data-properties="oneperline"], [data-properties="toggler"]');
				var $object = {};
				jQuery.each($fields, function () {
					var $name = jQuery(this).attr('id');

					$object[$name] = {
						selector: $name
					};

					if (jQuery(this).attr('data-properties') == 'toggler') {
						$object[$name].data = jQuery.parseJSON( jQuery(this).attr('data-toggle') );
					}
				});

				jQuery('.rsform_hide').trigger('renderedLayout', $object);
				
				RSFormPro.editModal.open();
			}
		});
	}
};

/* formTabs */
jQuery.formTabs = {
	tabTitles: {},
	tabContents: {},

	build: function (startindex) {
		this.each(function (index, el) {
			var tid = jQuery(el).attr('id');
			jQuery.formTabs.grabElements(el,tid);
			jQuery.formTabs.makeTitlesClickable(tid);
			jQuery.formTabs.setAllContentsInactive(tid);
			jQuery.formTabs.setTitleActive(startindex,tid);
			jQuery.formTabs.setContentActive(startindex,tid);
		});
	},

	grabElements: function(el,tid) {
		var children = jQuery(el).children();
		children.each(function(index, child) {
			if (index == 0)
				jQuery.formTabs.tabTitles[tid] = jQuery(child).find('a');
			else if (index == 1)
				jQuery.formTabs.tabContents[tid] = jQuery(child).children();
		});
	},

	setAllTitlesInactive: function (tid) {
		this.tabTitles[tid].each(function(index, title) {
			jQuery(title).removeClass('active');
		});
	},

	setTitleActive: function (index,tid) {
		index = parseInt(index);
		if (tid == 'rsform_tab2') document.getElementById('ptab').value = index;
		jQuery(this.tabTitles[tid][index]).addClass('active');
	},

	setAllContentsInactive: function (tid) {
		this.tabContents[tid].each(function(index, content) {
			jQuery(content).hide();
		});
	},

	setContentActive: function (index,tid) {
		index = parseInt(index);
		jQuery(this.tabContents[tid][index]).show();
		
		jQuery(this.tabContents[tid][index]).trigger('formtabs.shown');
	},

	makeTitlesClickable: function (tid) {
		this.tabTitles[tid].each(function(index, title) {
			jQuery(title).on('click', function () {
				if (jQuery(this).css('cursor') == 'not-allowed')
				{
					// Do nothing if we've "disabled" the button
					return false;
				}

				jQuery.formTabs.setAllTitlesInactive(tid);
				jQuery.formTabs.setTitleActive(index,tid);

				jQuery.formTabs.setAllContentsInactive(tid);
				jQuery.formTabs.setContentActive(index,tid);
			});
		});
	}
}

jQuery.fn.extend({
	formTabs: jQuery.formTabs.build
});

/* gridModal functions */
RSFormPro.gridModal = {
	selector: '#gridModal',
	
	params: [],
	
	open: function(element, is_new_row) {
		this.params = [element, is_new_row];
		
		jQuery(this.selector).modal('show');
	},
	
	close: function() {
		jQuery(this.selector).modal('hide');
	},
	
	save: function(new_columns) {
		var element = this.params[0],
			row = jQuery(element).parents('.rsfp-grid-row'),
			columns = row.children('.rsfp-grid-column'),
			size = new_columns[0],
			is_new_row = typeof this.params[1] != 'undefined' ? this.params[1] : false;
		
		// Added new row
		if (is_new_row)
		{
			row.after([
				'<div class="rsfp-grid-row">',
					'<div class="rsfp-grid-column rsfp-grid-column' + size + '">',
						'<h3>' + size + '/12</h3>',
					'</div>',
					'<div class="clearfix"></div>',
					'<div class="rsfp-row-controls">',
						'<button type="button" class="btn" onclick="RSFormPro.gridModal.open(this);">' + Joomla.JText._('RSFP_ROW_OPTIONS') + '</button>',
						'<button type="button" class="btn btn-success" onclick="RSFormPro.gridModal.open(this, true);">' + Joomla.JText._('RSFP_ADD_NEW_ROW') + '</button>',
						'<button type="button" class="btn btn-danger" onclick="RSFormPro.Grid.deleteRow(this);">' + Joomla.JText._('RSFP_DELETE_ROW') + '</button>',
					'</div>',
				'</div>'
			].join("\n"));
			
			row = row.next('.rsfp-grid-row');
			columns = row.children('.rsfp-grid-column');
		}
		
		// Process only if we've changed column sizes or if we've added a new row
		if (columns.length != new_columns.length || is_new_row)
		{
			columns.removeClass(function(index, className) {
				return (className.match(/(^|\s)rsfp-grid-column\S+/g) || []).join(' ');
			}).removeClass('rsfp-grid-column-unresizable');
			
			// Add new class
			columns.addClass('rsfp-grid-column' + size);
			
			// Change h3 text
			columns.find('h3').text(size + '/12');
			
			// We've selected a layout with more columns, just add empty columns
			if (columns.length < new_columns.length)
			{
				var diff = new_columns.length - columns.length;
				
				for (var i = 0; i < diff; i++)
				{
					// Refresh
					columns = row.children('.rsfp-grid-column');
					
					columns.last().after('<div class="rsfp-grid-column rsfp-grid-column' + size + '"><h3>' + size + '/12</h3></div>');
				}
			}
			// We've selected a layout with less columns, must move fields inside closest column
			else if (columns.length > new_columns.length)
			{
				var diff = columns.length - new_columns.length;
				
				for (var i = 0; i < diff; i++)
				{
					// Refresh
					columns = row.children('.rsfp-grid-column');
					
					// Grab fields from last column and add them to the previous - keep doing that until we reach the last column
					columns.last().prev('.rsfp-grid-column').append(
						columns.last().children('.rsfp-grid-field')
					);
						
					columns.last().remove();
				}
			}
			
			// Last one must be unresizable
			row.children('.rsfp-grid-column').last().addClass('rsfp-grid-column-unresizable');
		}
		
		RSFormPro.Grid.initialize();
		
		this.close();
	}
};

RSFormPro.Grid = {
	// Make rows sortable (Y axis)
	currentSortableRowsObj: false,
	sortableRows: function() {
		var rows = jQuery('#rsfp-grid-row-container');
		if (this.currentSortableRowsObj)
		{
			try
			{
				this.currentSortableRowsObj.sortable('destroy');
			}
			catch (err){}		
		}
		
		this.currentSortableRowsObj = rows.sortable({
			placeholder: 'rsfp-grid-row-placeholder',
			items: '.rsfp-grid-row:not(.rsfp-grid-row-unsortable)',
			forcePlaceholderSize: true,
			axis: 'y',
			opacity: 0.8,
			tolerance: 'pointer',
			start: function(event, ui) {
				ui.item.css({
					'left'				: '50%',
					'-webkit-transform' : 'translateX(-50%)',
					'-moz-transform' 	: 'translateX(-50%)',
					'transform' 		: 'translateX(-50%)'
				});
				ui.item.closest('.rsfp-grid-row').find('.rsfp-grid-row-placeholder').css({
					'margin-left'	: 'auto',
					'margin-right'	: 'auto',
					'width'			: ui.item.width()
				});
			},
			stop: function(event, ui) {
				ui.item.removeAttr('style');
				
				// Save the layout
				RSFormPro.Grid.toJson();
			}
		}).disableSelection();
	},
	
	// Make elements (fields) sortable
	currentSortableElementsObj: false,
	sortableElements: function() {
		if (this.currentSortableElementsObj)
		{
			try
			{
				this.currentSortableElementsObj.sortable('destroy');
			}
			catch (err){}
		}
		
		var columns = jQuery('.rsfp-grid-column');
		
		this.currentSortableElementsObj = columns.sortable({
			connectWith: columns.not('.rsfp-grid-column-unconnectable'),
			items: '.rsfp-grid-field:not(.rsfp-grid-field-unsortable)', // Disable items that cannot be removed from container row (eg. pagebreaks)
			placeholder: 'rsfp-grid-field-placeholder',
			forcePlaceholderSize: true,
			opacity: 0.8,
			tolerance: 'pointer',
			start: function(event, ui) {
				ui.item.css('margin-bottom', '0');
			},
			over: function(event, ui) {
				ui.item.css('width', ui.placeholder.width());
			},
			stop: function(event, ui) {
				ui.item.removeAttr('style');
				
				// Save the layout
				RSFormPro.Grid.toJson();
			}
		}).disableSelection();
	},
	
	// Make hidden fields sortable
	currentSortableHiddenElementsObj: false,
	sortableHiddenElements: function() {
		if (this.currentSortableHiddenElementsObj)
		{
			try
			{
				this.currentSortableHiddenElementsObj.sortable('destroy');
			}
			catch (err){}
		}
		
		var container = jQuery('#rsfp-grid-hidden-container');
		
		this.currentSortableHiddenElementsObj = container.sortable({
			connectWith: container,
			items: '.rsfp-grid-field', // Disable items that cannot be removed from container row (eg. pagebreaks)
			placeholder: 'rsfp-grid-field-placeholder',
			forcePlaceholderSize: true,
			opacity: 0.8,
			tolerance: 'pointer',
			start: function(event, ui) {
				ui.item.css('margin-bottom', '0');
			},
			over: function(event, ui) {
				ui.item.css('width', ui.placeholder.width());
			},
			stop: function(event, ui) {
				ui.item.removeAttr('style');
				
				// Save the layout
				RSFormPro.Grid.toJson();
			}
		}).disableSelection();
	},
	
	// Allow columns to be resized
	currentResizableObj: false,
	resizableGrid: function() {
		var obj = jQuery('.rsfp-grid-column:not(.rsfp-grid-column-unresizable)');
		
		// Remove all previously resizable columns
		if (this.currentResizableObj)
		{
			try
			{
				this.currentResizableObj.resizable('destroy');
			}
			catch (err){}
		}
		
		var
			columns = 12,
			fullWidth = obj.parent().width(),
			columnWidth = fullWidth / columns,
			totalCol, // this is filled by start event handler
			updateClass = function(el, col) {
				el.css('width', ''); // remove width, our class already has it
				el.css('height', ''); // remove inline height
				el.removeClass(function(index, className) {
					return (className.match(/(^|\s)rsfp-grid-column\S+/g) || []).join(' ');
				}).addClass('rsfp-grid-column' + col);
			};

	  this.currentResizableObj = obj.resizable({
		containment: obj.parent(),
		handles: 'e',
		create: function(event, ui) {
			var $target = jQuery(event.target);
			var $handle = $target.find('.ui-resizable-handle');
			var size = $target.next('.rsfp-grid-column').css('margin-left');
			
			$handle.css({width: size, right: '-' + size});
		},
		start: function(event, ui) {
		  var
			target = ui.element,
			next = target.next('.rsfp-grid-column'),
			targetCol = Math.round(target.width() / columnWidth),
			nextCol = Math.round(next.width() / columnWidth);
			
		  // set totalColumns globally
		  totalCol = targetCol + nextCol;
		  
		  var gridTotal = 0;
		  target.parent().children('.rsfp-grid-column').each(function(index, element){
			  var match = jQuery(element).attr('class').match(/rsfp-grid-column([0-9]+)/) || [];
			  var size = typeof match[1] != 'undefined' ? parseInt(match[1]) : 0;
			  
			  gridTotal += size;
		  });
		  
		  if (gridTotal < 12)
		  {
			  totalCol += 12 - gridTotal;
		  }
		  else if (gridTotal > 12)
		  {
			  // Something went wrong, let's re-create the column sizes
			  var eqSize = 12 / target.parent().children('.rsfp-grid-column').length;
			  
			  target.parent().children('.rsfp-grid-column').addClass('rsfp-grid-column' + eqSize);
		  }
		  
		  target.resizable('option', 'minWidth', columnWidth);
		  target.resizable('option', 'maxWidth', ((totalCol - 1) * columnWidth));
		},
		resize: function(event, ui) {
		  var
			target = ui.element,
			next = target.next('.rsfp-grid-column'),
			targetColumnCount = Math.round(target.width() / columnWidth),
			nextColumnCount = Math.round(next.width() / columnWidth),
			targetSet = totalCol - nextColumnCount,
			nextSet = totalCol - targetColumnCount;
			
		  // Just showing class names inside headings
		  target.find('h3').text(targetSet + '/12');
		  next.find('h3').text(nextSet + '/12');

		  updateClass(target, targetSet);
		  updateClass(next, nextSet);
		},
		
		stop: function(event, ui) {
			ui.element.removeAttr('style');
			
			// Save the layout
			RSFormPro.Grid.toJson();
		}
	  });
	},
	
	clipboard: [],
	
	rightClickMenu: function() {
		if (!this.initialized)
		{
			var $menu = jQuery('<ul class="dropdown-menu" id="rsfp-grid-contextmenu">' +
				  '<li id="rsfp-grid-contextmenu-cut"><a href="#"><span class="icon-scissors"></span><span class="rsfp-text">' + Joomla.JText._('RSFP_GRID_CUT') + '</span></a></li>' +
				  '<li id="rsfp-grid-contextmenu-paste"><a href="#" class="disabled"><span class="icon-copy"></span><span class="rsfp-text">' + Joomla.JText._('RSFP_GRID_NOTHING_TO_PASTE') + '</span></a></li>' +
				'</ul>');

			jQuery('body').append($menu);
			jQuery('body').on('click', function(e){
				$menu.hide();
			});
		}
		else
		{
			var $menu = jQuery('#rsfp-grid-contextmenu');
		}
		
		var $cut = $menu.find('#rsfp-grid-contextmenu-cut');
		var $paste = $menu.find('#rsfp-grid-contextmenu-paste')
		
		// Right clicking on a field should bring up a menu - only fields that are neither hidden fields nor pages.
		jQuery('.rsfp-grid-row').not('.rsfp-grid-row-unsortable').find('.rsfp-grid-field').not('.rsfp-grid-field-unsortable').off('contextmenu').on('contextmenu', function(e){
			var $this = jQuery(this);
			
			// Clicking on "Cut" will add this field to the clipboard
			$cut.children('a').removeClass('disabled');
			$cut.off('click').on('click', function(e){
				e.preventDefault();

				// Add to clipboard
				RSFormPro.Grid.clipboard.push($this);
				
				// Hide the field
				$this.hide();
				
				// Hide the menu
				$menu.hide();
			});

			// Clicking on "Paste" will paste all fields from the clipboard below the current element
			$paste.off('click').on('click', function(e){
				e.preventDefault();

				// Loop through all items in the clipboard and show them
				jQuery(RSFormPro.Grid.clipboard).each(function(index, item){
					jQuery(item).show();
				});

				// Add them after the current element
                $this.after(RSFormPro.Grid.clipboard);
				
				// Empty the clipboard
				RSFormPro.Grid.clipboard = [];
				
				// Hide the menu
				$menu.hide();

				// Save the Grid
				RSFormPro.Grid.toJson();
			});
			
			// If we don't have anything in the clipboard, the 'Paste' link must be disabled
			if (RSFormPro.Grid.clipboard.length > 0)
			{
				$paste.children('a').removeClass('disabled');
				$paste.find('.rsfp-text').text(Joomla.JText._('RSFP_GRID_PASTE_ITEMS').replace('%d', RSFormPro.Grid.clipboard.length));
			}
			else
			{
				$paste.children('a').addClass('disabled');
				$paste.find('.rsfp-text').text(Joomla.JText._('RSFP_GRID_NOTHING_TO_PASTE'));
			}

			// Show the menu next to the cursor
			$menu.css({
				left: e.pageX,
				top: e.pageY
			}).show();
			
			return false;
		});
		
		// Right clicking on an empty row should bring up a menu so we can paste the clipboard fields in there
		jQuery('.rsfp-grid-column').each(function(index, column){
			jQuery(column).off('contextmenu').on('contextmenu', function(e){
				var $this = jQuery(this);

				if ($this.children('.rsfp-grid-field:visible').length == 0)
				{
					// The "Cut" link should be disabled since we can't cut an entire row
					$cut.children('a').addClass('disabled');

					// Remove the click event
					$cut.off('click');

					// Clicking on "Paste" should append the clipboard items to this row
					$paste.off('click').on('click', function(e){
						e.preventDefault();
						
						// Loop through all items in the clipboard and append them to the current row
						jQuery(RSFormPro.Grid.clipboard).each(function(index, item){
							$this.append(jQuery(item).show());
						});
						
						// Empty the clipboard
						RSFormPro.Grid.clipboard = [];

						// Hide the menu
						$menu.hide();

						// Save the Grid
						RSFormPro.Grid.toJson();
					});

					// If we don't have anything in the clipboard, the 'Paste' link must be disabled
					if (RSFormPro.Grid.clipboard.length > 0)
					{
						$paste.children('a').removeClass('disabled');
						$paste.find('.rsfp-text').text(Joomla.JText._('RSFP_GRID_PASTE_ITEMS').replace('%d', RSFormPro.Grid.clipboard.length));
					}
					else
					{
						$paste.children('a').addClass('disabled');
						$paste.find('.rsfp-text').text(Joomla.JText._('RSFP_GRID_NOTHING_TO_PASTE'));
					}

					// Show the menu next to the cursor
					$menu.css({
						left: e.pageX,
						top: e.pageY
					}).show();
				}
				
				return false;
			});
		});
	},
	
	initialized: false,
	
	initialize: function() {
		RSFormPro.Grid.sortableElements();
		RSFormPro.Grid.sortableHiddenElements();
		RSFormPro.Grid.sortableRows();
		RSFormPro.Grid.resizableGrid();
		RSFormPro.Grid.rightClickMenu();
		
		this.initialized = true;
		
		// Save the layout
		RSFormPro.Grid.toJson();
	},

	hide: function() {
		jQuery('#gridlayout').css('cursor', 'not-allowed');

	},

	show: function() {
        jQuery('#gridlayout').css('cursor', '');
	},
	
	resize: function(e) {
		if (e.target == window && RSFormPro.Grid.initialized && jQuery('#gridlayoutdiv').is(':visible')) {
			RSFormPro.Grid.initialize();
		}
	},
	
	deleteField: function(componentId) {
		var $field = jQuery('#rsfp-grid-field-id-' + componentId);

		// Is this a page? Remove the row
		if ($field.parents('.rsfp-grid-page-container').length > 0)
		{
			$field.parents('.rsfp-grid-page-container').remove();
		}
		else
		{
			// Remove the field from the grid
			$field.remove();
		}

		RSFormPro.Grid.toJson();
	},
	
	deleteRow: function(element) {
		var row = jQuery(element).parents('.rsfp-grid-row');
		
		if (row.find('.rsfp-grid-field').length > 0)
		{
			alert(Joomla.JText._('RSFP_GRID_CANNOT_REMOVE_ROW'));
		}
		else
		{
			if (confirm(Joomla.JText._('RSFP_GRID_REMOVE_ROW_CONFIRM')))
			{
				row.remove();
			}
		}

		// Save the layout
		RSFormPro.Grid.toJson();
	},
	
	toJson: function() {
		var rows = [],
			hidden = [];
		
		jQuery('#rsfp-grid-row-container > .rsfp-grid-row').not('.rsfp-grid-row-unsortable').each(function(rowId, row) {
			rows[rowId] = {
				columns: [],
				sizes: []
			};
			
			jQuery(row).children('.rsfp-grid-column').each(function(columnId, column) {
				var match = column.className.match(/rsfp-grid-column([0-9]+)/) || [];
				if (match.length > 0)
				{
					var size = match[1];
					
					rows[rowId].columns[columnId] = [];
					rows[rowId].sizes.push(size);
					
					jQuery(column).find('input').each(function(fieldId, input) {
						rows[rowId].columns[columnId].push(jQuery(input).val());
					});
				}
			});
		});
		
		jQuery('#rsfp-grid-row-container > .rsfp-grid-row.rsfp-grid-row-unsortable').find('input').each(function(fieldId, input) {
			hidden.push(jQuery(input).val());
		});
		
		var old_val = jQuery('[name=GridLayout]').val();
		var new_val = JSON.stringify([rows, hidden]);
		
		if (new_val != old_val)
		{
			// Loading
			stateLoading();
			
			// Save value to hidden field
			jQuery('[name=GridLayout]').val(new_val);
			
			// Let's reorder fields
			var table = jQuery('#componentPreview > tbody'),
				table_rows = table.children('tr');
			
			// Remove current rows
			table.children('tr').remove();
			
			// Start reordering based on Grid position
			jQuery('#rsfp-grid-row-container').find('input').each(function(index, input) {
				var row = table_rows.find('input#preview-id-' + jQuery(input).val()).parent().parent();
				table.append(row);
			});
			
			// Send AJAX request
			jQuery.post(
				'index.php?option=com_rsform&task=forms.savegridlayout',
				{ formId: jQuery('#formId').val(), GridLayout: new_val },
				function(response){
					// Done loading
					stateDone();
					
					// If layout auto-generation is enabled, grab it from the request
					if (document.getElementById('FormLayoutAutogenerate1').checked == true) {
						var hasCodeMirror = typeof Joomla.editors.instances['formLayout'] != 'undefined';

						jQuery('#formLayout').val(response);
						if (hasCodeMirror)
						{
							Joomla.editors.instances['formLayout'].setValue(response);
						}
					}
				}
			);
		}
	}
};