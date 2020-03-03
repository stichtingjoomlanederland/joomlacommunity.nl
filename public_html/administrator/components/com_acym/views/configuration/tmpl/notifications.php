<?php
defined('_JEXEC') or die('Restricted access');
?><div class="acym__content acym_area padding-vertical-1 padding-horizontal-2 margin-bottom-2">
	<div class="acym_area_title"><?php echo acym_translation('ACYM_NOTIFICATIONS'); ?></div>
	<div class="grid-x grid-margin-x">
        <?php
        foreach ($data['notifications'] as $identifier => $notification) {
            ?>
			<div class="cell xxlarge-4 large-5 medium-6">
				<label for="acym__config__<?= acym_escape($identifier); ?>">
                    <?= acym_escape(acym_translation($notification['label'])); ?>
				</label>
			</div>
			<div class="cell xlarge-4 large-5 medium-6">
                <?php
                $saved = explode(',', $this->config->get($identifier));
                $selected = [];
                foreach ($saved as $i => $value) {
                    if (acym_isValidEmail($value)) {
                        $selected[$value] = $value;
                    }
                }

                echo acym_selectMultiple(
                    $selected,
                    'config['.acym_escape($identifier).']',
                    $selected,
                    [
                        'id' => 'acym__config__'.acym_escape($identifier),
                        'class' => 'acym__multiselect__email',
                    ]
                );
                ?>
			</div>
			<div class="cell large-2 medium-4 shrink">
				<a class="button" href="<?= acym_completeLink('mails&task=edit&notification='.$identifier.'&type_editor=acyEditor'); ?>">
                    <?= acym_translation('ACYM_EDIT_EMAIL'); ?>
				</a>
			</div>
			<div class="cell xxlarge-2 xlarge-1 hide-for-large-only medium-8 hide-for-small-only"></div>
            <?php
        }
        ?>
	</div>
</div>

