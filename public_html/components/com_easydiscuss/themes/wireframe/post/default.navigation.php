<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>

<?php if ($navigation) { ?>
	<div class="o-pager t-lg-mt--lg t-lg-pt--lg">
	<?php if (!empty($navigation->prev)) { ?>
	<div class="o-pager__item o-pager__item--prev">
		<a href="<?php echo $navigation->prev->link;?>" class="o-pager__link">
			<i class="o-pager__icon fa fa-angle-left"></i>
			<span><?php echo $navigation->prev->title;?></span>
		</a>
	</div>
	<?php } ?>

	<?php if (!empty($navigation->next)) { ?>
	<div class="o-pager__item o-pager__item--next">
		<a href="<?php echo $navigation->next->link;?>" class="o-pager__link">
			<span><?php echo $navigation->next->title;?></span>
			<i class="o-pager__icon fa fa-angle-right"></i>
		</a>
	</div>
	<?php } ?>
</div>
<?php } ?>