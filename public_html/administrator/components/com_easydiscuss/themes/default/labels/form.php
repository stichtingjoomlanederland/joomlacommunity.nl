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
	<div class="tab-content">
		<div id="general" class="tab-pane active in">
			<div class="row">
				<div class="col-md-6">
					<div class="panel">
						<?php echo $this->html('panel.head', 'COM_ED_POST_LABEL_TAB_GENERAL'); ?>

						<div class="panel-body">
							<div class="o-form-horizontal">
								<?php echo $this->html('forms.textbox', 'title', 'COM_ED_POST_LABEL_TITLE', $this->html('string.escape', $label->title),
									array('placeholder' => 'COM_ED_POST_LABEL_TITLE_PLACEHOLDER')
								); ?>

								<?php echo $this->html('forms.colorpicker', 'colour', 'COM_ED_POST_LABEL_COLOUR', $label->colour, $label->colour); ?>

								<?php echo $this->html('forms.toggle', 'published', 'COM_ED_POST_LABEL_PUBLISHED', $label->published); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo $this->html('form.action', 'labels', 'labels'); ?>
	<input type="hidden" name="id" value="<?php echo $label->id ?>" />

</form>
