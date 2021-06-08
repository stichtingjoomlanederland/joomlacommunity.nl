<?php
if (acym_level(ACYM_ESSENTIAL)) {
    $licenseKey = acym_escape($this->config->get('license_key', ''));
    $cronUrl = acym_frontendLink('cron');
    $automaticSend = acym_escape($this->config->get('active_cron', 0));
    ?>
	<div class="acym__content acym_area padding-vertical-1 padding-horizontal-2 margin-bottom-2 cell margin-y">
		<div class="acym__title acym__title__secondary"><?php echo acym_translation('ACYM_MY_LICENSE'); ?>
			<?php echo acym_externalLink('ACYM_GET_MY_LICENSE_KEY', ACYM_REDIRECT.'subscription-page', true, true, ['margin-left-1']) ?>
		</div>
		<div class="cell grid-x grid-margin-x acym_vcenter">
			<label for="acym__configuration__license-key" class="cell medium-2">
                <?php echo acym_translation('ACYM_YOUR_LICENSE_KEY'); ?>
			</label>
			<input type="text" name="config[license_key]" id="acym__configuration__license-key" class="medium-4 cell" value="<?php echo $licenseKey; ?>">
			<button type="button"
					id="acym__configuration__button__license"
					class="cell shrink button"
					data-acym-linked="<?php echo empty($licenseKey) ? 0 : 1; ?>"><?php echo acym_translation(
                    empty($licenseKey) ? 'ACYM_ATTACH_MY_LICENSE' : 'ACYM_UNLINK_MY_LICENSE'
                ); ?></button>
		</div>
        <?php if (!empty($licenseKey)) { ?>
			<div class="acym__title acym__title__secondary"><?php echo acym_translation('ACYM_CRON'); ?></div>
			<div class="cell grid-x grid-margin-x acym_vcenter">
				<label class="cell medium-2"><?php echo acym_translation('ACYM_AUTOMATED_TASKS').acym_info('ACYM_AUTOMATED_TASKS_DESC'); ?></label>
				<label class="cell shrink"><strong><?php echo acym_translation(empty($automaticSend) ? 'ACYM_DEACTIVATED' : 'ACYM_ACTIVATED'); ?></strong></label>
				<button data-acym-active="<?php echo $automaticSend; ?>" id="acym__configuration__button__cron" class="cell shrink button"><?php echo acym_translation(
                        empty($automaticSend) ? 'ACYM_ACTIVATE_IT' : 'ACYM_DEACTIVATE_IT'
                    ); ?></button>
			</div>
			<div class="cell grid-x grid-margin-x acym_vcenter">
				<label class="cell medium-2"><?php echo acym_translation('ACYM_CRON_LINK').acym_info('ACYM_CRON_LINK_DESC'); ?></label>
				<a class="cell shrink" target="_blank" href="<?php echo $cronUrl; ?>"><?php echo $cronUrl; ?></a>
			</div>
        <?php } ?>
	</div>
<?php }
if (!acym_level(ACYM_ESSENTIAL)) {
    include acym_getView('configuration', 'upgrade_license', true);
}
