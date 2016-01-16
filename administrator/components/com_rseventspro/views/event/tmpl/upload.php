<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
$maxHeight = $this->height > $this->width ? $this->customheight : 550; ?>

<script type="text/javascript">
jQuery(document).ready(function (){
	var $parent = window.parent.jQuery;
	<?php if ($this->icon) { ?>$parent('#rsepro-photo').prop('src','<?php echo JURI::root(); ?>index.php?option=com_rseventspro&task=image&id=<?php echo $this->item->id; ?>&width=188&uniqueid=<?php echo uniqid(''); ?>');<?php } ?>
	$parent('#rsepro-image-loader').css('display','none');
	$parent('#rsepro-image-frame').css('display','');
	<?php if ($this->item->icon) { ?>
	$parent('#rsepro-crop-icon-btn').css('display','');
	$parent('#rsepro-delete-icon-btn').css('display','');
	$parent('#rsepro-image-frame').parent().css('max-height', <?php echo $maxHeight > 650 ? 650 : $maxHeight; ?>);
	<?php if ($this->height > $this->width) { ?>$parent('#rsepro-image-frame').css('max-height', <?php echo $this->customheight; ?>);<?php } else { ?>$parent('#rsepro-image-frame').css('max-height','');<?php } ?>
	$parent('#rsepro-image-frame').css('height', <?php echo $maxHeight; ?>);
	$parent('#aspectratiolabel').css('display', '');
	$parent('#aspectratio').prop('checked',<?php echo $this->item->aspectratio ? 'true' : 'false'; ?>);
	
	// Set events
	$parent('#aspectratio').off('change').on('change', function(){
		RSEventsPro.Crop.changeRatio();
	});
	
	$parent('#rsepro-crop-icon-btn').off('click').on('click', function(){
		jQuery('input[name="task"]').val('event.crop');
		jQuery('#uploadForm').submit();
	});
	
	$parent('#rsepro-delete-icon-btn').off('click').on('click', function(){
		if (confirm('<?php echo JText::_('COM_RSEVENTSPRO_DELETE_ICON_INFO',true); ?>')) {
			$parent('#rsepro-image-loader').css('display','');
			$parent('#rsepro-image-frame').removeProp('style');
			$parent('#rsepro-image-frame').css('display','none');
			
			jQuery.ajax({
				url: 'index.php?option=com_rseventspro',
				type: 'post',
				data: 'task=event.deleteicon&id=<?php echo $this->item->id; ?>',
			}).done(function( response ) {
				var start = response.indexOf('RS_DELIMITER0') + 13;
				var end = response.indexOf('RS_DELIMITER1');
				response = response.substring(start, end);
				
				if (parseInt(response) == 1) {
					$parent('#rsepro-crop-icon-btn').css('display','none');
					$parent('#rsepro-delete-icon-btn').css('display','none');
					$parent('#rsepro-image-frame').prop('src','<?php echo JRoute::_('index.php?option=com_rseventspro&view=event&layout=upload&tmpl=component&id='.$this->item->id,false); ?>');
					$parent('#aspectratiolabel').css('display', 'none');
					$parent('#rsepro-photo').prop('src','<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/profile_pic.png');
				}
			});
		}
	});
	<?php } ?>
});

function rsepro_upload_photo() {
	jQuery('#upload_iframe').slideUp();
	jQuery('#upload_actions').css('display','none');
	jQuery('#upload_loader').css('display','');
	jQuery('input[name="task"]').val('event.upload');
	jQuery('#uploadForm').submit();
}

var RSEventsPro = {};
	
RSEventsPro.Crop = {
	// Allows access to the global imgAreaSelect object for manipulation
	instance: false,
	
	// Initialize cropping function
	init: function() {
		RSEventsPro.Crop.instance = jQuery('#rsepro-crop-image').imgAreaSelect({
			instance: true,
			aspectRatio: <?php echo $this->item->aspectratio ? 'false' : "'4:3'"; ?>,
			imageWidth: <?php echo $this->width; ?>,
			imageHeight: <?php echo $this->height; ?>,
			handles: true,
			onSelectChange: RSEventsPro.Crop.update,
			x1: <?php echo $this->left_crop; ?>,
			y1: <?php echo $this->top_crop; ?>,
			x2: <?php echo $this->width_crop + $this->left_crop; ?>,
			y2: <?php echo $this->height_crop + $this->top_crop."\n"; ?>
		});
	},
	
	// Update selection
	update: function(img, selection) {
		jQuery('#x1').val(selection.x1);
		jQuery('#y1').val(selection.y1);
		jQuery('#width').val(selection.width);
		jQuery('#height').val(selection.height);
	},
	
	getRatio: function() {
		var $parent = window.parent.jQuery;
		jQuery('input[name=aspectratio]').val($parent('#aspectratio').prop('checked') ? 1 : 0);
		return $parent('#aspectratio').prop('checked') ? false : '4:3';
	},
	
	changeRatio: function() {
		RSEventsPro.Crop.instance.setOptions({aspectRatio: RSEventsPro.Crop.getRatio()});
		RSEventsPro.Crop.instance.update();
	}
}

function rsepro_open_media() {
	jQuery('#upload_iframe').slideToggle();
	<?php if($this->item->icon) { ?>
	setTimeout(function() { RSEventsPro.Crop.instance.update(); }, 300);
	setTimeout(function() { RSEventsPro.Crop.instance.update(); }, 500);
	<?php } ?>
}

function rsepro_select_image(file) {
	jQuery('#local').val(file);
	jQuery('#upload_iframe').slideUp();
	jQuery('#upload_actions').css('display','none');
	jQuery('#upload_loader').css('display','');
	jQuery('input[name="task"]').val('event.upload');
	jQuery('#uploadForm').submit();
}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro'); ?>" method="post" name="uploadForm" id="uploadForm" class="form-horizontal" enctype="multipart/form-data">
	<div id="upload_actions" class="row-fluid">
		<a class="btn btn-primary" style="position: relative;" href="javascript:void(0)">
			<?php echo JText::_('COM_RSEVENTSPRO_UPLOAD_IMAGE'); ?>
			<input type="file" id="upload_file" onchange="rsepro_upload_photo();" size="10" name="icon" />
		</a>
		<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_OR'); ?> <a class="btn" href="javascript:void(0);" onclick="rsepro_open_media();"><?php echo JText::_('COM_RSEVENTSPRO_SELECT_IMAGE_LOCAL'); ?></a>
	</div>
	<div id="upload_loader" style="display:none;">
		<img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/loader.gif" alt="" style="vertical-align:middle;" />
	</div>
	
	<iframe id="upload_iframe" src="<?php echo JRoute::_('index.php?option=com_rseventspro&view=media&tmpl=component'); ?>" style="display: none;"></iframe>
	
	<?php if($this->item->icon) { ?>
	<div class="rsepro-crop-container">
		<div id="cropcontainer" style="display:none; width: <?php echo $this->divwidth; ?>px;">
			<img id="rsepro-crop-image" src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/events/<?php echo $this->item->icon; ?>" alt="" width="<?php echo $this->divwidth; ?>" />
		</div>
		<div id="crop_loader" style="margin-bottom:10px;text-align:center;">
			<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/load.gif" />
		</div>
	</div>
	<?php } ?>
	
	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="x1" id="x1" value="<?php echo $this->left_crop; ?>" />
	<input type="hidden" name="y1" id="y1" value="<?php echo $this->top_crop; ?>" />
	<input type="hidden" name="width" id="width" value="<?php echo $this->width_crop; ?>" />
	<input type="hidden" name="height" id="height" value="<?php echo $this->height_crop; ?>" />
	<input type="hidden" name="aspectratio" value="<?php echo (int) $this->item->aspectratio; ?>" />
	<input type="hidden" name="local" id="local" value="" />
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
</form>

<script type="text/javascript">
	var objImagePreloader = new Image();
	objImagePreloader.onload = function() {
		jQuery('#crop_loader').css('display','none');
		jQuery('#cropcontainer').css('display','');
		RSEventsPro.Crop.init();
		objImagePreloader.onload=function(){};
	};
	objImagePreloader.src = '<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/events/<?php echo $this->item->icon; ?>';
</script>