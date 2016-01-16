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

<?php if(! is_null( $pagination ) ) : ?>
	<?php if( $pagination->total > $pagination->limit ): ?>

		<?php echo $pagination->getPagesLinks(); ?>
	<?php endif; ?>
<?php endif; ?>

<input type="hidden" name="start" id="pagination-start" value="<?php echo (! is_null($pagination) ) ? $pagination->limit : '0';?>" />
<input type="hidden" name="sorting" id="pagination-sorting" value="<?php echo $sort;?>" />
<input type="hidden" name="filter" id="pagination-filter" value="<?php echo $filter;?>" />
<input type="hidden" name="discuss_parent" id="discuss_parent" value="<?php echo $parent_id;?>" />
<input type="hidden" name="query" id="pagination-query" value="<?php echo isset($query) ? $query : '';?>" />
<input type="hidden" name="category" id="pagination-category" value="<?php echo isset($activeCategory->id) ? $activeCategory->id : '';?>" />
