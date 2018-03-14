<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($post->published == DISCUSS_ID_PENDING) { ?>
	<a
		href="javascript:void(0);"
		data-moderate-dialog
		data-id="<?php echo $post->id;?>"
	>
		<img src="<?php echo rtrim( JURI::root() , '/' );?>/administrator/components/com_easydiscuss/themes/default/images/moderate.png" />
	</a>
<?php } else { ?>
	<a
		class="ed-state-<?php echo $post->published ? 'published' : 'unpublished';?> "
		title=""
		onclick="return listItemTask('cb<?php echo $index;?>','<?php echo ($post->published) ? 'unpublish' : 'publish';?>')"
		href="#toggle">
	</a>
<?php } ?>
