<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rscomments&task=message.edit&tag='.$this->item->tag); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal">
<?php foreach ($this->fieldsets as $name => $fieldset) {
		$this->tabs->title($fieldset->label, $name);
		$content = $this->loadTemplate($fieldset->name);
		$this->tabs->content($content);
	}
	
	// render tabs
	echo $this->tabs->render();
?>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" value="" name="task" />
</form>