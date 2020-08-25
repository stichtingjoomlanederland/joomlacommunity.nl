<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rscomments&view=group&layout=edit&IdGroup='.(int) $this->item->IdGroup); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
	<?php
		$this->tabs->addTitle(JText::_('COM_RSCOMMENTS_GROUP_DETAILS'), 'general');
		$this->tabs->addContent($this->loadTemplate('general'));
			
		$this->tabs->addTitle(JText::_('COM_RSCOMMENTS_GROUPS_SETTINGS_BBCODE'), 'bbcode');
		$this->tabs->addContent($this->loadTemplate('bbcode'));

		echo $this->tabs->render();
	?>
		
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo $this->form->getInput('IdGroup'); ?>
</form>