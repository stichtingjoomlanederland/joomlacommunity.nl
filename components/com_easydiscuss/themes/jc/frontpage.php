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
<script type="text/javascript">
EasyDiscuss
.require()
.script('posts')
.done(function($){
	$('.discussPostsList').implement( EasyDiscuss.Controller.PostItems,
	{
		activefiltertype: '<?php echo $activeFilter; ?>'
	});
});
</script>
<?php if( $categories ){ ?>
	<?php foreach( $categories as $category ){ ?>
		<?php echo $this->loadTemplate( 'frontpage.category.php' , array( 'category' => $category ) ); ?>
	<?php } ?>
<?php } else { ?>
	<div class="discuss-empty"><?php echo JText::_( 'COM_EASYDISCUSS_NO_CATEGORIES_CREATED_YET' ); ?></div>
<?php } ?>

<?php if( $system->config->get( 'layout_board_stats' ) ){ ?>
<?php echo DiscussHelper::getBoardStatistics(); ?>
<?php } ?>