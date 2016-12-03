<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
$count = count($this->locations); ?>

<?php if ($this->params->get('show_page_heading', 1)) { ?>
<?php $title = $this->params->get('page_heading', ''); ?>
<h1><?php echo !empty($title) ? $this->escape($title) : JText::_('COM_RSEVENTSPRO_LOCATIONS'); ?></h1>
<?php } ?>

<?php if (!empty($this->locations)) { ?>
<ul class="rs_events_container rsepro-locations-list" id="rs_events_container">
	<?php foreach($this->locations as $location) { ?>
	<li>
		<div class="well">
			<div class="rs_options" style="display:none;">
				<?php if ((!empty($this->permissions['can_edit_locations']) || $this->admin) && !empty($this->user)) { ?>
					<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=editlocation&id='.rseventsproHelper::sef($location->id,$location->name)); ?>">
						<i class="fa fa-pencil"></i>
					</a>
				<?php } ?>
				<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=location&id='.rseventsproHelper::sef($location->id,$location->name)); ?>">
					<i class="fa fa-eye"></i>
				</a>
			</div>
			
			<div class="rs_heading">
				<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&location='.rseventsproHelper::sef($location->id,$location->name)); ?>">
					<?php echo $location->name; ?>
					<?php if ($this->params->get('events',0)) { ?>
					<?php $events = (int) $this->getNumberEvents($location->id,'locations'); ?>
					<?php if (!empty($events)) { ?>
					<small>(<?php echo $this->getNumberEvents($location->id,'locations'); ?>)</small>
					<?php } ?>
					<?php } ?>
				</a>
			</div>
			<div class="rs_description">
				<?php echo rseventsproHelper::shortenjs($location->description,$location->id,255,$this->params->get('type', 1)); ?>
			</div>
		</div>
	</li>
	<?php } ?>
</ul>
<?php } ?>

<span id="total" class="rs_hidden"><?php echo $this->total; ?></span>
<span id="Itemid" class="rs_hidden"><?php echo JFactory::getApplication()->input->getInt('Itemid'); ?></span>

<div class="rs_loader" id="rs_loader" style="display:none;">
	<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/loader.gif" alt="" />
</div>

<?php if ($this->total > $count) { ?>
	<a class="rs_read_more" id="rsepro_loadmore"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_LOAD_MORE'); ?></a>
<?php } ?>

<script type="text/javascript">
	jQuery(document).ready(function() {
		<?php if ($this->total > $count) { ?>
		jQuery('#rsepro_loadmore').on('click', function() {
			rspagination('locations', jQuery('#rs_events_container > li').length);
		});
		<?php } ?>
		
		<?php if (!empty($count)) { ?>
		jQuery('#rs_events_container li').on({
			mouseenter: function() {
				jQuery(this).find('div.rs_options').css('display','');
			},
			mouseleave: function() {
				jQuery(this).find('div.rs_options').css('display','none');
			}
		});
		<?php } ?>
	});
</script>