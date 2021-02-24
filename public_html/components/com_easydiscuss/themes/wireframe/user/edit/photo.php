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
<div class="tab-item user-photo">

	<div class="ed-form-panel__bd">
		<?php if($this->config->get('layout_avatarIntegration') == 'gravatar') { ?>
		<div class="o-flag">
			<div class="o-flag__image">
				<div class="o-avatar o-avatar--xl t-lg-mr--lg" >
				    <img src="<?php echo $profile->getAvatar(false); ?>" data-ed-avatar/>
				    <div class="ed-avatar-crop-preview" data-ed-avatar-preview></div>
				</div>		
			</div>
		</div>		

		<?php } else { ?>
		<?php } ?>
	</div>
</div>
