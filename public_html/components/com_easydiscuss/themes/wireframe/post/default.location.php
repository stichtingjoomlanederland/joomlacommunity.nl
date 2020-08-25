<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

if (!$post->hasLocation()) {
	return;
}

if (!$post->isQuestion() && !$this->config->get('main_location_reply')) {
	return;
}

if ($post->isQuestion() && !$this->config->get('main_location_discussion')) {
	return;
}

?>
<div class="ed-post-map" data-ed-location>
    <div class="ed-post-map__hd">
        <div class="pull-left">
            <i class="fa fa-map-marker"></i>&nbsp; <?php echo $post->address; ?>
        </div>
        <a href="http://www.google.com/maps?q=<?php echo urlencode($post->address);?>&amp;hl=en" class="pull-right" target="_blank">
        	<?php echo JText::_('COM_EASYDISCUSS_LOCATION_VISIT'); ?>&nbsp; <i class="fa fa-external-link"></i>
        </a>
    </div>
    
    <div class="ed-location" data-ed-location-map-wrapper>
	    <?php if ($this->config->get('main_location_static')) { ?>
            <div class="ed-location__map-static" style="background-image:url('<?php echo ED::getMapRequestURL($post); ?>');"></div>
            <style>
            @media print {
                .ed-location__map-static:before {
                    content: url('<?php echo ED::getMapRequestURL($post, true); ?>');
                }
            }
            </style>
	    <?php } else { ?>
            <iframe 
                width="100%" 
                height="200px" 
                frameborder="0" 
                style="border:0" 
                src="<?php echo ED::getMapRequestURL($post); ?>" 
                allowfullscreen
            ></iframe>
	    <?php } ?>
    </div>

</div>
