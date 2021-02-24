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
<div class="t-flex-shrink--0 t-pl--xs">
	<div class="l-cluster l-spaces--2xs">
		<div>
			<?php foreach ($badges as $badge) { ?>
			<a href="<?php echo $badge->getPermalink();?>" class="o-avatar o-avatar--xs"
				data-ed-popbox="ajax://site/views/popbox/badge"
				data-ed-popbox-position="bottom-left"
				data-ed-popbox-toggle="hover"
				data-ed-popbox-offset="4"
				data-ed-popbox-type="ed-avatar"
				data-ed-popbox-component="o-popbox--user"
				data-ed-popbox-cache="1"
				data-args-id="<?php echo $badge->id; ?>"
			>
				<img src="<?php echo $badge->getAvatar();?>" alt="<?php echo $this->html('string.escape', $badge->title);?>" width="24" />
			</a>
			<?php } ?>
		</div>
	</div>
</div>