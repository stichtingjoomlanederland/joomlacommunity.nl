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
<div class="ed-ratings<?php echo $voted ? ' is-voted' : ''; ?>" data-ed-ratings>

	<meta itemprop="ratingValue" content="<?php echo $ratingSchemaHasValue ? $score : $ratingSchemaValue;?>" />
	<meta itemprop="worstRating" content="1">
	<meta itemprop="bestRating" content="5">

	<div class="o-col ed-ratings__note-col" data-ed-ratings-message>
		<?php if ($voted) { ?>
			<?php echo JText::_('COM_EASYDISCUSS_RATINGS_ALREADY_RATED');?>
		<?php } ?>
		<?php if (!$locked) { ?>
			<?php echo JText::_('COM_EASYDISCUSS_RATINGS_PLEASE_RATE');?>
		<?php } ?>
	</div>
	<div class="o-col ed-ratings__star-col">
		<div class="ed-rating" data-ed-ratings-stars data-id="<?php echo $post->id; ?>" data-score="<?php echo $score; ?>" data-locked="<?php echo $locked; ?>"></div>
	</div>
	<div class="o-col">
		<div class="ed-ratings__link">
			<span class="ed-ratings__value">
				<?php if ($ratingSchemaHasTotal) { ?>
					<span data-ed-ratings-total itemprop="ratingCount" content="<?php echo $ratingSchemaTotal; ?>"><?php echo $total;?></span>
				<?php } else { ?>
					<span data-ed-ratings-total itemprop="ratingCount" content="<?php echo $ratingSchemaTotal; ?>"><?php echo $total;?></span>
				<?php } ?>
				
				<b title="<?php echo JText::_('COM_EASYDISCUSS_RATINGS_HAS_RATED');?>" class="ed_ratings__state-voted">
					<i class="fa fa-check"></i>
				</b>
			</span>
		</div>
	</div>
</div>
 