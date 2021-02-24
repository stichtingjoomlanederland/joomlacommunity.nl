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
<?php foreach ($tags as $tag) { ?>
<div class="t-min-width--0">
	<div class="t-d--flex t-text--truncate">
		<a href="<?php echo EDR::_('view=tags&id=' . $tag->id);?>" class="o-label t-bg--primary-100 t-text--primary t-text--truncate">
			<i class="fa fa-tag"></i>&nbsp; <?php echo $this->html('string.escape', $tag->title);?>
		</a>
	</div>
</div>
<?php } ?>