<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var $this Html */

use Akeeba\AdminTools\Admin\Helper\Select;
use Akeeba\AdminTools\Admin\Model\WAFEmailTemplates;
use Akeeba\AdminTools\Admin\View\WAFEmailTemplates\Html;
use Joomla\CMS\Editor\Editor;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/** @var WAFEmailTemplates $item */
$item = $this->getItem();
?>

<div class="akeeba-panel">
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-form--horizontal">
		<div class="akeeba-form-group">
			<label for="key_field">
				<?php echo Text::_('COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_REASON_SELECT'); ?>
			</label>

			<?php echo Select::reasons('reason', $item->reason, ['all' => 1, 'misc' => 1]); ?>
		</div>

		<div class="akeeba-form-group">
			<label for="subject_field">
				<?php echo Text::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_FIELD_SUBJECT_LBL'); ?>
			</label>

			<input type="text" id="subject_field" name="subject" value="<?php echo $this->escape($item->subject); ?>" />
			<span class="akeeba-help-text"><?php echo Text::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_FIELD_SUBJECT_DESC'); ?></span>
		</div>

		<div class="akeeba-form-group">
			<label for="enabled">
				<?php echo Text::_('JPUBLISHED'); ?>
			</label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'enabled', $item->enabled) ?>
		</div>

		<div class="akeeba-form-group">
			<label for="language">
				<?php echo Text::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_FIELD_LANGUAGE_LBL'); ?>
			</label>

			<?php echo Select::languages('language', $item->language); ?>

			<span class="akeeba-help-text"><?php echo Text::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_FIELD_LANGUAGE_DESC'); ?></span>
		</div>

		<div class="akeeba-form-group">
			<label for="language">
				<?php echo Text::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_FIELD_SENDLIMIT_LBL'); ?>
			</label>

			<div>
				<input class="input-mini" type="text" size="5" name="email_num"
					   value="<?php echo (int) $item->email_num; ?>" />
				<span><?php echo Text::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_NUMFREQ'); ?></span>
				<input class="input-mini" type="text" size="5" name="email_numfreq"
					   value="<?php echo (int) $item->email_numfreq; ?>" />
				<?php echo Select::trsfreqlist('email_freq', ['class' => 'input-small'], $item->email_freq); ?>

				<span
						class="akeeba-help-text"><?php echo Text::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_FIELD_SENDLIMIT_DESC'); ?></span>
			</div>
		</div>

		<div class="akeeba-form-group">
			<label for="template">
				<?php echo Text::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_FIELD_TEMPLATE_LBL'); ?>
			</label>

			<?php echo Editor::getInstance($this->container->platform->getConfig()->get('editor', 'tinymce'))->display('template', $item->template, '97%', '391', '50', '20', false); ?>
			<span class="akeeba-help-text"><?php echo Text::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_FIELD_TEMPLATE_DESC'); ?></span>
			<span class="akeeba-help-text"><?php echo Text::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_FIELD_TEMPLATE_DESC_2'); ?></span>
		</div>

		<input type="hidden" name="option" value="com_admintools" />
		<input type="hidden" name="view" value="WAFEmailTemplates" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="admintools_waftemplate_id"
			   value="<?php echo (int) $item->admintools_waftemplate_id; ?>" />
		<input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1" />
	</form>
</div>
