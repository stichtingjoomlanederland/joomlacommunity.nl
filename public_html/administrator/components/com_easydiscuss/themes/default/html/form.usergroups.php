<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-usergroups-tree">
    <?php foreach ($groups as $group) { ?>
    <div class="tree-control">
        <div class="checkbox">
            <input type="checkbox" id="usergroups-<?php echo $group->id;?>-<?php echo $uid;?>" value="<?php echo $group->id;?>" name="<?php echo $name;?>[]"
                <?php echo in_array($group->id, $selected) ? ' checked="checked"' : '';?> 
            />
            <label for="usergroups-<?php echo $group->id;?>-<?php echo $uid;?>">
                <div class="tree-title">
                    <?php echo str_repeat('<span class="gi"></span>', $group->level);?> <b><?php echo $group->title;?></b>
                </div>
            </label>
        </div>
    </div>
    <?php } ?>
</div>
