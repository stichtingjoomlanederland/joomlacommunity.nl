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
<div class="ed-search-results t-lg-mt--lg">

	<?php echo $this->output('site/search/inputs', array('query' => $query, 'tagFilters' => $tagFilters, 'catFilters' => $catFilters, 'postTypes' => $postTypes)); ?>

	<?php if ($posts) { ?>
		<div role="alert" class="o-alert o-alert--info">
			<?php echo JText::sprintf('COM_EASYDISCUSS_SEARCH_RESULT_FOUND', $pagination->total, $query); ?>
		</div>

		<div class="ed-list">
			<?php foreach ($posts as $post) { ?>
				<?php echo $this->output('site/search/item', array('post' => $post)); ?>
			<?php } ?>
		</div>

		<div class="discuss-pagination text-center">
			<?php echo $this->output('site/widgets/pagination', array('sort' => 'latest', 'filter' => 'allposts')); ?>
		</div>
	<?php } ?>

	<?php if ($query && !$posts) { ?>
	<div class="o-alert o-alert--error">
		<?php echo JText::sprintf('COM_EASYDISCUSS_SEARCH_NO_RESULT', $query); ?>
	</div>
	<?php } ?>

	<?php if (!$query) { ?>
	<div class="o-alert o-alert--info"><?php echo JText::_( 'COM_EASYDISCUSS_SEARCH_PLEASE_ENTER_SOMETHING' ) ?></div>
	<?php } ?>
</div>
