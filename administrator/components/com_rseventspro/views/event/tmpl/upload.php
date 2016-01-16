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
$customheight = round(($height * ($width < 380 ? $width : 380)) / $width) + 50;
?>

<style type="text/css">
	body { background:#F8F8F8 !important; }
</style>
<script type="text/javascript">
function rs_upload_image() {
	<?php if ($this->icon) { ?> $('rs_extra').style.display = 'none';<?php } ?>
	$('rs_icon').style.display = 'none';
	$('rs_loading').style.display = '';
	$('uploadForm').submit();
}

function rs_edit_image() {
	window.parent.hm('box');
	window.parent.rs_modal('<?php echo JRoute::_('index.php?option=com_rseventspro&view=event&layout=crop&tmpl=component&id='.$this->item->id,false); ?>',640,<?php echo $height > $width ? $customheight : 550; ?>);
}

function rs_delete_image() {
	var req = new Request({
		method: 'post',
		url: 'index.php?option=com_rseventspro',
		data: 'task=event.deleteicon&id=<?php echo $this->item->id; ?>',
		onSuccess: function(responseText){
			var response = responseText;
			var start = response.indexOf('RS_DELIMITER0') + 13;
			var end = response.indexOf('RS_DELIMITER1');
			response = response.substring(start, end);
			
			if (parseInt(response) == 1) {
				$('rs_extra').style.display = 'none';
				window.parent.$('rs_icon_img').src = '<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/edit/profile_pic.png';
			}
		}
	});
	req.send();
}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro'); ?>" method="post" name="uploadForm" id="uploadForm" enctype="multipart/form-data">
	<p>
		<?php echo JText::_('COM_RSEVENTSPRO_SELECT_IMAGE'); ?> 
		<input type="file" id="rs_icon" onchange="rs_upload_image();" size="30" name="icon" class="rs_inp" style="width: 50%" /> 
		<span id="rs_loading" style="display:none;">
			<img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/loader.gif" alt="" style="vertical-align:middle;" />
		</span>
		<?php if($this->item->icon) { ?> 
			<div id="rs_extra">
				<a href="javascript:void(0)" onclick="rs_edit_image();"><?php echo JText::_('COM_RSEVENTSPRO_EDIT_CURRENT_FILE'); ?></a>
				<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_OR'); ?>
				<a href="javascript:void(0)" onclick="rs_delete_image();"><?php echo JText::_('COM_RSEVENTSPRO_DELETE_CURRENT_FILE'); ?></a>.
			</div> 
		<?php } ?>
	</p>
	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="task" value="event.upload" />
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
</form>