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
<?php echo $this->loadTemplate( $childtheme, array( 'isEmail' => $isEmail ) ); ?>

<?php if( !$isEmail ){ ?>

	<?php if( $attachment->deleteable() ){ ?>
	<p style="text-align:center;">
		<a class="btn btn-small btn-danger btn-remove" href="javascript:void(0);" data-id="<?php echo $attachment->id; ?>" data-attachment-remove-button>
			<i></i>
			<?php //echo JText::_( 'COM_EASYDISCUSS_DELETE_BUTTON' );?>
		</a>
	</p>
	<?php } ?>

<?php } ?>
