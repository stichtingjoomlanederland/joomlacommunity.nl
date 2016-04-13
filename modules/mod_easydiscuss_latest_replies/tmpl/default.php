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

$avatarSize			= $params->get( 'replies_avatar_size', '' );
$useAvatarResize	= ( empty( $avatarSize ) ) ? '' : ' style="width: ' . $avatarSize . 'px;" ';
?>
<div class="discuss-mod latest-replies<?php echo $params->get( 'moduleclass_sfx' ) ?>">
<?php if( $replies ){ ?>
	<div class="list-item">

			<?php for($i = 0 ; $i < count( $replies ); $i++) {
					$reply  = $replies[$i];

			?>
			<div class="item">
				<div class="item-user">
					<?php if( $params->get( 'show_replies_avatar' , 1 ) ){ ?>
					<a class="item-avatar float-l" href="<?php echo $reply->profile->getLink(); ?>">
						<img class="avatar" src="<?php echo $reply->profile->getAvatar(); ?>" <?php echo $useAvatarResize; ?> title="<?php echo JText::sprintf( 'MOD_LATESTREPLIES_REPLIED_BY', $reply->profile->getName() ); ?>" />
					</a>
					<?php } ?>


				</div>

				<div class="item-story">
					<div class="item-question">
						<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id='.$reply->parent_id ); ?>">
						<?php echo DiscussStringHelper::escape($reply->title); ?>
						</a>
					</div>
					<div class="small"><?php echo JText::sprintf( 'MOD_LATESTREPLIES_REPLIED_BY' , '<a href="' . $reply->profile->getLink() . '">' . $reply->profile->getName() . '</a>' );?></div>
					<div class="item-answer small">
						<?php
						$content	= $reply->content;
						$content	= JString::substr( strip_tags( $reply->content ) , 0 , $params->get( 'maxlength' , 200 ) );
						echo ED::parser()->filter( $content );

						?>
					</div>

					<div class="item-info push-top small">
						<span>
							<img src="<?php echo JURI::root(); ?>modules/mod_easydiscuss_latest_replies/images/clock.png" width="16" height="16">
							<?php echo DiscussDateHelper::getLapsedTime($reply->created); ?>
						</span>
					</div>
					<?php
						unset($reply);
						unset($content);
					?>
				</div>
			</div>
			<?php } //end foreach ?>

	</div>
	<?php } else { ?>
	<div class="no-item">
		<?php echo JText::_('MOD_EASYDISCUSS_NO_ENTRIES'); ?>
	</div>
	<?php } //end if ?>
</div>
