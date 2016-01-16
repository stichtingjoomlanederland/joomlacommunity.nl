<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<div class="fd-cf">
<h2 class="discuss-component-title"><?php echo JText::_('COM_EASYDISCUSS_TAGS'); ?></h2>
</div>
<hr/>
<?php if ( !empty( $tagCloud ) ) { ?>
<ul class="discuss-tag-list unstyled">
	<?php echo $this->loadTemplate( 'tags.item.php' );?>
</ul>


<?php } else { ?>
<div class="dc_alert msg_in"><?php echo JText::_('COM_EASYDISCUSS_NO_RECORDS_FOUND'); ?></div>
<?php } ?>
