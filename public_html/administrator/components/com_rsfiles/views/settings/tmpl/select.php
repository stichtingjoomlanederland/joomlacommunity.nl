<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.keepalive'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rsfiles&view=settings&layout=select'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal" autocomplete="off">
	<div class="row-fluid">
		<div class="span12">
			<div style="text-align: right;">
				<button type="button" onclick="Joomla.submitbutton('settings.savepath')" class="btn btn-primary button"><?php echo JText::_('COM_RSFILES_SAVE'); ?></button>
				<button type="button" onclick="window.parent.SqueezeBox.close();" class="btn button"><?php echo JText::_('COM_RSFILES_CANCEL'); ?></button>
			</div>
			
			<div class="rsf_select_path">
				<?php foreach ($this->elements as $element) { ?>
					<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&view=settings&layout=select&tmpl=component&type='.$this->type.'&folder='.$element->fullpath); ?>">
						<?php echo $element->name; ?>
					</a> <?php echo rsfilesHelper::ds(); ?>
				<?php } ?>
			</div>
			
			<table class="table table-bordered table-striped adminlist" id="rsf_select">
				<tr class="row0">
					<td width="1%"></td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&view=settings&layout=select&tmpl=component&type='.$this->type.'&folder='.$this->previous); ?>">
							<i class="icon-up"></i>
						</a>
					</td>
				</tr>
				<?php foreach ($this->folders as $i => $folder) { ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td width="1%">
						<?php $checked = !empty($this->config->{$this->type.'_folder'}) ? (urldecode($this->config->{$this->type.'_folder'}) == urldecode($folder->fullpath) ? ' checked="checked"' : '') : ''; ?>
						<input type="radio" name="thefolder" value="<?php echo $folder->fullpath; ?>" <?php echo $checked; ?> />
					</td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&view=settings&layout=select&tmpl=component&type='.$this->type.'&folder='.$folder->fullpath); ?>">
							<i class="icon-folder"></i> <?php echo $folder->name; ?>
						</a>
					</td>
				</tr>
				<?php } ?>
			</table>
		</div>
		
		<div>
			<?php echo JHtml::_('form.token'); ?>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
			<input type="hidden" name="tmpl" value="component" />
		</div>
	</div>
</form>