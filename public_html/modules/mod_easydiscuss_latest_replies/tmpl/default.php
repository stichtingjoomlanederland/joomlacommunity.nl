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
<div id="ed" class="ed-mod ed-mod--latest-replies <?php echo $lib->getModuleWrapperClass();?>">
	
	<div class="ed-mod-card">
		<div class="ed-mod-card__body">
			<?php foreach($replies as $reply) { ?>
			<div class="o-card t-bg--100">
				<div class="o-card__body l-stack">
					<?php echo $lib->html('post.title', $reply, ['customClass' => 'o-title si-link t-d--inline-block l-spaces--sm']); ?>
					<div class="o-meta t-flex-grow--1 l-cluster">
						<div class="">
							<?php if ($params->get('show_replies_avatar')) { ?>
							<div class="t-min-width--0 t-d--flex t-align-items--c" data-user-avatar="" data-isanonymous="0">
								<?php if ($reply->isAnonymous()) { ?>
									<?php echo $lib->html('user.anonymous', $reply->user, []); ?>
									&nbsp;
									<?php echo $lib->html('user.username', $reply->user, ['posterName' => $reply->user->getName(), 'isAnonymous' => true, 'canViewAnonymousUsername' => $reply->canAccessAnonymousPost()]); ?>
								<?php } else { ?>
									<?php echo $lib->html('user.avatar', $reply->user, []); ?>
									&nbsp;
									<?php echo $lib->html('user.username', $reply->user, []);?>
								<?php } ?>
							</div>
							<?php } ?>

							<?php if ($params->get('show_replies_date')) {?>
							<div class="">
								<?php echo ED::date()->toLapsed($reply->created); ?>
							</div>
							<?php } ?>

							<div class="">
								<?php echo $reply->content;?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>