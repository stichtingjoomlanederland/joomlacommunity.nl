<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

$image = @getimagesize(JPATH_SITE.'/components/com_rseventspro/assets/images/events/'.$this->icon);
$width = isset($image[0]) ? $image[0] : 800;
$height = isset($image[1]) ? $image[1] : 380;
$customheight = round(($height * ($width < 380 ? $width : 380)) / $width) + 50;
?>

<style type="text/css">
	body, #main { background:#F8F8F8 !important; }
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
	window.parent.rs_modal('<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=crop&tmpl=component&id='.$this->id,false); ?>',640,<?php echo $height > $width ? $customheight : 550; ?>);
}

function rs_delete_image() {
	window.parent.location = '<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.deleteicon&id='.$this->id,false); ?>';
}
</script>

<form action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=default'); ?>" method="post" name="uploadForm" id="uploadForm" enctype="multipart/form-data">
	<p>
		<?php echo JText::_('COM_RSEVENTSPRO_SELECT_IMAGE'); ?> 
		<input type="file" id="rs_icon" onchange="rs_upload_image();" size="30" name="icon" class="rs_inp" style="width: 50%" /> 
		<span id="rs_loading" style="display:none;">
			<img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/loader.gif" alt="" style="vertical-align:middle;" />
		</span>
		<?php if($this->icon) { ?> 
			<div id="rs_extra">
				<a href="javascript:void(0)" onclick="rs_edit_image();"><?php echo JText::_('COM_RSEVENTSPRO_EDIT_CURRENT_FILE'); ?></a>
				<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_OR'); ?>
				<a href="javascript:void(0)" onclick="rs_delete_image();"><?php echo JText::_('COM_RSEVENTSPRO_DELETE_CURRENT_FILE'); ?></a>
			</div>
		<?php } ?>
	</p>
	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="task" value="rseventspro.upload" />
	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
</form>