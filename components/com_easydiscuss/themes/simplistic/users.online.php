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
<div class="discuss-member-view">
	<div class="discuss-member-view-hd">
		<span class="discuss-widget-title"><?php echo JText::_( 'COM_EASYDISCUSS_VIEWERS_ON_PAGE' );?></span>
	</div>
	<div class="discuss-member-view-bd">
		<ul class="unstyled">
			<?php if(!empty($users)) { ?>
			<?php foreach( $users as $user ){ ?>
			<li>
				<a href="<?php echo $user->getLink();?>" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo $this->escape( $user->getName() );?>">
					<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
					<span class="discuss-avatar avatar-circle">
						<img src="<?php echo $user->getAvatar();?>" alt="<?php echo $this->escape( $user->getName() );?>"<?php echo DiscussHelper::getHelper( 'EasySocial' )->getPopbox( $user->id );?> />
					</span>
					<?php } else { ?>
						<?php echo $this->escape( $user->getName() );?>
					<?php } ?>
				</a>
			</li>
			<?php } ?>
			<?php } ?>
		</ul>
	</div>
</div>
