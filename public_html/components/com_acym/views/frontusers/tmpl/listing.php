<?php
defined('_JEXEC') or die('Restricted access');
?><form id="acym_form" action="<?php echo acym_completeLink(acym_getVar('cmd', 'ctrl')); ?>" method="post" name="acyForm">
	<div id="acym__users" class="acym__content cell">
        <?php if (empty($data['allUsers']) && empty($data['search']) && empty($data['status'])) {
            include acym_getView('users', 'listing_empty', true);
        } else {
            include acym_getView('frontusers', 'listing_header');
            include acym_getView('users', 'listing_listing', true);
        } ?>
	</div>
    <?php acym_formOptions(); ?>
</form>
