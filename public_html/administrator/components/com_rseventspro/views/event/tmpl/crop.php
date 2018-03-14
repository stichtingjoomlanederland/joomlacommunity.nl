<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

$image = @getimagesize(JPATH_SITE.'/components/com_rseventspro/assets/images/events/'.$this->item->icon);
$width = isset($image[0]) ? $image[0] : 800;
$height = isset($image[1]) ? $image[1] : 380;

if ($height > $width) {
	$divwidth = $width < 380 ? $width : 380;
	$left_crop = isset($this->item->properties['left']) ? $this->item->properties['left'] : 0;
	$top_crop = isset($this->item->properties['top']) ? $this->item->properties['top'] : ($width/2);
	$width_crop = isset($this->item->properties['width']) ? $this->item->properties['width'] : ($height > $width ? ($width < 380 ? $width : 380) : $width/2);
	$height_crop = isset($this->item->properties['height']) ? $this->item->properties['height'] : ($height > $width ? ($width < 380 ? $width : 380) : $height/2);
} else {
	if ($width < 600) {
		$divwidth = $width;
	} else {
		$ratio = $height / $width;
        $newHeight = (int) (600 * $ratio);
		
		if ($newHeight > 400) {
			$divwidth = 400;
		} else {
			$divwidth = 600;
		}
	}
	
	$left_crop = isset($this->item->properties['left']) ? $this->item->properties['left'] : 250;
	$top_crop = isset($this->item->properties['top']) ? $this->item->properties['top'] : 250;
	$width_crop = isset($this->item->properties['width']) ? $this->item->properties['width'] : $width/2;
	$height_crop = isset($this->item->properties['height']) ? $this->item->properties['height'] : ($height/2 - 20);
}
?>

<style type="text/css">
	body { background:#F8F8F8 !important; }
	
	.borderDiv{
		border-top:1px solid #555;
		border-left:1px solid #555;
		border-right:1px solid #CCC;
		border-bottom: 1px solid #CCC;
		width:<?php echo $divwidth; ?>px;
		float:left;
		margin-bottom: 20px;
	}
</style>

<script type="text/javascript">
	function updateForm(coordinates) {
		$('width').value = coordinates.width;
		$('height').value = coordinates.height;
		$('left').value = coordinates.left;
		$('top').value = coordinates.top;
	}
	
	function initCrop() {
		var cropObject = new DG.ImageCrop('imagecrop', {
			lazyShadows : true,		
			resizeConfig: {
				preserveAspectRatio : false,
				minWidth : 50,
				minHeight: 50
			},
			moveConfig : {
				keyNavEnabled : true
			},
			<?php if (isset($this->item->properties['left']) && isset($this->item->properties['top']) && isset($this->item->properties['width']) && isset($this->item->properties['height'])) { ?>
			initialCoordinates : {
				left : <?php echo $left_crop; ?>,
				top : <?php echo $top_crop; ?>,
				width: <?php echo $width_crop; ?>,
				height: <?php echo $height_crop; ?>
			},
			<?php } ?>
			originalCoordinates : {
				width: 	<?php echo $width; ?>,
				height : <?php echo $height; ?>
			},
			previewCoordinates : {
				width: <?php echo $divwidth; ?>
			},
			listeners : {
				render : function() {
					updateForm(this.getCoordinates());	
				},
				crop : function() {
					updateForm(this.getCoordinates());	
				}									
			}			
		});
	}	
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="borderDiv" id="cropcontainer" style="display:none;">
		<div style="position:relative;width:<?php echo $divwidth; ?>px;" id="imagecrop">
			<img id="theimage" src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/events/<?php echo $this->item->icon; ?>" alt="" width="<?php echo $divwidth; ?>" />
		</div>
	</div>
	<div id="rs_loading" style="margin-bottom:10px;text-align:center;">
		<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/load.gif" />
	</div>
	
	<div style="text-align:center;">
		<button type="submit" class="rs_crop_btn"><?php echo JText::_('COM_RSEVENTSPRO_GLOABAL_CROP_BTN'); ?></button>
		<button type="button" onclick="window.parent.hm('box');" class="rs_crop_btn"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
	</div>
	
	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="width" id="width" value="" />
	<input type="hidden" name="height" id="height" value="" />
	<input type="hidden" name="left" id="left" value="" />
	<input type="hidden" name="top" id="top" value="" />
	
	<input type="hidden" name="task" value="event.crop" />
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
</form>
<script type="text/javascript">
var objImagePreloader = new Image();
objImagePreloader.onload = function() {
	$('rs_loading').style.display = 'none';
	$('cropcontainer').style.display = '';
	initCrop();
	
	//	clear onLoad, IE behaves irratically with animated gifs otherwise
	objImagePreloader.onload=function(){};
};
objImagePreloader.src = '<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/events/<?php echo $this->item->icon; ?>';
</script>