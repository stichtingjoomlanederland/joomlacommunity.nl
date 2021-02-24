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
<div class="o-avatar o-avatar--icon o-avatar--rounded t-flex-shrink--0
	<?php echo $size;?> 
	<?php echo $border;?> 
	<?php echo $wrapperClasses;?>"
>
	<i class="<?php echo $category->getIcon();?> fa-fw" style="color: <?php echo $category->getColour();?>;"></i>
</div>