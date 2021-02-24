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
<div class="o-popbox-content">
	<div class="o-popbox-content__bd">
		<div class="t-p--md l-stack">
			<div class="o-title t-text--center"><?php echo $title; ?></div>
			<div class="ed-<?php echo $type; ?><?php echo !$users ? ' is-empty' : ''; ?>">
				<div class="l-cluster">
					<div>
						<?php foreach($users as $user) { ?>
							<?php echo $this->html('user.avatar', $user, ['status' => true, 'size' => 'md', 'popbox' => false]); ?>
						<?php } ?>
					</div>
				</div>
				<div class="o-empty o-empty--height-no">
					<div class="o-empty__text t-font-size--01"><?php echo $emptyMessage; ?></div>
				</div>
			</div>
		</div>
	</div>
</div>
