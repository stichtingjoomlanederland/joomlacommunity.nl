<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\AdminTools\Admin\Helper\Select;

/** @var $this \Akeeba\AdminTools\Admin\View\WAFBlacklistedRequests\Html */

defined('_JEXEC') or die;

$this->addJavascriptFile('admin://components/com_admintools/media/js/Wafblacklist.min.js');

/** @var \Akeeba\AdminTools\Admin\Model\WAFBlacklistedRequests $item */
$item = $this->getItem();

?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-form--horizontal akeeba-panel">
	<div class="akeeba-container--66-33">
		<div>
            <div class="akeeba-form-group">
                <label for="dest">
					<?php echo JText::_('JPUBLISHED'); ?>
                </label>

				<?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'enabled', $item->enabled)?>
                <p>
					<?php echo JText::_('COM_ADMINTOOLS_REDIRECTIONS_FIELD_PUBLISHED_DESC')?>
                </p>
            </div>

            <div class="akeeba-form-group">
                <label for="application">
					<?php echo JText::_('COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_APPLICATION'); ?>
                </label>

                <?php echo Select::wafApplication('application', null, $item->application)?>
                <p>
					<?php echo JText::_('COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_APPLICATION_DESC')?>
                </p>
            </div>

            <div class="akeeba-form-group">
                <label for="verb">
					<?php echo JText::_('COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_VERB'); ?>
                </label>

				<?php echo Select::httpVerbs('verb', null, $item->verb)?>
                <p>
					<?php echo JText::_('COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_VERB_TIP')?>
                </p>
            </div>

			<div class="akeeba-form-group">
				<label for="foption">
					<?php echo JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_OPTION'); ?>
				</label>

                <input type="text" name="foption" id="foption" value="<?php echo $this->escape($item->option); ?>" />
                <p>
					<?php echo JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_OPTION_TIP')?>
                </p>
			</div>

			<div class="akeeba-form-group">
				<label for="fview">
					<?php echo JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_VIEW'); ?>
				</label>

                <input type="text" name="fview" id="fview" value="<?php echo $this->escape($item->view); ?>" />

				<p>
					<?php echo JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_VIEW_TIP')?>
				</p>
			</div>

            <div class="akeeba-form-group">
                <label for="ftask">
					<?php echo JText::_('COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_TASK'); ?>
                </label>

                <input type="text" name="ftask" id="ftask" value="<?php echo $this->escape($item->ftask); ?>" />

                <p>
					<?php echo JText::_('COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_TASK_TIP')?>
                </p>
            </div>

            <div class="akeeba-form-group">
                <label for="query_type">
					<?php echo JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_QUERY'); ?>
                </label>

				<?php echo Select::queryParamType('query_type', null, $item->query_type)?>
            </div>

            <div class="akeeba-form-group">
                <label for="fquery">
					<?php echo JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_QUERY'); ?>
                </label>

                <input type="text" name="fquery" id="fquery" value="<?php echo $this->escape($item->query); ?>" />

                <p>
					<?php echo JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_QUERY_TIP')?>
                </p>
            </div>

            <div class="akeeba-form-group">
                <label for="query_content">
					<?php echo JText::_('COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_QUERY_CONTENT'); ?>
                </label>

                <input type="text" name="query_content" id="query_content" value="<?php echo $this->escape($item->query_content); ?>" />

                <p>
					<?php echo JText::_('COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_QUERY_CONTENT_TIP')?>
                </p>
            </div>
		</div>
	</div>

	<div class="akeeba-hidden-fields-container">
		<input type="hidden" name="option" value="com_admintools" />
		<input type="hidden" name="view" value="WAFBlacklistedRequests" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" id="id" value="<?php echo (int)$item->id; ?>" />
		<input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1" />
	</div>
</form>
