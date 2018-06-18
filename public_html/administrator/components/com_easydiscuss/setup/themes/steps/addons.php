<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$source = $input->get('source', '', 'default');
?>
<script type="text/javascript">
$(document).ready(function(){
	ed.ajaxUrl	= "<?php echo JURI::root();?>administrator/index.php?option=<?php echo ED_IDENTIFIER;?>&ajax=1";

	// Immediately proceed with synchronization
	ed.options.path = '<?php echo addslashes($source);?>';
	ed.addons.retrieveList();
});
</script>

<form name="installation" data-installation-form>

	<p class="section-desc"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_ADDONS_DESC'); ?></p>

	<div data-addons-retrieving style="margin-top:30px;"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_ADDONS_RETRIEVING_LIST');?></div>

	<div class="hide" style="margin-top: 30px;" data-addons-progress>
		<div class="install-progress" style="border-bottom: 1px #ddd;">
			<div class="row-table">
				<div class="col-cell">
					<div class="hide" data-progress-complete-message><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_MODULE_AND_PLUGINS_COMPLETED');?></div>
					<div data-progress-active-message><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INSTALLING_MODULES');?></div>
				</div>
				<div class="col-cell cell-result text-right">
					<div class="progress-result" data-progress-bar-result="">0%</div>
				</div>
			</div>

			<div class="progress">
				<div class="progress-bar progress-bar-info progress-bar-striped" data-progress-bar="" style="width: 1%;"></div>
			</div>
		</div>
	</div>

	<div class="hide" style="margin-top: 30px;" data-sync-progress>

		<div class="install-progress" style="border-bottom: 1px #ddd;">

			<div class="row-table">
				<div class="col-cell">
					<div class="hide" data-sync-progress-complete-message><?php echo JText::_('COM_ED_INSTALLATION_MAINTENANCE_EXEC_SCRIPTS_SUCCESS');?></div>
					<div data-sync-progress-active-message><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_MAINTENANCE_EXEC_SCRIPTS');?></div>
				</div>
				<div class="col-cell cell-result text-right">
					<div class="progress-result" data-sync-progress-bar-result>0%</div>
				</div>
			</div>

			<div class="progress">
				<div class="progress-bar progress-bar-info progress-bar-striped" data-sync-progress-bar style="width: 0%;"></div>
			</div>

			<ol class="install-logs list-reset" data-progress-logs>
				<li class="pending" data-progress-execscript>
					<div class="notes">
						<ul style="list-unstyled" data-progress-execscript-items>
						</ul>
					</div>
				</li>
			</ol>
		</div>
	</div>

	<div data-addons-container></div>

	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="active" value="complete" />
</form>