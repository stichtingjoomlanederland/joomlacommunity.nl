<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

$details	= rseventsproHelper::details($this->event->id, null, true);
$event		= $details['event'];
$categories = $details['categories'];
$tags		= $details['tags'];
$files		= $details['files'];
$repeats	= $details['repeats'];
$speakers	= $details['speakers'];
$full		= rseventsproHelper::eventisfull($this->event->id);
$ongoing	= rseventsproHelper::ongoing($this->event->id); 
$featured 	= $event->featured ? ' rs_featured_event' : ''; 
$description= empty($event->description) ? $event->small_description : $event->description;
$links		= rseventsproHelper::getConfig('modal','int');
$tmpl		= $links == 0 ? '' : '&tmpl=component';

$subscribeURL	= $links == 2 ? 'javascript:void(0);' : rseventsproHelper::route('index.php?option=com_rseventspro&layout=subscribe&id='.rseventsproHelper::sef($event->id,$event->name).$tmpl);
$inviteURL		= $links == 2 ? 'javascript:void(0);' : rseventsproHelper::route('index.php?option=com_rseventspro&layout=invite&id='.rseventsproHelper::sef($event->id,$event->name).$tmpl);
$messageURL		= $links == 2 ? 'javascript:void(0);' : rseventsproHelper::route('index.php?option=com_rseventspro&layout=message&id='.rseventsproHelper::sef($event->id,$event->name).$tmpl);
$unsubscribeURL	= $links == 0 || $links == 2 ? 'javascript:void(0);' : rseventsproHelper::route('index.php?option=com_rseventspro&layout=unsubscribe&id='.rseventsproHelper::sef($event->id,$event->name).'&tmpl=component');

rseventsproHelper::richSnippet($details); ?>

<?php if (!empty($this->options['show_counter'])) { ?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	RSEventsPro.Counter.options = {
		userTime: <?php echo !empty($this->options['counter_utc']) ? 'true' : 'false';  ?>,
		counterID: 'rsepro-counter',
		containerID: 'rsepro-counter-container',
		deadlineUTC: '<?php echo rseventsproHelper::showdate($event->start,'c',false,'UTC'); ?>',
		deadline: '<?php echo rseventsproHelper::showdate($event->start,'Y-m-d\TH:i:s',false); ?>+00:00'
	}
	
	RSEventsPro.Counter.init();
});
</script>
<?php } ?>

<!-- Initialize map -->
<?php if (!empty($this->options['show_map']) && !empty($event->coordinates) && rseventsproHelper::getConfig('enable_google_maps','int')) { ?>
<script type="text/javascript">
var rseproeventmap;
jQuery(document).ready(function (){
	rseproeventmap = jQuery('#map-canvas').rsjoomlamap({
		zoom: <?php echo (int) $this->config->google_map_zoom ?>,
		center: '<?php echo $this->config->google_maps_center; ?>',
		markerDraggable: false,
		markers: [
			{
				title : '<?php echo addslashes($event->name); ?>',
				position: '<?php echo $this->escape($event->coordinates); ?>',
				<?php if ($event->marker) echo "icon : '".addslashes(rseventsproHelper::showMarker($event->marker))."',\n"; ?>
				content: '<div id="content"><b><?php echo addslashes($event->name); ?></b> <br /> <?php echo JText::_('COM_RSEVENTSPRO_LOCATION_ADDRESS',true); ?>: <?php echo addslashes($event->address); ?> <?php if (!empty($event->locationlink)) { echo '<br /><a target="_blank" href="'.addslashes($event->locationlink).'">'.addslashes($event->locationlink).'</a>'; } ?></div>'
			}
		]
	});
});
</script>
<?php } ?>
<!--//end Initialize map-->

<?php JFactory::getApplication()->triggerEvent('rsepro_onBeforeEventDisplay',array(array('event' => &$event, 'categories' => &$categories, 'tags' => &$tags))); ?>

<div id="rs_event_show">
	
	<!-- Event Message -->
	<?php if ($full && $event->event_full && !$this->eventended) { ?>
	<div class="alert rse_event_message"><?php echo $event->event_full; ?></div>
	<?php } ?>
	<?php if ($this->eventended && $event->event_ended) { ?>
	<div class="alert rse_event_message"><?php echo $event->event_ended; ?></div>
	<?php } ?>
	<!-- end Event Message -->

	<!-- Event Title -->
	<h1 class="<?php echo $full ? ' rs_event_full' : ''; ?><?php echo $ongoing ? ' rs_event_ongoing' : ''; ?><?php echo $featured; ?>"><?php echo $this->escape($event->name); ?></h1>
	<!--//end Event Title -->

	<div class="rs_controls">
	<!-- Admin options -->
		<?php if ($this->admin || $event->owner == $this->user || $event->sid == $this->user) { ?>
		<div class="btn-group">
			<button data-toggle="dropdown" class="btn dropdown-toggle"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_ADMIN_OPTIONS'); ?> <span class="caret"></span></button>
			<ul class="dropdown-menu">
				<li>
					<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=edit&id='.rseventsproHelper::sef($event->id,$event->name)); ?>">
						<i class="fa fa-pencil fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_EDIT'); ?>
					</a>
				</li>
				<?php if ($event->rsvp) { ?>
				<li>
					<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=rsvp&id='.rseventsproHelper::sef($event->id,$event->name)); ?>">
						<i class="fa fa-users fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_RSVP_GUESTS'); ?>
					</a>
				</li>
				<?php } ?>
				<li>
					<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=subscribers&id='.rseventsproHelper::sef($event->id,$event->name)); ?>">
						<i class="fa fa-users fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_SUBSCRIBERS'); ?>
					</a>
				</li>
				<li>
					<a href="<?php echo $messageURL; ?>" rel="rs_message"<?php if ($links == 2) echo ' onclick="jQuery(\'#rseMessageModal\').modal(\'show\');"'; ?>>
						<i class="fa fa-envelope-o fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_MESSAGE_TO_GUESTS'); ?>
					</a>
				</li>
				<?php if (!$this->eventended) { ?>
				<li>
					<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=reminder&id='.rseventsproHelper::sef($event->id,$event->name)); ?>">
						<i class="fa fa-envelope fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_SEND_REMINDER'); ?>
					</a>
				</li>
				<?php } ?>
				<?php if ($this->eventended) { ?>
				<li>
					<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=postreminder&id='.rseventsproHelper::sef($event->id,$event->name)); ?>">
						<i class="fa fa-envelope fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_SEND_POST_REMINDER'); ?>
					</a>
				</li>
				<?php } ?>
				<?php if ($this->report) { ?>
				<li>
					<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=reports&id='.rseventsproHelper::sef($event->id,$event->name)); ?>">
						<i class="fa fa-flag fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_REPORTS'); ?>
					</a>
				</li>
				<?php } ?>
				<li>
					<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=scan&id='.rseventsproHelper::sef($event->id,$event->name)); ?>">
						<i class="fa fa-barcode fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_SCAN_TICKET'); ?>
					</a>
				</li>
				<li>
					<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.remove&id='.rseventsproHelper::sef($event->id,$event->name)); ?>" onclick="return confirm('<?php echo JText::_('COM_RSEVENTSPRO_EVENT_DELETE_CONFIRMATION'); ?>');">
						<i class="fa fa-trash fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_DELETE_EVENT'); ?>
					</a>
				</li>
			</ul>
		</div>
		<?php } ?>
	<!--//end Admin options -->

		<?php if (!($this->admin || $event->owner == $this->user || $event->sid == $this->user) && $this->permissions['can_edit_events']) { ?>
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=edit&id='.rseventsproHelper::sef($event->id,$event->name)); ?>" class="btn">
			<i class="fa fa-pencil fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_EDIT'); ?>
		</a>
		<?php } ?>
		
		<?php if (!($this->admin || $event->owner == $this->user || $event->sid == $this->user) && $this->permissions['can_delete_events']) { ?>
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.remove&id='.rseventsproHelper::sef($event->id,$event->name)); ?>" class="btn" onclick="return confirm('<?php echo JText::_('COM_RSEVENTSPRO_EVENT_DELETE_CONFIRMATION'); ?>');">
			<i class="fa fa-trash fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_DELETE_EVENT'); ?>
		</a>
		<?php } ?>

	<!-- Invite/Join/Unsubscribe -->	
		<?php if ($this->cansubscribe['status']) { ?>
		<a href="<?php echo $subscribeURL; ?>" class="btn" rel="rs_subscribe"<?php if ($links == 2) echo ' onclick="jQuery(\'#rseSubscribeModal\').modal(\'show\');"'; ?>>
			<i class="fa fa-check fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_JOIN'); ?>
		</a>
		<?php } ?>	
		<?php if (!$this->eventended) { ?>
		<?php if ($this->issubscribed) { ?>
		<?php if ($this->canunsubscribe) { ?>
		<?php if ($this->issubscribed == 1) { ?>
		<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.unsubscribe&id='.rseventsproHelper::sef($event->id,$event->name)); ?>" class="btn">
			<i class="fa fa-times fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_UNSUBSCRIBE'); ?>
		</a>
		<?php } else { ?>
		<a href="<?php echo $unsubscribeURL; ?>" class="btn" <?php echo $links == 1 ? 'rel="rs_unsubscribe"' : 'onclick="jQuery(\'#rseUnsubscribeModal\').modal(\'show\');"'; ?>>
			<i class="fa fa-times fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_UNSUBSCRIBE'); ?>
		</a>
		<?php } ?>
		<?php } ?>
		<?php } ?>
		<?php } ?>
		
		<?php if ((!$this->eventended && !empty($this->options['show_invite'])) || $this->report || !empty($this->options['show_print']) || !empty($this->options['show_export']) || $this->config->timezone) { ?>
		<div class="btn-group">
			<button data-toggle="dropdown" class="btn dropdown-toggle"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_USER_OPTIONS'); ?> <span class="caret"></span></button>
			<ul class="dropdown-menu">
				<?php if (!$this->eventended && !empty($this->options['show_invite'])) { ?>
				<li>
					<a href="<?php echo $inviteURL; ?>" rel="rs_invite"<?php if ($links == 2) echo ' onclick="jQuery(\'#rseInviteModal\').modal(\'show\');"'; ?>>
						<i class="fa fa-plus fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_INVITE'); ?>
					</a>
				</li>
				<?php } ?>
				
				<?php if ($this->report) { ?>
				<li>			
					<a href="javascript:void(0);" onclick="jQuery('#rseReportModal').modal('show');">
						<i class="fa fa-flag fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_REPORT'); ?>
					</a>
				</li>
				<?php } ?>
				
				<?php if (!empty($this->options['show_print'])) { ?>
				<li>
					<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=print&tmpl=component&id='.rseventsproHelper::sef($event->id,$event->name)); ?>" onclick="window.open(this.href,'print','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,top=200,left=200,directories=no,location=no'); return false;">
						<i class="fa fa-print fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_PRINT'); ?>
					</a>
				</li>
				<?php } ?>
				
				<?php if (!empty($this->options['show_export'])) { ?>
				<li>
					<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.export&id='.rseventsproHelper::sef($event->id,$event->name)); ?>">
						<i class="fa fa-calendar fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_EXPORT_EVENT'); ?>
					</a> 
				</li> 
				<?php } ?>
				
				<?php if ($this->config->timezone) { ?>
				<li>
					<a href="#timezoneModal" data-toggle="modal">
						<i class="fa fa-clock-o fa-fw"></i> <?php echo rseventsproHelper::getTimezone(); ?>
					</a> 
				</li>
				<?php } ?>
			</ul>
		</div>
		<?php } ?>
	<!--//end Invite/Join/Unsubscribe -->

		<?php if ($event->rsvp) { ?>
		<?php $rsvpOptions = rseventsproHelper::getRSVPOptions($event->id); ?>
		<?php if (rseventsproHelper::canRSVP($event->id)) { ?>
		<?php if (!$this->eventended) { ?>
		<div id="rsepro_rsvp" class="btn-group">
			<a id="rsepro_going" class="btn <?php if ($rsvpOptions->rsvp == 'going') echo 'btn-success hasTooltip'; ?><?php echo $rsvpOptions->offClass; ?>" title="<?php if ($rsvpOptions->rsvp == 'going') echo JText::_('COM_RSEVENTSPRO_RSVP_INFO'); ?> <?php echo $rsvpOptions->offTitle; ?>" <?php if ($rsvpOptions->canRSVP) { ?>onclick="rsepro_rsvp(<?php echo $event->id; ?>, 'going');"<?php } ?>>
				<?php echo JText::_('COM_RSEVENTSPRO_RSVP_GOING'); ?>
			</a>
			<a id="rsepro_interested" class="btn <?php if ($rsvpOptions->rsvp == 'interested') echo 'btn-success hasTooltip'; ?><?php echo $rsvpOptions->offClass; ?>" title="<?php if ($rsvpOptions->rsvp == 'interested') echo JText::_('COM_RSEVENTSPRO_RSVP_INFO'); ?> <?php echo $rsvpOptions->offTitle; ?>" <?php if ($rsvpOptions->canRSVP) { ?>onclick="rsepro_rsvp(<?php echo $event->id; ?>, 'interested');"<?php } ?>>
				<?php echo JText::_('COM_RSEVENTSPRO_RSVP_INTERESTED'); ?>
			</a>
			<a id="rsepro_notgoing" class="btn <?php if ($rsvpOptions->rsvp == 'notgoing') echo 'btn-success hasTooltip'; ?><?php echo $rsvpOptions->offClass; ?>" title="<?php if ($rsvpOptions->rsvp == 'notgoing') echo JText::_('COM_RSEVENTSPRO_RSVP_INFO'); ?> <?php echo $rsvpOptions->offTitle; ?>" <?php if ($rsvpOptions->canRSVP) { ?>onclick="rsepro_rsvp(<?php echo $event->id; ?>, 'notgoing')"<?php } ?>>
				<?php echo JText::_('COM_RSEVENTSPRO_RSVP_NOT_GOING'); ?>
			</a>
		</div>
		<?php } else { ?>
		<?php if (isset($rsvpOptions->rsvp)) { ?>
		<button class="btn btn-success disabled"><?php echo rseventsproHelper::RSVPStatus($rsvpOptions->rsvp); ?></button>
		<?php } ?>
		<?php } ?>
		<?php } ?>
		<?php } ?>
	</div>
	<div class="rs_clear"></div>
	
	<?php if (!empty($this->options['show_counter'])) { ?>
	<div id="rsepro-counter-container" class="rsepro-counter">
		<div id="rsepro-counter">
			<div>
				<span class="rsepro-counter-days"></span>
				<div class="rsepro-counter-text"><?php echo JText::_('COM_RSEVENTSPRO_COUNTER_DAYS'); ?></div>
			</div>
			<div>
				<span class="rsepro-counter-hours"></span>
				<div class="rsepro-counter-text"><?php echo JText::_('COM_RSEVENTSPRO_COUNTER_HOURS'); ?></div>
			</div>
			<div>
				<span class="rsepro-counter-minutes"></span>
				<div class="rsepro-counter-text"><?php echo JText::_('COM_RSEVENTSPRO_COUNTER_MINUTES'); ?></div>
			</div>
			<div>
				<span class="rsepro-counter-seconds"></span>
				<div class="rsepro-counter-text"><?php echo JText::_('COM_RSEVENTSPRO_COUNTER_SECONDS'); ?></div>
			</div>
		</div>
	</div>
	<?php } ?>

	<!-- Image -->
	<?php if (!empty($details['image_b'])) { ?>
	<div class="rs_image">
		<a href="javascript:void(0);" onclick="rsepro_show_image('<?php echo $details['image']; ?>');" class="thumbnail">
			<img src="<?php echo $details['image_b']; ?>" alt="<?php echo $this->escape($event->name); ?>" width="<?php echo rseventsproHelper::getConfig('icon_big_width','int'); ?>px" />
		</a>
	</div>
	<?php } ?>
	<!--//end Image -->

	<!-- Start / End date -->
	<?php if ($event->allday) { ?>
		<?php if (!empty($this->options['start_date'])) { ?>
		<div class="rsep_date">
			<i class="fa fa-calendar fa-fw"></i> 
			<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_ON'); ?> <?php echo rseventsproHelper::showdate($event->start,$this->config->global_date,true); ?> 
		</div>
		<?php } ?>
	<?php } else { ?>
		
		<?php if (!empty($this->options['start_date']) || !empty($this->options['start_time']) || !empty($this->options['end_date']) || !empty($this->options['end_time'])) { ?>
		<div class="rsep_date">
			<i class="fa fa-calendar fa-fw"></i> 
			<?php if (!empty($this->options['start_date']) || !empty($this->options['start_time'])) { ?>
				<?php if ((!empty($this->options['start_date']) || !empty($this->options['start_time'])) && empty($this->options['end_date']) && empty($this->options['end_time'])) { ?>
				<?php echo JText::_('COM_RSEVENTSPRO_EVENT_STARTING_ON'); ?>
				<?php } else { ?>
				<?php echo JText::_('COM_RSEVENTSPRO_EVENT_FROM'); ?> 
				<?php } ?>
				
				<?php echo rseventsproHelper::showdate($event->start,rseventsproHelper::showMask('start',$this->options),true); ?>
			<?php } ?>
			
			<?php if (!empty($this->options['end_date']) || !empty($this->options['end_time'])) { ?>
				<?php if ((!empty($this->options['end_date']) || !empty($this->options['end_time'])) && empty($this->options['start_date']) && empty($this->options['start_time'])) { ?>
				<?php echo JText::_('COM_RSEVENTSPRO_EVENT_ENDING_ON'); ?>
				<?php } else { ?>
				<?php echo JText::_('COM_RSEVENTSPRO_EVENT_UNTIL'); ?>
				<?php } ?>
			
				<?php echo rseventsproHelper::showdate($event->end,rseventsproHelper::showMask('end',$this->options),true); ?>
			<?php } ?>
			
		</div>
		<?php } ?>
		
	<?php } ?>
	<!--//end Start / End date -->


	<div class="rsep_contact_block">
		<!-- Location -->
		<?php if (!empty($event->lpublished) && !empty($this->options['show_location'])) { ?>
		<div class="rsep_location">
			<i class="fa fa-map-marker fa-fw"></i> 
			<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_AT'); ?> <a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=location&id='.rseventsproHelper::sef($event->locationid,$event->location)); ?>"><?php echo $event->location; ?></a>
		</div>
		<?php } ?>
		<!--//end Location -->

		<!-- Posted By -->
		<?php if (!empty($this->options['show_postedby'])) { ?>
		<div class="rsep_posted">
			<i class="fa fa-user fa-fw"></i> 
			<?php echo JText::_('COM_RSEVENTSPRO_EVENT_POSTED_BY'); ?> 
			<?php if (!empty($event->ownerprofile)) { ?><a href="<?php echo $event->ownerprofile; ?>"><?php } ?>
			<?php echo $event->ownername; ?>
			<?php if (!empty($event->ownerprofile)) { ?></a><?php } ?>
		</div>
		<?php } ?>
		<!--//end Posted By -->

		<!--Contact information -->
		<?php if (!empty($this->options['show_contact'])) { ?>
		<?php if (!empty($event->email)) { ?>
		<div class="rsep_mail">
			<i class="fa fa-envelope fa-fw"></i> <a href="mailto:<?php echo $event->email; ?>"><?php echo $event->email; ?></a>
		</div>
		<?php } ?>
		<?php if (!empty($event->phone)) { ?>
		<div class="rsep_phone">	
			<i class="fa fa-phone fa-fw"></i> <?php echo $event->phone; ?>
		</div>
		<?php } ?>
		<?php if (!empty($event->URL)) { ?>
		<div class="rsep_url">
			<i class="fa fa-globe fa-fw"></i> <a href="<?php echo $event->URL; ?>" target="_blank"><?php echo $event->URL; ?></a>
		</div>
		<?php } ?>
		<?php } ?>
		<!--//end Contact information -->
		
	</div>

	<div class="rsep_taxonomy_block">
	
		<!-- Categories -->
		<?php if (!empty($categories) && !empty($this->options['show_categories'])) { ?>
		<div class="rsep_categories">
			<i class="fa fa-folder fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CATEGORIES'); ?>: <?php echo $categories; ?>
		</div>
		<?php } ?>

		<!-- Tags -->
		<?php if (!empty($tags) && !empty($this->options['show_tags'])) { ?>
		<div class="rsep_tags">
			<i class="fa fa-tags fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_TAGS'); ?>: <?php echo $tags; ?>
		</div>
		<?php } ?>
		<!--//end Tags -->

		<?php if (!empty($this->options['show_hits'])) { ?>
		<!-- Hits -->
		<div class="rsep_hits">
			<i class="fa fa-eye fa-fw"></i> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_HITS'); ?>: <?php echo $event->hits; ?>
		</div>
		<!--//end Hits -->
		<?php } ?>

		<!-- Rating -->
		<?php if (!empty($this->options['enable_rating'])) { ?>
		<?php echo rseventsproHelper::rating($event->id); ?>
		<div class="rs_clear"></div>
		<?php } ?>
		<!--//end Rating -->

	</div>

	<!-- FB / Twitter / Gplus sharing -->
	<?php if (!empty($this->options['enable_fb_like']) || !empty($this->options['enable_twitter']) || !empty($this->options['enable_gplus']) || !empty($this->options['enable_linkedin'])) { ?>
	<div class="rs_clear"></div>
	<div class="rs_sharing">	
		<?php if (!empty($this->options['enable_fb_like'])) { ?>
			<div class="rsepro-social" id="rsep_fb_like">
				<div class="fb-like" data-href="<?php echo rseventsproHelper::shareURL($event->id,$event->name); ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>
			</div>
		<?php } ?>

		<?php if (!empty($this->options['enable_twitter'])) { ?>
			<div class="rsepro-social" id="rsep_twitter">
				<a href="https://twitter.com/share" class="twitter-share-button" data-text="<?php echo $this->escape($event->name); ?>">Tweet</a>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
			</div>
		<?php } ?>
		
		<?php if (!empty($this->options['enable_gplus'])) { ?>
			<div class="rsepro-social" id="rsep_gplus">
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
			<div class="rsepro-social" id="rsep_linkedin">
				<script src="//platform.linkedin.com/in.js" type="text/javascript"></script>
				<script type="IN/Share" data-counter="right"></script>
			</div>
		<?php } ?>
	</div>
	<div class="rs_clear"></div>
	<?php } ?>
	<!--//end FB / Twitter / Gplus sharing -->

	<!-- Description -->
	<?php if (!empty($this->options['show_description']) && !empty($description)) { ?>
		<span class="description"><?php echo $description; ?></span>
		<div class="rs_clear"></div>
	<?php } ?>
	<!--//end Description -->
	
	<?php if ($speakers) { ?>
	<?php $chunks = array_chunk($speakers, 4); ?>
	<!-- Speakers -->
	<h3><?php echo JText::_('COM_RSEVENTSPRO_EVENT_LIST_SPEAKERS'); ?></h3>
	<?php foreach ($chunks as $speakers) { ?>
	<ul class="thumbnails rsepro-speakers">
	<?php foreach($speakers as $speaker) { ?>
		<li class="span3">
			<div class="thumbnail">
				<?php if ($speaker->image) { ?>
				<img class="rsepro-speaker-image" src="<?php echo JUri::root(); ?>components/com_rseventspro/assets/images/speakers/<?php echo $speaker->image; ?>" alt="<?php echo $speaker->name; ?>" width="<?php echo rseventsproHelper::getConfig('speaker_icon_width', 'int', 100); ?>" height="<?php echo rseventsproHelper::getConfig('speaker_icon_height', 'int', 150); ?>" />
				<?php } else { ?>
				<?php echo JHtml::image('com_rseventspro/blankuser.png', $speaker->name, array('class' => 'rsepro-speaker-image', 'width' => rseventsproHelper::getConfig('speaker_icon_width', 'int', 100), 'height' => rseventsproHelper::getConfig('speaker_icon_height', 'int', 150)), true); ?>
				<?php } ?>
				<div class="caption">
					<p class="rsepro-speaker-name"><?php echo $speaker->name; ?></p>
					
					<ul class="rsepro-speaker-info">
						<?php if ($speaker->email) { ?>
						<li>
							<a href="mailto:<?php echo $speaker->email; ?>">
								<i class="fa fa-envelope"></i>
							</a>
						</li>
						<?php } ?>
						<?php if ($speaker->url) { ?>
						<li>
							<a href="<?php echo $speaker->url; ?>" target="_blank">
								<i class="fa fa-link"></i>
							</a>
						</li>
						<?php } ?>
						<?php if ($speaker->phone) { ?>
						<li>
							<a href="tel:<?php echo $speaker->phone; ?>">
								<i class="fa fa-phone"></i>
							</a>
						</li>
						<?php } ?>
						<?php if ($speaker->facebook) { ?>
						<li>
							<a href="<?php echo $speaker->facebook; ?>" target="_blank">
								<i class="fa fa-facebook"></i>
							</a>
						</li>
						<?php } ?>
						<?php if ($speaker->twitter) { ?>
						<li>
							<a href="<?php echo $speaker->twitter; ?>" target="_blank">
								<i class="fa fa-twitter"></i>
							</a>
						</li>
						<?php } ?>
						<?php if ($speaker->linkedin) { ?>
						<li>
							<a href="<?php echo $speaker->linkedin; ?>" target="_blank">
								<i class="fa fa-linkedin"></i>
							</a>
						</li>
						<?php } ?>
						<li></li>
					</ul>
				</div>
				<div class="rsepro-speaker-description"><?php echo $speaker->description; ?></div>
			</div>
		</li>
	<?php } ?>
	</ul>
	<?php } ?>
	
	<div id="rsepro-speaker-overlay" class="rsepro-speaker-overlay">
		<div class="rsepro-close">x</div>
		<div class="rsepro-speaker-overlay-container">
			<div id="rsepro-speaker-overlay-image"></div>
			<div id="rsepro-speaker-overlay-name"></div>
			<div id="rsepro-speaker-overlay-info"></div>
			<div id="rsepro-speaker-overlay-description"></div>
		</div>
	</div>
	<!--//end Speakers -->
	<?php } ?>

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
	<?php if ($repeat->id == $event->id) continue; ?>
		<li>
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($repeat->id,$repeat->name),false,rseventsproHelper::itemid($repeat->id)); ?>"><?php echo $repeat->name; ?></a>
			<?php $dateMask = $repeat->allday ? rseventsproHelper::getConfig('global_date') : null; ?>
			(<?php echo rseventsproHelper::showdate($repeat->start,$dateMask,true); ?>)
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
	<?php if ($event->rsvp && $event->rsvp_guests) { ?>
	<?php if (!empty($this->RSVPguests)) { ?>
	
	<?php foreach ($this->RSVPguests as $type => $guests) { ?>
	<?php if (!empty($guests)) { ?>
	<h3><?php echo JText::_('COM_RSEVENTSPRO_RSVP_EVENT_GUESTS_'.strtoupper($type)); ?></h3>
	<ul class="thumbnails">
	<?php foreach ($guests as $guest) { ?>
		<li class="thumbnail">
			<?php if (!empty($guest->url)) { ?><a href="<?php echo $guest->url; ?>"><?php } ?>
			<?php echo $guest->avatar; ?>
			<p class="center"><?php echo $guest->name; ?></p>
			<?php if (!empty($guest->url)) { ?></a><?php } ?>
		</li>
	<?php } ?>
	</ul>
	<div class="clearfix"></div>
	<?php } ?>
	<?php } ?>
	<?php } ?>
	<?php } ?>
	<!--//end Show subscribers -->

	<?php JFactory::getApplication()->triggerEvent('rsepro_onAfterEventDisplay',array(array('event' => $event, 'categories' => $categories, 'tags' => $tags))); ?>

	<!-- Comments -->
	<?php if ($event->comments) { ?>
		<div class="rs_comments">
			<?php echo rseventsproHelper::comments($event->id,$event->name); ?>
		</div>
		<div class="rs_clear"></div>
	<?php } ?>
	<!--//end Comments -->

	<?php if (($event->comments && rseventsproHelper::getConfig('event_comment','int') == 1) || !empty($this->options['enable_fb_like'])) { ?>
	<div id="fb-root"></div>
	<script type="text/javascript">
	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4&appId=<?php echo $this->escape(rseventsproHelper::getConfig('facebook_app_id')); ?>";
		fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
	</script>
	<?php } ?>
</div>

<?php if ($this->config->timezone) { ?>
<?php echo rseventsproHelper::timezoneModal(); ?>
<?php } ?>

<?php 
if ($this->report) {
	echo JHtml::_('bootstrap.renderModal', 'rseReportModal', array('title' => '&nbsp;', 'url' => rseventsproHelper::route('index.php?option=com_rseventspro&layout=report&tmpl=component&id='.rseventsproHelper::sef($event->id,$event->name)), 'bodyHeight' => 70));
}

echo JHtml::_('bootstrap.renderModal', 'rseImageModal', array('title' => '&nbsp;', 'bodyHeight' => 70));

if ($links == 2) {
	if ($this->cansubscribe['status']) {
		echo JHtml::_('bootstrap.renderModal', 'rseSubscribeModal', array('title' => JText::_('COM_RSEVENTSPRO_EVENT_JOIN'), 'url' => rseventsproHelper::route('index.php?option=com_rseventspro&layout=subscribe&id='.rseventsproHelper::sef($event->id,$event->name).$tmpl), 'bodyHeight' => 70));
	}
	
	if ($this->admin || $event->owner == $this->user || $event->sid == $this->user) {
		echo JHtml::_('bootstrap.renderModal', 'rseMessageModal', array('title' => JText::_('COM_RSEVENTSPRO_EVENT_MESSAGE_TO_GUESTS'), 'url' => rseventsproHelper::route('index.php?option=com_rseventspro&layout=message&id='.rseventsproHelper::sef($event->id,$event->name).$tmpl), 'bodyHeight' => 70));
	}
	
	if (!$this->eventended && !empty($this->options['show_invite'])) {
		echo JHtml::_('bootstrap.renderModal', 'rseInviteModal', array('title' => JText::_('COM_RSEVENTSPRO_EVENT_INVITE'), 'url' => rseventsproHelper::route('index.php?option=com_rseventspro&layout=invite&id='.rseventsproHelper::sef($event->id,$event->name).$tmpl), 'bodyHeight' => 70));
	}
}

if ($links != 1) {
	if (!$this->eventended && $this->canunsubscribe && $this->issubscribed != 1) {
		echo JHtml::_('bootstrap.renderModal', 'rseUnsubscribeModal', array('title' => JText::_('COM_RSEVENTSPRO_UNSUBSCRIBE_UNSUBSCRIBE'), 'url' => rseventsproHelper::route('index.php?option=com_rseventspro&layout=unsubscribe&id='.rseventsproHelper::sef($event->id,$event->name).'&tmpl=component'), 'bodyHeight' => 70));
	}
}
?>