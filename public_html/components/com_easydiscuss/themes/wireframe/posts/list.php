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
?>
<?php if ($featured) { ?>
	<?php if (!isset($hideTitles) || isset($hideTitles) && !$hideTitles) { ?> 
	<h4 class="o-title"><?php echo JText::_('COM_EASYDISCUSS_FEATURED_POSTS');?></h4>
	<?php } ?>

	<?php foreach ($featured as $featuredPost) { ?>
		<?php echo $this->html('card.post', $featuredPost); ?>
	<?php } ?>
<?php } ?>

<?php if ($posts) { ?>
	<?php if (!isset($hideTitles) || isset($hideTitles) && !$hideTitles) { ?> 
	<h4 class="o-title"><?php echo JText::_('COM_ED_POSTS');?></h4>
	<?php } ?>

	<?php 
		$cardOptions = array();
		if (isset($isSearch) && $isSearch) {
			$cardOptions['isSearch'] = true;
		}
	?>

	<?php foreach ($posts as $post) { ?>
		<?php echo $this->html('card.post', $post, $cardOptions); ?>
	<?php } ?>

	<div data-frontpage-pagination>
		<?php echo $pagination->getPagesLinks();?>
	</div>
<?php } ?>