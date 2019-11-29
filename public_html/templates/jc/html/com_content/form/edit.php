<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

// At the moment this is only in use for the bedrijvengids, so no extended article submission support
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('formbehavior.chosen', 'select');

$user = Factory::getUser();

$this->tab_name = 'com-content-form';

// Create shortcut to parameters.
$params = $this->state->get('params');

JFactory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(task)
	{
		if (task == 'article.cancel' || document.formvalidator.isValid(document.getElementById('adminForm')))
		{
			" . $this->form->getField('articletext')->save() . "
			Joomla.submitform(task);
		}
	}
");

// Customize submisson form for bedrijvengids
$this->form->setFieldAttribute('title', 'label', 'Bedrijfsnaam');
$this->form->setFieldAttribute('articletext', 'label', 'Korte introductie over jouw bedrijf');
$this->form->setFieldAttribute('articletext', 'buttons', false);
$this->form->setFieldAttribute('articletext', 'rows', 5);
$this->form->setFieldAttribute('articletext', 'class', 'form-control');
$this->form->setFieldAttribute('articletext', 'required', true);
$this->form->setFieldAttribute('catid', 'type', 'hidden');
$this->form->setFieldAttribute('certified', 'layout', 'joomla.form.field.subform.repeatable', 'com_fields');
$this->form->setFieldAttribute('portfolio', 'layout', 'joomla.form.field.subform.repeatable', 'com_fields');
?>

<div class="row">
	<div class="content-4">
		<div class="panel panel-home">
			<div class="panel-heading">
				<h3 class="panel-title">Je bedrijf toevoegen/bewerken</h3>
			</div>
			<div class="panel-body">
				<p>Hallo <?php echo $user->name; ?>,</p>
				<p>Leuk dat je jouw bedrijf een plekje in de Joomla-bedrijvengids wilt geven!</p>
				<p>We gaan er vanuit dat je dit naar waarheid invult zodat we snel je bedrijf kunnen goedkeuren en kunnen publiceren in de bedrijvengids.</p>
				<p>Heb je nog vragen of suggesties voor de Joomla-bedrijvengids? Neem dan even
					<a href="/contact">contact</a> met ons op.</p>
				<p>Team JoomlaCommunity.nl</p>
			</div>
		</div>
	</div>
	<div class="content-8">
		<form action="<?php echo JRoute::_('index.php?option=com_content&a_id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-vertical well">
			<?php echo $this->form->renderField('title'); ?>
			<?php echo $this->form->renderField('kvk', 'com_fields', null, ['description' => $this->form->getField('kvk', 'com_fields')->getAttribute('description')]); ?>
			<?php echo $this->form->renderField('articletext'); ?>
			<p class="help-block">Graag geen extra links in de teksten plaatsen, gebruik daarvoor de website en portfolio velden.</p>

			<div class="image-logo">
				<?php echo $this->form->renderField('logo', 'com_fields', null, ['description' => $this->form->getField('logo', 'com_fields')->getAttribute('description')]); ?>
			</div>
			<div class="image-foto">
				<?php echo $this->form->renderField('foto', 'com_fields', null, ['description' => $this->form->getField('foto', 'com_fields')->getAttribute('description')]); ?>
			</div>

			<h3>Over het bedrijf</h3>
			<?php echo $this->form->renderField('specialiteiten', 'com_fields', null, ['description' => $this->form->getField('specialiteiten', 'com_fields')->getAttribute('description')]); ?>
			<?php echo $this->form->renderField('bedrijfsgrootte', 'com_fields', null, ['description' => $this->form->getField('bedrijfsgrootte', 'com_fields')->getAttribute('description')]); ?>
			<?php echo $this->form->renderField('certified', 'com_fields', null, ['description' => $this->form->getField('certified', 'com_fields')->getAttribute('description')]); ?>

			<h3>Contactgegevens</h3>
			<?php echo $this->form->renderField('contactpersoon', 'com_fields', null, ['description' => $this->form->getField('contactpersoon', 'com_fields')->getAttribute('description')]); ?>
			<?php echo $this->form->renderField('adres', 'com_fields', null, ['description' => $this->form->getField('adres', 'com_fields')->getAttribute('description')]); ?>
			<?php echo $this->form->renderField('postcode', 'com_fields', null, ['description' => $this->form->getField('postcode', 'com_fields')->getAttribute('description')]); ?>
			<?php echo $this->form->renderField('plaats', 'com_fields', null, ['description' => $this->form->getField('plaats', 'com_fields')->getAttribute('description')]); ?>
			<?php echo $this->form->renderField('provincie', 'com_fields', null, ['description' => $this->form->getField('provincie', 'com_fields')->getAttribute('description')]); ?>
			<?php echo $this->form->renderField('telefoon', 'com_fields', null, ['description' => $this->form->getField('telefoon', 'com_fields')->getAttribute('description')]); ?>
			<?php echo $this->form->renderField('email', 'com_fields', null, ['description' => $this->form->getField('email', 'com_fields')->getAttribute('description')]); ?>
			<?php echo $this->form->renderField('website', 'com_fields', null, ['description' => $this->form->getField('website', 'com_fields')->getAttribute('description')]); ?>

			<h3>Portfolio</h3>
			<?php echo $this->form->renderField('portfolio', 'com_fields', null, ['description' => $this->form->getField('portfolio', 'com_fields')->getAttribute('description')]); ?>

			<div class="alert alert-info" role="alert">Na het toevoegen van jouw bedrijf zullen de moderatoren van JoomlaCommunity.nl je toevoeging controleren en publiceren. Ook hier werken we met vrijwilligers, dus het kan een aantal dagen duren voordat je bedrijf zichtbaar wordt in de bedrijvengids.</div>

			<?php if ($this->captchaEnabled) : ?>
				<?php echo $this->form->renderField('captcha'); ?>
			<?php endif; ?>

			<div class="btn-toolbar">
				<div class="btn-group">
					<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('article.save')">
						<?php echo Text::_('JSAVE') ?>
					</button>
				</div>
				<div class="btn-group">
					<button type="button" class="btn" onclick="Joomla.submitbutton('article.cancel')">
						<?php echo Text::_('JCANCEL') ?>
					</button>
				</div>
			</div>

			<?php echo $this->form->renderField('catid'); ?>
			<input type="hidden" name="jform[language]" value="*" />
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="return" value="<?php echo $this->return_page; ?>"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</form>
	</div>
</div>