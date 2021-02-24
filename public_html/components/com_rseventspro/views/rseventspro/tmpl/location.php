<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="rsepro-location-content">
	<h1><?php echo $this->row->name; ?></h1>

	<div class="rsepro-location-info">
		<b><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_ADDRESS'); ?>: </b> <?php echo $this->row->address; ?> 
		<?php if ($this->row->url) { ?> (<a href="<?php echo $this->row->url; ?>"><?php echo $this->row->url; ?></a>)<?php } ?>
	</div>
	
	<div class="rsepro-location-description"><?php echo rseventsproHelper::removereadmore($this->row->description); ?></div>
	
	<?php if (rseventsproHelper::isGallery()) { ?><div class="rsepro-location-gallery"><?php echo rseventsproHelper::gallery('location',$this->row->id); ?></div><?php } ?>
	
	<?php if (rseventsproHelper::getConfig('map') && !empty($this->row->coordinates)) { ?>
	<div id="map-canvas" style="width: 100%; height: 400px"></div>
	
	<?php if (rseventsproHelper::getConfig('map') == 'google' && rseventsproHelper::getConfig('google_map_directions')) { ?>
		<div style="margin:15px 0;">
			<h3><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_GET_DIRECTIONS'); ?></h3>
			<?php echo RSEventsproAdapterGrid::inputGroup('<input type="text" size="25" id="rsepro-directions-from" placeholder="'.JText::_('COM_RSEVENTSPRO_LOCATION_FROM').'" class="form-control" name="rsepro-directions-from" value="" />', null, '<button id="rsepro-get-directions" type="button" class="'.RSEventsproAdapterGrid::styles(array('btn')).'">'.JText::_('COM_RSEVENTSPRO_LOCATION_GET_DIRECTIONS').'</button>'); ?>
		</div>
		<div class="alert alert-danger" id="rsepro-map-directions-error" style="display: none;"></div>
		<div class="clearfix"></div>
		<div id="rsepro-directions-panel"></div>
		<?php }	?>
		<?php }	?>
</div>
<a href="javascript:history.go(-1);"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_BACK'); ?></a>