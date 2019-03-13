<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

/** @var  \Akeeba\Backup\Admin\View\Alice\Html $this */
?>
<?php if (!(empty($this->logs))): ?>
    <form name="adminForm" id="adminForm" action="index.php" method="post" class="akeeba-form--inline">
		<?php if ($this->autorun): ?>
            <div class="akeeba-block--warning">
				<p>
					<?php echo \JText::_('ALICE_AUTORUN_NOTICE'); ?>
                </p>
            </div>
		<?php endif; ?>

        <div class="akeeba-form-group">
            <label for="tag">
				<?php echo \JText::_('COM_AKEEBA_LOG_CHOOSE_FILE_TITLE'); ?>
            </label>
			<?php echo \JHtml::_('select.genericlist', $this->logs, 'log', [
				'onchange' => "this.options[this.selectedIndex].value ? document.getElementById('analyze-log').style.display = 'inline-block' : document.getElementById('analyze-log').style.display = 'none'",
			], 'value', 'text', $this->log); ?>
        </div>

        <div class="akeeba-form-group--actions">
            <button class="akeeba-btn--primary" id="analyze-log" style="display:none">
                <span class="akion-ios-analytics"></span>
		        <?php echo \JText::_('COM_AKEEBA_ALICE_ANALYZE'); ?>
            </button>

            <button class="akeeba-btn--dark" id="download-log"
                    data-url="<?php echo $this->escape(JUri::base()); ?>index.php?option=com_akeeba&view=Log&task=download&tag=[TAG]"
                    style="display: none;">
                <span class="akion-ios-download"></span>
		        <?php echo \JText::_('COM_AKEEBA_LOG_LABEL_DOWNLOAD'); ?>
            </button>
        </div>

        <div class="akeeba-hidden-fields-container">
            <input name="option" value="com_akeeba" type="hidden"/>
            <input name="view" value="Alice" type="hidden"/>
            <input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
        </div>
    </form>

    <div id="stepper-holder" style="margin-top: 15px">
        <div id="stepper-loading" style="text-align: center;display: none">
            <img src="<?php echo $this->escape($this->getContainer()->template->parsePath('media://com_akeeba/icons/loading.gif')); ?>"/>
        </div>
        <div id="stepper-progress-pane" style="display: none">
            <div class="akeeba-block--information">
				<?php echo \JText::_('COM_AKEEBA_BACKUP_TEXT_BACKINGUP'); ?>
            </div>

            <h4>
                <?php echo \JText::_('COM_AKEEBA_ALICE_ANALYZE_LABEL_PROGRESS'); ?>
            </h4>

            <div id="stepper-progress-content">
                <div id="stepper-steps">
                </div>
                <div id="stepper-status">
                    <div id="stepper-step"></div>
                    <div id="stepper-substep"></div>
                </div>
                <div id="stepper-percentage" class="akeeba-progress">
                    <div class="akeeba-progress-fill" style="width: 0"></div>
                </div>
                <div id="response-timer">
                    <div class="color-overlay"></div>
                    <div class="text"></div>
                </div>
            </div>

            <span id="ajax-worker"></span>
        </div>

        <div id="output-plain" class="akeeba-panel--information" style="display:none; margin-bottom: 20px;">
            <div class="akeeba-block--warning">
                <p>
                    <?php echo \JText::_('COM_AKEEBA_ALICE_ANALYZE_RAW_OUTPUT'); ?>
                </p>
            </div>

            <textarea style="width:50%; margin:auto; display:block; height: 100px;" readonly="readonly"
                      onclick="this.focus();this.select();"></textarea>
        </div>
        <div id="stepper-complete" style="display: none">
            <table class="akeeba-table--striped">
                <thead>
                <tr>
                    <th>
                        <?php echo JText::_('COM_AKEEBA_ALICE_HEADER_CHECK'); ?>
                    </th>
                    <th width="120">
	                    <?php echo JText::_('COM_AKEEBA_ALICE_HEADER_RESULT'); ?>
                    </th>
                </tr>
                </thead>
                <tbody id="alice-messages"></tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php if (empty($this->logs)): ?>
    <div class="akeeba-block--failure">
		<p><?php echo \JText::_('COM_AKEEBA_LOG_NONE_FOUND'); ?></p>
    </div>
<?php endif; ?>
