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
<form id="frmMergePost" action="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=posts&task=merge' );?>" method="post">
<p><?php echo JText::_( 'COM_EASYDISCUSS_MERGE_POST_DESC' ); ?></p>


<div class="mt-20">
	<?php if( $posts ){ ?>
	<select name="id" class="inputbox full-width">
		<?php foreach( $posts as $post ){ ?>
			<?php if( $post->id != $current ){ ?>
				<option value="<?php echo $post->id;?>"><?php echo $post->id; ?> - <?php echo $this->escape( $post->title ); ?></option>
			<?php } ?>
		<?php } ?>
	</select>
	<?php } else { ?>
	<div class="alert alert-error"><?php echo JText::_( 'COM_EASYDISCUSS_MERGE_NO_POSTS' );?></div>
	<?php } ?>
</div>

<div>
<span class="label label-info small"><?php echo JText::_( 'COM_EASYDISCUSS_NOTE' );?>:</span>
<span class="small"><?php echo JText::_( 'COM_EASYDISCUSS_MERGE_NOTES' );?></span>
</div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="current" value="<?php echo $current;?>" />
</form>
