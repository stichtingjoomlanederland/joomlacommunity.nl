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
<div class="ed-navbar__support-toggle pull-right">
    <?php echo JText::_('COM_EASYDISCUSS_SUPPORT_IS_CURRENTLY');?>

    <a href="javascript:void(0);" class="o-label o-label--<?php echo ($isOnline) ? 'success' : 'danger';?>-o"
        data-ed-popbox
        data-ed-popbox-toggle="hover"
        data-ed-popbox-position="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'bottom-left' : 'bottom-right';?>"
        data-ed-popbox-offset="4"
        data-ed-popbox-type="navbar-support"
        data-ed-popbox-component="popbox--navbar"
        data-ed-popbox-target="[data-ed-support-dropdown]"
    >
        <span class="t-inline-block">
            <?php echo $label; ?>
        </span>
    </a>

    <div class="t-hidden" data-ed-support-dropdown>
        <div class="popbox popbox--holiday top-left">
            <?php echo $this->output($namespace); ?>
        </div>
    </div>
</div>
