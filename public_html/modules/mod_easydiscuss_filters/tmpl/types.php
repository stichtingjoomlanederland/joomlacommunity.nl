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
<div id="ed" class="ed-mod ed-mod--filters <?php echo $params->get('moduleclass_sfx') ?>" data-module-filters>
	<div class="es-mod-filters">
		<div class="l-stack">
			<?php foreach ($filters as $filter) { ?>
			<div class="es-mod-filters__item <?php echo $filter->active ? 'is-active' : '';?>" data-module-filter="type" data-id="<?php echo $filter->alias;?>">
				<a href="javascript:void(0);" class="si-link">
					<?php echo JText::_($filter->title);?>
				</a>
			</div>
			<?php } ?>
		</div>
	</div>
</div>