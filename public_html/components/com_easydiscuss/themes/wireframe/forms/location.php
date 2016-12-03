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

if ($post->isQuestion() && !$this->config->get('main_location_discussion')) {
    return;
}

if ($post->isReply() && !$this->config->get('main_location_reply')) {
    return;
}
?>

<div class="ed-editor-widget">
    <div class="ed-editor-widget__title">
        <?php echo JText::_('COM_EASYDISCUSS_SHARE_LOCATION'); ?>
    </div>
    <div class="ed-editor-widget__note">
    	<p><?php echo JText::_('COM_EASYDISCUSS_SHARE_LOCATION_INFO'); ?></p>
    </div>

    <?php echo $this->output('site/forms/location.form', array('latitude' => $post->latitude, 'longitude' => $post->longitude, 'address' => $post->address)); ?>
</div>
