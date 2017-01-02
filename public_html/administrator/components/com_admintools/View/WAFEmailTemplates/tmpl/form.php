<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var $this \Akeeba\AdminTools\Admin\View\WAFEmailTemplates\Form */

use Akeeba\AdminTools\Admin\Helper\Select;

defined('_JEXEC') or die;

?>

<div class="ats-ticket-replyarea">
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="form form-horizontal">
		<input type="hidden" name="option" value="com_admintools"/>
		<input type="hidden" name="view" value="WAFEmailTemplates"/>
		<input type="hidden" name="task" value="save"/>
		<input type="hidden" name="admintools_waftemplate_id"
			   value="<?php echo (int) $this->item->admintools_waftemplate_id; ?>"/>
		<input type="hidden" name="<?php echo \JFactory::getSession()->getFormToken(); ?>" value="1"/>

		<div class="control-group">
			<label for="key_field" class="control-label">
				<?php echo \JText::_('COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_REASON_SELECT'); ?>
			</label>

			<div class="controls">
				<?php echo Select::reasons($this->item->reason, 'reason', array('all' => 1, 'misc' => 1)); ?>

			</div>
		</div>

		<div class="control-group">
			<label for="subject_field" class="control-label">
				<?php echo \JText::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_FIELD_SUBJECT_LBL'); ?>
			</label>

			<div class="controls">
				<input type="text" class="input-xxlarge" id="subject_field" name="subject"
					   value="<?php echo $this->escape($this->item->subject); ?>"/>
				<span class="help-block"><?php echo \JText::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_FIELD_SUBJECT_DESC'); ?></span>
			</div>
		</div>

		<div class="control-group">
			<label for="enabled" class="control-label">
				<?php echo \JText::_('JPUBLISHED'); ?>
			</label>

			<?php echo \JHtml::_('select.booleanlist', 'enabled', null, $this->item->enabled); ?>
		</div>

		<div class="control-group">
			<label for="language" class="control-label">
				<?php echo \JText::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_FIELD_LANGUAGE_LBL'); ?>
			</label>

			<div class="controls">
                <?php echo Select::languages($this->item->language, 'language'); ?>

                <span
					class="help-block"><?php echo \JText::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_FIELD_LANGUAGE_DESC'); ?></span>
			</div>
		</div>

		<div class="control-group">
			<label for="language" class="control-label">
				<?php echo \JText::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_FIELD_SENDLIMIT_LBL'); ?>
			</label>

			<div class="controls">
				<input class="input-mini" type="text" size="5" name="email_num"
					   value="<?php echo (int) $this->item->email_num; ?>"/>
				<span><?php echo \JText::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_NUMFREQ'); ?></span>
				<input class="input-mini" type="text" size="5" name="email_numfreq"
					   value="<?php echo (int)$this->item->email_numfreq; ?>"/>
				<?php echo Select::trsfreqlist('email_freq', array('class' => 'input-small'), $this->item->email_freq); ?>

				<span
					class="help-block"><?php echo \JText::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_FIELD_SENDLIMIT_DESC'); ?></span>
			</div>
		</div>

		<div class="control-group">
			<label for="template" class="control-label">
				<?php echo \JText::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_FIELD_TEMPLATE_LBL'); ?>
			</label>

			<div class="controls">
                <?php echo JEditor::getInstance($this->container->platform->getConfig()->get('editor', 'tinymce'))->display('template', $this->item->template, '97%', '391', '50', '20', false); ?>
				<span class="help-block"><?php echo \JText::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_FIELD_TEMPLATE_DESC'); ?></span>
				<span class="help-block"><?php echo \JText::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_FIELD_TEMPLATE_DESC_2'); ?></span>
			</div>
		</div>

	</form>
</div>