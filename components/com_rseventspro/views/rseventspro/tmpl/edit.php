<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.html.editor');
JText::script('COM_RSEVENTSPRO_NO_OVERBOOKING_TICKETS_CONFIG'); 
JText::script('COM_RSEVENTSPRO_NO_RESULTS'); ?>

<script type="text/javascript">
var rsFx = new Fx.Scroll(window,{ wheelStops : false });
window.addEvent('domready', function() {
	
	$('name').focus();
	
	<?php if ($this->tab) { ?>
	$$('#rs_event_menu a').each(function (el,index){
		el.removeClass('active');
		if (index == 0) $('rs_right_1').style.display = 'none';
		if (index == <?php echo $this->tab; ?>) {
			el.addClass('active');
			theid = el.id.replace('rs_menu_item_','');
			$('rs_right_'+theid).reveal();
		}
	});
	<?php } ?>
	
	<?php if (!rseventsproHelper::isJ3()) { ?>
	$$('.rschosen').chosen({
		disable_search_threshold : 10
	});
	<?php } ?>
	
	$$('.rs_submit').each(function (el){
		el.addEvent('click', function(){
			$$('#rs_event_menu a').each(function (el,index){
				if (el.hasClass('active') && el.id != 'rs_menu_item_tc')
					$('tab').value = index;
			});
			<?php if (empty($this->row->parent) && (!empty($this->permissions['can_repeat_events']) || $this->admin)) { ?>
			rs_selectRepeatAlso();
			<?php } ?>
			rs_addTags();
			validateRSForm();
		});
	});
	
	$$('.rs_cancel').each(function (el) {
		el.addEvent('click', function(){
			document.location = '<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->row->id,$this->row->name),false); ?>';
		});
	});
	
});

function rs_addTags() {
	jQuery("#rs_keywords_span .as-original input.rs_inp").each(function (index,el){
		var c = jQuery.Event('keydown', { keyCode: 9 });
		jQuery(el).trigger(c);
	});
	
	jQuery("#rs_tags_span .as-original input.rs_inp").each(function (index,el){
		var c = jQuery.Event('keydown', { keyCode: 9 });
		jQuery(el).trigger(c);
	});
}

function rs_selectRepeatAlso() {
	for (i=0; i < $('repeatalso').options.length; i++) {
		$('repeatalso').options[i].selected = true;
	}
}

function validateRSForm() {
	ret = true;
	var msg = new Array();
	var create_tab = 0;
	var categories_tab = 0;
	var registration_tab = 0;
	var discounts_tab = 0;
	var contact_tab = 0;
	
	if ($('name').value == '') 
	{
		ret = false; 
		$('name').addClass('rse_error'); 
		msg.push('<?php echo JText::_('COM_RSEVENTSPRO_NO_NAME_ERROR',true); ?>');
		create_tab = 1;
	} else { $('name').removeClass('rse_error'); }
	
	if ($('start').value == '')
	{
		ret = false; 
		$('start').addClass('rse_error'); 
		msg.push('<?php echo JText::_('COM_RSEVENTSPRO_NO_START_ERROR',true); ?>');
		create_tab = 1;
	} else { $('start').removeClass('rse_error'); }
	
	if ($('end').value == '' && !$('allday').checked) 
	{
		ret = false; 
		$('end').addClass('rse_error'); 
		msg.push('<?php echo JText::_('COM_RSEVENTSPRO_NO_END_ERROR',true); ?>');
		create_tab = 1;
	} else { $('end').removeClass('rse_error'); }
	
	if ($('location').value == '' || $('location').value == 0) 
	{
		ret = false; 
		$('rs_location').addClass('rse_error'); 
		msg.push('<?php echo JText::_('COM_RSEVENTSPRO_NO_LOCATION_ERROR',true); ?>');
		create_tab = 1;
	} else { $('rs_location').removeClass('rse_error'); }
	
	if ($('categories').getSelected().length == 0) 
	{
		ret = false; 
		$('categories').addClass('rse_error'); 
		$('categories_chzn').addClass('rse_error');
		msg.push('<?php echo JText::_('COM_RSEVENTSPRO_NO_CATEGORY_ERROR',true); ?>');
		categories_tab = 1;
	} else { $('categories').removeClass('rse_error'); $('categories_chzn').removeClass('rse_error'); }
	
	if (strtotime($('end').value) <= strtotime($('start').value) && !$('allday').checked) 
	{ 
		ret = false; 
		$('end').addClass('rse_error'); 
		msg.push('<?php echo JText::_('COM_RSEVENTSPRO_END_BIGGER_ERROR',true); ?>');
		create_tab = 1;
	} else { $('end').removeClass('rse_error'); }
	
	<?php if ($this->row->registration) { ?>
	if ($('start_registration').value != '' && $('end_registration').value != '' && strtotime($('end_registration').value) <= strtotime($('start_registration').value))
	{
		ret = false;
		$('end_registration').addClass('rse_error');
		msg.push('<?php echo JText::_('COM_RSEVENTSPRO_END_REG_BIGGER_ERROR',true); ?>');
		registration_tab = 1;
	} else { $('end_registration').removeClass('rse_error'); }
	<?php } ?>
	
	<?php if ($this->row->discounts) { ?>
	if (parseInt($('early_fee').value) > 0 && $('early_fee_end').value == '')
	{
		ret = false;
		$('early_fee_end').addClass('rse_error');
		msg.push('<?php echo JText::_('COM_RSEVENTSPRO_EARLY_FEE_ERROR',true); ?>');
		discounts_tab = 1;
	} else { $('early_fee_end').removeClass('rse_error'); }
	
	if (parseInt($('late_fee').value) > 0 && $('late_fee_start').value == '')
	{
		ret = false;
		$('late_fee_start').addClass('rse_error');
		msg.push('<?php echo JText::_('COM_RSEVENTSPRO_LATE_FEE_ERROR',true); ?>');
		discounts_tab = 1;
	} else { $('late_fee_start').removeClass('rse_error'); }
	
	if (parseInt($('early_fee').value) > 0 && $('early_fee_end').value != '' && parseInt($('late_fee').value) > 0 && $('late_fee_start').value != '' && strtotime($('late_fee_start').value) <= strtotime($('early_fee_end').value))
	{
		ret = false;
		$('late_fee_start').addClass('rse_error');
		msg.push('<?php echo JText::_('COM_RSEVENTSPRO_LATE_FEE_BIGGER_ERROR',true); ?>');
		discounts_tab = 1;
	} else { $('late_fee_start').removeClass('rse_error'); }
	<?php } ?>
	
	<?php if (!rseventsproHelper::getConfig('time_format','int')) { ?>
	var date_error = 0;
	var regex=/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/;
	var ymd=/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9])?$/;
	
	if ($('allday').checked) {
		if (!$('start').value.match(ymd)) 
		{
			$('start').addClass('rse_error'); 
			date_error = 1;
			create_tab = 1;
		} else { $('start').removeClass('rse_error'); }
	} else {	
		if (!$('start').value.match(regex)) 
		{
			$('start').addClass('rse_error'); 
			date_error = 1;
			create_tab = 1;
		} else { $('start').removeClass('rse_error'); }
	}
	
	if (!$('end').value.match(regex) && !$('allday').checked) 
	{
		$('end').addClass('rse_error'); 
		date_error = 1;
		create_tab = 1;
	} else { $('end').removeClass('rse_error'); }
	
	if (!$('start_registration').value.match(regex) && $('start_registration').value != '') 
	{
		ret = false; 
		$('start_registration').addClass('rse_error'); 
		date_error = 1;
		registration_tab = 1;
	} else { $('start_registration').removeClass('rse_error'); }
	
	if (!$('end_registration').value.match(regex) && $('end_registration').value != '') 
	{
		$('end_registration').addClass('rse_error'); 
		date_error = 1;
		registration_tab = 1;
	} else { $('end_registration').removeClass('rse_error'); }
	
	if (!$('unsubscribe_date').value.match(regex) && $('unsubscribe_date').value != '') 
	{
		$('unsubscribe_date').addClass('rse_error'); 
		date_error = 1;
		registration_tab = 1;
	} else { $('unsubscribe_date').removeClass('rse_error'); }
	
	if (!$('early_fee_end').value.match(regex) && $('early_fee_end').value != '') 
	{
		$('early_fee_end').addClass('rse_error'); 
		date_error = 1;
		discounts_tab = 1;
	} else { $('early_fee_end').removeClass('rse_error'); }
	
	if (!$('late_fee_start').value.match(regex) && $('late_fee_start').value != '') 
	{
		$('late_fee_start').addClass('rse_error'); 
		date_error = 1;
		discounts_tab = 1;
	} else { $('late_fee_start').removeClass('rse_error'); }
	
	$$('input[id^=coupon_start]').each(function (el){
		if (el.value != '' && !el.value.match(regex))
		{
			date_error = 1;
			el.addClass('rse_error');
			var id = el.id.replace('coupon_start','');
			$$('#rs_li_c'+id+' a').addClass('error');
		}
	});
	
	$$('input[id^=coupon_end]').each(function (el){
		if (el.value != '' && !el.value.match(regex))
		{
			date_error = 1;
			el.addClass('rse_error');
			var id = el.id.replace('coupon_end','');
			$$('#rs_li_c'+id+' a').addClass('error');
		}
	});
	
	if (date_error)
	{
		ret = false; 
		msg.push('<?php echo JText::_('COM_RSEVENTSPRO_WRONG_DATE_FORMAT_ERROR',true); ?>');
	}
	
	<?php } ?>
	
	if (ret) {
		$('rs_errors').innerHTML = '';
		$$('#rs_li_1 a').removeClass('error');
		$$('#rs_li_2 a').removeClass('error');
		$$('#rs_li_7 a').removeClass('error');
		$$('#rs_li_9 a').removeClass('error');
		$$('#rs_li_4 a').removeClass('error');
		$('adminForm').submit();
	} else {
		$('rs_errors').innerHTML = '<p class="rs_error">'+msg.join('<br />')+'</p>';
		
		if (create_tab) $$('#rs_li_1 a').addClass('error'); else $$('#rs_li_1 a').removeClass('error');
		if (categories_tab) $$('#rs_li_2 a').addClass('error'); else $$('#rs_li_2 a').removeClass('error');
		if (registration_tab) $$('#rs_li_7 a').addClass('error'); else $$('#rs_li_7 a').removeClass('error');
		if (discounts_tab) $$('#rs_li_9 a').addClass('error'); else $$('#rs_li_9 a').removeClass('error');
		if (contact_tab) $$('#rs_li_4 a').addClass('error'); else $$('#rs_li_4 a').removeClass('error');
		
		rsFx.toTop();
	}
	return false;
}

function rs_show_overbooking(what) {
	if (what.checked) {
		if (document.getElementById('rs_check_ticketsconfig').checked) {
			what.checked = false;
			alert('<?php echo JText::_('COM_RSEVENTSPRO_NO_OVERBOOKING_TICKETS_CONFIG',true); ?>');
			return;
		}
	}
	
	if (what.checked) {
		$('max_tickets').checked = false;
		$('max_tickets').disabled = true;
		$('max_tickets_label').style.color = 'grey';
		$('overbooking_value').style.display = '';
		$('max_tickets_value').style.display = 'none';
	} else {
		$('overbooking_value').style.display = 'none';
		$('max_tickets_check').style.display = '';
		$('max_tickets_value').style.display = 'none';
		$('max_tickets').disabled = false;
		$('max_tickets_label').style.color = '';
	}
}

function rs_show_max_tickets(what) {
	if (what.checked) 	{
		$('overbooking').checked = false;
		$('overbooking').disabled = true;
		$('overbooking_label').style.color = 'grey';
		$('max_tickets_value').style.display = '';
		$('overbooking_value').style.display = 'none';
	} else {
		$('max_tickets_value').style.display = 'none';
		$('overbooking_check').style.display = '';
		$('overbooking_value').style.display = 'none';
		$('overbooking').disabled = false;
		$('overbooking_label').style.color = '';
	}
}

function rs_modal_tickets() {
	rs_modal('<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=seats&tmpl=component&id='.rseventsproHelper::sef($this->row->id,$this->row->name),false); ?>',1280,800);
}
</script>

<?php if (rseventsproHelper::getConfig('enable_google_maps') && (!empty($this->permissions['can_add_locations']) || $this->admin)) { ?>
<script type="text/javascript">
	var map;
	var geocoder;
	var marker;
	
	function rsinitialize() {
		geocoder = new google.maps.Geocoder();
		var mapDiv = document.getElementById('location_map');
		
		// Create the map object
		map = new google.maps.Map(mapDiv, {
				center: new google.maps.LatLng(<?php echo rseventsproHelper::getConfig('google_maps_center'); ?>),
				zoom: <?php echo rseventsproHelper::getConfig('google_map_zoom') ?>,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				streetViewControl: false
		});

		// Create the default marker icon
		marker = new google.maps.Marker({
			map: map,
			position: new google.maps.LatLng(<?php echo rseventsproHelper::getConfig('google_maps_center'); ?>),
			draggable: true
		});
		
		// Add event to the marker
		google.maps.event.addListener(marker, 'drag', function() {
			geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					if (results[0]) {
						document.getElementById('location_address').value = results[0].formatted_address;
						document.getElementById('location_coordinates').value = marker.getPosition().toUrlValue();
					}
				}
			});
		});
	}
	
	// Initialize google map
	google.maps.event.addDomListener(window, 'load', rsinitialize);
	
	// Search for addresses	
	function getLocations(term) {
		var content = $('rsepro_results');
		address = $('location_address').getSize();
		
		$('rsepro_results').setStyle('width', address.x - 4);
		$('rsepro_results').style.display = 'none';
		$$('#rsepro_results li').each(function(el) {
				el.dispose();
			});
		
		if (term != '') {
			geocoder.geocode( {'address': term }, function(results, status) {
				if (status == 'OK') {
					results.each(function(item) {
						
						theli = new Element('li');
						thea = new Element('a', {
							href: 'javascript:void(0)',
							'text': item.formatted_address
						});
						
						thea.addEvent('click', function() {
							$('location_address').value = item.formatted_address;
							$('location_coordinates').value = item.geometry.location.lat().toFixed(7) + ',' + item.geometry.location.lng().toFixed(7);
							var location = new google.maps.LatLng(item.geometry.location.lat().toFixed(7), item.geometry.location.lng().toFixed(7));
							marker.setPosition(location);
							map.setCenter(location);
							$('rsepro_results').style.display = 'none';
						});
						
						thea.inject(theli);
						theli.inject(content);
						
					});
				
					$('rsepro_results').style.display = '';
				}
			});
		}
	}
	
	function clearLocations() {
		setTimeout( function () {
			$('rsepro_results').style.display = 'none';
		},1000);
	}
</script>
<?php } ?>

<form action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=edit'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

	<div id="rs_errors"></div>
	<div id="rs_event_main">
	
		<!-- Events info tab -->
		<div class="rs_right" id="rs_right_1">			
			<fieldset>
            	<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_INFORMATION'); ?></legend>
                <p>
                	<label for="name"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_NAME'); ?></label>
                    <input type="text" value="<?php echo $this->escape($this->row->name); ?>" class="rs_inp" name="jform[name]" id="name" tabindex="1" />
                </p>
				<?php if (empty($this->permissions['event_moderation']) || $this->admin) { ?>
				<div class="rs_period">
					<label for="published"><?php echo JText::_('COM_RSEVENTSPRO_PUBLISH_EVENT'); ?></label>
					<div class="rs_calendar">
						<select name="jform[published]" class="rs_sel">
							<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', $this->states), 'value', 'text', $this->item->published, true); ?>
						</select>
					</div>
				</div>
				<?php } ?>
                <div class="rs_period">
                 	<div class="rs_calendar">
						<label for="start"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_STARTING'); ?></label>
						<div class="rs_starting">
							<?php $start = $this->row->allday ? rseventsproHelper::date($this->row->start,'Y-m-d') : rseventsproHelper::date($this->row->start,'Y-m-d H:i:s'); ?>
							<?php echo JHTML::_('rseventspro.calendar', $start, 'jform[start]', 'start', '%Y-%m-%d %H:%M:%S',false,false,false,$this->row->allday); ?>
						</div>
                    </div>
					
                    <div class="rs_calendar" id="enddate" <?php echo $this->row->allday ? 'style="display:none;"' : ''; ?>>
						<label for="end"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_ENDING'); ?></label>
						<div class="rs_starting">
							<?php $end = $this->row->allday ? '' : rseventsproHelper::date($this->row->end,'Y-m-d H:i:s'); ?>
							<?php echo JHTML::_('rseventspro.calendar', $end, 'jform[end]', 'end','%Y-%m-%d %H:%M:%S'); ?>
						</div>
                    </div>
					
					<div class="rs_calendar">
						<label>&nbsp;</label>
						<div class="rs_starting">
							<input type="checkbox" id="allday" name="jform[allday]" value="1" class="rs_check" onchange="rs_edit_allday(this);" <?php echo $this->row->allday ? 'checked="checked"' : ''; ?> /> 
							<label for="allday" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_ALL_DAY'); ?></label>
						</div>
                    </div>
					
					
                </div>
				<?php if (!empty($this->permissions['can_repeat_events']) || $this->admin) { ?>
				<?php if (empty($this->row->parent)) { ?>
				<p>
					<input name="jform[recurring]" type="checkbox" value="1" <?php echo $this->row->recurring ? 'checked="checked"' : ''; ?> class="rs_check" id="rs_check_recurring" />
					<label for="rs_check_recurring" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_RECURRING'); ?></label>
					<span id="rs_repeating"><?php echo JText::sprintf('COM_RSEVENTSPRO_EVENT_RECURING_TIMES','<span id="rs_repeating_event_total">'.$this->eventClass->getChild().'</span>') ?></span>
				</p>
				<?php } ?>
				<?php } ?>
                <div class="rs_period" style="position: relative; z-index: 99;">
                	<label for="location"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_LOCATION'); ?></label>
                    <input type="text" value="<?php echo $this->escape($this->row->locationname); ?>" class="rs_inp" name="rs_location" id="rs_location" tabindex="3" />
					<input type="hidden" name="jform[location]" id="location" value="<?php echo $this->row->location; ?>"/>
                    
					<?php if (!empty($this->permissions['can_add_locations']) || $this->admin) { ?>
					<div id="rs_location_window" class="rs_display_none" style="z-index:9999999; position: absolute;">
                    	<p><?php echo JText::sprintf('COM_RSEVENTSPRO_EVENT_ADD_LOCATION_INFO',' <span id="rs_new_location"></span> '); ?></p>
                        <p>
							<label for="location_address"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_LOCATION_ADDRESS'); ?></label>
							<?php if (rseventsproHelper::getConfig('enable_google_maps')) { ?>
							<input type="text" class="rs_inp" name="location_address" id="location_address" value="" onkeyup="getLocations(this.value)" onblur="clearLocations();" autocomplete="off" />
							<ul id="rsepro_results" style="display:none; margin-left: -32px !important;margin-top: -4px !important;"></ul>
							<?php } else { ?>
							<input type="text" class="rs_inp" name="location_address" id="location_address" value="" />
							<?php } ?>
						</p>
                        <p><label for="location_description"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_LOCATION_DESCRIPTION'); ?></label>
                        <textarea class="rs_txt" name="location_description" id="location_description"></textarea></p>
                        <?php if (rseventsproHelper::getConfig('enable_google_maps')) { ?>
						<p>
							<span id="location_map" class="rsepro_location_map" style="margin:0 auto; height: 120px;"></span>
						</p>
						<?php } ?>
						<p>
							<button type="button" onclick="rs_edit_save_location();" id="rs_add_new_location"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_LOCATION_ADD_LOCATION'); ?></button>
							<button type="button" onclick="$('rs_location_window').setStyle('display','none');" id="rs_add_new_location"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
						</p>
						<input type="hidden" name="location_coordinates" value="" id="location_coordinates" />
					</div>
					<?php } ?>
                </div>
				
                <p style="position: relative; z-index: 98;">
					<?php echo JEditor::getInstance(JFactory::getConfig()->get('editor'))->display('jform[description]',$this->escape($this->row->description),'100%', '50%', 20, 7, rseventsproHelper::getConfig('enable_buttons','bool')); ?>
                </p>
				
				<?php if ($this->admin) { ?>
				<p>
					<label for="groups" id="wider_label" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_GROUPS'); ?></label> 
				</p>
				
				<div class="rs_period">
					<select class="inputbox rschosen" name="groups[]" id="groups" multiple="multiple">
						<?php echo JHtml::_('select.options', $this->eventClass->groups(),'value','text',$this->eventClass->getGroups()); ?>
					</select>
				</div>
				<?php } ?>
				
				<p style="position: relative; z-index: 97;">
					<input name="jform[comments]" type="checkbox" <?php echo $this->row->comments ? 'checked="checked"' : ''; ?> value="1" class="rs_check" id="rs_comments" />
					<label for="rs_comments" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_ENABLE_COMMENTS'); ?></label>
				</p>
				
				<p style="position: relative; z-index: 97;">
					<input name="jform[registration]" type="checkbox" <?php echo $this->row->registration ? 'checked="checked"' : ''; ?> value="1" class="rs_check" id="rs_check_registration" />
					<label for="rs_check_registration" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_ENABLE_REGISTRATION'); ?></label>
				</p>
			</fieldset>
			<button type="button" class="rs_button rs_submit"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
			<button type="button" class="rs_button rs_cancel"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
        </div>
		<!-- End Events info tab -->
		
		<!-- Categories and Tags tab -->
		<div class="rs_right rs_display_none" id="rs_right_2">
			<fieldset>
				<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_CATEGORIES'); ?></legend>
				<div class="rs_period">
					<select class="inputbox rschosen" name="categories[]" id="categories" multiple="multiple">
						<?php echo JHtml::_('select.options', JHtml::_('category.options','com_rseventspro', array('filter.published' => array(1))),'value','text',$this->eventClass->getCategories()); ?>
					</select>
					<?php if (!empty($this->permissions['can_create_categories']) || $this->admin) { ?>
					<a class="rs_add_category" id="rs_add" onclick="sm('rs_category_add',400,250)"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_ADD_CATEGORY'); ?></a>
					<?php } ?>
				</div>            
			</fieldset>
			<fieldset>
				<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAGS'); ?></legend>
				<p>
					<span id="rs_tags_span">
						<input type="text" value="<?php echo $this->eventClass->getTags(); ?>" class="rs_inp" name="tags" id="rs_tags" />
					</span>
					<div style="float:left;clear:both;">
						<small><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAGS_INFO'); ?></small>
					</div>
				</p> 
			</fieldset>
			<button type="button" class="rs_button rs_submit"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
			<button type="button" class="rs_button rs_cancel"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
		</div>
		<!-- End Categories and Tags tab -->
		
		<?php if (!empty($this->permissions['can_upload']) || $this->admin) { ?>
		<!-- Files tab -->
		<div class="rs_right rs_display_none" id="rs_right_3">
			<fieldset>
				<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_ADD_FILES'); ?></legend>
				<div id="rs_files">
					<p><input type="file" class="rs_inp rs_inp_file" name="files[]" /></p>
					<p><input type="file" class="rs_inp rs_inp_file" name="files[]" /></p>
					<p><input type="file" class="rs_inp rs_inp_file" name="files[]" /></p>
				</div>
				<p><a href="javascript:void(0)" id="rs_add_more"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_ADD_MORE_FILES'); ?></a></p>
			</fieldset>
			
			<?php $eventFiles = $this->eventClass->getFiles(); ?>
			<?php if (!empty($eventFiles)) { ?>
			<fieldset>
				<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_EDIT_FILES'); ?></legend>
				<ul id="rs_list_files">
					<?php foreach ($eventFiles as $file) { ?>
					<li id="<?php echo $file->id; ?>">
						<a id="rs_file_<?php echo $file->id; ?>" href="javascript:void(0)" onclick="rs_modal('<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=file&id='.rseventsproHelper::sef($file->id,$file->name).'&tmpl=component'); ?>',350,250)"><?php echo $file->name; ?></a>
						<a href="javascript:void(0)" onclick="if(confirm('<?php echo JText::_('COM_RSEVENTSPRO_EVENT_DELETE_FILE_CONFIRM',true); ?>')) { rs_edit_remove_file(<?php echo $file->id; ?>); }" class="rs_remove"></a>
					</li>
					<?php } ?>
				</ul>
			</fieldset>
			<?php } ?>
			<button type="button" class="rs_button rs_submit"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
			<button type="button" class="rs_button rs_cancel"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
		</div>
		<!-- End Files tab -->
		<?php } ?>
		
		
		<!-- Contact tab -->
		<div class="rs_right rs_display_none" id="rs_right_4">
			<fieldset>
				<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_CONTACT'); ?></legend>
				<p>
					<label for="URL"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_WEB'); ?></label>
					<input type="text" value="<?php echo $this->escape($this->row->URL); ?>" class="rs_inp" id="URL" name="jform[URL]" />
				</p> 
				<p>
					<label for="phone"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_PHONE'); ?></label>
					<input type="text" value="<?php echo $this->escape($this->row->phone); ?>" class="rs_inp" id="phone" name="jform[phone]" />                                       
				</p> 
				<p>
					<label for="email"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_EMAIL'); ?></label>
					<input type="text" value="<?php echo $this->escape($this->row->email); ?>" class="rs_inp" id="email" name="jform[email]" />                                       
				</p> 
			</fieldset>
			<button type="button" class="rs_button rs_submit"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
			<button type="button" class="rs_button rs_cancel"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
		</div>
		<!-- End Contact tab -->
		
		<!-- Meta details tab -->
		<div class="rs_right rs_display_none" id="rs_right_5">
			<fieldset>
				<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_META'); ?></legend>
				<p>
					<label for="metaname"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_PAGE_TITLE'); ?></label>
					<input type="text" value="<?php echo $this->escape($this->row->metaname); ?>" class="rs_inp" id="metaname" name="jform[metaname]" />
				</p>
				<p>
					<label for="meta_key"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_PAGE_KEYWORDS'); ?></label>
					<span id="rs_keywords_span">
						<input type="text" value="<?php echo $this->row->metakeywords; ?>" class="rs_inp" name="jform[metakeywords]" id="meta_key" />
					</span>
					<div style="float:left;clear:both;">
						<small><?php echo JText::_('COM_RSEVENTSPRO_EVENT_PAGE_KEYWORDS_INFO'); ?></small>
					</div>
				</p>
				<p>
					<label for="metadescription"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_PAGE_DESCRIPTION'); ?></label>
					<textarea class="rs_txt" id="metadescription" name="jform[metadescription]"><?php echo $this->row->metadescription; ?></textarea>
				</p>
			</fieldset>               
			<button type="button" class="rs_button rs_submit"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
			<button type="button" class="rs_button rs_cancel"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
		</div>
		<!-- End Meta details tab -->
		
		<!-- Frontend tab -->
		<div class="rs_right rs_display_none" id="rs_right_11">
			<?php $eventOptions = $this->eventClass->getEventOptions(); ?>
			<fieldset>
				<legend><?php echo JText::_('COM_RSEVENTSPRO_SHARING_OPTIONS'); ?></legend>
				<p>
					<input type="checkbox" name="jform[options][enable_rating]" id="enable_rating" value="1" <?php echo (isset($eventOptions['enable_rating']) && $eventOptions['enable_rating'] == 1) ? 'checked="checked"' : ''; ?> />
					<label for="enable_rating" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_ENABLE_EVENT_RATING'); ?></label>
				</p>
				<p>
					<input type="checkbox" name="jform[options][enable_fb_like]" id="enable_facebook_like" value="1" <?php echo (isset($eventOptions['enable_fb_like']) && $eventOptions['enable_fb_like'] == 1) ? 'checked="checked"' : ''; ?> />
					<label for="enable_facebook_like" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_ENABLE_FACEBOOK_LIKE'); ?></label>
				</p>
				<p>
					<input type="checkbox" name="jform[options][enable_twitter]" id="enable_twitter" value="1" <?php echo (isset($eventOptions['enable_twitter']) && $eventOptions['enable_twitter'] == 1) ? 'checked="checked"' : ''; ?> />
					<label for="enable_twitter" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_ENABLE_TWITTER'); ?></label>
				</p>
				<p>
					<input type="checkbox" name="jform[options][enable_gplus]" id="enable_gplus" value="1" <?php echo (isset($eventOptions['enable_gplus']) && $eventOptions['enable_gplus'] == 1) ? 'checked="checked"' : ''; ?> />
					<label for="enable_gplus" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_ENABLE_GOOGLEPLUS'); ?></label>
				</p>
				<p>
					<input type="checkbox" name="jform[options][enable_linkedin]" id="enable_linkedin" value="1" <?php echo (isset($eventOptions['enable_linkedin']) && $eventOptions['enable_linkedin'] == 1) ? 'checked="checked"' : ''; ?> />
					<label for="enable_linkedin" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_ENABLE_LINKEDIN'); ?></label>
				</p>
			</fieldset>
			
			<fieldset>
				<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_OPTIONS'); ?></legend>
				<p>
					<input type="checkbox" name="jform[options][start_date]" id="start_date" value="1" <?php echo (isset($eventOptions['start_date']) && $eventOptions['start_date'] == 1) ? 'checked="checked"' : ''; ?> />
					<label for="start_date" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_START_DATE'); ?></label>
				</p>
				<p>
					<?php
						if (!isset($eventOptions['start_time'])) {
							$start_time_checked = true;
						} else {
							if ($eventOptions['start_time'] == 1)
								$start_time_checked = true;
							else 
								$start_time_checked = false;
						}
					?>
					<input type="checkbox" name="jform[options][start_time]" id="start_time" value="1" <?php echo $start_time_checked ? 'checked="checked"' : ''; ?> />
					<label for="start_time" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_START_TIME'); ?></label>
				</p>
				<p>
					<input type="checkbox" name="jform[options][end_date]" id="end_date" value="1" <?php echo (isset($eventOptions['end_date']) && $eventOptions['end_date'] == 1) ? 'checked="checked"' : ''; ?> />
					<label for="end_date" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_END_DATE'); ?></label>
				</p>
				<p>
					<?php
						if (!isset($eventOptions['end_time'])) {
							$end_time_checked = true;
						} else {
							if ($eventOptions['end_time'] == 1)
								$end_time_checked = true;
							else 
								$end_time_checked = false;
						}
					?>
					<input type="checkbox" name="jform[options][end_time]" id="end_time" value="1" <?php echo $end_time_checked ? 'checked="checked"' : ''; ?> />
					<label for="end_time" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_END_TIME'); ?></label>
				</p>
				<p>
					<input type="checkbox" name="jform[options][show_description]" id="show_description" value="1" <?php echo (isset($eventOptions['show_description']) && $eventOptions['show_description'] == 1) ? 'checked="checked"' : ''; ?> />
					<label for="show_description" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_DESCRIPTION'); ?></label>
				</p>
				<p>
					<input type="checkbox" name="jform[options][show_location]" id="show_location" value="1" <?php echo (isset($eventOptions['show_location']) && $eventOptions['show_location'] == 1) ? 'checked="checked"' : ''; ?> />
					<label for="show_location" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_LOCATION'); ?></label>
				</p>
				<p>
					<input type="checkbox" name="jform[options][show_categories]" id="show_categories" value="1" <?php echo (isset($eventOptions['show_categories']) && $eventOptions['show_categories'] == 1) ? 'checked="checked"' : ''; ?> />
					<label for="show_categories" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_CATEGORIES'); ?></label>
				</p>
				<p>
					<input type="checkbox" name="jform[options][show_tags]" id="show_tags" value="1" <?php echo (isset($eventOptions['show_tags']) && $eventOptions['show_tags'] == 1) ? 'checked="checked"' : ''; ?> />
					<label for="show_tags" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_TAGS'); ?></label>
				</p>
				<p>
					<input type="checkbox" name="jform[options][show_files]" id="show_files" value="1" <?php echo (isset($eventOptions['show_files']) && $eventOptions['show_files'] == 1) ? 'checked="checked"' : ''; ?> />
					<label for="show_files" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_FILES'); ?></label>
				</p>
				<p>
					<input type="checkbox" name="jform[options][show_contact]" id="show_contact" value="1" <?php echo (isset($eventOptions['show_contact']) && $eventOptions['show_contact'] == 1) ? 'checked="checked"' : ''; ?> />
					<label for="show_contact" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_CONTACT'); ?></label>
				</p>
				<p>
					<input type="checkbox" name="jform[options][show_map]" id="show_map" value="1" <?php echo (isset($eventOptions['show_map']) && $eventOptions['show_map'] == 1) ? 'checked="checked"' : ''; ?> />
					<label for="show_map" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_MAP'); ?></label>
				</p>
				<p>
					<input type="checkbox" name="jform[options][show_export]" id="show_export" value="1" <?php echo (isset($eventOptions['show_export']) && $eventOptions['show_export'] == 1) ? 'checked="checked"' : ''; ?> />
					<label for="show_export" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_EXPORT'); ?></label>
				</p>
				<p>
					<input type="checkbox" name="jform[options][show_invite]" id="show_invite" value="1" <?php echo (isset($eventOptions['show_invite']) && $eventOptions['show_invite'] == 1) ? 'checked="checked"' : ''; ?> />
					<label for="show_invite" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_INVITE'); ?></label>
				</p>
				<p>
					<input type="checkbox" name="jform[options][show_postedby]" id="show_postedby" value="1" <?php echo (isset($eventOptions['show_postedby']) && $eventOptions['show_postedby'] == 1) ? 'checked="checked"' : ''; ?> />
					<label for="show_postedby" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_POSTEDBY'); ?></label>
				</p>
				<p>
					<input type="checkbox" name="jform[options][show_repeats]" id="show_repeats" value="1" <?php echo (isset($eventOptions['show_repeats']) && $eventOptions['show_repeats'] == 1) ? 'checked="checked"' : ''; ?> />
					<label for="show_repeats" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_SHOW_REPEATS'); ?></label>
				</p>
			</fieldset>
			<button type="button" class="rs_button rs_submit"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
			<button type="button" class="rs_button rs_cancel"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
		</div>
		<!-- End Frontend tab -->
		
		<?php if (!empty($this->permissions['can_repeat_events']) || $this->admin) { ?>
		<?php if (empty($this->row->parent)) { ?>
		<?php $repeats = $this->eventClass->getRepeats(); ?>
		<!-- Recurring tab -->
		<div class="rs_right rs_display_none" id="rs_right_6">
			<fieldset>
			<legend><?php echo JText::_('COM_RSEVENTSPRO_RECURRING_EVENT'); ?></legend>
				<p class="rs_notification"><?php echo JText::sprintf('COM_RSEVENTSPRO_EVENT_RECURING_TIMES','<span id="rs_repeating_total">'.$this->eventClass->getChild($this->row->id).'</span>') ?></p>
				<p>
					<label for="repeat_interval"><?php echo JText::_('COM_RSEVENTSPRO_REAPEAT_EVERY'); ?></label>
					<input type="text" value="<?php echo $this->escape($this->row->repeat_interval); ?>" id="repeat_interval" name="jform[repeat_interval]" onchange="createRepeats()" class="rs_inp rs_inp_short" size="3" /> 
					<select class="rs_sel" name="jform[repeat_type]" id="repeat_type" onchange="rs_check_repeat(this.value)">
						<?php echo JHtml::_('select.options', $this->eventClass->repeatType(),'value','text',$this->item->repeat_type); ?>
					</select>
				</p>
				<div class="rs_period">
					<div class="rs_calendar">
						<label for="repeat_end"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_END_REPEAT'); ?></label>
						<div class="rs_starting">
							<?php $repeat_end = (empty($this->row->repeat_end) || $this->row->repeat_end == $this->eventClass->getNullDate()) ? '' : $this->row->repeat_end; ?>
							<?php echo JHTML::_('rseventspro.calendar', $repeat_end, 'jform[repeat_end]', 'repeat_end','%Y-%m-%d',true,'onchange="createRepeats();"',true); ?>
						</div>
					</div>
				</div>
				
				<div class="rs_period" id="rs_repeat_days" style="position: relative; z-index: 99;">
					<label for="repeat_days"><?php echo JText::_('COM_RSEVENTSPRO_REAPEAT_ON'); ?></label> 
					<div class="rs_repeat">
						<select class="rschosen" name="repeat_days[]" id="repeat_days" multiple="multiple">
							<?php echo JHtml::_('select.options', $this->eventClass->repeatDays(), 'value','text',$this->eventClass->repeatEventDays()); ?>
						</select>
					</div>	
				</div>
				<div class="rs_period" style="position: relative; z-index: 98;">
					<div class="rs_calendar">
						<label for="repeat_date"><?php echo JText::_('COM_RSEVENTSPRO_REAPEAT_ALSO_ON'); ?></label>
						<div class="rs_starting">
							<?php echo JHTML::_('rseventspro.calendar', '', 'repeat_date', 'repeat_date','%Y-%m-%d',true,'onchange="rs_add_date();"',true); ?>
						</div>
						<select class="rs_sel" name="jform[repeat_also][]" id="repeatalso" multiple="multiple" style="float:left;">
							<?php echo JHtml::_('select.options', $this->eventClass->repeatAlso()); ?>
						</select>
						<div class="rs_repeatblock">							
							<div style="text-align:center;">
								<button type="button" class="rs_inp" onclick="rs_remove_dates();"><?php echo JText::_('COM_RSEVENTSPRO_REMOVE_SELECTED'); ?></button>
							</div>
						</div>
						
					</div>
				</div>
				<p>&nbsp;</p>
				<p>
					<input id="apply_changes" name="apply_changes" type="checkbox" value="1" class="rs_check" />
					<label for="apply_changes" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_APPLY_CHANGES'); ?></label>
				</p>
				<?php if (!empty($repeats)) { ?>
				<div id="repeatedevents">
					<label id="repeatslabel" onclick="rs_show_repeats();"><b><?php echo JText::_('COM_RSEVENTSPRO_EVENT_REPEATED_EVENTS'); ?></b> <span id="repeatimg" class="repeatimg_down"></span></label>
					<ul class="rse_repeats" id="rs_repeats">
						<?php foreach ($repeats as $event) { ?>
						<li>
							<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=edit&id='.rseventsproHelper::sef($event->id,$event->name)); ?>"><?php echo $event->name; ?></a> 
							<?php echo $event->name; ?> 
							<?php if ($event->allday) { ?>
							(<?php echo rseventsproHelper::date($event->start, $this->config->global_date); ?>)
							<?php } else { ?>
							(<?php echo rseventsproHelper::date($event->start); ?> - <?php echo rseventsproHelper::date($event->end); ?>)
							<?php } ?>
						</li>
						<?php } ?>
					</ul>
				</div>
				<?php } ?>
			</fieldset>
			<button type="button" class="rs_button rs_submit"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
			<button type="button" class="rs_button rs_cancel"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
        </div>
		<!-- End Recurring tab -->
		<?php } ?>
		<?php } ?>
		
		<!-- Registration tab -->
		<div class="rs_right rs_display_none" id="rs_right_7">
			<fieldset>
				<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_REGISTRATION'); ?></legend>
				<div class="rs_period">
					<div class="rs_calendar">
						<label for="start_registration"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_FROM'); ?></label>
						<div class="rs_starting">
							<?php $startregistration = (!empty($this->row->start_registration) && $this->row->start_registration != $this->eventClass->getNullDate() ? JHTML::_('date', $this->row->start_registration, 'Y-m-d H:i:s') : ''); ?>
							<?php echo JHTML::_('rseventspro.calendar', $startregistration, 'jform[start_registration]', 'start_registration','%Y-%m-%d %H:%M:%S'); ?>
						</div>
					</div>

					<div class="rs_calendar">
						<label for="end_registration"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TO'); ?></label>
						<div class="rs_starting" id="rs_ending_calendar">
							<?php $endregistration = (!empty($this->row->end_registration) && $this->row->end_registration != $this->eventClass->getNullDate() ? JHTML::_('date', $this->row->end_registration, 'Y-m-d H:i:s') : ''); ?>
							<?php echo JHTML::_('rseventspro.calendar', $endregistration, 'jform[end_registration]', 'end_registration','%Y-%m-%d %H:%M:%S'); ?>
						</div>
					</div>
				</div>
				<div class="rs_period">
					<div class="rs_calendar">
						<label for="unsubscribe_date" class="hasTip" title="<?php echo JText::_('COM_RSEVENTSPRO_UNSUBSCRIPTION_DATE_DESC'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_UNSUBSCRIPTION_DATE'); ?></label>
						<div class="rs_starting">
							<?php $unsubscribe_date = (!empty($this->row->unsubscribe_date) && $this->row->unsubscribe_date != $this->eventClass->getNullDate() ? JHTML::_('date', $this->row->unsubscribe_date, 'Y-m-d H:i:s') : ''); ?>
							<?php echo JHTML::_('rseventspro.calendar', $unsubscribe_date, 'jform[unsubscribe_date]', 'unsubscribe_date','%Y-%m-%d %H:%M:%S'); ?>
						</div>
					</div>
				</div>
				<p>
					<label for="payment_method"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_PAYMENTS'); ?></label>
					<div class="rs_payments">
						<select class="inputbox rschosen" name="jform[payments][]" id="payment_method" multiple="multiple">
							<?php echo JHtml::_('select.options', rseventsproHelper::getPayments(),'value','text',$this->eventClass->getPayments()); ?>
						</select>
					</div>
				</p>
				<p id="overbooking_check">
					<input id="overbooking" name="jform[overbooking]" type="checkbox" value="1" onchange="rs_show_overbooking(this);" class="rs_check" <?php echo $this->row->overbooking ? 'checked="checked"' : ''; ?> />
					<label for="overbooking" class="rs_inline" id="overbooking_label"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_OVERBOOKING'); ?></label>
				</p>
				<p id="overbooking_value" style="display:none;">
					<label for="overbooking_amount" class="hasTip" title="<?php echo JText::_('COM_RSEVENTSPRO_EVENT_OVERBOOKING_AMOUNT_DESC'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_OVERBOOKING_AMOUNT'); ?></label>
					<input type="text" name="jform[overbooking_amount]" id="overbooking_amount" class="rs_inp_smaller" value="<?php echo $this->escape($this->row->overbooking_amount); ?>">
				</p>
				<p id="max_tickets_check">
					<input id="max_tickets" name="jform[max_tickets]" type="checkbox" value="1" onchange="rs_show_max_tickets(this);" class="rs_check" <?php echo $this->row->max_tickets ? 'checked="checked"' : ''; ?> />
					<label for="max_tickets" class="rs_inline" id="max_tickets_label"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_MAX_TICKETS'); ?></label>
				</p>
				<p id="max_tickets_value" style="display:none;">
					<label for="max_tickets_amount" class="hasTip" title="<?php echo JText::_('COM_RSEVENTSPRO_MAX_TICKETS_AMOUNT_DESC'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_MAX_TICKETS_AMOUNT'); ?></label>
					<input type="text" name="jform[max_tickets_amount]" id="max_tickets_amount" class="rs_inp_smaller" value="<?php echo $this->escape($this->row->max_tickets_amount); ?>">
				</p>
				<?php if (rseventsproHelper::paypal() && rseventsproHelper::getConfig('payment_paypal')) { ?>
				<p>
                	<label for="paypal_email"><?php echo JText::_('COM_RSEVENTSPRO_PAYPAL_EMAIL'); ?></label>
                    <input type="text" id="paypal_email" name="jform[paypal_email]" class="rs_inp" value="<?php echo $this->escape($this->row->paypal_email); ?>">
                </p>
				<?php } ?>
				<p>
					<input id="notify_me" name="jform[notify_me]" type="checkbox" value="1" class="rs_check" <?php echo $this->row->notify_me ? 'checked="checked"' : ''; ?> />
					<label for="notify_me" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_SUBSCRIPTION_NOTIFICATION'); ?></label>
				</p>
				<p>
					<input id="notify_me_unsubscribe" name="jform[notify_me_unsubscribe]" type="checkbox" value="1" class="rs_check" <?php echo $this->row->notify_me_unsubscribe ? 'checked="checked"' : ''; ?> />
					<label for="notify_me_unsubscribe" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_UNSUBSCRIBE_NOTIFICATION'); ?></label>
				</p>
				<p>
					<input id="show_registered" name="jform[show_registered]" type="checkbox" value="1" class="rs_check" <?php echo $this->row->show_registered ? 'checked="checked"' : ''; ?> />
					<label for="show_registered" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_SHOW_GUESTS'); ?></label>
				</p>
				<p>
					<input id="automatically_approve" name="jform[automatically_approve]" type="checkbox" value="1" class="rs_check" <?php echo $this->row->automatically_approve ? 'checked="checked"' : ''; ?> />
					<label for="automatically_approve" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_APPROVE'); ?></label>
				</p>
				<p>
					<label for="registration_form"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_REGISTRATION_FORM'); ?></label>
					<a id="rse_form_name" class="modal rs_inp" rel="{handler: 'iframe', size: {x: 800, y:600}}" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=forms&tmpl=component&id='.rseventsproHelper::sef($this->row->id,$this->row->name)); ?>"><?php echo $this->eventClass->getForm(); ?></a> 
					<br /><br /> <a href="http://www.rsjoomla.com/joomla-extensions/joomla-form.html" target="_blank"><?php echo JText::_('COM_RSEVENTSPRO_RSFORMPRO'); ?></a>
				</p>
				<p>
					<input name="jform[ticketsconfig]" type="checkbox" value="1" class="rs_check" id="rs_check_ticketsconfig" <?php echo $this->row->ticketsconfig ? 'checked="checked"' : ''; ?> />
					<label for="rs_check_ticketsconfig" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_ENABLE_TICKETS_CONFIGURATION'); ?></label>
				</p>
				<p>
					<input name="jform[discounts]" type="checkbox" value="1" class="rs_check" id="rs_check_discounts" <?php echo $this->row->discounts ? 'checked="checked"' : ''; ?> />
					<label for="rs_check_discounts" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_ENABLE_DISCOUNTS'); ?></label>
				</p>
			</fieldset>
			<button type="button" class="rs_button rs_submit"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
			<button type="button" class="rs_button rs_cancel"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
		</div>
		<!-- End Registration tab -->
        
		<!-- New ticket tab -->
		<div class="rs_right rs_display_none" id="rs_right_8">
			<fieldset>
				<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_NEWTICKET'); ?></legend>
				<p>
					<label for="ticket_name" id="name_label"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TICKET_NAME'); ?></label>
					<input type="text" value="" class="rs_inp" name="ticket_name" id="ticket_name" />
				</p>
				<p>
					<label for="ticket_price" id="price_label"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TICKET_PRICE'); ?></label>
					<input type="text" value="" class="rs_inp" name="ticket_price" id="ticket_price" onkeyup="this.value=this.value.replace(/[^0-9\.\,]/g, '');" />
				</p>
				<p>
					<label for="ticket_seats" id="seats_label"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TICKET_SEATS'); ?></label>
					<input type="text" value="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED'); ?>" onfocus="if (this.value=='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>') this.value=''" onblur="if (this.value=='') this.value='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>'" onkeyup="this.value=this.value.replace(/[^0-9]/g, '');" class="rs_inp" name="ticket_seats" id="ticket_seats" />
				</p>
				<p>
					<label for="ticket_user_seats" id="user_seats_label"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TICKET_USER_SEATS'); ?></label>
					<input type="text"  value="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED'); ?>" onfocus="if (this.value=='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>') this.value=''" onblur="if (this.value=='') this.value='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>'" onkeyup="this.value=this.value.replace(/[^0-9]/g, '');" class="rs_inp" name="ticket_user_seats" id="ticket_user_seats" />
				</p>
				<div class="rs_period">
					<label for="ticket_groups" id="tgroups_label"><?php echo JText::_('COM_RSEVENTSPRO_TICKET_GROUPS_INFO'); ?></label>
					<div class="rs_ticket_groups">
						<select class="inputbox rschosen" name="ticket_groups[]" id="ticket_groups" multiple="multiple">
							<?php echo JHtml::_('select.options', $this->eventClass->groups()); ?>
						</select>
					</div>	
				</div>
				<p>
					<label for="ticket_description" id="description_label"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TICKET_DESCRIPTION'); ?></label>
					<textarea class="rs_txt" name="ticket_description" id="ticket_description"></textarea>
				</p>
			</fieldset>
			<button type="button" class="rs_button" onclick="rs_edit_addticket('<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>','<?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT', true); ?>','<?php echo JText::_('COM_RSEVENTSPRO_EMPTY_TICKET_NAME',true); ?>','<?php echo JText::_('COM_RSEVENTSPRO_REMOVE_TICKET',true); ?>','<?php echo JText::_('COM_RSEVENTSPRO_CONFIRM_DELETE_TICKET',true); ?>');"><?php echo JText::_('COM_RSEVENTSPRO_ADD_TICKET'); ?></button>
			<button type="button" class="rs_button rs_submit"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
		</div>
		<!-- End New ticket tab -->
        
		<!-- Tickets tab -->
		<?php $eventTickets = $this->eventClass->getTickets(); ?>
		<?php if (!empty($eventTickets)) { ?>
		<?php foreach($eventTickets as $ticket) { ?>
		<div class="rs_right rs_display_none" id="rs_right_t<?php echo $ticket->id; ?>">
			<fieldset>
				<legend><?php echo $ticket->name; ?></legend>
				<p>
					<label for="ticket_name<?php echo $ticket->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TICKET_NAME'); ?></label>
					<input type="text" value="<?php echo $this->escape($ticket->name); ?>" class="rs_inp" name="tickets[<?php echo $ticket->id; ?>][name]" id="ticket_name<?php echo $ticket->id; ?>" />
				</p>
				<p>
					<label for="ticket_price<?php echo $ticket->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TICKET_PRICE'); ?></label>
					<input type="text" value="<?php echo $this->escape($ticket->price); ?>" class="rs_inp" name="tickets[<?php echo $ticket->id; ?>][price]" id="ticket_price<?php echo $ticket->id; ?>" onkeyup="this.value=this.value.replace(/[^0-9\.\,]/g, '');" />
				</p>
				<p>
					<label for="ticket_seats<?php echo $ticket->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TICKET_SEATS'); ?></label>
					<input type="text" value="<?php echo empty($ticket->seats) ? JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED') : $ticket->seats; ?>" onfocus="if (this.value=='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>') this.value=''" onblur="if (this.value=='') this.value='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>'" onkeyup="this.value=this.value.replace(/[^0-9]/g, '');" class="rs_inp" name="tickets[<?php echo $ticket->id; ?>][seats]" id="ticket_seats<?php echo $ticket->id; ?>" />
				</p>
				<p>
					<label for="ticket_user_seats<?php echo $ticket->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TICKET_USER_SEATS'); ?></label>
					<input type="text"  value="<?php echo empty($ticket->user_seats) ? JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED') : $ticket->user_seats; ?>" onfocus="if (this.value=='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>') this.value=''" onblur="if (this.value=='') this.value='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>'" onkeyup="this.value=this.value.replace(/[^0-9]/g, '');" class="rs_inp" name="tickets[<?php echo $ticket->id; ?>][user_seats]" id="ticket_user_seats<?php echo $ticket->id; ?>" />
				</p>
				<div class="rs_period">
					<label for="ticket_groups<?php echo $ticket->id; ?>" id="tgroups_label<?php echo $ticket->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_TICKET_GROUPS_INFO'); ?></label>
					<div class="rs_ticket_groups_<?php echo $ticket->id; ?>">
						<select class="inputbox rschosen" name="tickets[<?php echo $ticket->id; ?>][groups][]" id="ticket_groups<?php echo $ticket->id; ?>" multiple="multiple">
							<?php echo JHtml::_('select.options', $this->eventClass->groups(),'value','text', $ticket->groups); ?>
						</select>
					</div>	
				</div>
				<p>
					<label for="ticket_description<?php echo $ticket->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TICKET_DESCRIPTION'); ?></label>
					<textarea class="rs_txt" name="tickets[<?php echo $ticket->id; ?>][description]" id="ticket_description<?php echo $ticket->id; ?>"><?php echo $ticket->description; ?></textarea>
				</p>
			</fieldset>
			<button type="button" class="rs_button" onclick="if (confirm('<?php echo JText::_('COM_RSEVENTSPRO_CONFIRM_DELETE_TICKET',true); ?>')) { rs_edit_removeticket(<?php echo $ticket->id; ?>); }"><?php echo JText::_('COM_RSEVENTSPRO_REMOVE_TICKET'); ?></button>
			<button type="button" class="rs_button rs_submit"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
		</div>
		<?php } ?>
		<?php } ?>
		
		<span id="new_tickets"></span>
		<!-- End Tickets tab -->
		
		<!-- Discounts tab -->
		<div class="rs_right rs_display_none" id="rs_right_9">
			<fieldset>
				<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_DISCOUNTS') ?></legend>
				<div class="rs_period">
					<label for="early_fee" class="rs_discounts_label"><?php echo JText::_('COM_RSEVENTSPRO_APPLY_DISCOUNT') ?></label>
					<input type="text" value="<?php echo $this->row->early_fee; ?>" class="rs_inp_smaller" name="jform[early_fee]" id="early_fee" onkeyup="this.value = this.value.replace(/[^0-9]/g, '');" />
					<span class="rs_currency2">
						<select class="rs_sel rs_sel_smaller" name="jform[early_fee_type]" id="eventearly_fee_type">
							<?php echo JHtml::_('select.options', $this->eventClass->getDiscountTypes(),'value','text',$this->row->early_fee_type); ?>
						</select>
					</span>
				</div>
				<div class="rs_period">
					<label class="rs_discounts_label" for="early_fee_end"><?php echo JText::_('COM_RSEVENTSPRO_BOOKINGS_MADE_UNTIL'); ?></label>
					<div class="rs_starting" id="rs_ending_calendar" style="float: left;">
						<?php $early_fee_end = (!empty($this->row->early_fee_end) && $this->row->early_fee_end != $this->eventClass->getNullDate() ? JHTML::_('date', $this->row->early_fee_end, 'Y-m-d H:i:s') : ''); ?>
						<?php echo JHTML::_('rseventspro.calendar', $early_fee_end, 'jform[early_fee_end]', 'early_fee_end','%Y-%m-%d %H:%M:%S'); ?>
						
					</div>
				</div>
				<div class="rs_period">
					<label for="late_fee" class="rs_discounts_label"><?php echo JText::_('COM_RSEVENTSPRO_APPLY_FEE'); ?></label>
					<input type="text" value="<?php echo $this->row->late_fee; ?>" class="rs_inp_smaller" name="jform[late_fee]" id="late_fee" onkeyup="this.value = this.value.replace(/[^0-9]/g, '');" />
					<span class="rs_currency2">
						<select class="rs_sel rs_sel_smaller" name="jform[late_fee_type]" id="eventlate_fee_type">
							<?php echo JHtml::_('select.options', $this->eventClass->getDiscountTypes(),'value','text',$this->row->late_fee_type); ?>
						</select>
					</span>
				</div>
				<div class="rs_period">
					<label class="rs_discounts_label" for="late_fee_start"><?php echo JText::_('COM_RSEVENTSPRO_BOOKINGS_MADE_AFTER'); ?></label>
					<div class="rs_starting" id="rs_ending_calendar" style="float: left;">
						<?php $late_fee_start = (!empty($this->row->late_fee_start) && $this->row->late_fee_start != $this->eventClass->getNullDate() ? JHTML::_('date', $this->row->late_fee_start, 'Y-m-d H:i:s') : ''); ?>
						<?php echo JHTML::_('rseventspro.calendar', $late_fee_start, 'jform[late_fee_start]', 'late_fee_start','%Y-%m-%d %H:%M:%S'); ?>
					</div>
				</div>
			</fieldset>
			<button type="button" class="rs_button rs_submit"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
			<button type="button" class="rs_button rs_cancel"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
		</div>
		<!-- End Discounts tab -->
		
		<!-- New coupon tab -->
		<div class="rs_right rs_display_none" id="rs_right_10">
			<fieldset>
				<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_NEWCOUPON'); ?></legend>
				<p>
					<label for="coupon_name" id="cname_label"><?php echo JText::_('COM_RSEVENTSPRO_COUPON_NAME'); ?></label>
					<input type="text" value="" class="rs_inp" name="coupon_name" id="coupon_name" />
				</p>
				<p>
					<label for="coupon_code" id="ccode_label"><?php echo JText::_('COM_RSEVENTSPRO_COUPON_CODE'); ?></label>
					<textarea class="rs_txt" name="coupon_code" id="coupon_code"></textarea>
					<span class="rs_currency" id="cgenerate"><?php echo JText::_('COM_RSEVENTSPRO_GENERATE_FOR'); ?></span>
					<input type="text" value="3" class="rs_inp_smaller" name="coupon_times" id="coupon_times" onkeyup="this.value=this.value.replace(/[^0-9]/g, '');" />
					<span class="rs_currency" id="ccoupons" style="margin-right: 11px;"><?php echo JText::_('COM_RSEVENTSPRO_COUPONS'); ?></span>
					<a class="rs_generate_submit" id="coupon_href" onclick="rs_generate('coupon_code',$('coupon_times').value);"><?php echo JText::_('COM_RSEVENTSPRO_GENERATE'); ?></a>
				</p>
				<div class="rs_period">
					<div class="rs_calendar">
						<label for="coupon_start" id="cstart_label"><?php echo JText::_('COM_RSEVENTSPRO_COUPON_AVAILABILITY'); ?></label>
						<div class="rs_starting">
							<?php echo JHTML::_('rseventspro.calendar', '', 'coupon_start', 'coupon_start','%Y-%m-%d %H:%M:%S'); ?>
						</div>
					</div>
					<div class="rs_calendar">
						<label for="coupon_end" id="coupon_end_text"><?php echo JText::_('COM_RSEVENTSPRO_TO_LOWERCASE'); ?></label>
						<div class="rs_starting" id="rs_ending_calendar">
							<?php echo JHTML::_('rseventspro.calendar', '', 'coupon_end', 'coupon_end','%Y-%m-%d %H:%M:%S'); ?>
						</div>
					</div>
				</div>
				<p>
					<label for="coupon_usage" id="cusage_label"><?php echo JText::_('COM_RSEVENTSPRO_MAX_USAGE'); ?></label>
					<input type="text"  value="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED'); ?>" onfocus="if (this.value=='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>') this.value=''" onblur="if (this.value=='') this.value='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>'" class="rs_inp" name="coupon_usage" id="coupon_usage" onkeyup="this.value=this.value.replace(/[^0-9]/g, '');" />
				</p>
				<div class="rs_period">
					<label for="coupon_discount" id="cdiscount_label"><?php echo JText::_('COM_RSEVENTSPRO_APPLY_DISCOUNT'); ?></label>
					<input type="text" value="" class="rs_inp_smaller" name="coupon_discount" id="coupon_discount" />
					<span class="rs_currency">
						<select class="rs_sel rs_sel_smaller" name="coupon_type" id="coupon_type">
							<?php echo JHtml::_('select.options', $this->eventClass->getDiscountTypes()); ?>
						</select>
					</span>
					<span style="margin-right:20px;"><?php echo JText::_('COM_RSEVENTSPRO_TO_LOWERCASE'); ?></span>
					<select class="rs_sel" name="coupon_action" id="coupon_action">
						<?php echo JHtml::_('select.options', $this->eventClass->getDiscountActions()); ?>
					</select>
				</div>
				<div class="rs_period">
					<label for="coupon_groups" id="cgroups_label"><?php echo JText::_('COM_RSEVENTSPRO_INSTANT_DISCOUNT'); ?></label>
					<div class="rs_coupon_groups">
						<select class="inputbox rschosen" name="coupon_groups[]" id="coupon_groups" multiple="multiple">
							<?php echo JHtml::_('select.options', $this->eventClass->groups()); ?>
						</select>
					</div>	
				</div>
			</fieldset>
			<button type="button" class="rs_button" onclick="rs_edit_add_coupon('<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>','<?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?>','<?php echo JText::_('COM_RSEVENTSPRO_EMPTY_COUPON_NAME',true); ?>','<?php echo JText::_('COM_RSEVENTSPRO_EMPTY_DISCOUNT',true); ?>','<?php echo JText::_('COM_RSEVENTSPRO_REMOVE_COUPON',true); ?>','<?php echo JText::_('COM_RSEVENTSPRO_CONFIRM_DELETE_COUPON',true); ?>');"><?php echo JText::_('COM_RSEVENTSPRO_ADD_COUPON'); ?></button>
			<button type="button" class="rs_button rs_submit"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
		</div>
		<!-- End New coupon tab -->
		
		<!-- Coupons tab -->
		
		<?php $eventCoupons = $this->eventClass->getCoupons(); ?>
		<?php if (!empty($eventCoupons)) { ?>
		<?php foreach($eventCoupons as $coupon) { ?>
		<div class="rs_right rs_display_none" id="rs_right_c<?php echo $coupon->id; ?>">
			<fieldset>
				<legend><?php echo $coupon->name; ?></legend>
				<p>
					<label for="coupon_name<?php echo $coupon->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_COUPON_NAME'); ?></label>
					<input type="text" value="<?php echo $this->escape($coupon->name); ?>" class="rs_inp" name="coupons[<?php echo $coupon->id; ?>][name]" id="coupon_name<?php echo $coupon->id; ?>" />
				</p>
				<p>
					<label for="coupon_code<?php echo $coupon->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_COUPON_CODE'); ?></label>
					<textarea class="rs_txt rs_txt_small" name="coupons[<?php echo $coupon->id; ?>][code]" id="coupon_code<?php echo $coupon->id; ?>"><?php echo $coupon->code; ?></textarea>
					<span class="rs_currency" id="cgenerate"><?php echo JText::_('COM_RSEVENTSPRO_GENERATE_FOR'); ?></span>
					<input type="text" value="3" class="rs_inp_smaller" name="coupon_times" id="coupon_times<?php echo $coupon->id; ?>" onkeyup="this.value=this.value.replace(/[^0-9]/g, '');" />
					<span class="rs_currency" id="ccoupons" style="margin-right: 11px;"><?php echo JText::_('COM_RSEVENTSPRO_COUPONS'); ?></span>
					<a class="rs_generate_submit" id="coupon_href" onclick="rs_generate('coupon_code<?php echo $coupon->id; ?>',$('coupon_times<?php echo $coupon->id; ?>').value);"><?php echo JText::_('COM_RSEVENTSPRO_GENERATE'); ?></a>
				</p>
				<div class="rs_period">
					<div class="rs_calendar">
						<label for="coupon_start<?php echo $coupon->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_COUPON_AVAILABILITY'); ?></label>
						<div class="rs_starting">
							<?php $coupon_from = (!empty($coupon->from) && $coupon->from != $this->eventClass->getNullDate() ? JHTML::_('date', $coupon->from, 'Y-m-d H:i:s') : ''); ?>
							<?php echo JHTML::_('rseventspro.calendar', $coupon_from, 'coupons['.$coupon->id.'][from]', 'coupon_start'.$coupon->id,'%Y-%m-%d %H:%M:%S'); ?>
						</div>
					</div>
					<div class="rs_calendar">
						<label><?php echo JText::_('COM_RSEVENTSPRO_TO_LOWERCASE'); ?></label>
						<div class="rs_starting" id="rs_ending_calendar">
							<?php $coupon_to = (!empty($coupon->to) && $coupon->to != $this->eventClass->getNullDate() ? JHTML::_('date', $coupon->to, 'Y-m-d H:i:s') : ''); ?>
							<?php echo JHTML::_('rseventspro.calendar', $coupon_to, 'coupons['.$coupon->id.'][to]', 'coupon_end'.$coupon->id,'%Y-%m-%d %H:%M:%S'); ?>
						</div>
					</div>
				</div>
				<p>
					<label for="coupon_usage<?php echo $coupon->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_MAX_USAGE'); ?>:</label>
					<input type="text"  value="<?php echo empty($coupon->usage) ? JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED') : $coupon->usage; ?>" onfocus="if (this.value=='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>') this.value=''" onblur="if (this.value=='') this.value='<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNLIMITED',true); ?>'" class="rs_inp" name="coupons[<?php echo $coupon->id; ?>][usage]" id="coupon_usage<?php echo $coupon->id; ?>" onkeyup="this.value=this.value.replace(/[^0-9]/g, '');" />
				</p>
				<div class="rs_period">
					<label for="coupon_discount<?php echo $coupon->id; ?>"><?php echo JText::_('COM_RSEVENTSPRO_APPLY_DISCOUNT'); ?></label>
					<input type="text" value="<?php echo $coupon->discount; ?>" class="rs_inp_smaller" name="coupons[<?php echo $coupon->id; ?>][discount]" id="coupon_discount<?php echo $coupon->id; ?>" />
					<span class="rs_currency">
						<select class="rs_sel rs_sel_smaller" name="coupons[<?php echo $coupon->id; ?>][type]" id="couponstype">
							<?php echo JHtml::_('select.options', $this->eventClass->getDiscountTypes(),'value','text',$coupon->type); ?>
						</select>
					</span>
					<span style="margin-right:20px;"><?php echo JText::_('COM_RSEVENTSPRO_TO_LOWERCASE'); ?></span>
					<select class="rs_sel" name="coupons[<?php echo $coupon->id; ?>][action]" id="couponsaction">
						<?php echo JHtml::_('select.options', $this->eventClass->getDiscountActions(),'value','text', $coupon->action); ?>
					</select>
				</div>
				<div class="rs_period">
					<label for="coupon_groups" id="cgroups_label"><?php echo JText::_('COM_RSEVENTSPRO_INSTANT_DISCOUNT'); ?></label>
					<div class="rs_coupon_groups_<?php echo $coupon->id; ?>">
						<select class="inputbox rschosen" name="coupons[<?php echo $coupon->id; ?>][groups][]" id="coupon_groups<?php echo $coupon->id; ?>" multiple="multiple">
							<?php echo JHtml::_('select.options', $this->eventClass->groups(),'value','text', $coupon->groups); ?>
						</select>
					</div>	
				</div>
			</fieldset>
			<button type="button" class="rs_button" onclick="if (confirm('<?php echo JText::_('COM_RSEVENTSPRO_CONFIRM_DELETE_COUPON',true); ?>')) { rs_edit_remove_coupon(<?php echo $coupon->id; ?>); }"><?php echo JText::_('COM_RSEVENTSPRO_REMOVE_COUPON'); ?></button>
			<button type="button" class="rs_button rs_submit"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
		</div>
		<?php } ?>
		<?php } ?>
		
		
		<span id="new_coupons"></span>
		<!-- End Coupons tab -->
		
		<?php if (rseventsproHelper::isGallery()) { ?>
		<!-- Start RSMediaGallery tab -->
		
		<div class="rs_right rs_display_none" id="rs_right_rsm">
			<fieldset>
				<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_GALLERY'); ?></legend>
				<p>
					<label for="gallery_tags" class="rs_inline"><?php echo JText::_('COM_RSEVENTSPRO_GALLERY_TAGS'); ?></label>
					<div class="rs_gallery_tags">
						<select class="inputbox rschosen" name="jform[gallery_tags][]" id="gallery_tags" multiple="multiple">
							<?php echo JHtml::_('select.options', rseventsproHelper::getGalleryTags(), 'value','text',$this->eventClass->getSelectedGalleryTags()); ?>
						</select>
					</div>
				</p>
			</fieldset>
			<button type="button" class="rs_button rs_submit"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
			<button type="button" class="rs_button rs_cancel"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
		</div>
		
		<!-- End RSMediaGallery tab -->
		<?php } ?>
		
		<?php if (rseventsproHelper::pdf()) { ?>
		<!-- Start Ticket PDF tab -->
		
		<div class="rs_right rs_display_none" id="rs_right_td">
			<fieldset>
				<legend><?php echo JText::_('COM_RSEVENTSPRO_TICKET_PDF'); ?></legend>
				<p>
					<label for="eventticket_pdf"><?php echo JText::_('COM_RSEVENTSPRO_TICKET_PDF_ATTACH'); ?></label>
					<select name="jform[ticket_pdf]" id="eventticket_pdf" size="1" class="rs_sel rs_sel_smaller">
						<option value="0" <?php if ($this->row->ticket_pdf == 0) echo 'selected="selected"'; ?>><?php echo JText::_('JNO'); ?></option>
						<option value="1" <?php if ($this->row->ticket_pdf == 1) echo 'selected="selected"'; ?>><?php echo JText::_('JYES'); ?></option>
					</select>
				</p>
				<p>
					<label for="ticket_pdf_layout"><?php echo JText::_('COM_RSEVENTSPRO_TICKET_PDF_LAYOUT'); ?></label>
					<?php echo JEditor::getInstance(JFactory::getConfig()->get('editor'))->display('jform[ticket_pdf_layout]',$this->row->ticket_pdf_layout,'100%','50%',70,10, rseventsproHelper::getConfig('enable_buttons','bool')); ?>
				</p>
			</fieldset>
			<button type="button" class="rs_button rs_submit"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
			<button type="button" class="rs_button rs_cancel"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
		</div>
		
		<!-- End Ticket PDF tab -->
		<?php } ?>
		
        <?php JFactory::getApplication()->triggerEvent('rsepro_addMenuContent',array(array('data'=>&$this->row))); ?>
		
		<div id="rs_left">
			<div id="rs_event_photo">
				<a href="javascript:void(0)" id="rs_photo" onclick="rs_modal('<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=upload&id='.rseventsproHelper::sef($this->row->id,$this->row->name).'&tmpl=component'); ?>',400,80)">
					<?php 
						if ($this->row->icon && file_exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/e_'.$this->row->icon)) {
							$image = @getimagesize(JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/e_'.$this->row->icon);
							$width = isset($image[0]) ? $image[0] : 800;
							$height = isset($image[1]) ? $image[1] : 380;
							$iconsrc = JURI::root().'components/com_rseventspro/assets/images/events/thumbs/e_'.$this->row->icon;
							$customheight = $height > $width ? 'height="180"' : '';
						} else {
							$iconsrc = JURI::root().'components/com_rseventspro/assets/images/edit/profile_pic.png';
							$customheight = '';
						}
					?>
					<img id="rs_icon_img" src="<?php echo $iconsrc.'?nocache='.uniqid(''); ?>" alt="" <?php echo $customheight; ?> />
				</a>
				<a href="javascript:void(0)" id="rs_add_photo" onclick="rs_modal('<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=upload&id='.rseventsproHelper::sef($this->row->id,$this->row->name).'&tmpl=component'); ?>',400,80)"><?php echo $this->row->icon ? JText::_('COM_RSEVENTSPRO_CHANGE_EVENT_PHOTO') : JText::_('COM_RSEVENTSPRO_ADD_EVENT_PHOTO'); ?></a>
			</div>
			
			<ul id="rs_event_menu">
				<li id="rs_li_1"><a href="javascript:void(0)" class="rs_title_1 active" id="rs_menu_item_1"><span id="rs_icon_1"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_CREATE'); ?></span></a></li>
				<li id="rs_li_2"><a href="javascript:void(0)" class="rs_title_1" id="rs_menu_item_2"><span id="rs_icon_2"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_CATEGORIES'); ?></span></a></li>
				<li id="rs_li_7" class="rs_display_none" <?php echo $this->row->registration ? 'style="display:block;"' : ''; ?>><a href="javascript:void(0)" class="rs_title_2" id="rs_menu_item_7"><span id="rs_icon_3"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_REGISTRATION'); ?></span></a></li>
				<li id="rs_li_8" class="rs_display_none" <?php echo $this->row->registration ? 'style="display:block;"' : ''; ?>><a href="javascript:void(0)" class="rs_title_3" id="rs_menu_item_8"><span id="rs_icon_4"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_NEWTICKET'); ?></span></a></li>

				<?php if (!empty($eventTickets)) { ?>
				<?php foreach($eventTickets as $ticket) { ?>
				<li id="rs_li_t<?php echo $ticket->id; ?>" class="rs_display_none" <?php echo $this->row->registration ? 'style="display:block;"' : ''; ?>><a href="javascript:void(0)" class="rs_title_3" id="rs_menu_item_t<?php echo $ticket->id; ?>"><?php echo $ticket->name; ?></a></li>
				<?php } ?>
				<?php } ?>
				
				<li id="rs_li_tc" class="rs_display_none" <?php echo $this->row->ticketsconfig ? 'style="display:block;"' : ''; ?>><a href="javascript:void(0)" onclick="rs_modal_tickets();" class="rs_title_1" id="rs_menu_item_tc"><span id="rs_icon_td"><?php echo JText::_('COM_RSEVENTSPRO_TICKETS_CONFIGURATION'); ?></span></a></li>
				
				<?php if (rseventsproHelper::pdf()) { ?>
				<li id="rs_li_td" class="rs_display_none" <?php echo $this->row->registration ? 'style="display:block;"' : ''; ?>><a href="javascript:void(0)" class="rs_title_1" id="rs_menu_item_td"><span id="rs_icon_td"><?php echo JText::_('COM_RSEVENTSPRO_TICKET_PDF'); ?></span></a></li>
				<?php } ?>
				
				<?php JFactory::getApplication()->triggerEvent('rsepro_addMenuOptionRegistration'); ?>
				
				<li id="rs_li_9" class="rs_display_none" <?php echo $this->row->discounts ? 'style="display:block;"' : ''; ?>><a href="javascript:void(0)" class="rs_title_2" id="rs_menu_item_9"><span id="rs_icon_5"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_DISCOUNTS'); ?></span></a></li>
				<li id="rs_li_10" class="rs_display_none" <?php echo $this->row->discounts ? 'style="display:block;"' : ''; ?>><a href="javascript:void(0)" class="rs_title_3" id="rs_menu_item_10"><span id="rs_icon_6"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_NEWCOUPON'); ?></span></a></li>
				
				<?php if (!empty($eventCoupons)) { ?>
				<?php foreach($eventCoupons as $coupon) { ?>
				<li id="rs_li_c<?php echo $coupon->id; ?>" class="rs_display_none" <?php echo $this->row->discounts ? 'style="display:block;"' : ''; ?>><a href="javascript:void(0)" class="rs_title_3" id="rs_menu_item_c<?php echo $coupon->id; ?>"><?php echo $coupon->name; ?></a></li>
				<?php } ?>
				<?php } ?>
				
				<?php if (empty($this->row->parent)) { ?>
				<li id="rs_li_6" class="rs_display_none" <?php echo $this->row->recurring ? 'style="display:block;"' : ''; ?>><a href="javascript:void(0)" class="rs_title_2" id="rs_menu_item_6"><span id="rs_icon_7"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_RECURRING'); ?></span></a></li>
				<?php } ?>
				
				<?php if (!empty($this->permissions['can_upload']) || $this->admin) { ?>
				<li id="rs_li_3"><a href="javascript:void(0)" class="rs_title_1" id="rs_menu_item_3"><span id="rs_icon_8"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_FILES'); ?></span></a></li>
				<?php } ?>
				<li id="rs_li_4"><a href="javascript:void(0)" class="rs_title_1" id="rs_menu_item_4"><span id="rs_icon_9"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_CONTACT'); ?></span></a></li>
				<li id="rs_li_5"><a href="javascript:void(0)" class="rs_title_1" id="rs_menu_item_5"><span id="rs_icon_10"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_META'); ?></span></a></li>
				<li id="rs_li_11"><a href="javascript:void(0)" class="rs_title_1" id="rs_menu_item_11"><span id="rs_icon_11"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_FRONTEND'); ?></span></a></li>
				<?php if (rseventsproHelper::isGallery()) { ?>
				<li id="rs_li_rsm"><a href="javascript:void(0)" class="rs_title_1" id="rs_menu_item_rsm"><span id="rs_icon_rsm"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_GALLERY'); ?></span></a></li>
				<?php } ?>
				<?php JFactory::getApplication()->triggerEvent('rsepro_addMenuOption'); ?>
			</ul>            
        </div>
		
		<?php if (!empty($this->permissions['can_create_categories']) || $this->admin) { ?>
		<div id="rs_category_add" class="dialog">
			<span onclick="hm('box');" class="rs_modal_close"></span>
			<p><strong><?php echo JText::_('COM_RSEVENTSPRO_EVENT_ADD_CATEGORY'); ?></strong></p>
			<p><label><?php echo JText::_('COM_RSEVENTSPRO_EVENT_CATEGORY_NAME'); ?></label><input type="text" value="" id="category" name="category" class="rs_inp" /></p>
			<p>
				<label><?php echo JText::_('COM_RSEVENTSPRO_EVENT_CHOOSE_PARENT'); ?></label>
				<select class="rs_sel" name="parent" id="parent">
					<?php echo JHtml::_('select.options', JHtml::_('category.categories','com_rseventspro')); ?>
				</select>
			</p>
			<p><button type="button" class="rs_button" onclick="rs_edit_save_category()"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_ADD_CATEGORY_ADD'); ?></button></p>
		</div>
		<?php } ?>
	</div>
	
	
	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="task" value="rseventspro.save" />
	<input type="hidden" name="tab" value="0" id="tab" />
	<input type="hidden" name="frontend" value="1" id="frontend" />
	<input type="hidden" name="is12" value="<?php echo rseventsproHelper::getConfig('time_format','int'); ?>" id="is12" />
	<input type="hidden" name="jform[form]" value="<?php echo $this->row->form; ?>" id="form"/>
	<input type="hidden" name="jform[id]" id="eventID" value="<?php echo $this->row->id; ?>" />
</form>
<script type="text/javascript">
<?php if (!empty($this->permissions['can_repeat_events']) || $this->admin) { ?>
<?php if (empty($this->row->parent)) { ?>rs_check_repeat(<?php echo (int) $this->row->repeat_type; ?>);<?php } ?>
<?php } ?>
<?php if ($this->row->max_tickets) { ?>
rs_show_max_tickets($('max_tickets'));
<?php } ?>
<?php if ($this->row->overbooking) { ?>
rs_show_overbooking($('overbooking'));
<?php } ?>
</script>

<?php JHTML::_('behavior.keepalive'); ?>