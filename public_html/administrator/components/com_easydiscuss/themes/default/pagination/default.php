<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="t-text--center" data-grid-pagination>
	<ul class="o-pagination">
		<?php if( $data->previous ){ ?>
		<li class="<?php echo !$data->previous->link ? ' disabled' : '';?>" data-pagination-link data-limitstart="<?php echo $data->previous->base;?>">
			<a href="javascript:void(0);" class="previousItem">&laquo;</a>
		</li>
		<?php } ?>

		<?php foreach( $data->pages as $page ){ ?>
		<li class="<?php echo !$page->link ? ' active' : '';?>" data-pagination-link data-limitstart="<?php echo $page->base ? $page->base : 0;?>">
			<a href="javascript:void(0);" class="pageItem"><?php echo $page->text;?></a>
		</li>
		<?php } ?>

		<?php if( $data->next ){ ?>
		<li class="<?php echo !$data->next->link ? ' disabled' :'';?>" data-pagination-link data-limitstart="<?php echo $data->next->base;?>">
			<a href="javascript:void(0);" class="nextItem">&raquo;</a>
		</li>
		<?php } ?>
	</ul>
	<input id="limitstart" name="limitstart" value="<?php echo $pagination->limitstart;?>" type="hidden" data-limitstart-value />
</div>
