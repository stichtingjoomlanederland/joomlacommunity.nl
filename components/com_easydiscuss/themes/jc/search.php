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
<?php if( !empty( $posts ) ){ ?>

	<div class="alert alert-success"><?php echo JText::sprintf('COM_EASYDISCUSS_SEARCH_RESULT_FOUND' , $pagination->total , $query ); ?></div>
	<ul class="discuss-list reset-ul">
		<?php echo $this->loadTemplate( 'search.item.php' ); ?>
	</ul>
	<?php echo $this->loadTemplate( 'pagination.php' , array( 'sort' => 'latest' , 'filter' => 'allposts' ) );?>

<?php } else { ?>

	<?php if( !empty($query) ): ?>
	<div class="alert alert-error"><?php echo JText::sprintf( 'COM_EASYDISCUSS_SEARCH_NO_RESULT' , $query ) ?></div>
	<?php endif; ?>

	<?php if( empty($query) ): ?>
	<div class="alert alert-info"><?php echo JText::_( 'COM_EASYDISCUSS_SEARCH_PLEASE_ENTER_SOMETHING' ) ?></div>
	<?php endif; ?>

<?php } ?>
