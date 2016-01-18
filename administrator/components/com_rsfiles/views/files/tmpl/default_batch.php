<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access'); ?>

<div class="rsfiles-modal-header">
	<button data-dismiss="modal" class="close" type="button">×</button>
	<h3><?php echo JText::_('COM_RSFILES_BATCH_FILES'); ?></h3>
</div>
<div class="rsfiles-modal-body rsfiles-modal-batch">
	<div class="row-fluid form-horizontal">
		<div class="span6 rsspan6 rslft">
			<?php echo JHtml::_('rsfieldset.start', 'adminform'); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('published'), $this->form->getInput('published')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('FileStatistics'), $this->form->getInput('FileStatistics')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('IdLicense'), $this->form->getInput('IdLicense')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('DownloadMethod'), $this->form->getInput('DownloadMethod')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('show_preview'), $this->form->getInput('show_preview')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('DownloadLimit'), $this->form->getInput('DownloadLimit')); ?>
			<?php echo JHtml::_('rsfieldset.end'); ?>
		</div>
		<div class="span6 rsspan6 rslft">
			<?php echo JHtml::_('rsfieldset.start', 'adminform'); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('CanEdit'), $this->form->getInput('CanEdit')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('CanDelete'), $this->form->getInput('CanDelete')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('CanView'), $this->form->getInput('CanView')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('CanDownload'), $this->form->getInput('CanDownload')); ?>
			<?php echo JHtml::_('rsfieldset.end'); ?>
		</div>
	</div>
	<div class="rsf_clear"></div>
</div>
<div class="rsfiles-modal-footer">
	<button type="button" data-dismiss="modal" class="btn"><?php echo JText::_('COM_RSFILES_CANCEL'); ?></button>
	<button onclick="Joomla.submitbutton('files.batch');" type="button" class="btn btn-primary"><?php echo JText::_('COM_RSFILES_PROCESS'); ?></button>
</div>