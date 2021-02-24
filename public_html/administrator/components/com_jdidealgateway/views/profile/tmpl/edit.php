<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/** @var JdidealgatewayViewProfile $this */

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

if (JVERSION < 4)
{
	HTMLHelper::_('formbehavior.chosen');
}
else
{
	Factory::getApplication()->getDocument()->getWebAssetManager()
		->usePreset('choicesjs')
		->useScript('webcomponent.field-fancy-select');

	foreach ($this->form->getFieldset('emails') as $field) {
		if ($field->getAttribute('type') === 'radio')
		{
			$this->form->setFieldAttribute($field->getAttribute('name'), 'layout', 'joomla.form.field.radio.switcher');
		}
	}
}
?>
<form action="<?php echo Route::_('index.php?option=com_jdidealgateway&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" enctype="multipart/form-data" id="adminForm" class="form-validate">
	<?php
	$url = Uri::getInstance();

	if ($this->activeProvider === 'mollie' && $url->getHost() === 'localhost')
	{
		Factory::getApplication()->enqueueMessage(Text::sprintf('COM_ROPAYMENTS_MUST_BE_ONLINE', ucfirst($this->activeProvider)), 'error');
	}

	if ($this->activeProvider === 'ing-lite')
	{
		Factory::getApplication()->enqueueMessage(Text::_('COM_ROPAYMENTS_NO_FEEDBACK'), 'warning');
	}
	?>
	<?php if (JVERSION < 4) : ?>
	<div class="form-inline form-inline-header">
		<?php
		echo $this->form->renderField('psp');
		echo $this->form->renderField('name');
		echo $this->form->renderField('alias');
		?>
	</div>
	<?php else: ?>
	<div class="form-inline form-inline-header row title-alias form-vertical mb-3">
		<div class="span12 col-12 col-md-4">
			<?php echo $this->form->renderField('psp'); ?>
		</div>
		<div class="span12 col-12 col-md-4">
			<?php echo $this->form->renderField('name'); ?>
		</div>
		<div class="span12 col-12 col-md-4">
			<?php echo $this->form->renderField('alias'); ?>
		</div>
	</div>
	<?php endif; ?>
	<hr />
	<div class="form-horizontal">
		<?php echo HTMLHelper::_('bootstrap.startTabSet', 'pspTab', ['active' => 'settings']); ?>
		<?php echo HTMLHelper::_('bootstrap.addTab', 'pspTab', 'settings', Text::_('COM_ROPAYMENTS_PROFILE_TAB_SETTINGS')); ?>
		<?php
            if ($this->pspForm)
            {
                echo $this->loadTemplate($this->activeProvider);
            }
        ?>
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
		<?php echo HTMLHelper::_('bootstrap.addTab', 'pspTab', 'email', Text::_('COM_ROPAYMENTS_PROFILE_TAB_EMAIL')); ?>
		<?php echo $this->form->renderFieldset('emails'); ?>
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
		<?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="" />
	<?php echo $this->form->getInput('id'); ?>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
