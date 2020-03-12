<?php
defined('_JEXEC') or die('Restricted access');
?><form id="acym_form" action="<?php echo acym_completeLink(acym_getVar('cmd', 'ctrl')); ?>" method="post" name="acyForm">
	<div id="acym__lists" class="acym__content">
        <?php if (empty($data['lists']) && empty($data['search']) && empty($data['tag']) && empty($data['status'])) {
            include acym_getView('lists', 'listing_empty', true);
        } else {
            include acym_getView('frontlists', 'listing_header');
            include acym_getView('lists', 'listing_listing', true);
        } ?>
	</div>
    <?php acym_formOptions(); ?>
</form>

