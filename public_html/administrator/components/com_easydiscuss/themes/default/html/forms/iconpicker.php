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
		<?php echo $this->html('form.label', JText::_($title), JText::_($desc)); ?>
	</div>

	<div class="col-md-7">
		<div class="o-btn-group dropdown">
			<button type="button" class="o-btn o-btn--default-o iconpicker-component">
				<i class="<?php echo $value !== '' ? $value : $defaultIcon; ?>"></i>
			</button>
			<button type="button" data-icon-selection class="icp icp-dd o-btn o-btn--default-o dropdown-toggle"
					data-selected="<?php echo $value; ?>" data-toggle="dropdown"></button>
			
			<div class="dropdown-menu"></div>
		</div>

		<button type="button" class="o-btn o-btn--default-o t-text--danger <?php echo $value == '' ? 't-hidden' : '';?>" data-icon-remove>
			<i class="fa fa-times"></i>
		</button>

		<input type="hidden" id="<?php echo $name;?>" name="<?php echo $name;?>" data-icon-input value="<?php echo $value;?>" />
	</div>
</div>