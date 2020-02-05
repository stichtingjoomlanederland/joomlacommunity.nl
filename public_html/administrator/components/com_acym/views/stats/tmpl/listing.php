<?php
defined('_JEXEC') or die('Restricted access');
?><form id="acym_form" action="<?php echo acym_completeLink(acym_getVar('cmd', 'ctrl')); ?>" method="post" name="acyForm">
	<div class="acym__content acym__content__tab" id="acym_stats">
		<div class="cell grid-x acym_vcenter" id="acym_stats__select">
			<h2 class="cell medium-6 text-right acym_stats__title__choose"><?php echo acym_translation('ACYM_SELECT_A_MAIL'); ?></h2>
			<div class="cell large-2 medium-4 margin-left-1"><?php echo $data['mail_filter']; ?></div>
		</div>
        <?php

        $data['tab']->startTab(acym_translation('ACYM_GLOBAL_STATISTICS'));
        include dirname(__FILE__).DS.'global_stats.php';
        $data['tab']->endTab();

        $data['tab']->startTab(acym_translation('ACYM_DETAILED_STATS'));
        if (acym_level(1)) {
            include dirname(__FILE__).DS.'detailed_stats.php';
        }

        if (!acym_level(1)) {
            $data['version'] = 'essential';
            include ACYM_VIEW.'dashboard'.DS.'tmpl'.DS.'upgrade.php';
        }
        $data['tab']->endTab();

        if (!empty($data['selectedMailid'])) {
            $data['tab']->startTab(acym_translation('ACYM_CLICK_MAP'));
            if (acym_level(1)) {
                include dirname(__FILE__).DS.'click_map.php';
            }

            if (!acym_level(1)) {
                $data['version'] = 'essential';
                include ACYM_VIEW.'dashboard'.DS.'tmpl'.DS.'upgrade.php';
            }
            $data['tab']->endTab();
        }

        $data['tab']->display('stats');
        ?>
	</div>
</form>

