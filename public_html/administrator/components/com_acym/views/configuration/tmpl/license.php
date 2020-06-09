<?php
defined('_JEXEC') or die('Restricted access');
?><?php
if (acym_level(1)) {
    $licenseKey = acym_escape($this->config->get('license_key', ''));
    $cronUrl = acym_frontendLink('cron');
    $automaticSend = acym_escape($this->config->get('active_cron', 0));
    $cronFrequency = $this->config->get('cron_updateme_frequency', 900);
    if (empty($cronFrequency)) $cronFrequency = 900;
    ?>
	<div class="acym__content acym_area padding-vertical-1 padding-horizontal-2 margin-bottom-2 cell grid-margin-y">
		<div class="acym_area_title"><?php echo acym_translation('ACYM_MY_LICENSE'); ?>
			<a target="_blank" id="acym__configuration__subscription__page" class="margin-left-1" href="<?php echo ACYM_REDIRECT.'subscription-page'; ?>"><?php echo acym_translation('ACYM_GET_MY_LICENSE_KEY'); ?></a>
		</div>
		<div class="cell grid-x grid-margin-x acym_vcenter">
			<label for="acym__configuration__license-key" class="cell medium-2">
                <?php echo acym_translation('ACYM_YOUR_LICENSE_KEY'); ?>
			</label>
			<input type="text" name="config[license_key]" id="acym__configuration__license-key" class="medium-4 cell" value="<?php echo $licenseKey; ?>">
			<button type="button" id="acym__configuration__button__license" class="cell shrink button" data-acym-linked="<?php echo empty($licenseKey) ? 0 : 1; ?>"><?php echo acym_translation(empty($licenseKey) ? 'ACYM_ATTACH_MY_LICENSE' : 'ACYM_UNLINK_MY_LICENSE'); ?></button>
		</div>
        <?php if (!empty($licenseKey)) { ?>
			<div class="acym_area_title"><?php echo acym_translation('ACYM_CRON'); ?></div>
			<div class="cell grid-x grid-margin-x acym_vcenter">
				<label class="cell medium-2"><?php echo acym_translation('ACYM_SEND_PROCESS_FREQUENCY'); ?></label>
				<div class="cell medium-2">
                    <?php
                    $frequency = [
                        900 => acym_translation('ACYM_15_MINUTES'),
                        1800 => acym_translation('ACYM_30_MINUTES'),
                        3600 => acym_translation('ACYM_1_HOUR'),
                    ];
                    echo acym_select($frequency, 'config[cron_updateme_frequency]', $cronFrequency, 'class="acym__select"');
                    ?>
				</div>
			</div>
			<div class="cell grid-x grid-margin-x acym_vcenter">
				<label class="cell medium-2"><?php echo acym_translation('ACYM_AUTOMATIC_SEND_PROCESS').acym_info('ACYM_AUTOMATIC_SEND_PROCESS_DESC'); ?></label>
				<label class="cell shrink"><strong><?php echo acym_translation(empty($automaticSend) ? 'ACYM_DEACTIVATED' : 'ACYM_ACTIVATED'); ?></strong></label>
				<button data-acym-active="<?php echo $automaticSend; ?>" id="acym__configuration__button__cron" class="cell shrink button"><?php echo acym_translation(acym_translation(empty($automaticSend) ? 'ACYM_ACTIVATE_IT' : 'ACYM_DEACTIVATE_IT')); ?></button>
			</div>
			<div class="cell grid-x grid-margin-x acym_vcenter">
				<label class="cell medium-2"><?php echo acym_translation('ACYM_CRON_LINK').acym_info('ACYM_CRON_LINK_DESC'); ?></label>
				<a class="cell shrink" target="_blank" href="<?php echo $cronUrl; ?>"><?php echo $cronUrl; ?></a>
			</div>
        <?php } ?>
	</div>
<?php }
if (!acym_level(1)) {
    $data['version'] = 'essential';
    include acym_getView('configuration', 'upgrade_license', true);
}

