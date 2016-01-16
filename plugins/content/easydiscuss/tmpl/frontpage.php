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
<div class="discuss-frontpage-tools">
	<div class="align-<?php echo $params->get( 'frontpage_alignment' , 'right');?>">
		<?php if( $params->get( 'frontpage_show_hits' , true ) ){ ?>
		<a href="<?php echo $url;?>" class="discuss-hits"><?php echo JText::sprintf( 'COM_EASYDISCUSS_PLUGIN_HITS' , $hits );?></a>
		<?php } ?>

		<?php if( $params->get( 'frontpage_show_discussion' , true ) ){ ?>
		<a href="<?php echo $url;?>" class="discuss-this"><?php echo JText::sprintf( 'COM_EASYDISCUSS_PLUGIN_DISCUSS_THIS' , $total );?></a>
		<?php } ?>
	</div>
</div>
