<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access'); ?>

<div class="<?php echo RSCommentsAdapterGrid::row(); ?>">
	<div class="<?php echo RSCommentsAdapterGrid::column(12); ?>">
		<fieldset class="options-form">
			<?php foreach ($this->form->getFieldset('bbcode') as $field) { ?>
			<?php echo $field->renderField(); ?>
			<?php } ?>
		</fieldset>
	</div>
</div>