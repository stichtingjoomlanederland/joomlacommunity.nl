<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div id="config-document">
	<div id="page-main" class="tab">
		<table class="noshow" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td><?php echo $this->loadTemplate('kunena');?></td>
			</tr>
		</table>
	</div>
	<div id="page-jomsocialgroups" class="tab">
		<table class="noshow" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td><?php echo $this->loadTemplate('jomsocialgroups');?></td>
			</tr>
		</table>
	</div>
</div>
<div class="clr"></div>
<input type="hidden" name="active" id="active" value="" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="controller" value="settings" />
</form>
