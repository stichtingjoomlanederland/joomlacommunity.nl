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
<form action="index.php" method="post" name="adminForm" id="adminForm">

    <div class="app-tabs">
        <ul class="app-tabs-list g-list-unstyled">
            <?php $i = 0; ?>
            <?php foreach ($tabs as $tab) { ?>
            <li class="tabItem<?php echo ($i == 0 && !$active) || ($active == $tab) ? ' active' : '';?>">
                <a href="#ed-<?php echo $tab;?>" data-ed-toggle="tab" data-ed-tab data-id="ed-<?php echo $tab;?>"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_TAB_' . strtoupper($tab));?></a>
            </li>
            <?php $i++;?>
            <?php } ?>
        </ul>
    </div>

    <div class="tab-content">
        <?php $i = 0; ?>
        <?php foreach ($tabs as $tab) { ?>
        <div id="ed-<?php echo $tab;?>" class="tab-pane<?php echo ($i == 0 && !$active) || ($active == $tab) ? ' active in' : '';?>">
            <?php echo $this->output('admin/settings/' . $layout . '/' . $tab); ?>
        </div>
        <?php $i++;?>
        <?php } ?>
    </div>

    <input type="hidden" name="layout" value="<?php echo $layout;?>" />
    <input type="hidden" name="active" value="<?php echo $active;?>" data-ed-active-tab />

    <?php echo $this->html('form.hidden', 'settings'); ?>

</form>