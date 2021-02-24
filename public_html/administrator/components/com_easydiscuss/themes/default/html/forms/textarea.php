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
<div class="o-form-group">
	<div class="col-md-5 o-form-label">
		<?php echo $this->html('form.label', JText::_($title), JText::_($desc), $name); ?>
	</div>

	<div class="col-md-7">
		<div class="">
			<?php if ($options->size) { ?>
			<div class="row">
				<div class="col-sm-<?php echo $options->size;?>">
			<?php } ?>

			<?php echo $this->html('form.textarea', $name, $value, $options->rows, $options->attributes); ?>

			<?php if ($options->size) { ?>
				</div>
			</div>
			<?php } ?>

			<?php if ($options->instructions) { ?>
			<div class="t-mt--sm">
				<?php echo JText::_($options->instructions);?>
			</div>
			<?php } ?>
		</div>
	</div>
</div>