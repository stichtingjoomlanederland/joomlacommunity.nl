<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );?>

<?php if (rseventsproHelper::getConfig('enable_google_maps') && !empty($this->row->coordinates)) { ?>
<script type="text/javascript">
var rseprolocationmap;
jQuery(document).ready(function (){
	rseprolocationmap = jQuery('#map-canvas').rsjoomlamap({
		locationCoordonates: '<?php echo $this->escape($this->row->coordinates); ?>',
		directionsBtn : 'rsepro-get-directions',
		directionsPanel : 'rsepro-directions-panel',
		directionsFrom : 'rsepro-directions-from',
		directionNoResults: '<?php echo JText::_('COM_RSEVENTSPRO_DIRECTIONS_NO_RESULT',true); ?>',
		zoom: <?php echo (int) $this->config->google_map_zoom ?>,
		center: '<?php echo $this->config->google_maps_center; ?>',
		markerDraggable: false,
		markers: [
			{
				title : '<?php echo addslashes($this->row->name); ?>',
				position: '<?php echo $this->escape($this->row->coordinates); ?>',
				content: '<div id="content"><h3><?php echo addslashes($this->row->name); ?></h3> <br /> <?php echo JText::_('COM_RSEVENTSPRO_LOCATION_ADDRESS',true); ?>: <?php echo addslashes($this->row->address); ?> <?php if (!empty($this->row->url)) echo '<br /><a target="_blank" href="'.addslashes($this->row->url).'">'.addslashes($this->row->url).'</a></div>'; ?>'
			}
		]
	});
});
</script>
<?php } ?>

<div class="rsepro-location-content">
	<h1><?php echo $this->row->name; ?></h1>

	<div class="row-fluid">
		<b><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_ADDRESS'); ?>: </b> <?php echo $this->row->address; ?> 
		<?php if ($this->row->url) { ?> (<a href="<?php echo $this->row->url; ?>"><?php echo $this->row->url; ?></a>)<?php } ?>
	</div>

	<div class="row-fluid">
		<?php echo rseventsproHelper::removereadmore($this->row->description); ?>
	</div>
	<hr />

	<?php if (rseventsproHelper::isGallery()) { ?>
	<div class="row-fluid">
	<?php echo rseventsproHelper::gallery('location',$this->row->id); ?>
	</div>
	<hr />
	<?php } ?>
	
	<?php if (rseventsproHelper::getConfig('enable_google_maps') && !empty($this->row->coordinates)) { ?>
	<div id="map-canvas" style="width: 100%; height: 400px"></div>
	
	<?php if (rseventsproHelper::getConfig('google_map_directions')) { ?>
		<div style="margin:15px 0;">
			<h3><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_GET_DIRECTIONS'); ?></h3>
			<label for="rsepro-directions-from"><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_FROM'); ?></label>
			<div class="input-append">
				<input type="text" size="25" id="rsepro-directions-from" name="rsepro-directions-from" value="" />
				<button id="rsepro-get-directions" type="button" class="button btn"><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_GET_DIRECTIONS'); ?></button>
			</div>
		</div>
		<div class="alert alert-error" id="rsepro-map-directions-error" style="display: none;"></div>
		<div class="clearfix"></div>
		<div id="rsepro-directions-panel"></div>
		<?php }	?>
		<?php }	?>
</div>
<a href="javascript:history.go(-1);"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_BACK'); ?></a>