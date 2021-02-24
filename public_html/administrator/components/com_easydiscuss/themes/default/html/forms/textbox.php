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
<div class="o-form-group <?php echo $options->wrapperClass;?>" <?php echo $options->wrapperAttributes;?>>
	<div class="col-md-5 o-form-label">
		<?php echo $this->html('form.label', JText::_($title), JText::_($desc), $name); ?>
	</div>

	<div class="col-md-7">
		<div class="">
			<?php if ($options->size) { ?>
			<div class="row">
				<div class="col-sm-<?php echo $options->size;?>">
			<?php } ?>

				<?php if ($options->prefix || $options->postfix) { ?>
				<div class="o-input-group <?php echo $options->inputWrapperClass;?>">
				<?php }?>

					<?php if ($options->prefix) { ?>
					<span class="o-input-group-addon"><?php echo JText::_($options->prefix); ?></span>
					<?php } ?>

					<?php echo $this->html('form.textbox', $name, $value, $options->placeholder, $options->class, 
						array(
							'attr' => $options->attributes
						)
					); ?>

					<?php if ($options->postfix) { ?>
					<span class="o-input-group__text"><?php echo JText::_($options->postfix); ?></span>
					<?php } ?>

				<?php if ($options->prefix || $options->postfix) { ?>
				</div>
				<?php } ?>

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