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
<div class="t-min-width--0" data-selection-item>
	<div class="t-d--flex t-text--truncate">
		<a href="javascript:void(0);" class="o-label o-label--ed-filter-label t-bg--primary-100 t-text--primary t-border--primary-100 t-text--truncate"
			data-ed-remove-filter="<?php echo $type;?>"
			data-id="<?php echo $id;?>"
		>
			<?php echo $title;?>
			&nbsp;<i class="fa fa-times t-text--600"></i>
		</a>
	</div>
</div>