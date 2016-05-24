<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<input type="hidden" name="option" value="com_akeeba" />
	<input type="hidden" name="view" value="Profiles" />
	<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
	<input type="hidden" name="task" id="task" value="save" />
	<input type="hidden" name="id" id="id" value="<?php echo (int)$this->item->id; ?>" />
	<input type="hidden" name="<?php echo \JFactory::getSession()->getFormToken(); ?>" value="1" />
	
	<div class="control-group">
		<label class="control-label" for="description">
			<?php echo \JHtml::_('tooltip', JText::_('COM_AKEEBA_PROFILES_LABEL_DESCRIPTION_TOOLTIP'), '', '', JText::_('COM_AKEEBA_PROFILES_LABEL_DESCRIPTION')); ?>
		</label>
		<div class="controls">
			<input type="text" name="description" class="span6" id="description" value="<?php echo $this->escape($this->item->description); ?>" />
		</div>
	</div>
</form>