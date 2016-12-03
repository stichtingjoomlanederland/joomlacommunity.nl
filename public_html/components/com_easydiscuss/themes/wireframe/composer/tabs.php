<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-editor-tab">
    <ul class="o-nav o-nav--block ed-editor-tab__nav" data-ed-ask-tabs>
        <?php foreach ($tabs as $tab) { ?>
            <?php echo $tab->heading; ?>
        <?php } ?>
    </ul>

    <div class="tab-content" data-ed-ask-tabs-content>
        <?php foreach ($tabs as $tab) { ?>
            <?php echo $tab->contents; ?>
        <?php } ?>
    </div>
</div>