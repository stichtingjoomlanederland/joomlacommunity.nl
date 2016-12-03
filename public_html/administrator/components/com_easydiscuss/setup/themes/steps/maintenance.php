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
?>
<script type="text/javascript">
$(document).ready(function(){
	ed.ajaxUrl = "<?php echo JURI::root();?>administrator/index.php?option=com_easydiscuss&ajax=1&update=1";

	// Immediately proceed with synchronization
	ed.maintenance.init();
});
</script>
<form name="installation" data-installation-form>

	<p class="section-desc"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_MAINTENANCE_DESC'); ?></p>

	<div data-sync-progress>
		<ol class="install-logs list-reset" data-progress-logs="">
			<li class="pending" data-progress-execscript>
				<b class="split__title"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_MAINTENANCE_EXEC_SCRIPTS');?></b>
				<span class="progress-state text-info"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_MAINTENANCE_EXECUTING');?></span>
				<div class="notes">
					<ul style="list-unstyled" data-progress-execscript-items>
					</ul>
				</div>
			</li>
		</ol>
	</div>

	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="active" value="complete" />
</form>
