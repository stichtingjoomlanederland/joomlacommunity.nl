<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if (!$post->user_id) { ?>
	<?php echo $post->poster_name; ?>
<?php } else { ?>
	   
    <?php if ($post->getOwner() && $post->getOwner() instanceof DiscussProfile) { ?>
		<?php echo $post->getOwner()->getName();?>	
	<?php } else { ?>
		<?php echo $post->getOwner()->name; ?>
	<?php } ?>

<?php } ?>