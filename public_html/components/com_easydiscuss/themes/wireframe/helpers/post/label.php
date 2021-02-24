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
<a href="<?php echo EDR::_('view=index&labels[0]=' . $label->id);?>" class="o-label t-text--truncate" 
	data-ed-label
	data-ed-filter-api="label"
	data-id="<?php echo $label->id;?>"
	style="max-width:180px;background-color: <?php echo $label->colour;?>;color: <?php echo $fontColor;?>">
	<?php echo JText::_($label->title);?>
</a>