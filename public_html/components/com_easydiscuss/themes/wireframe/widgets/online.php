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
<div class="ed-post-who-view__hd">
	<?php echo JText::_( 'COM_EASYDISCUSS_VIEWERS_ON_PAGE' );?>
</div>
<div class="ed-post-who-view__ft t-bdt-no">
    <div class="ed-avatar-list">
		<?php if (!empty($users)) { ?>
			<?php foreach ($users as $user) { ?>
				<?php echo $this->html('user.avatar', $user, array('status' => true)); ?> 
			<?php } ?>
		<?php } ?>
    </div>
</div>
