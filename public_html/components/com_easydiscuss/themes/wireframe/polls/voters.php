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
<?php if ($voters) { ?>
    <?php foreach ($voters as $voter) { ?>
    <a href="<?php echo $voter->getPermalink();?>" class="o-avatar o-avatar--sm">
        <img src="<?php echo $voter->getAvatar();?>" alt="<?php echo $this->html('string.escape', $voter->getName());?>" />
    </a>
    <?php } ?>
<?php } ?>