<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<h1><?php echo JText::sprintf('COM_RSEVENTSPRO_EDIT_LOCATION',$this->row->name); ?></h1>

<?php if (rseventsproHelper::getConfig('enable_google_maps','int')) { ?>
<script type="text/javascript">
	var rseproeditlocationmap;
	jQuery(document).ready(function (){
		rseproeditlocationmap = jQuery('#map-canvas').rsjoomlamap({
			address: 'jform_address',
			coordinates: 'jform_coordinates',
			pinpointBtn: 'rsepro-pinpoint',
			zoom: <?php echo (int) $this->config->google_map_zoom ?>,
			center: '<?php echo $this->config->google_maps_center; ?>',
			markerDraggable: true,
			resultsWrapperClass: 'rsepro-locations-results-wrapper',
			resultsClass: 'rsepro-locations-results'
		});
	});
</script>
<?php } ?>

<form action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=editlocation'); ?>" method="post" name="locationForm" id="locationForm">
	<div class="control-group">
		<div class="control-label">
			<label for="jform_name"><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_NAME'); ?></label>
		</div>
		<div class="controls">
			<input type="text" id="jform_name" name="jform[name]" value="<?php echo $this->escape($this->row->name); ?>" class="input-large" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<label for="jform_url"><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_URL'); ?></label>
		</div>
		<div class="controls">
			<input type="text" id="jform_url" name="jform[url]" value="<?php echo $this->escape($this->row->url); ?>" class="input-large" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<label for="jform_address"><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_ADDRESS'); ?></label>
		</div>
		<div class="controls">
			<input type="text" autocomplete="off" id="jform_address" name="jform[address]" value="<?php echo $this->escape($this->row->address); ?>" class="input-large" />
			<button type="button" style="border:medium none;height:30px;" id="rsepro-pinpoint"><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_PINPOINT'); ?></button>
		</div>
	</div>
	
	<?php if (rseventsproHelper::isGallery()) { ?>
	<div class="control-group">
		<div class="control-label">
			<label for="jform_gallery_tags" style="margin-top: 10px;"><?php echo JText::_('COM_RSEVENTSPRO_GALLERY_TAGS'); ?></label>
		</div>
		<div class="controls">
			<select name="jform[gallery_tags][]" id="jform_gallery_tags" multiple="multiple" class="rs200 rschosen">
				<?php echo JHtml::_('select.options',rseventsproHelper::getGalleryTags(),'value','text', $this->row->gallery_tags); ?>
			</select>
		</div>
	</div>
	<?php } ?>
	
	<div class="control-group clearfix">
		<div class="controls">
			<?php echo JEditor::getInstance(JFactory::getConfig()->get('editor'))->display('jform[description]',$this->row->description,'80%', '70%', 20, 7, rseventsproHelper::getConfig('enable_buttons','bool')); ?>
		</div>
	</div>
	
	<?php if (rseventsproHelper::getConfig('enable_google_maps','int')) { ?>
	<div class="control-group">
		<div class="controls">
			<div id="map-canvas" style="width:100%;height: 400px"></div>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<label for="jform_coordinates"><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_COORDINATES'); ?></label>
		</div>
		<div class="controls">
			<input type="text" id="jform_coordinates" name="jform[coordinates]" value="<?php echo $this->escape($this->row->coordinates); ?>" class="input-large" />
		</div>
	</div>
	<?php } ?>
	
	<div class="form-actions">
		<button type="button" class="button btn btn-primary" onclick="document.locationForm.submit();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SAVE'); ?></button>
		<a class="btn" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=locations'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></a>
	</div>
	
	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="task" value="rseventspro.savelocations" />
	<input type="hidden" name="jform[id]" value="<?php echo $this->row->id; ?>" />
</form>