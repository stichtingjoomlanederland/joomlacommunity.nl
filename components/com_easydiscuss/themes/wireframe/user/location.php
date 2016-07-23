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
<div class="discuss-location locationForm">
    <div class="ed-form-panel__hd">
        <div class="ed-form-panel__title"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_LOCATION');?></div>
        <div class="ed-form-panel__"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_LOCATION_DESC');?></div>
    </div>
    <div class="ed-form-panel__bd">
        <?php $locations = array('latitude' => $profile->latitude, 'longitude' => $profile->longitude, 'address' => $profile->location, 'hasLocation' => $profile->hasLocation()) ?>
        <?php echo $this->output('site/forms/location.form', $locations); ?>
    </div>
</div>
