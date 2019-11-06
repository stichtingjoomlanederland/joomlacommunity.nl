<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="arrow"></div>
<div class="popbox--user-wrap">
	<div class="t-lg-p--lg">
		<div class="o-flag">
			<div class="o-flag__image t-lg-pr--lg">
				<div class="o-avatar-status<?php echo ($user->isOnline()) ? ' is-online': ' is-offline'; ?>">
					<?php if ($this->config->get('layout_user_online')) { ?>
						<div class="o-avatar-status__indicator"></div>
					<?php } ?>
					<a class="o-avatar o-avatar--lg <?php echo ED::themes()->renderAvatarClass($user); ?>" href="<?php echo $user->getPermalink();?>">
						<?php if ($this->config->get('layout_avatar')) { ?>
							<img src="<?php echo $user->getAvatar();?>" alt="<?php echo $this->escape($user->getName());?>" />
						<?php } else { ?>
							<?php echo $user->getNameInitial()->text;?>
						<?php } ?>
					</a>
				</div>
			</div>
			<div class="o-flag__body o-flag--top">
				<?php echo $this->html('user.username', $user, array('lgMarginBottom' => true)); ?>
				<div class="ed-user-rank t-lg-mb--sm"><?php echo $this->escape(ED::getUserRanks($user->id)); ?></div>

				<div class="ed-rank-bar">
					<div class="ed-rank-bar__progress" style="width: <?php echo $this->escape(ED::getUserRankScore($user->id)); ?>%"></div>
				</div>
			</div>
		</div>
	</div>

	<div class="popbox--user-stats">
		<div class="o-col popbox--user-stats__col">
			<?php echo $this->getNouns('COM_EASYDISCUSS_QUESTION_COUNT' , $user->getTotalQuestions() , true ); ?>
		</div>
		<div class="o-col popbox--user-stats__col">
			<?php echo $this->getNouns('COM_EASYDISCUSS_REPLY_COUNT' , $user->getTotalReplies() , true ); ?>
		</div>
	</div>
	<div class="popbox--user-ft">
		<?php echo $this->html('user.pm', $user->id, 'popbox'); ?>
	</div>

</div>
