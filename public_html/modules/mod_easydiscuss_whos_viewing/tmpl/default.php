<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="ed" class="ed-mod m-whos-viewing <?php echo $params->get('moduleclass_sfx');?>">   
    <div class="ed-mod__section">
        <div class="o-avatar-list">
            <?php foreach($users as $user) { ?>
                <?php echo ED::themes()->html('user.avatar', $user, array('status' => true, 'size' => 'md')); ?>
            <?php } ?>
        </div>
    </div>
</div>