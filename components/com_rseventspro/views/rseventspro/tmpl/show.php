<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.modal','.rs_modal');

$details	= rseventsproHelper::details($this->event->id);
$event		= $details['event'];
$categories = $details['categories'];
$tags		= $details['tags'];
$files		= $details['files'];
$repeats	= $details['repeats'];
$full		= rseventsproHelper::eventisfull($this->event->id);
$ongoing	= rseventsproHelper::ongoing($this->event->id); 
$featured 	= $event->featured ? ' rs_featured_event' : ''; ?>

<!-- Initialize map -->
<?php if (!empty($this->options['show_map']) && !empty($event->coordinates) && rseventsproHelper::getConfig('enable_google_maps','int')) { ?>
<?php JFactory::getDocument()->addScript('https://maps.google.com/maps/api/js?sensor=false'); ?>
<script type="text/javascript">
	var map_event;
	var marker_event;
	function initialize() {
		var mapDiv = document.getElementById('map-canvas');
		map_event = new google.maps.Map(mapDiv, {
		  center: new google.maps.LatLng(<?php echo $event->coordinates; ?>),
		  zoom: <?php echo rseventsproHelper::getConfig('google_map_zoom') ?>,
		  mapTypeId: google.maps.MapTypeId.ROADMAP,
		  scrollwheel: false
		});

		marker_event = new google.maps.Marker({
		  map: map_event,
		  title: '<?php echo addslashes($event->name); ?>',
		  position: new google.maps.LatLng(<?php echo $this->escape($event->coordinates); ?>),
		  draggable: false
		});
		
		var contentString = '<b><?php echo addslashes($event->name); ?></b> <br /> <?php echo JText::_('COM_RSEVENTSPRO_LOCATION_ADDRESS',true); ?>: <?php echo addslashes($event->address); ?> <?php if (!empty($event->locationlink)) echo '<br /><a target="_blank" href="'.addslashes($event->locationlink).'">'.addslashes($event->locationlink).'</a>'; ?>';
		
		var infowindow = new google.maps.InfoWindow({
			content: contentString
		});
		
		google.maps.event.addListener(marker_event, 'click', function() {
		  infowindow.open(map_event,marker_event);
		});
		
		google.maps.event.addListener(map_event, 'click', function() {
		  infowindow.close();
		});
	}
	google.maps.event.addDomListener(window, 'load', initialize);
</script>
<?php } ?>
<!--//end Initialize map-->

<!-- Initialize admin options -->
<?php if ($this->admin || $event->owner == $this->user || $event->sid == $this->user) { ?>
<script type="text/javascript">
	window.addEvent('domready', function(){
		var showMenu;
		$('rsep_admin_option').addEvent('click', function(e){
			if (!showMenu) {
				if(e) e.stopPropagation();
				$('rsep_admin_options').setStyle('display', 'block');
				showMenu = 1;
				$('rsep_admin_option').addClass('rs_is_open');
			} else {
				$('rsep_admin_option').removeClass('rs_is_open');
			}
		});
		
		$(document.body).addEvent('click',function(e) {
			if(showMenu) { 
				$('rsep_admin_options').setStyle('display', 'none');
				showMenu = 0;
				$('rsep_admin_option').removeClass('rs_is_open');
			}
		});
	});
</script>
<?php } ?>
<!--//end Initialize admin options -->

<?php 
	$links = rseventsproHelper::getConfig('modal','int');
	$class = ''; $rel_s = ''; $rel_i = ''; $rel_g = '';
	if ($links == 2) $class = ' modal';
	if ($links == 1) $rel_s = ' rel="rs_subscribe"'; else if ($links == 2) $rel_s = ' rel="{handler: \'iframe\',size: {x:650,y:400}}"';
	if ($links == 1) $rel_i = ' rel="rs_invite"'; else if ($links == 2) $rel_i = ' rel="{handler: \'iframe\',size: {x:650,y:400}}"';
	if ($links == 1) $rel_g = ' rel="rs_message"'; else if ($links == 2) $rel_g = ' rel="{handler: \'iframe\',size: {x:650,y:400}}"';
	$tmpl = $links == 0 ? '' : '&tmpl=component';
?>


<div id="rs_event_show" itemscope itemtype="http://schema.org/Event">

<!-- Event Title -->
<h1 class="<?php echo $full ? ' rs_event_full' : ''; ?><?php echo $ongoing ? ' rs_event_ongoing' : ''; ?><?php echo $featured; ?>" itemprop="name"><?php echo $this->escape($event->name); ?></h1>
<!--//end Event Title -->

<div class="rs_controls">
<!-- Admin options -->
	<?php if ($this->admin || $event->owner == $this->user || $event->sid == $this->user) { ?>
	<ul id="rsep_admin">
		<li class="rs_admin_first">
			<a href="javascript:void(0);" id="rsep_admin_option" class="rs_button_control" ><span class="rsep_admin"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_ADMIN_OPTIONS'); ?></span></a>
			<ul class="rs_admin_subs" id="rsep_admin_options" style="display: none;">
				<li><a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=edit&id='.rseventsproHelper::sef($event->id,$event->name)); ?>"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_EDIT'); ?></a></li>
				<li><a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=subscribers&id='.rseventsproHelper::sef($event->id,$event->name)); ?>"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_SUBSCRIBERS'); ?></a></li>
				<li><a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=message&id='.rseventsproHelper::sef($event->id,$event->name).$tmpl); ?>" class="<?php echo $class; ?>"<?php echo $rel_g; ?>><?php echo JText::_('COM_RSEVENTSPRO_EVENT_MESSAGE_TO_GUESTS'); ?></a></li>
				<?php if (!$this->eventended) { ?>
				<li><a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=reminder&id='.rseventsproHelper::sef($event->id,$event->name)); ?>"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_SEND_REMINDER'); ?></a></li>
				<?php } ?>
				<?php if ($this->eventended) { ?>
				<li><a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=postreminder&id='.rseventsproHelper::sef($event->id,$event->name)); ?>"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_SEND_POST_REMINDER'); ?></a></li>
				<?php } ?>
				<?php if ($this->report) { ?>
				<li><a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=reports&id='.rseventsproHelper::sef($event->id,$event->name)); ?>"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_REPORTS'); ?></a></li>
				<?php } ?>
				<li><a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=scan&id='.rseventsproHelper::sef($event->id,$event->name)); ?>"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_SCAN_TICKET'); ?></a></li>
				<li><a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.remove&id='.rseventsproHelper::sef($event->id,$event->name)); ?>" onclick="return confirm('<?php echo JText::_('COM_RSEVENTSPRO_EVENT_DELETE_CONFIRMATION'); ?>');"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_DELETE_EVENT'); ?></a></li>
			</ul>
		</li>
	</ul>
	<?php } ?>
<!--//end Admin options -->

	<?php if (!($this->admin || $event->owner == $this->user || $event->sid == $this->user) && $this->permissions['can_edit_events']) { ?>
	<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=edit&id='.rseventsproHelper::sef($event->id,$event->name)); ?>" class="rs_button_control rsep_edit"><span class="rsep_edit"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_EDIT'); ?></span></a>
	<?php } ?>

<!-- Invite/Join/Unsubscribe -->	
	<?php if ($this->cansubscribe['status']) { ?>
	<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=subscribe&id='.rseventsproHelper::sef($event->id,$event->name).$tmpl); ?>" class="rs_button_control<?php echo $class; ?> rsep_join"<?php echo $rel_s; ?> ><span class="rsep_join"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_JOIN'); ?></span></a> 
	<?php } ?>	
	<?php if (!$this->eventended) { ?>
	<?php if ($this->issubscribed) { ?>
	<?php if ($this->canunsubscribe) { ?>
	<?php if ($this->issubscribed == 1) { ?>
	<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.unsubscribe&id='.rseventsproHelper::sef($event->id,$event->name)); ?>" class="rs_button_control rsep_unsubscribe"><span class="rsep_unsubscribe"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_UNSUBSCRIBE'); ?></span></a> 
	<?php } else { ?>
	<?php $Uclass = $links == 0 || $links == 2 ? 'modal' : ''; ?>
	<?php $Urel = $links == 0 || $links == 2 ? 'rel="{handler: \'iframe\'}"' : 'rel="rs_unsubscribe"'; ?>
	<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=unsubscribe&id='.rseventsproHelper::sef($event->id,$event->name).'&tmpl=component'); ?>" class="rs_button_control rsep_unsubscribe <?php echo $Uclass; ?>" <?php echo $Urel; ?>><span class="rsep_unsubscribe"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_UNSUBSCRIBE'); ?></span></a> 
	<?php } ?>
	<?php } ?>
	<?php } ?>
	<?php if (!empty($this->options['show_invite'])) { ?>
	<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=invite&id='.rseventsproHelper::sef($event->id,$event->name).$tmpl); ?>" class="rs_button_control<?php echo $class; ?> rsep_invite"<?php echo $rel_i; ?>><span class="rsep_invite"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_INVITE'); ?></span></a>
	<?php } ?>
	<?php } ?>
	
	<?php if ($this->report) { ?>
	<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=report&tmpl=component&id='.rseventsproHelper::sef($event->id,$event->name)); ?>" class="rs_button_control modal rsep_flag" rel="{handler: 'iframe', size: {x:400,y:300}}"><span class="rsep_flag"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_REPORT'); ?></span></a>
	<?php } ?>
	
<!--//end Invite/Join/Unsubscribe -->
</div>
<div class="rs_clear"></div>


<!-- Image -->
<?php if (!empty($details['image_b'])) { ?>
<p class="rs_image" itemprop="image">
	<a href="<?php echo $details['image']; ?>" class="rs_modal">
		<img src="<?php echo $details['image_b']; ?>" alt="<?php echo $this->escape($event->name); ?>" width="<?php echo rseventsproHelper::getConfig('icon_big_width','int'); ?>px" />
	</a>
</p>
<?php } ?>
<!--//end Image -->

<!-- Start / End date -->
<?php if ($event->allday) { ?>
	<?php if (!empty($this->options['start_date'])) { ?>
	<p class="rsep_date">
		<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_ON'); ?> <?php echo rseventsproHelper::date($event->start,$this->config->global_date,true); ?> 
		<?php if (!empty($this->options['show_export'])) { ?>
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.export&id='.rseventsproHelper::sef($event->id,$event->name)); ?>">
			<span class="rsep_export"><?php echo JText::_('COM_RSEVENTSPRO_EXPORT_EVENT'); ?></span>
		</a> 
		<?php } ?>
	</p>
	<?php } ?>
<?php } else { ?>
	<?php if (!empty($this->options['start_date']) && !empty($this->options['end_date'])) { ?>
	<p class="rsep_date">
		<?php echo JText::_('COM_RSEVENTSPRO_EVENT_FROM'); ?> <?php echo rseventsproHelper::date($event->start,rseventsproHelper::showMask('start',$this->options),true); ?> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_UNTIL'); ?> <?php echo rseventsproHelper::date($event->end,rseventsproHelper::showMask('end',$this->options),true); ?> 
		<?php if (!empty($this->options['show_export'])) { ?>
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.export&id='.rseventsproHelper::sef($event->id,$event->name)); ?>">
			<span class="rsep_export"><?php echo JText::_('COM_RSEVENTSPRO_EXPORT_EVENT'); ?></span>
		</a> 
		<?php } ?>
	</p>

	<?php } else if (!empty($this->options['start_date'])) { ?>

	<p class="rsep_date">
		<?php echo JText::_('COM_RSEVENTSPRO_EVENT_STARTING_ON'); ?> <?php echo rseventsproHelper::date($event->start,rseventsproHelper::showMask('start',$this->options),true); ?> 
		<?php if (!empty($this->options['show_export'])) { ?>
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.export&id='.rseventsproHelper::sef($event->id,$event->name)); ?>">
			<span class="rsep_export"><?php echo JText::_('COM_RSEVENTSPRO_EXPORT_EVENT'); ?></span>
		</a> 
		<?php } ?>
	</p>

	<?php } else if (!empty($this->options['end_date'])) { ?>
	<p class="rsep_date">
		<?php echo JText::_('COM_RSEVENTSPRO_EVENT_ENDING_ON'); ?> <?php echo rseventsproHelper::date($event->end,rseventsproHelper::showMask('end',$this->options),true); ?>
		<?php if (!empty($this->options['show_export'])) { ?>
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.export&id='.rseventsproHelper::sef($event->id,$event->name)); ?>">
			<span class="rsep_export"><?php echo JText::_('COM_RSEVENTSPRO_EXPORT_EVENT'); ?></span>
		</a> 
		<?php } ?>
	</p>

	<?php } ?>
<?php } ?>
<!--//end Start / End date -->


<div class="rsep_contact_block">
<!-- Location -->
<?php if (!empty($event->lpublished) && !empty($this->options['show_location'])) { ?>
<p class="rsep_location" itemscope itemtype="http://schema.org/EventVenue">
	<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_AT'); ?> <a itemprop="name" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=location&id='.rseventsproHelper::sef($event->locationid,$event->location)); ?>"><?php echo $event->location; ?></a>
	<span itemprop="address" style="display:none;"><?php echo $event->address; ?></span>
</p>
<?php } ?>
<!--//end Location -->

<!-- Posted By -->
<?php if (!empty($this->options['show_postedby'])) { ?>
<p class="rsep_posted">
	<?php echo JText::_('COM_RSEVENTSPRO_EVENT_POSTED_BY'); ?> 
	<?php if (!empty($event->ownerprofile)) { ?><a href="<?php echo $event->ownerprofile; ?>"><?php } ?>
	<?php echo $event->ownername; ?>
	<?php if (!empty($event->ownerprofile)) { ?></a><?php } ?>
</p>
<?php } ?>
<!--//end Posted By -->

<!--Contact information -->
<?php if (!empty($this->options['show_contact'])) { ?>
<?php if (!empty($event->email)) { ?>
<p class="rsep_mail">
	<a href="mailto:<?php echo $event->email; ?>"><?php echo $event->email; ?></a>
</p>
<?php } ?>
<?php if (!empty($event->phone)) { ?>
<p class="rsep_phone">	
	<?php echo $event->phone; ?>
</p>
<?php } ?>
<?php if (!empty($event->URL)) { ?>
<p class="rsep_url">
	<a href="<?php echo $event->URL; ?>" target="_blank"><?php echo $event->URL; ?></a>
</p>
<?php } ?>
<?php } ?>
<!--//end Contact information -->

</div>

<div class="rsep_taxonomy_block">

<!-- Categories -->
<?php if (!empty($categories) && !empty($this->options['show_categories'])) { ?>
<p class="rsep_categories">
	<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CATEGORIES'); ?>: <?php echo $categories; ?>
</p>
<?php } ?>

<!-- Tags -->
<?php if (!empty($tags) && !empty($this->options['show_tags'])) { ?>
<p class="rsep_tags">
	<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_TAGS'); ?>: <?php echo $tags; ?>
</p>
<?php } ?>
<!--//end Tags -->

<!-- Rating -->
<?php if (!empty($this->options['enable_rating'])) { ?>
	<div class="rs_rating_text">
		<?php echo JText::_('COM_RSEVENTSPRO_RATE_EVENT'); ?>: 
	</div>
	<?php echo rseventsproHelper::rating($event->id); ?>
	<div class="rs_clear"></div>
<?php } ?>
<!--//end Rating -->

</div>

<!-- FB / Twitter / Gplus sharing -->
<?php if (!empty($this->options['enable_fb_like']) || !empty($this->options['enable_twitter']) || !empty($this->options['enable_gplus']) || !empty($this->options['enable_linkedin'])) { ?>
<div class="rs_sharing">	
	<?php if (!empty($this->options['enable_fb_like'])) { ?>
		<div style="float:left;" id="rsep_fb_like">
			<div id="fb-root"></div>
			<fb:like href="<?php echo rseventsproHelper::shareURL($event->id,$event->name); ?>" send="true" layout="button_count" width="150" show_faces="false"></fb:like>
		</div>
	<?php } ?>

	<?php if (!empty($this->options['enable_twitter'])) { ?>
		<div style="float:left;" id="rsep_twitter">
			<a href="https://twitter.com/share" class="twitter-share-button" data-text="<?php echo $this->escape($event->name); ?>">Tweet</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</div>
	<?php } ?>
	
	<?php if (!empty($this->options['enable_gplus'])) { ?>
		<div style="float:left;" id="rsep_gplus">
			<!-- Place this tag where you want the +1 button to render -->
			<g:plusone size="medium"></g:plusone>

			<!-- Place this render call where appropriate -->
			<script type="text/javascript">
			  (function() {
				var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
				po.src = 'https://apis.google.com/js/plusone.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
			  })();
			</script>
		</div>
	<?php } ?>
	
	<?php if (!empty($this->options['enable_linkedin'])) { ?>
		<div style="float:left;" id="rsep_linkedin">
			<script src="//platform.linkedin.com/in.js" type="text/javascript"></script>
			<script type="IN/Share" data-counter="right"></script>
		</div>
	<?php } ?>
</div>
<div class="rs_clear"></div>
<?php } ?>
<!--//end FB / Twitter / Gplus sharing -->

<!-- Description -->
<?php if (!empty($this->options['show_description']) && !empty($event->description)) { ?>
	<span itemprop="description"><?php echo $event->description; ?></span>
	<div class="rs_clear"></div>
<?php } ?>
<!--//end Description -->

<!-- Google maps -->
<?php if (!empty($this->options['show_map']) && !empty($event->coordinates) && rseventsproHelper::getConfig('enable_google_maps','int')) { ?>
	<div id="map-canvas" style="width: 100%; height: 200px;"></div>
	<br />
<?php } ?>
<!--//end Google maps -->


<!-- RSMediaGallery! -->
<?php echo rseventsproHelper::gallery('event',$event->id); ?>
<!--//end RSMediaGallery! -->

<!-- Repeated events -->
<?php if (!empty($this->options['show_repeats']) && !empty($repeats)) { ?>
<div class="rs_clear"></div>
<h3><?php echo JText::_('COM_RSEVENTSPRO_EVENT_REPEATS'); ?></h3>
<ul class="rs_repeats" id="rs_repeats">
<?php foreach ($repeats as $repeat) { ?>
	<li>
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($repeat->id,$repeat->name)); ?>"><?php echo $repeat->name; ?></a>
		<?php $dateMask = $repeat->allday ? rseventsproHelper::getConfig('global_date') : null; ?>
		(<?php echo rseventsproHelper::date($repeat->start,$dateMask,true); ?>)
	</li>
<?php } ?>
</ul>
<div class="rs_repeats_control" id="rs_repeats_control" style="display:none;">
	<a id="more" href="javascript:void(0)" onclick="show_more();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_MORE') ?></a>
	<a id="less" href="javascript:void(0)" onclick="show_less();" style="display:none;"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_LESS') ?></a>
</div>
<div class="rs_clear"></div>
<?php } ?>
<!--//end Repeated events -->

<!-- Files -->
<?php if (!empty($this->options['show_files']) && !empty($files)) { ?>
	<div class="rs_files_container">
		<h3><?php echo JText::_('COM_RSEVENTSPRO_EVENT_FILES'); ?></h3>
		<?php echo $files; ?>
	</div>
	<div class="rs_clear"></div>
<?php } ?>
<!--//end Files -->

<!-- Show subscribers -->
<?php if ($event->show_registered) { ?>
<?php if (!empty($this->guests)) { ?>
<h3><?php echo JText::_('COM_RSEVENTSPRO_EVENT_GUESTS'); ?></h3>
<ul class="rs_guests">
<?php foreach ($this->guests as $guest) { ?>
	<li>
		<?php if (!empty($guest->url)) { ?><a href="<?php echo $guest->url; ?>"><?php } ?>
		<?php echo $guest->avatar; ?>
		<?php echo $guest->name; ?>
		<?php if (!empty($guest->url)) { ?></a><?php } ?>
	</li>
<?php } ?>
</ul>
<div class="rs_clear"></div>
<?php } ?>
<?php } ?>
<!--//end Show subscribers -->

<!-- Comments -->
<?php if ($event->comments) { ?>
	<div class="rs_comments">
		<?php echo rseventsproHelper::comments($event->id,$event->name); ?>
	</div>
	<div class="rs_clear"></div>
<?php } ?>
<!--//end Comments -->

<?php if (($event->comments && rseventsproHelper::getConfig('event_comment','int') == 1) || !empty($this->options['enable_fb_like'])) { ?>
<script src="http://connect.facebook.net/en_US/all.js" type="text/javascript"></script>
<script type="text/javascript">
	FB.init({appId: '340486642645761', status: true, cookie: true, xfbml: true});
</script>
<?php } ?>

<div style="display:none"><span itemprop="startDate"><?php echo rseventsproHelper::date($event->start,'Y-m-d H:i:s'); ?></span></div>
<div style="display:none"><span itemprop="endDate"><?php echo rseventsproHelper::date($event->end,'Y-m-d H:i:s'); ?></span></div>

</div>