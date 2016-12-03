<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.keepalive'); ?>

<script type="text/javascript">
function selectFile(file) {
	if (confirm('<?php echo JText::_('COM_RSEVENTSPRO_MEDIA_SELECT_FILE',true); ?>')) {
		window.parent.rsepro_select_image(file);
	}
}
</script>

<ul class="rsepro_media thumbnails">
	<?php if ($this->state->get('folder') != '') { ?>
		<li class="thumbnail center">
			<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=media&tmpl=component&folder='.$this->previous); ?>">
				<div class="height-50">
					<span class="fa fa-arrow-up fa-3x"></span>
				</div>
				<div class="small">
					<?php echo JText::_('COM_RSEVENTSPRO_MEDIA_UP'); ?>
				</div>
			</a>
		</li>
	<?php } ?>
	
	<?php if (!empty($this->folders)) { ?>
	<?php foreach ($this->folders as $folder) { ?>
	<li class="thumbnail center">
		<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&view=media&tmpl=component&folder='.$folder->path_relative); ?>">
			<div class="height-50">
				<span class="fa fa-folder fa-3x"></span>
			</div>
			<div class="small">
				<?php echo JHtml::_('string.truncate', $folder->name, 10, false); ?>
			</div>
		</a>
	</li>
	<?php } ?>
	<?php } ?>
	
	<?php if (!empty($this->images)) { ?>
	<?php foreach ($this->images as $image) { ?>
	<li class="thumbnail center">
		<a class="img-preview" href="javascript:selectFile('<?php echo $image->path_relative; ?>')" title="<?php echo $image->name; ?>" >
			<div class="height-50" style="overflow: hidden;">
				<?php echo JHtml::_('image', $this->baseURL . '/' . $image->path_relative, $image->name, array('width' => 60, 'height' => 60)); ?>
			</div>
			<div class="small">
				<?php echo JHtml::_('string.truncate', $image->name, 10, false); ?>
			</div>
		</a>
	</li>
	<?php } ?>
	<?php } ?>
</ul>