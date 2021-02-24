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

// Fixed for the previous version
$colorCodes = array(
	'success' => '#39b54a',
	'warning' => '#c77c11',
	'danger' => '#d9534f',
	'info' => '#5bc0de',
	'default' => '#777777'
);

if (array_key_exists($role->colorcode, $colorCodes)) {
	$role->colorcode = $colorCodes[$role->colorcode];
}
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_ROLE_FORM_GENERAL'); ?>

				<div class="panel-body">
					<div class="o-form-horizontal">
						<?php echo $this->html('forms.textbox', 'title', 'COM_EASYDISCUSS_ROLE_TITLE', $role->title); ?>
						<?php echo $this->html('forms.dropdown', 'usergroup_id', 'COM_EASYDISCUSS_ROLE_USERGROUP', $role->usergroup_id, $groups); ?>
						<?php echo $this->html('forms.colorpicker', 'colorcode', 'COM_EASYDISCUSS_ROLE_LABEL_COLOUR', $role->colorcode, '#ffffff'); ?>
						<?php echo $this->html('forms.toggle', 'published', 'COM_EASYDISCUSS_ROLE_PUBLISHED', $role->published); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo $this->html('form.action', 'roles', 'save'); ?>
	
	<input type="hidden" name="role_id" value="<?php echo $role->id;?>" />
	<input type="hidden" name="savenew" id="savenew" value="0" />
</form>