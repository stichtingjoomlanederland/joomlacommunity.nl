<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form name="installation" method="post" data-installation-form>
	<div class="mb-1">
		<?php echo t('INSTALLATION_INSTALLING_DESC');?>
	</div>

	<div class="si-alert si-alert--success d-none mt-4 mb-4" role="alert" data-installation-completed>
		<?php echo t('INSTALLATION_INSTALLING_COMPLETED'); ?>
	</div>

	<div class="si-container-overflow mt-4 mb-4 pr-3" style="height: 35vh;max-height: 35vh;" data-install-progress>
		<ol class="si-install-logs mt-4" data-progress-logs>
			<li class="si-install-logs__item" data-progress-extract>
				<div class="si-install-logs__title">
					<?php echo t('INSTALLATION_INSTALLING_EXTRACTING_FILES');?>
				</div>

				<?php include(__DIR__ . '/log.state.php'); ?>
			</li>

			<?php include(dirname(__FILE__) . '/installing.steps.php'); ?>
		</ol>
	</div>


	<input type="hidden" name="option" value="<?php echo SI_IDENTIFIER;?>" />
	<input type="hidden" name="active" value="<?php echo $active; ?>" />
	<input type="hidden" name="source" data-source />
</form>

<script>
$(document).ready(function(){
	ed.installation.extract();
});
</script>