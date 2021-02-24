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
<div class="o-card o-card--ed-active-category">
	<div class="o-card__body">
		<div class="t-text--center">
			<h3>'<?php echo $this->html('string.escape', $query);?>'</h3>
			<p>
				<?php echo JText::sprintf('<b>%1$s results</b> found based on the keyword <b>%2$s</b>', $pagination->total, $this->html('string.escape', $query)); ?>
			</p>
		</div>
	</div>
</div>