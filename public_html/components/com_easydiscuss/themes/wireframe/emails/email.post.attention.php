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
<?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_HELLO' ); ?>,

<?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_REPORTING_STATEMENT' ); ?>
<?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_CONTENT_IS_BELOW' ); ?>

<?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_VIEW_AT' );?>: <?php echo $postLink;?>


<?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_CONTENT' );?>:

<?php echo $postContent;?>


<?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_AUTHOR' );?>:

<?php echo $postAuthor;?>

<?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_POSTEDDATE' );?>:

<?php echo $postDate; ?>
