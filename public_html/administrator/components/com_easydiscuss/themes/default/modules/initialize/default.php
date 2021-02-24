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
<form name="adminForm" id="adminForm" method="post" data-ed-table-grid>
	<div class="panel languages-loader" data-languages-wrapper>
		<div class="panel-body text-center">

			<?php echo JText::_('Initializing modules list from our server');?>

			<div class="alert alert-danger hide" data-languages-error style="margin-top:50px;"></div>
		</div>
	</div>
</form>