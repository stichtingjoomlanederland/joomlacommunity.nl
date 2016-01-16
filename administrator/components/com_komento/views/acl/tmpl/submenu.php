<?php
/**
* @package      Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="adminform-head">
	<form action="index.php?option=com_komento&view=acl" method="post" name="adminForm" id="aclForm">
		<table class="adminform">
			<tr>
				<td width="50%">
					<label for="component"><?php echo JText::_( 'COM_KOMENTO_ACL_SELECT_COMPONENT' ); ?> :</label>
					<?php echo $this->components; ?>
				</td>
				<td width="50%">
					<!-- todo: joomla group / assigned -->
				</td>
			</tr>
		</table>

		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>
