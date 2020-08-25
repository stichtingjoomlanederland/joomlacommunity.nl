<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rscomments&task=message.edit&tag='.$this->item->tag); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal">
<?php 
	foreach ($this->fieldsets as $name => $fieldset) {
		$this->tabs->addTitle($fieldset->label, $name);
		$this->tabs->addContent($this->loadTemplate($fieldset->name));
	}
	echo $this->tabs->render();
?>

<?php echo JHtml::_( 'form.token' ); ?>
<input type="hidden" value="" name="task" />
</form>