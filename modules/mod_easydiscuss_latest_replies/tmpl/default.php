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
<div class="latest-replies<?php echo $params->get( 'moduleclass_sfx' ) ?>">
<?php if( $replies ){ ?>
	<div id="discuss-mod">
		<div class="list-item">
			<?php for($i = 0 ; $i < count( $replies ); $i++) {
					$reply  = $replies[$i];
			?>
				<div class="item">
					<div class="item-question">
						<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id='.$reply->parent_id ); ?>">
						<?php echo DiscussStringHelper::escape($reply->title); ?>
						</a>
					</div>
					<div class="item-answer">
						<?php
						$content	= $reply->content;
						$content	= JString::substr( strip_tags( $reply->content ) , 0 , $params->get( 'maxlength' , 200 ) );
						echo EasyDiscussParser::filter( $content );
						unset($reply);
						unset($content);
						?>
					</div>
				</div>
			<?php } //end foreach ?>

		</div>
	</div>
	<?php } else { ?>
	<div class="no-item">
		<?php echo JText::_('MOD_EASYDISCUSS_NO_ENTRIES'); ?>
	</div>
	<?php } //end if ?>
</div>
