<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

$this->row = Komento::getHelper( 'comment' )->process( $this->row, 1 ); ?>

<tr id="<?php echo 'kmt-' . $this->row->id; ?>" class="<?php echo 'row' . $this->k; ?> kmt-row" childs="<?php echo $this->row->childs; ?>" depth="<?php echo $this->row->depth; ?>" parentid="<?php echo $this->row->parent_id; ?>">

	<!-- Row Number -->
	<td class="center">
		<?php echo isset( $this->pagination ) ? $this->pagination->getRowOffset( $this->i ) : ''; ?>
	</td>

	<!-- Checkbox -->
	<td class="center">
		<?php echo JHTML::_('grid.id', $this->i, $this->row->id); ?>
	</td>

	<!-- Comment
		todo::truncate comments (overflow css)
		todo::better edit hyperlink
	 -->
	<td class="comment-cell">
		<div class="row-table cell-top">
			<?php if( $this->row->depth > 0 ) {
				echo str_repeat( '<div class="col-cell cell-tight">|—</div>', $this->row->depth );
			} ?>
			<div class="col-cell"><?php echo $this->row->comment; ?></div>
		</div>
	</td>
	
	<!-- Publish/Unpublish -->
	<td class="published-cell center">
		<?php if( $this->row->published == 2 ) { ?>
			<a class="btn btn-micro active hasTooltip" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $this->i; ?>', 'publish')" title="" data-original-title="<?php echo JText::_('COM_KOMENTO_PUBLISH_ITEM'); ?>">
				<i class="fa fa-clock-o"></i>
			</a>
		<?php } else {
			if( $this->row->published != 1 ) {
				$this->row->published = 0;
			}
			echo JHTML::_('jgrid.published', $this->row->published, $this->i );
		} ?>
	</td>
	
	<!-- Sticked -->
	<td class="sticked-cell center">
		<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $this->i; ?>','<?php echo $this->row->sticked ? 'unstick' : 'stick';?>')">
			<i class="icon-star<?php echo $this->row->sticked ? '' : '-empty'; ?>"></i>
		</a>
	</td>

	<!-- Link to childs / parent
	todo::check if child exist
	if rgt-lft > 1
	-->
	<td class="linked-cell center">
	<?php if( !$this->search ) {
		if( $this->row->childs ) { ?>
		<a href="javascript:void(0);" onclick="Komento.actions.loadReplies(<?php echo $this->row->id; ?>);"><?php echo JText::_('COM_KOMENTO_VIEW_CHILD'); ?></a>
		<?php } else {
			echo JText::_('COM_KOMENTO_NO_CHILD');
		}
	} else {
		if($this->row->parent_id) { ?>
		<a href="<?php echo JRoute::_('index.php?option=com_komento&amp;view=comments&amp;controller=comment&amp;nosearch=1&amp;parentid=' . $this->row->parent_id); ?>"><?php echo JText::_('COM_KOMENTO_VIEW_PARENT'); ?></a>
		<?php } else {
			echo JText::_('COM_KOMENTO_NO_PARENT');
		}
	} ?>
	</td>
	
	<!-- Edit -->	
	<td class="center">
		<a href="<?php echo JRoute::_('index.php?option=com_komento&view=comment&amp;task=edit&amp;c=comment&amp;commentid=' . $this->row->id); ?>"><?php echo JText::_('COM_KOMENTO_COMMENT_EDIT'); ?></a>
	</td>

	<!-- Component -->
	<td class="center">
		<?php echo $this->row->componenttitle; ?>
	</td>

	<!-- Article Title -->
	<td class="center">
		<?php if( $this->row->extension ) { ?>
		<a href="<?php echo $this->row->pagelink; ?>" target=_blank><?php echo $this->row->contenttitle; ?></a>
		<?php } else { ?>
		<span class="error"><?php echo $this->row->contenttitle; ?></span>
		<?php } ?>
	</td>

	<!-- Date -->
	<td class="center">
		<?php echo $this->row->created->toMySQL(); ?>
	</td>

	<!-- Author -->
	<td class="center">
		<?php echo $this->row->name; ?>
	</td>

	<!-- ID -->
	<td class="center">
		<?php if( $this->row->extension ) { ?>
		<a href="<?php echo $this->row->permalink; ?>"><?php echo $this->row->id; ?></a>
		<?php } else { ?>
		<span class="error"><?php echo $this->row->id; ?></span>
		<?php } ?>
	</td>
</tr>
