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
<form action="index.php" method="post" name="adminForm" id="adminForm" data-ed-state-tabs>

    <div class="app-tabs">
        <ul class="app-tabs-list g-list-unstyled">
            <li class="tabItem <?php echo $active == 'general' ? ' active' : '';?>">
                <a href="#general" data-ed-toggle="tab"><?php echo JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_TAB_MAIN');?></a>
            </li>

            <li class="tabItem <?php echo $active == 'permissions' ? ' active' : '';?>">
                <a href="#permissions" data-ed-toggle="tab"><?php echo JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_TAB_PERMISSION');?></a>
            </li>
        </ul>
    </div>

	<div class="tab-content">
		<div id="general" class="tab-pane <?php echo $active == 'general' ? ' active' : '';?>">
			<?php echo $this->output('admin/fields/form.general'); ?>
		</div>

		<div id="permissions" class="tab-pane <?php echo $active == 'permissions' ? ' active' : '';?>">
		  <?php echo $this->output('admin/fields/form.permissions'); ?>
		</div>
	</div>
			
    <?php echo $this->html('form.hidden', 'customfields', 'customfields', ''); ?>

	<input type="hidden" name="id" value="<?php echo $field->id;?>" />
    <input type="hidden" name="active" value="<?php echo $active;?>" data-ed-state-tabs-current />
</form>