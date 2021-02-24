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
<div class="l-cluster l-spaces--xs">
	<div>
		<?php if ($participants) { ?>
			<?php foreach ($participants as $user) { ?>
			<div>
				<?php echo $this->html('user.avatar', $user, [
				]); ?>
			</div>
			<?php } ?>
			
			<div>
				<div class="o-avatar-status is-online">
					<a href="" class="o-avatar o-avatar--sm o-avatar--rounded o-avatar--border--sm t-bg--100 t-text--center t-text--600">
						<i class="fas fa-ellipsis-h"></i>
					</a>
				</div>
			</div>

		<?php } else { ?>
			&mdash;
		<?php } ?>
	</div>
</div>