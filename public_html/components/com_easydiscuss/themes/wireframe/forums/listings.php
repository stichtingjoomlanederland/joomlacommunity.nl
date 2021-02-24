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
<?php echo ED::renderModule('easydiscuss-forums-listings-start'); ?>

<div class="ed-forums" data-forums data-id="<?php echo $activeCategory->id; ?>">
	<?php echo $this->output('site/forums/active', array('activeCategory' => $activeCategory, 'listing' => $listing, 'childs' => $childs)); ?>

	<div class="ed-filters">
		<?php echo $this->output('site/frontpage/filters', array('baseUrl' => $baseUrl, 'activeFilter' => $activeFilter, 'activeSort' => $activeSort, 'menuCatId' => '', $labels = '')); ?>
	</div>

	<div data-list-wrapper>
		<div class="ed-list" data-list-item>
			<?php echo $this->output('site/forums/threads', array('threads' => $threads)); ?>
		</div>

		<div class="o-loading">
			<div class="o-loading__content">
				<i class="fa fa-spinner fa-spin"></i>
			</div>
		</div>
	</div>

	<?php if (isset($pagination)) { ?>
	<div class="ed-pagination" data-forums-pagination>
		<?php echo $pagination->getPagesLinks();?>
	</div>
	<?php } ?>
</div>

<?php echo ED::renderModule('easydiscuss-forums-listings-end'); ?>
