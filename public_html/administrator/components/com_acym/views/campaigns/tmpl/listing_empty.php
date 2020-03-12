<?php
defined('_JEXEC') or die('Restricted access');
?><div class="grid-x text-center">
	<h1 class="acym__listing__empty__title cell"><?php echo acym_translation('ACYM_YOU_DONT_HAVE_ANY_CAMPAIGN'); ?></h1>
	<h1 class="acym__listing__empty__subtitle cell"><?php echo acym_translation('ACYM_CREATE_ONE_NOW'); ?></h1>
	<div class="medium-4"></div>
	<div class="medium-4 cell">
		<button data-task="edit" data-step="chooseTemplate" type="button" class="button expanded acy_button_submit"><?php echo acym_translation('ACYM_CREATE_NEW_CAMPAIGN'); ?></button>
	</div>
	<div class="medium-4"></div>
</div>
