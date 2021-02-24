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
<div class="row" data-user-browser>
	<div class="col-lg-12">
		<div class="o-input-group">
			<input type="text" id="<?php echo $id;?>-placeholder" class="o-form-control" value="<?php echo $authorName;?>" disabled="disabled" />
			
				<button class="o-btn o-btn--default-o t-text--danger" type="button" data-remove>
					<i class="fa fa-times"></i>
				</button>
				<button class="o-btn o-btn--default-o" type="button" data-browse>
					<i class="fa fa-users"></i>&nbsp; <?php echo JText::_('COM_ED_BROWSE');?>
				</button>
			
		</div>
		<input type="hidden" name="<?php echo $name;?>" id="<?php echo $id;?>" value="<?php echo $value;?>" <?php echo $attributes; ?> />
	</div>
</div>