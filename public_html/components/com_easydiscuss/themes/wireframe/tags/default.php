<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>

<div class="ed-filter-bar t-lg-mb--md">
	<div class="ed-filter-bar__sort-tabs">
		<h2 class="ed-page-title t-lg-mb--no"><?php echo JText::_('COM_EASYDISCUSS_TAGS'); ?></h2>		
	</div>
	<div class="ed-filter-bar__sort-action">
		<select data-index-sort-filter>
			<option value="title" <?php echo $activeSort == 'title' ? ' selected="true"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_SORT_TITLE');?></option>
			<option value="postcount" <?php echo $activeSort == 'postcount' ? ' selected="true"' : '';?>><?php echo JText::_('COM_ED_SORT_TAG_WEIGHT');?></option>
		</select>
	</div>
</div>


<?php if ($tags) { ?>
	<div class="ed-tags" data-list-item>
		<?php foreach ($tags as $tag) { ?>
			<?php echo $this->output('site/tags/default.item', array('tag' => $tag)); ?>
		<?php } ?>

		<div class="o-loading">
			<div class="o-loading__content">
				<i class="fa fa-spinner fa-spin"></i>
			</div>
		</div>
	</div>

	<div class="ed-pagination" data-tags-pagination>
		<?php echo $pagination->getPagesLinks();?>
	</div>

<?php } else { ?>
	<div class="dc_alert msg_in">
		<?php echo JText::_('COM_EASYDISCUSS_NO_RECORDS_FOUND'); ?>
	</div>
<?php } ?>
