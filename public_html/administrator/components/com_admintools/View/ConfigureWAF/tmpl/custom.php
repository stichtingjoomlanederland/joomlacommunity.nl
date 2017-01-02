<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var Akeeba\AdminTools\Admin\View\ConfigureWAF\Html    $this */
use Akeeba\AdminTools\Admin\Helper\Select;

defined('_JEXEC') or die;
?>
<div class="control-group">
	<label class="control-label" for="custom403msg"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_CUSTOMMESSAGE_LABEL'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_CUSTOMMESSAGE_DESC'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_CUSTOMMESSAGE_LABEL'); ?>
	</label>

	<div class="controls">
		<input type="text" class="input-xxlarge" name="custom403msg"
			   value="<?php echo htmlentities($this->wafconfig['custom403msg']) ?>"
			   title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_CUSTOMMESSAGE_DESC'); ?>"/>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="use403view"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_USE403VIEW'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_USE403VIEW_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_USE403VIEW'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('use403view', array(), $this->wafconfig['use403view']); ?>

	</div>
</div>
