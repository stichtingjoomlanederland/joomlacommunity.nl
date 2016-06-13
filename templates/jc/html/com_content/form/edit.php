<?php
/**
 * @package    Perfect_Frontend_Editing
 *
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.tabstate');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');

// Add validate script
JHtml::script(JURI::base() . 'media/system/js/validate.js');
JHtml::script(JURI::base() . '/media/system/js/mootools-core.js');
JHtml::script(JURI::base() . '/media/system/js/mootools-more.js');
JHtml::script(JURI::base() . '/media/system/js/caption.js');
JHtml::script(JURI::base() . '/media/system/js/core.js');
JHtml::script(JURI::base() . '/media/system/js/modal.js');

// Create shortcut to parameters.
$params  = $this->state->get('params');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'article.cancel' || document.formvalidator.isValid(document.getElementById('adminForm')))
		{
			<?php echo $this->form->getField('articletext')->save(); ?>
			Joomla.submitform(task);
		}
	};
	$(function () {
		$('.hasTooltip').tooltip({'html':true})
	})
</script>

<div class="edit frontendediting item-page<?php echo $this->pageclass_sfx; ?>">
	<?php if ($params->get('show_page_heading', 1)) : ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($params->get('page_heading')); ?>
			</h1>
		</div>
	<?php endif; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_content&a_id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-vertical">
		<!-- Visible fieldset -->
		<fieldset>
			<div class="row-fluid">
				<!-- Left column -->
				<div class="content-8">
					<?php echo JHtml::_('bootstrap.startTabSet', 'pfe', array('active' => 'editor')); ?>

					<!-- Editor tab -->
					<?php echo JHtml::_('bootstrap.addTab', 'pfe', 'editor', JText::_('COM_CONTENT_ARTICLE_CONTENT', true)); ?>
					<?php echo $this->form->renderField('title'); ?>
					<?php echo $this->form->renderField('articletext', null, null, array('hiddenLabel' => true)); ?>
					<?php echo JHtml::_('bootstrap.endTab'); ?>

					<!-- Image tab -->
					<?php if ($params->get('show_urls_images_frontend')) : ?>
						<?php echo JHtml::_('bootstrap.addTab', 'pfe', 'images', JText::_('COM_CONTENT_IMAGES_AND_URLS', true)); ?>

						<?php echo JHtml::_('bootstrap.startAccordion', 'article-image', array('active' => 'full-image')); ?>

						<?php echo JHtml::_('bootstrap.addSlide', 'article-image', JText::_("PLG_PERFECTFRONTENDEDITING_IMAGE_FULL_LABEL"), 'full-image'); ?>
						<?php foreach($this->form->getFieldset('image-full') as $field) : ?>
							<?php echo $this->form->renderField($field->fieldname, $field->group); ?>
						<?php endforeach; ?>
						<?php echo JHtml::_('bootstrap.endSlide'); ?>

						<?php echo JHtml::_('bootstrap.addSlide', 'article-image', JText::_("PLG_PERFECTFRONTENDEDITING_IMAGE_INTRO_LABEL"), 'intro-image'); ?>
						<?php foreach($this->form->getFieldset('image-intro') as $field) : ?>
							<?php echo $this->form->renderField($field->fieldname, $field->group); ?>
						<?php endforeach; ?>
						<?php echo JHtml::_('bootstrap.endSlide'); ?>

						<?php echo JHtml::_('bootstrap.addSlide', 'article-image', JText::_("PLG_PERFECTFRONTENDEDITING_ARTICLE_URLS"), 'urls'); ?>
						<?php foreach($this->form->getGroup('urls') as $field) : ?>
							<?php echo $this->form->renderField($field->fieldname, $field->group); ?>
						<?php endforeach; ?>
						<?php echo JHtml::_('bootstrap.endSlide'); ?>

						<?php echo JHtml::_('bootstrap.endAccordion'); ?>

						<?php echo JHtml::_('bootstrap.endTab'); ?>
					<?php endif; ?>

					<!-- Author tab -->
					<?php echo JHtml::_('bootstrap.addTab', 'pfe', 'author', JText::_('JAUTHOR', true)); ?>
					<?php echo $this->form->renderField('created_by_alias'); ?>
					<?php echo JHtml::_('bootstrap.endTab'); ?>

					<!-- Seo tab -->
					<?php echo JHtml::_('bootstrap.addTab', 'pfe', 'seo', JText::_('PLG_PERFECTFRONTENDEDITING_ARTICLE_SEO', true)); ?>
					<?php
						$this->form->setFieldAttribute("metadesc", "class", "form-control");
						echo $this->form->renderField('metadesc');
					?>
					<?php
					$this->form->setFieldAttribute("metakey", "class", "form-control");
						echo $this->form->renderField('metakey');
					?>
					<?php echo JHtml::_('bootstrap.endTab'); ?>

					<!-- Language tab -->
					<!-- If the languagefilter plugin is not enabled, the site is not multi-language so language setting are not needed -->
					<?php if (JPluginHelper::isEnabled("system", "languagefilter") == true) : ?>
						<?php echo JHtml::_('bootstrap.addTab', 'pfe', 'language', JText::_('JFIELD_LANGUAGE_LABEL', true)); ?>
						<div class="tab-pane" id="language">
							<?php echo $this->form->renderField('language'); ?>
						</div>
						<?php echo JHtml::_('bootstrap.endTab'); ?>
					<?php endif; ?>

					<!-- Tabs for additonal parameters -->
					<?php foreach ($this->form->getFieldsets('params') as $name => $fieldSet) : ?>
						<?php echo JHtml::_('bootstrap.addTab', 'pfe', $name, JText::_($fieldset->label, true)); ?>
						<?php foreach ($this->form->getFieldset($name) as $field) : ?>
							<?php echo $this->form->renderField($field->fieldname, $field->group); ?>
						<?php endforeach; ?>
						<?php echo JHtml::_('bootstrap.endTab'); ?>
					<?php endforeach; ?>
					<?php echo JHtml::_('bootstrap.endTabSet'); ?>
				</div><!-- End Left column -->

				<!-- Right column -->
				<div class="content-4">
					<div class="well">
						<h3 class="page-header"><?php echo JText::_('PLG_PERFECTFRONTENDEDITING_FORM_PUBLICATIONDETAILS') ?></h3>
						<?php echo $this->form->renderField('state'); ?>
						<?php echo $this->form->renderField('access'); ?>
						<?php echo $this->form->renderField('catid'); ?>
						<?php echo $this->form->renderField('featured'); ?>
						<?php echo $this->form->renderField('publish_up'); ?>
						<?php echo $this->form->renderField('publish_down'); ?>
						<div class="btn-toolbar">
							<div class="btn-group">
								<button type="button" class="btn" onclick="Joomla.submitbutton('article.cancel')">
									<span class="icon-cancel"></span>&#160;<?php echo JText::_('JCANCEL') ?>
								</button>
							</div>
							<div class="btn-group">
								<button type="button" class="btn btn-success" onclick="Joomla.submitbutton('article.save')">
									<span class="icon-ok"></span>&#160;<?php echo JText::_('JSAVE') ?>
								</button>
							</div>
						</div>
					</div>
					<div class="well">
						<h3 class="page-header"><?php echo JText::_('JTAG') ?></h3>
						<?php echo $this->form->renderField('tags', null, null, array('hiddenLabel' => true)); ?>
					</div>
				</div><!-- Right column -->
			</div>
		</fieldset><!-- End Visible fieldset -->

		<!-- Hidden fieldset -->
		<fieldset class="hidden">
			<?php echo $this->form->getInput('alias'); ?>
			<?php echo JHtml::_('form.token'); ?>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
			<?php if ($this->params->get('enable_category', 0) == 1) :?>
				<input type="hidden" name="jform[catid]" value="<?php echo $this->params->get('catid', 1); ?>" />
			<?php endif; ?>
		</fieldset><!-- End Hidden fieldset -->
	</form>
</div>