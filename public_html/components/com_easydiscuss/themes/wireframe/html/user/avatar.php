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

$defaultName = isset($user->name) ? $user->name : '';
?>
<?php if ($renderAvatarImageOnly) { ?>

	<div class="o-avatar o-avatar--rounded o-avatar--<?php echo $size; ?> <?php echo ED::themes()->renderAvatarClass($user); ?>">
		<?php if (!$this->config->get('layout_text_avatar') || $this->config->get('layout_avatarIntegration') != 'default') { ?>
			<img src="<?php echo !$isAnonymous ? $user->getAvatar() : ED::getDefaultAvatar();?>" alt="<?php echo !$isAnonymous ? $this->escape($user->getName($defaultName)) : JText::_('COM_EASYDISCUSS_ANONYMOUS_USER');?>" />
		<?php } else { ?>
			<?php echo $textAvatarName;?>
		<?php } ?>
	</div>

<?php } else { ?>

	<div class="o-avatar-status<?php echo ($user->isOnline($useCache)) ? ' is-online': ' is-offline'; ?>">
		<?php if ($status && $this->config->get('layout_user_online')) { ?>
			<div class="o-avatar-status__indicator"></div>
		<?php } ?>

		<?php if ($hyperlink) { ?>
		<a href="<?php echo $userPermalink; ?>"
			class="
				o-avatar 
				o-avatar--<?php echo $size; ?> 
				<?php echo ED::themes()->renderAvatarClass($user); ?>
				o-avatar--rounded 
				<?php echo $customClasses;?>
			"

			<?php if ($popbox) { ?>
			data-ed-popbox="ajax://site/views/popbox/user"
			data-ed-popbox-position="bottom-left"
			data-ed-popbox-toggle="hover"
			data-ed-popbox-offset="4"
			data-ed-popbox-type="ed-avatar"
			data-ed-popbox-component="o-popbox--user"
			data-ed-popbox-cache="1"
			data-args-id="<?php echo $user->id; ?>"
			<?php } ?>
		>
		<?php } else { ?>
			<span class="
				o-avatar 
				o-avatar--<?php echo $size; ?> 
				<?php echo ED::themes()->renderAvatarClass($user); ?>
				o-avatar--rounded 
				<?php echo $customClasses;?>
			">
		<?php } ?>
			<?php if (!$this->config->get('layout_text_avatar') || $this->config->get('layout_avatarIntegration') != 'default') { ?>
				<img src="<?php echo !$isAnonymous ? $user->getAvatar() : ED::getDefaultAvatar();?>" 
					alt="<?php echo !$isAnonymous ? $this->escape($user->getName($defaultName)) : JText::_('COM_EASYDISCUSS_ANONYMOUS_USER');?>"
					<?php echo $easysocialPopbox ? ED::easysocial()->getPopbox($user->id) : '';?>
				/>
			<?php } else { ?>
				<?php echo $textAvatarName;?>
			<?php } ?>
		<?php if ($hyperlink) { ?>
		</a>
		<?php } else { ?>
			</span>
		<?php } ?>
	</div>

	<?php if ($this->config->get('main_ranking') && $rank) { ?>
		<div class="ed-rank-bar ed-rank-bar--max-width-no t-lg-mt--sm" data-original-title="<?php echo $this->escape(ED::ranks()->getRank($user->id)); ?>">
			<div class="ed-rank-bar__progress" style="width: <?php echo $this->escape(ED::ranks()->getScore($user->id)); ?>%"></div>
		</div>
	<?php } ?>

<?php } ?>