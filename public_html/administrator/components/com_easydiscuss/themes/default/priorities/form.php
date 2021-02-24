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
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_POST_TYPES_TAB_GENERAL'); ?>

				<div class="panel-body">
					<div class="o-form-horizontal">
						<?php echo $this->html('forms.textbox', 'title', 'COM_EASYDISCUSS_PRIORITY_TITLE', $priority->title, array('placeholder' => 'COM_EASYDISCUSS_PRIORITY_TITLE_PLACEHOLDER')); ?>

						<?php echo $this->html('forms.colorpicker', 'color', 'COM_EASYDISCUSS_PRIORITY_COLOR', $priority->color, $priority->color); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo $this->html('form.action', 'priorities', '', ''); ?>
	<input type="hidden" name="id" value="<?php echo $priority->id ?>" />

</form>
