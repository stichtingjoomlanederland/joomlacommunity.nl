<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();
?>
<h3>
    <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_RUN_BACKUPS'); ?>
</h3>

<p>
    <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_HEADERINFO'); ?>
</p>

<fieldset>
    <legend><?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_CLICRON'); ?></legend>

    <?php if(AKEEBA_PRO): ?>
    <p class="alert alert-info">
        <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_CLICRON_INFO'); ?>
        <br/>
        <a class="btn btn-mini btn-info" href="https://www.akeebabackup.com/documentation/akeeba-backup-documentation/native-cron-script.html" target="_blank">
            <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_GENERICREADDOC'); ?>
        </a>
    </p>
    <p>
        <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_GENERICUSECLI'); ?>
        <code>
            <?php echo $this->escape($this->croninfo->info->php_path); ?> <?php echo $this->escape($this->croninfo->cli->path); ?>

        </code>
    </p>
    <p>
        <span class="label label-important"><?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_CLIGENERICIMPROTANTINFO'); ?></span>
        <?php echo \JText::sprintf('COM_AKEEBA_SCHEDULE_LBL_CLIGENERICINFO', $this->croninfo->info->php_path); ?>
    </p>
    <?php endif; ?>

    <?php if ( ! (AKEEBA_PRO)): ?>
    <div class="alert">
        <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_UPGRADETOPRO'); ?>
        <br/>
        <a class="btn btn-primary" href="https://www.akeebabackup.com/subscribe.html" target="_blank">
            <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_UPGRADENOW'); ?>
        </a>
    </div>
    <?php endif; ?>
</fieldset>

<fieldset>
    <legend><?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_ALTCLICRON'); ?></legend>

    <?php if(AKEEBA_PRO): ?>
    <p class="alert alert-info">
        <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_ALTCLICRON_INFO'); ?>
        <br/>
        <a class="btn btn-mini btn-info" href="https://www.akeebabackup.com/documentation/akeeba-backup-documentation/alternative-cron-script.html" target="_blank">
            <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_GENERICREADDOC'); ?>
        </a>
    </p>

    <?php if(!$this->croninfo->info->feenabled): ?>
    <p class="alert alert-error">
        <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_DISABLED'); ?>
    </p>
    <?php endif; ?>

    <?php if($this->croninfo->info->feenabled && !trim($this->croninfo->info->secret)): ?>
    <p class="alert alert-error">
        <?php echo JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_SECRET'); ?>
    </p>
    <?php endif; ?>

    <?php if($this->croninfo->info->feenabled && trim($this->croninfo->info->secret)): ?>
    <p>
        <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_GENERICUSECLI'); ?>
        <code>
            <?php echo $this->escape($this->croninfo->info->php_path); ?> <?php echo $this->escape($this->croninfo->altcli->path); ?>

        </code>
    </p>
    <p>
        <span class="label label-important"><?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_CLIGENERICIMPROTANTINFO'); ?></span>
        <?php echo \JText::sprintf('COM_AKEEBA_SCHEDULE_LBL_CLIGENERICINFO', $this->croninfo->info->php_path); ?>
    </p>
    <?php endif; ?>

    <?php endif; ?>

    <?php if ( ! (AKEEBA_PRO)): ?>
    <div class="alert">
        <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_UPGRADETOPRO'); ?>
        <br/>
        <a class="btn btn-primary" href="https://www.akeebabackup.com/subscribe.html" target="_blank">
            <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_UPGRADENOW'); ?>
        </a>
    </div>
    <?php endif; ?>
</fieldset>

<fieldset>
    <legend><?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTENDBACKUP'); ?></legend>

    <p class="alert alert-info">
        <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTENDBACKUP_INFO'); ?>
        <br/>
        <a class="btn btn-mini btn-info" href="https://www.akeebabackup.com/documentation/akeeba-backup-documentation/automating-your-backup.html" target="_blank">
            <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_GENERICREADDOC'); ?>
        </a>
    </p>

    <?php if(!$this->croninfo->info->feenabled): ?>
    <p class="alert alert-error">
        <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_DISABLED'); ?>
    </p>
    <?php endif; ?>

    <?php if($this->croninfo->info->feenabled && !trim($this->croninfo->info->secret)): ?>
    <p class="alert alert-error">
        <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_SECRET'); ?>
    </p>
    <?php endif; ?>

    <?php if($this->croninfo->info->feenabled && trim($this->croninfo->info->secret)): ?>
    <p>
        <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTENDBACKUP_MANYMETHODS'); ?>
    </p>

    <?php echo \JHtml::_('bootstrap.startTabSet', 'abschedulingBackupTabs', array('active' => 'absTabBackupWget')); ?>
    <?php echo \JHtml::_('bootstrap.addTab', 'abschedulingBackupTabs', 'absTabBackupWebcron', JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTENDBACKUP_TAB_WEBCRON', true)); ?>
        <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_WEBCRON'); ?>
        <table class="table table-striped" width="100%">
            <tr>
                <td></td>
                <td>
                    <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_WEBCRON_INFO'); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_WEBCRON_NAME'); ?>
                </td>
                <td>
                    <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_WEBCRON_NAME_INFO'); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_WEBCRON_TIMEOUT'); ?>
                </td>
                <td>
                    <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_WEBCRON_TIMEOUT_INFO'); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_WEBCRON_URL'); ?>
                </td>
                <td>
                    <?php echo $this->escape($this->croninfo->info->root_url); ?>/<?php echo $this->escape($this->croninfo->frontend->path); ?>

                </td>
            </tr>
            <tr>
                <td>
                    <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_WEBCRON_LOGIN'); ?>
                </td>
                <td>
                    <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_WEBCRON_LOGINPASSWORD_INFO'); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_WEBCRON_PASSWORD'); ?>
                </td>
                <td>
                    <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_WEBCRON_LOGINPASSWORD_INFO'); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_WEBCRON_EXECUTIONTIME'); ?>
                </td>
                <td>
                    <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_WEBCRON_EXECUTIONTIME_INFO'); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_WEBCRON_ALERTS'); ?>
                </td>
                <td>
                    <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_WEBCRON_ALERTS_INFO'); ?>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_WEBCRON_THENCLICKSUBMIT'); ?>
                </td>
            </tr>
        </table>

    <?php echo \JHtml::_('bootstrap.endTab'); ?>
    <?php echo \JHtml::_('bootstrap.addTab', 'abschedulingBackupTabs', 'absTabBackupWget', JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTENDBACKUP_TAB_WGET', true)); ?>

        <p>
            <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_WGET'); ?>
            <code>
                wget --max-redirect=10000 "<?php echo $this->escape($this->croninfo->info->root_url); ?>/<?php echo $this->escape($this->croninfo->frontend->path); ?>" -O - 1>/dev/null 2>/dev/null
            </code>
        </p>

    <?php echo \JHtml::_('bootstrap.endTab'); ?>
    <?php echo \JHtml::_('bootstrap.addTab', 'abschedulingBackupTabs', 'absTabBackupCurl', JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTENDBACKUP_TAB_CURL', true)); ?>

        <p>
            <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_CURL'); ?>
            <code>
                curl -L --max-redirs 1000 -v "<?php echo $this->escape($this->croninfo->info->root_url); ?>/<?php echo $this->escape($this->croninfo->frontend->path); ?>" 1>/dev/null 2>/dev/null
            </code>
        </p>

    <?php echo \JHtml::_('bootstrap.endTab'); ?>
    <?php echo \JHtml::_('bootstrap.addTab', 'abschedulingBackupTabs', 'absTabBackupScript', JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTENDBACKUP_TAB_SCRIPT', true)); ?>

        <p>
            <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_CUSTOMSCRIPT'); ?>
        </p>
        <pre>
&lt;?php
    $curl_handle=curl_init();
    curl_setopt($curl_handle, CURLOPT_URL, '<?php echo $this->escape($this->croninfo->info->root_url); ?>/<?php echo $this->escape($this->croninfo->frontend->path); ?>');
    curl_setopt($curl_handle,CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($curl_handle,CURLOPT_MAXREDIRS, 10000);
    curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER, 1);
    $buffer = curl_exec($curl_handle);
    curl_close($curl_handle);
    if (empty($buffer))
        echo "Sorry, the backup didn't work.";
    else
        echo $buffer;
?&gt;
            </pre>

    <?php echo \JHtml::_('bootstrap.endTab'); ?>
    <?php echo \JHtml::_('bootstrap.addTab', 'abschedulingBackupTabs', 'absTabBackupUrl', JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTENDBACKUP_TAB_URL', true)); ?>

        <p>
            <?php echo \JText::_('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_RAWURL'); ?>
            <code>
                <?php echo $this->croninfo->info->root_url ?>/<?php echo $this->croninfo->frontend->path ?>
            </code>
        </p>

    <?php echo \JHtml::_('bootstrap.endTab'); ?>
    <?php echo \JHtml::_('bootstrap.endTabSet'); ?>

    <?php endif; ?>
</fieldset>