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
<form name="adminForm" id="adminForm" method="post" data-ed-table-grid>
    <div class="panel languages-loader">
        <div class="panel-body text-center">
            <div class="t-lg-mb--xl">
                <i class="fa fa-refresh fa-spin" style="font-size: 48px;"></i>
            </div>

            <?php echo JText::_('COM_EASYDISCUSS_INITIALIZING_LANGUAGE_LIST');?>
            <div class="alert alert-danger hide" data-ed-initialize-error style="margin-top:50px;"></div>
        </div>
    </div>
</form>
