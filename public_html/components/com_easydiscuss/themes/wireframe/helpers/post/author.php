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
<div class="t-min-width--0 t-d--flex t-align-items--c" data-user-avatar data-isAnonymous="<?php echo $post->isAnonymous() ? 1 : 0; ?>">
	<?php if ($post->isAnonymous()) { ?>
		<span class="o-avatar o-avatar--xs t-lg-mr--sm">
			<img src="<?php echo ED::getDefaultAvatar();?>" width="24" height="24" />
		</span>
		&nbsp;

		<?php if ($post->canAccessAnonymousPost()) { ?>
			<?php echo $this->html('user.username', $post->getOwner(), array('isAnonymous' => true, 'canViewAnonymousUsername' => $post->canAccessAnonymousPost(), 'posterName' => $post->poster_name)); ?>
		<?php } else { ?>
			<span class="ed-user-name t-text--truncate"><?php echo JText::_('COM_EASYDISCUSS_ANONYMOUS_USER');?></span>
		<?php } ?>
	<?php } ?>

	<?php if (!$post->isAnonymous()) { ?>
	<a href="<?php echo $user->getPermalink();?>" class="ed-user-name si-link t-text--truncate t-d--flex t-align-items--c"
		data-ed-popbox="ajax://site/views/popbox/user"
		data-ed-popbox-position="bottom-left"
		data-ed-popbox-toggle="hover"
		data-ed-popbox-offset="4"
		data-ed-popbox-type="ed-avatar"
		data-ed-popbox-component="o-popbox--user"
		data-ed-popbox-cache="1"
		data-args-id="<?php echo $user->id; ?>"
	>
		<?php echo $this->html('user.avatar', $post->getOwner(), [], $post->isAnonymous(), true); ?>
		&nbsp;
		<?php echo $this->html('user.username', $post->getOwner(), [
			'posterName' => $post->poster_name, 
			'popbox' => false,
			'hyperlink' => false
		]); ?>
	</a>
	<?php } ?>
</div>