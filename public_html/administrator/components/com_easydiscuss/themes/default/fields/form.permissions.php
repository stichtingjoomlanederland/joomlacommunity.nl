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
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_FIELDS_PERMISSIONS_VIEW'); ?>

			<div class="panel-body">
				<?php echo $this->html('form.usergroups', 'acl_group_view', $field->getAssignedGroups('view'));?>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_FIELDS_PERMISSIONS_POST'); ?>

			<div class="panel-body">
				<?php echo $this->html('form.usergroups', 'acl_group_input', $field->getAssignedGroups('input'));?>
			</div>
		</div>
	</div>
</div>