<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('formbehavior.chosen', 'select');

Factory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "custom.cancel" || document.formvalidator.isValid(document.getElementById("item-form")))
		{
			Joomla.submitform(task, document.getElementById("item-form"));
		}
	};
');
?>

<form action="<?php echo Route::_('index.php?option=com_pwtseo&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="item-form" class="form-validate">

    <div class="form-inline form-inline-header">
		<?php echo $this->form->renderField('url')	?>
    </div>

    <div class="form-horizontal">
	    <?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'attrib-seofieldset')); ?>
	    <?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'attrib-seofieldset', JText::_('PLG_SYSTEM_PWTSEO_FORM_TAB_LABEL')); ?>

        <?php echo $this->form->renderFieldset('seofieldset'); ?>

	    <?php echo HTMLHelper::_('bootstrap.endTab'); ?>
	    <?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>

    </div>

	<?php echo $this->form->renderFieldset('default'); ?>

    <input type="hidden" name="task" value=""/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
