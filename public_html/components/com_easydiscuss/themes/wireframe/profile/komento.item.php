<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-post-item">
    <div class="ed-post-item__hd">
        <h2 class="ed-post-item__title t-lg-mt--md t-lg-mb--md">
            <a href="<?php echo $item->getPermalink(); ?>"><?php echo $item->contenttitle; ?></a>
        </h2>
        <br>
        <?php echo $item->getContent(); ?>
    </div>

    <div class="ed-post-item__ft t-bdt-no">
        <ol class="g-list-inline g-list-inline--dashed">
            <li><span class=""><?php echo $item->created; ?></span></li>
        </ol>
    </div>

</div>