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
	<?php if( $voters ) {
		foreach( $voters as $voter ) { ?>
		<div class="discuss-voter">
			<a href="<?php echo $voter->getLink();?>">
				<div class="pull-left">
					<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
					<img src="<?php echo $voter->getAvatar();?>" width="40"/>
					<?php } ?>
				</div>
				<div class="table">
					<span class="table-cell"><?php echo $voter->getName();?></span>
				</div>

			</a>
		</div>
	<?php }
	} else { ?>
		<span class="small"><?php echo JText::_( 'COM_EASYDISCUSS_POLLS_NO_USER_VOTED_YET' ); ?></span>
	<?php } ?>
</div>
