<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var PwtSitemapViewMenu $this */

// Include the component HTML helpers.
HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

HTMLHelper::_('behavior.core');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('formbehavior.chosen', 'select');

Text::script('ERROR');

Factory::getDocument()->addScriptDeclaration("
		Joomla.submitbutton = function(task)
		{
			var form = document.getElementById('item-form');
			if (task == 'menu.cancel' || document.formvalidator.isValid(form))
			{
				Joomla.submitform(task, form);
			}
		};
");
?>
<form action="<?php echo Route::_('index.php?option=com_pwtsitemap&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="item-form">

	<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', ['active' => 'details']); ?>
		<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'details', Text::_('COM_PWTSITEMAP_MENU_DETAILS')); ?>

		<?php
		echo $this->form->renderField('menutype');
		echo $this->form->renderField('custom_title');
		echo $this->form->renderField('description');
		echo $this->form->renderField('client_id');
		echo $this->form->renderField('preset');
		?>

		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
		<?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
		<input type="hidden" name="task" value=""/>
		<?php echo $this->form->renderField('ordering'); ?>
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
