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
<a class="ed-state-<?php echo $class;?> badge" href="javascript:void(0);"
    <?php echo $allowed ? ' data-ed-table-state' : '';?>
    data-task="<?php echo $task;?>"
    <?php echo !$allowed ? ' disabled="disabled"' : '';?>
>
    <?php if ($class == 'default' || $class == 'featured') { ?>
    <i class="fa fa-star"></i>
    <?php } ?>

    <?php if ($class == 'published') { ?>
    <i class="fa fa-check"></i>
    <?php } ?>

    <?php if ($class == 'unpublished') { ?>
    <i class="fa fa-times"></i>
    <?php } ?>

    <?php if ($class == 'trash') { ?>
    <i class="fa fa-trash-o"></i>
    <?php } ?>

    <?php if ($class == 'scheduled') { ?>
    <i class="fa fa-clock-o"></i>
    <?php } ?>

    <?php if ($class == 'archived') { ?>
    <i class="fa fa-inbox"></i>
    <?php } ?>
</a>

