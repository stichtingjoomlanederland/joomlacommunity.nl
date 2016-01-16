<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<li class="voter-<?php echo $voter->user->id;?>">
	<a href="<?php echo $voter->getLink();?>" data-original-title="<?php echo $this->escape( $voter->getName() ); ?>" data-placement="top" rel="ed-tooltip" href="<?php echo DiscussRouter::getUserRoute( $voter->user->id );?>" class="discuss-avatar avatar-mini">
		<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
			<img src="<?php echo $voter->getAvatar();?>" alt="<?php echo $this->escape( $voter->getName() );?>"<?php echo DiscussHelper::getHelper( 'EasySocial' )->getPopbox( $voter->id );?> />
		<?php } else { ?>
			<?php echo $this->escape( $voter->getName() ); ?>
		<?php } ?>
	</a>
</li>
