<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');


$references	= $composer->getFieldData('references', $post->params);
$targetRef = $this->config->get('main_reference_link_new_window') ? ' target="_blank"' : '';

if (!$references) {
	return;
}

if (!$this->config->get('reply_field_references')) {
	return;
}
?>
<div class="ed-post-widget">
	<div class="ed-post-widget__hd">
	    <?php echo JText::_('COM_EASYDISCUSS_REFERENCES'); ?>
	</div>

    <div class="ed-post-widget__bd">
		<ol class="ed-post-ref-nav">
			<?php foreach ($references as $reference) { ?>
				<?php
				$reference = strip_tags($reference);

				if (JString::stristr($reference, 'https://') === false && JString::stristr($reference, 'http://') === false) {
					$reference = 'http://' . $reference;
				}

					// Remove quotes
					$reference = str_ireplace(array('"', "'"), '', $reference);
				
				?>
				<li>
					<a href="<?php echo $this->escape($reference); ?>"<?php echo $targetRef; ?><?php echo ED::getLinkAttributes(); ?>><?php echo $this->escape( $reference); ?></a>
				</li>
			<?php } ?>
		</ol>
	</div>
</div>