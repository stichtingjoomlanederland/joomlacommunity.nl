<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access'); 
JHtml::_('behavior.modal');
JText::script('RSFP_SUBM_DIR_DELETE_SURE');
?>

<?php if ($this->params->get('show_page_heading', 1)) { ?>
<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php } ?>

<?php echo $this->template; ?>

<?php if ($this->app->input->get('format') != 'pdf') { ?>
<div class="form-actions">
	<?php if ($this->canEdit) { ?><button type="button" class="btn button" onclick="document.location='<?php echo JRoute::_('index.php?option=com_rsform&view=directory&layout=edit&id='.$this->id); ?>'"><?php echo JText::_('RSFP_SUBM_DIR_EDIT'); ?></button><?php } ?>
    <?php if ($this->canDelete) { ?><button type="button" class="btn button" onclick="if (confirm(Joomla.JText._('RSFP_SUBM_DIR_DELETE_SURE'))) document.location='<?php echo JRoute::_('index.php?option=com_rsform&controller=directory&task=delete&id='.$this->id); ?>'"><?php echo JText::_('RSFP_SUBM_DIR_DELETE'); ?></button><?php } ?>
	<?php if ($this->directory->enablepdf) { ?><button type="button" class="btn button" onclick="document.location='<?php echo $this->pdfLink($this->id); ?>'"><?php echo JText::_('RSFP_SUBM_DIR_PDF'); ?></button><?php } ?>
	<button type="button" class="btn button" onclick="document.location='<?php echo JRoute::_('index.php?option=com_rsform&view=directory'); ?>'"><?php echo JText::_('RSFP_SUBM_DIR_BACK'); ?></button>
</div>
<?php } ?>