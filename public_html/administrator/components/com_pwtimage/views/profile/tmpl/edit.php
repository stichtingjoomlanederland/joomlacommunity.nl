<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen');

$message = Text::_('JLIB_FORM_FIELD_INVALID');

Factory::getDocument()->addScriptDeclaration(<<<JS
		Joomla.submitbutton = function(task)
		{
			if (task == 'profile.cancel' || document.formvalidator.isValid(document.getElementById('adminForm')))
			{
				Joomla.submitform(task, document.getElementById('adminForm'));
			}
			else
		    {
		        var message = '{$message}',
		        	error = {"error": []};
		        
		    	jQuery('.invalid').each(function(index, item) {
		    	    if (item.id.indexOf('jform_ratio') === 0) {
		    	    	error.error.push(message + jQuery(item).parents('td').data('column').replace("*", ""));
		    	    }
		    	});
		    	
		    	Joomla.renderMessages(error);
		    }
		};
JS
);
?>
<form action="<?php echo Route::_('index.php?option=com_pwtimage&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" enctype="multipart/form-data" id="adminForm" class="form-validate">
	<?php
	$url = JUri::getInstance();

	?>
	<div class="form-inline form-inline-header">
		<?php echo $this->form->renderField('name'); ?>
	</div>
	<hr/>
	<div class="form-horizontal">
		<?php echo HTMLHelper::_('bootstrap.startTabSet', 'profileTab', array('active' => 'general')); ?>
			<?php echo HTMLHelper::_('bootstrap.addTab', 'profileTab', 'general', Text::_('COM_PWTIMAGE_GENERAL_FIELDSET_LABEL')); ?>
				<?php echo $this->form->renderFieldset('general'); ?>
			<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
			<?php echo HTMLHelper::_('bootstrap.addTab', 'profileTab', 'features', Text::_('COM_PWTIMAGE_FEATURES_FIELDSET_LABEL')); ?>
				<?php echo $this->form->renderFieldset('features'); ?>
			<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
			<?php echo HTMLHelper::_('bootstrap.addTab', 'profileTab', 'extensions', Text::_('COM_PWTIMAGE_EXTENSIONS_FIELDSET_LABEL')); ?>
				<?php echo $this->loadTemplate('extensions'); ?>
			<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
			<?php echo HTMLHelper::_('bootstrap.addTab', 'profileTab', 'usergroups', Text::_('COM_PWTIMAGE_USERGROUPS_FIELDSET_LABEL')); ?>
			<?php echo $this->form->getLabel('usergroups'); ?>
			<?php echo HTMLHelper::_('access.usergroups', 'jform[usergroups]', $this->item->usergroups); ?>
			<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
			<?php echo HTMLHelper::_('bootstrap.addTab', 'profileTab', 'advanced', Text::_('COM_PWTIMAGE_ADVANCED_FIELDSET_LABEL')); ?>
			<?php echo $this->form->renderFieldset('advanced'); ?>
			<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
		<?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
	</div>

	<input type="hidden" name="task" value=""/>
	<?php echo $this->form->getInput('id'); ?>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
