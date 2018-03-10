<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
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
                <div class="o-avatar-list__item">
                    <a href="<?php echo $user->getLink();?>" class="o-avatar o-avatar--sm" rel="ed-tooltip"  data-ed-provide="tooltip" data-placement="top" data-original-title="<?php echo ED::string()->escape($user->getName());?>">
                        <img src="<?php echo $user->getAvatar();?>" alt="<?php echo ED::string()->escape($user->getName());?>" />
                    </a>    
                </div>
            <?php } ?>
        </div>
    </div>
</div>