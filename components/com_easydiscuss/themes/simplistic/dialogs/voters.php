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
<div class="discuss-voters">
<?php if( $users || $guests ){ ?>
	<?php if( $users ){ ?>
		<?php foreach( $users as $user ){ ?>
		<div class="discuss-voter">
			<a href="<?php echo $user->getLink();?>">
				<div class="pull-left">
					<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
					<img src="<?php echo $user->getAvatar();?>" width="40"/>
					<?php } ?>
				</div>
				<div class="table">
					<span class="table-cell"><?php echo $user->getName();?></span>
				</div>
			</a>
		</div>
		<?php } ?>
	<?php } ?>

	<?php if( $guests ){ ?>
		<div class="discuss-voters-guest">
			<?php echo JText::sprintf( 'COM_EASYDISCUSS_GUESTS_VOTERS' , $guests ); ?>
		</div>
	<?php } ?>

<?php } else { ?>
<div class="empty discuss-voters-guest">
	<?php echo JText::_( 'COM_EASYDISCUSS_NO_VOTERS_YET' ); ?>
</div>
<?php } ?>
</div>
