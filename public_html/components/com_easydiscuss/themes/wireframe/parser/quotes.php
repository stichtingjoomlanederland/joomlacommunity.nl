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
<blockquote class="ed-quotes">
	<?php if (isset($user) && $user) { ?>
	<div class="ed-quotes__from">
		<a href="<?php echo $permalink;?>">
			<?php echo JText::sprintf('COM_ED_QUOTE_HEADER', $user->getName(), $postDate); ?>
		</a>
	</div>
	<?php } ?>
	<div class="ed-quotes__content"><?php echo $contents;?></div>
</blockquote>