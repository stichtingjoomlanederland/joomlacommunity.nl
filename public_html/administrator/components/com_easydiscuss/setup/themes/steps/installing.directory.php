<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Get the file to install
$package = $input->get('package', '');
?>
<script type="text/javascript">
$(document).ready(function(){

	<?php if ($reinstall) { ?>
		ed.ajaxUrl = "<?php echo JURI::root();?>administrator/index.php?option=com_easydiscuss&ajax=1&reinstall=1";
	<?php } ?>

	<?php if ($update) { ?>
		ed.ajaxUrl = "<?php echo JURI::root();?>administrator/index.php?option=com_easydiscuss&ajax=1&update=1";
	<?php } ?>

	// Immediately proceed with installation
	ed.installation.extract('<?php echo $package;?>');
});

</script>
<form name="installation" method="post" data-installation-form>
	<p class="section-desc">
		<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INSTALLING_DESC');?>
	</p>

	<div class="alert alert-success" data-installation-completed style="display: none;">
		<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INSTALLING_COMPLETED'); ?>
	</div>

	<div data-install-progress>
		<div class="install-progress">
			<div class="row-table">
				<div class="col-cell">
					<div data-progress-active-message=""><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INSTALLING_EXTRACTING_FILES');?></div>
				</div>
				<div class="col-cell cell-result text-right">
					<div class="progress-result" data-progress-bar-result="">0%</div>
				</div>
			</div>

			<div class="progress">
				<div class="progress-bar progress-bar-info progress-bar-striped" data-progress-bar="" style="width: 1%"></div>
			</div>
		</div>


		<ol class="install-logs list-reset" data-progress-logs="">
			<li class="active" data-progress-extract>
				<b class="split__title"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INSTALLING_EXTRACTING_FILES');?></b>
				<span class="progress-state text-info"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INSTALLING_EXTRACTING');?></span>
				<div class="notes"></div>
			</li>

			<?php include(dirname(__FILE__) . '/installing.steps.php'); ?>
		</ol>
	</div>

	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="active" value="<?php echo $active; ?>" />
	<input type="hidden" name="source" data-source />

	<?php if ($reinstall) { ?>
	<input type="hidden" name="reinstall" value="1" />
	<?php } ?>

	<?php if ($update) { ?>
	<input type="hidden" name="update" value="1" />
	<?php } ?>

</form>