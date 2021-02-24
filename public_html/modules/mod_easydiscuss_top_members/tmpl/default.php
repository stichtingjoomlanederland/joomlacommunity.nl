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
<div id="ed" class="ed-mod ed-mod--leaderboard <?php echo $lib->getModuleWrapperClass();?>">
	<div class="ed-mod-card">
		<div class="ed-mod-card__body">
			<?php foreach($users as $user) { ?>
				<div class="o-card t-bg--100">
					<div class="o-card__body">
						<div class="o-media o-media--top">
							<?php if ($params->get('showavatar')) { ?>
							<div class="o-media__image">
								<div>
									<?php echo $lib->html('user.avatar', $user, ['size' => 'ml', 'status' => true]); ?>
								</div>
							</div>
							<?php } ?>

							<div class="o-media__body t-text--truncate l-stack l-spaces--xs">
								<?php echo $lib->html('user.username', $user); ?>

								<?php if ($params->get('showpost') || $params->get('showanswered') || $params->get('showlastonline')) { ?>
								<div class="o-meta l-cluster l-spaces--xs t-font-size--01">
									<div class="">
										<?php if ($params->get('showpost')) { ?>
										<div class="m-list__item">
											 <div class="">
											 	<?php echo JText::sprintf('MOD_EASYDISCUSS_TOP_MEMBERS_POSTS', $user->getNumTopicPosted()); ?>
											 </div>
										</div>
										<?php } ?>

										<?php if ($params->get('showanswered')) { ?>
										<div class="m-list__item">
											 <div class="">
											 	<?php echo JText::sprintf('MOD_EASYDISCUSS_TOP_MEMBERS_REPLIES', $user->getTotalReplies(array('ignoreCategoryACL' => true))); ?>
										 	</div>
										</div>
										<?php } ?>

										<?php if ($params->get('showlastonline')) { ?>
										<div class="m-list__item">
											 <div class="">
											 	<?php echo $user->getLastOnline(true); ?>
										 	</div>
										</div>
										<?php } ?>
									</div>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>