<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="btn-group-yesno" data-ed-toggle="radio-buttons">
	<button type="button" class="btn btn-yes<?php echo $checked ? ' active' : '';?>" data-ed-toggle-value="1"><?php echo $onText; ?></button>
	<button type="button" class="btn btn-no<?php echo !$checked ? ' active' : '';?>" data-ed-toggle-value="0"><?php echo $offText; ?></button>
	<input type="hidden" id="<?php echo empty( $id ) ? $name : $id; ?>" name="<?php echo $name ;?>" value="<?php echo $checked ? '1' : '0'; ?>" <?php echo $attributes; ?> />
</div>
