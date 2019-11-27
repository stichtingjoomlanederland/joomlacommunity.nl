<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.5.2
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA SAS - All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<form id="acym_form" action="<?php echo acym_completeLink(acym_getVar('cmd', 'ctrl')); ?>" method="post" name="acyForm">
	<div id="acym__list__subscribers" class="acym__content">
        <?php
        $workflow = acym_get('helper.workflow');
        echo $workflow->display($this->steps, $this->step);
        $entityHelper = acym_get('helper.entitySelect');
        echo $entityHelper->entitySelect(
            'user',
            [
                'join' => 'join_list-'.$data['listInformation']->id,
            ],
            [
                0 => 'email',
                1 => 'id',
                'join' => 'userlist.user_id',
            ]
        );
        ?>

		<div class="cell grid-x">
			<div class="cell medium-shrink medium-margin-bottom-0 margin-bottom-1"><?php echo acym_backToListing("lists"); ?></div>
			<div class="cell medium-auto grid-x text-right">
				<div class="cell medium-auto"></div>
				<button data-task="saveSubscribersExit" data-step="listing" type="submit" class="cell medium-shrink button medium-margin-bottom-0 margin-right-1 acy_button_submit">
                    <?php echo acym_translation('ACYM_SAVE_EXIT'); ?>
				</button>
			</div>
		</div>
	</div>
	<input type="hidden" name="id" value="<?php echo acym_escape($data['listInformation']->id); ?>">
	<input type="hidden" name="userid" id="id_user">
    <?php acym_formOptions(true, 'edit', 'subscribers'); ?>
</form>

