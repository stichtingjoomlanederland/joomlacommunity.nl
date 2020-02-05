<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<h2 class="ed-page-title"><?php echo JText::sprintf('COM_EASYDISCUSS_USER_POINTS_HISTORY', $user->getName()); ?></h2>

<?php if ($history) { ?>
	<div class="ed-points-history">
	    <?php foreach ($history as $group => $items) { ?>
		    <div class="ed-points-history__group">
		        <div class="ed-points-history__date"><?php echo $group; ?></div>
		        <?php foreach ($items as $item) { ?>
			        <div class="ed-points-history__info">
			             <span class="ed-points-history__label"><?php echo $item->points; ?></span> <?php echo $item->title; ?>
			        </div>
			    <?php } ?>
		    </div>
	    <?php } ?>
	</div>
<?php } else { ?>
	<div class="is-empty">
		<div class="o-empty o-empty--bordered">
			<div class="o-empty__content">
				<i class="o-empty__icon fa fa-book"></i>
				<div class="o-empty__text">
					<?php echo JText::_('COM_EASYDISCUSS_USER_NO_POINTS_HISTORY'); ?>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
