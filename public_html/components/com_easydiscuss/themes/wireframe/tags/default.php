<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<h2 class="ed-page-title"><?php echo JText::_('COM_EASYDISCUSS_TAGS'); ?></h2>

<?php if ($tags) { ?>
    <div class="ed-tags">
	    <?php foreach ($tags as $tag) { ?>
	        <?php echo $this->output('site/tags/default.item', array('tag' => $tag)); ?>
	    <?php } ?>
    </div>
<?php } else { ?>
    <div class="dc_alert msg_in">
        <?php echo JText::_('COM_EASYDISCUSS_NO_RECORDS_FOUND'); ?>
    </div>
<?php } ?>
