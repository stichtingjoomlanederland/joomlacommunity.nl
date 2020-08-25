<?php
defined('_JEXEC') or die('Restricted access');
?><div class="acym__content acym_area padding-vertical-1 padding-horizontal-2 margin-bottom-2">
	<div class="acym_area_title"><?php echo acym_translation('ACYM_CONFIGURATION_QUEUE'); ?></div>
	<div class="grid-x grid-margin-x">
		<div class="cell medium-3"><?php echo acym_translation('ACYM_CONFIGURATION_QUEUE_PROCESSING'); ?></div>
		<div class="cell medium-9">
            <?php
            $queueModes = [
                'auto' => acym_translation('ACYM_CONFIGURATION_QUEUE_AUTOMATIC'),
                'automan' => acym_translation('ACYM_CONFIGURATION_QUEUE_AUTOMAN'),
                'manual' => acym_translation('ACYM_CONFIGURATION_QUEUE_MANUAL'),
            ];
            echo acym_radio($queueModes, 'config[queue_type]', $this->config->get('queue_type', 'automan'));
            ?>
		</div>
		<div class="cell medium-3 margin-top-1"><?php echo acym_translation('ACYM_AUTO_SEND_PROCESS'); ?></div>
		<div class="cell medium-9 margin-top-1">
            <?php
            $delayTypeAuto = acym_get('type.delay');
            echo acym_translation_sprintf(
                'ACYM_SEND_X_EVERY_Y',
                '<input class="intext_input" type="text" name="config[queue_nbmail_auto]" value="'.intval($this->config->get('queue_nbmail_auto')).'" />',
                $delayTypeAuto->display('config[cron_frequency]', $this->config->get('cron_frequency'), 2)
            ); ?>
		</div>
		<div class="cell medium-3 margin-top-1"><?php echo acym_translation('ACYM_MANUAL_SEND_PROCESS'); ?></div>
		<div class="cell medium-9 margin-top-1">
            <?php
            $delayTypeAuto = acym_get('type.delay');
            echo acym_translation_sprintf(
                'ACYM_SEND_X_WAIT_Y',
                '<input class="intext_input" type="text" name="config[queue_nbmail]" value="'.intval($this->config->get('queue_nbmail')).'" />',
                $delayTypeAuto->display('config[queue_pause]', $this->config->get('queue_pause'), 0)
            ); ?>
		</div>
		<div class="cell medium-3 margin-top-1"><?php echo '<span>'.acym_translation('ACYM_MAX_NB_TRY').'</span>'.acym_info(acym_translation('ACYM_MAX_NB_TRY_DESC')); ?></div>
		<div class="cell medium-9 margin-top-1">
            <?php echo acym_translation_sprintf('ACYM_CONFIG_TRY', '<input class="intext_input" type="text" name="config[queue_try]" value="'.intval($this->config->get('queue_try')).'">');

            $failaction = acym_get('type.failaction');
            echo ' '.acym_translation_sprintf('ACYM_CONFIG_TRY_ACTION', $failaction->display('maxtry', $this->config->get('bounce_action_maxtry'))); ?>
		</div>
		<div class="cell medium-3 margin-top-1"><?php echo acym_translation('ACYM_MAX_EXECUTION_TIME'); ?></div>
		<div class="cell medium-9 margin-top-1">
            <?php
            echo acym_translation_sprintf('ACYM_TIMEOUT_SERVER', ini_get('max_execution_time')).'<br />';
            $maxexecutiontime = intval($this->config->get('max_execution_time'));
            if (intval($this->config->get('last_maxexec_check')) > (time() - 20)) {
                echo acym_translation_sprintf('ACYM_TIMEOUT_CURRENT', $maxexecutiontime);
            } else {
                if (!empty($maxexecutiontime)) {
                    echo acym_translation_sprintf('ACYM_MAX_RUN', $maxexecutiontime).'<br />';
                }
                echo '<span id="timeoutcheck"><a id="timeoutcheck_action" class="acym__color__blue">'.acym_translation('ACYM_TIMEOUT_AGAIN').'</a></span>';
            }
            ?>
		</div>
		<div class="cell medium-3 margin-top-1"><?php echo acym_translation('ACYM_ORDER_SEND_QUEUE'); ?></div>
		<div class="cell medium-9 margin-top-1">
            <?php
            $ordering = [];
            $ordering[] = acym_selectOption("user_id, ASC", 'user_id ASC');
            $ordering[] = acym_selectOption("user_id, DESC", 'user_id DESC');
            $ordering[] = acym_selectOption('rand', 'ACYM_RANDOM');
            echo acym_select(
                $ordering,
                'config[sendorder]',
                $this->config->get('sendorder', 'user_id, ASC'),
                'class="intext_select"',
                'value',
                'text',
                'sendorderid'
            );

            echo '</div>';

            if (acym_level(1)) {
                $expirationDate = $this->config->get('expirationdate', 0);
                if (empty($expirationDate) || (time() - 604800) > $this->config->get('lastlicensecheck', 0)) {
                    acym_checkVersion();
                }

                $cronUrl = acym_frontendLink('cron');

                if ($expirationDate > time()) {
                    ?>
					<div class="cell medium-3 margin-top-1"><?php echo acym_translation('ACYM_CRON_URL').acym_info(acym_translation('ACYM_CRON_URL_DESC')); ?></div>
					<div class="cell medium-9 margin-top-1">
						<a class="acym__color__blue" href="<?php echo acym_escape($cronUrl, true); ?>" target="_blank"><?php echo $cronUrl; ?></a>
					</div>
                    <?php
                }
            }
            ?>
		</div>
	</div>
    <?php
    if (acym_level(1)) {
    ?>
	<div class="acym__content acym_area padding-vertical-1 padding-horizontal-2 margin-bottom-2">
		<div class="acym_area_title"><?php echo acym_translation('ACYM_REPORT'); ?></div>
		<div class="grid-x grid-margin-x">
			<div class="cell large-2 medium-3"><label for="cronsendreport"><?php echo acym_translation('ACYM_REPORT_SEND').acym_info(acym_translation('ACYM_REPORT_SEND_DESC')); ?></label></div>
			<div class="cell large-4 medium-9">
                <?php
                $cronreportval = [
                    '0' => 'ACYM_NO',
                    '1' => 'ACYM_EACH_TIME',
                    '2' => 'ACYM_ONLY_ACTION',
                    '3' => 'ACYM_ONLY_SOMETHING_WRONG',
                ];

                echo acym_select(
                    $cronreportval,
                    'config[cron_sendreport]',
                    $this->config->get('cron_sendreport', 0),
                    [
                        'class' => 'acym__select',
                        'acym-data-infinite' => '',
                    ],
                    'value',
                    'text',
                    'cronsendreport',
                    true
                );
                ?>
			</div>
			<div class="cell large-2 medium-3"><label for="cron_sendto"><?php echo acym_translation('ACYM_REPORT_SEND_TO').acym_info(acym_translation('ACYM_REPORT_SEND_TO_DESC')); ?></label></div>
			<div class="cell large-4 medium-9">
                <?php
                $emails = [];
                $receivers = $this->config->get('cron_sendto');
                if (!empty($receivers)) {
                    $receivers = explode(',', $receivers);
                    foreach ($receivers as $value) {
                        $emails[$value] = $value;
                    }
                }
                echo acym_selectMultiple($emails, "config[cron_sendto]", $emails, ['id' => 'acym__configuration__cron__report--send-to', 'placeholder' => acym_translation('ACYM_MAILS')]); ?>
			</div>
			<div class="cell large-2 medium-3"><label for="cronsavereport"><?php echo acym_translation('ACYM_REPORT_SAVE').acym_info(acym_translation('ACYM_REPORT_SAVE_DESC')); ?></label></div>
			<div class="cell large-4 medium-9">
                <?php
                $cronsave = [];
                $cronsave['0'] = acym_translation('ACYM_NO');
                $cronsave['1'] = acym_translation('ACYM_SIMPLIFIED_REPORT');
                $cronsave['2'] = acym_translation('ACYM_DETAILED_REPORT');

                echo acym_select(
                    $cronreportval,
                    'config[cron_savereport]',
                    (int)$this->config->get('cron_savereport', 2),
                    [
                        'class' => 'acym__select',
                        'acym-data-infinite' => '',
                    ],
                    'value',
                    'text',
                    'cronsavereport',
                    true
                );
                ?>
			</div>
			<div class="cell large-2 medium-3"><label for="cron_savepath"><?php echo acym_translation('ACYM_REPORT_SAVE_TO').acym_info(acym_translation('ACYM_REPORT_SAVE_TO_DESC')); ?></label></div>
			<div class="cell large-4 medium-9">
				<input id="cron_savepath" type="text" name="config[cron_savepath]" value="<?php echo acym_escape($this->config->get('cron_savepath')); ?>">
			</div>
			<div class="cell">
                <?php
                $link = acym_completeLink('cpanel', true).'&amp;task=cleanreport';
                echo '<button type="submit" data-task="deletereport" class="margin-right-1 button acy_button_submit">'.acym_translation('ACYM_REPORT_DELETE').'</button>';

                echo acym_modal(
                    acym_translation('ACYM_REPORT_SEE'),
                    '',
                    null,
                    '',
                    'class="button" data-ajax="true" data-iframe="&ctrl=configuration&task=seereport"'
                );
                ?>
			</div>
		</div>
	</div>

	<div class="acym__content acym_area padding-vertical-1 padding-horizontal-2">
		<div class="acym_area_title"><?php echo acym_translation('ACYM_LAST_CRON'); ?></div>
		<div class="grid-x grid-margin-x">
			<div class="cell medium-3"><?php echo acym_translation('ACYM_LAST_RUN').acym_info(acym_translation('ACYM_LAST_RUN_DESC')); ?></div>
			<div class="cell medium-9">
                <?php
                $cronLast = $this->config->get('cron_last', 0);
                $diff = intval((time() - $cronLast) / 60);
                if ($diff > 500) {
                    if (empty($cronLast)) {
                        echo acym_translation('ACYM_NEVER');
                    } else {
                        echo acym_date($cronLast, 'd F Y H:i');
                        echo ' <span style="font-size:10px">('.acym_translation_sprintf('ACYM_CURRENT_TIME', acym_date('now', 'd F Y H:i')).')</span>';
                    }
                } else {
                    echo acym_translation_sprintf('ACYM_MINUTES_AGO', $diff);
                }
                ?>
			</div>
			<div class="cell medium-3"><?php echo acym_translation('ACYM_CRON_TRIGGERED_IP').acym_info(acym_translation('ACYM_CRON_TRIGGERED_IP_DESC')); ?></div>
			<div class="cell medium-9">
                <?php echo $this->config->get('cron_fromip'); ?>
			</div>
			<div class="cell medium-3"><?php echo acym_translation('ACYM_REPORT').acym_info(acym_translation('ACYM_REPORT_DESC')); ?></div>
			<div class="cell medium-9">
                <?php echo nl2br($this->config->get('cron_report')); ?>
			</div>
		</div>
	</div>
	<div class="acym__content acym_area padding-vertical-1 padding-horizontal-2">
		<div class="acym_area_title"><?php echo acym_translation('ACYM_AUTOMATED_TASKS'); ?></div>
		<div class="grid-x grid-margin-x">
			<div class="cell acym_auto_tasks">

                <?php

                $listHours = [];
                for ($i = 0 ; $i < 24 ; $i++) {
                    $value = $i < 10 ? '0'.$i : $i;
                    $listHours[] = acym_selectOption($value, $value);
                }
                $hours = acym_select($listHours, 'config[daily_hour]', $this->config->get('daily_hour', '12'), 'class="intext_select"');

                $listMinutess = [];
                for ($i = 0 ; $i < 60 ; $i += 5) {
                    $value = $i < 10 ? '0'.$i : $i;
                    $listMinutess[] = acym_selectOption($value, $value);
                }
                $minutes = acym_select($listMinutess, 'config[daily_minute]', $this->config->get('daily_minute', '00'), 'class="intext_select"');

                echo acym_translation_sprintf('ACYM_DAILY_TASKS', $hours, $minutes);

                ?>
			</div>
		</div>
	</div>
<?php
}
if (!acym_level(1)) {
    $data['version'] = 'essential';
    echo '<div class="acym_area">
            <div class="acym_area_title">'.acym_translation('ACYM_CRON').'</div>';
    include(ACYM_VIEW.'dashboard'.DS.'tmpl'.DS.'upgrade.php');
    echo '</div>';
}

