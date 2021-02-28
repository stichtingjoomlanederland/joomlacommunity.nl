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
<a href="<?php echo EDR::_('view=index&types[0]=' . $type->alias);?>" class="o-label t-bg--300 t-text--600 t-flex-shrink--0 <?php echo $typeSuffix; ?>"
	style="max-width:180px;"
	data-ed-type-item
	data-ed-filter-api="type"
	data-id="<?php echo $type->alias;?>"
>
	<?php if ($type->icon) { ?>
	<i class="t-align--middle <?php echo $type->icon;?>"></i>
	<?php } ?>
	&nbsp;<?php echo JText::_($type->title); ?>
</a>