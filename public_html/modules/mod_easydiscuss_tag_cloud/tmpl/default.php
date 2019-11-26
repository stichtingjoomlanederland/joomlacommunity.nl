<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="mod-easyblog-tagcloud" class="ezb-mod eblog-module-tagcloud<?php echo $params->get('moduleclass_sfx') ?>">
	<?php if (!empty($tagcloud)) { ?>
		<?php foreach($tagcloud as $tag){ ?>
		<a	style="font-size: <?php echo floor($tag->fontsize); ?>px;" class="tag-cloud"
        href="<?php echo EDR::getTagRoute($tag->id . ':' . $tag->alias); ?>">
        <?php echo ED::String()->escape($tag->title); ?>
        </a>
		<?php } ?>
	<?php } else { ?>
		<?php echo JText::_('MOD_EASYDISCUSSTAGCLOUD_NO_TAG'); ?>
	<?php } ?>
</div>
