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
<h2 class="discuss-component-title"><?php echo JText::_('COM_EASYDISCUSS_ASSIGNED_MY_POST'); ?></h2>
<hr />
<div class="discuss-assigned-stats">

	<div class="fd-cf discuss-stat-items">
		<div class="discuss-stat-item">
			<div class="discuss-stat-icon pull-left">
				<span><i class="icon-file"></i></span>
			</div>
			<ul class="unstyled">
				<li class="discuss-stat-no"><?php echo $totalAssigned;?></li>
				<li class="discuss-stat-title">
					<?php echo JText::_( 'COM_EASYDISCUSS_TOTAL_ASSIGNED_POSTS' );?>
				</li>
			</ul>
		</div>

		<div class="discuss-stat-item">
			<div class="discuss-stat-icon pull-left">
				<span><i class="icon-check"></i></span>
			</div>
			<ul class="unstyled">
				<li class="discuss-stat-no"><?php echo $totalResolved;?></li>
				<li class="discuss-stat-title">
					<?php echo JText::_( 'COM_EASYDISCUSS_TOTAL_SOLVED_POSTS' );?>
				</li>
			</ul>
		</div>

		<div class="discuss-stat-item">
			<div class="discuss-stat-icon pull-left">
				<span><i class="icon-repeat"></i></span>
			</div>
			<ul class="unstyled">
				<li class="discuss-stat-no"><?php echo $percentage;?>%</li>
				<li class="discuss-stat-title">
					<?php echo JText::_( 'COM_EASYDISCUSS_YOUR_OVERALL_PROGRESS' );?>
				</li>
			</ul>
		</div>
	</div>

	<?php if( $posts ){ ?>
	<div class="progress">
		<div class="bar bar-success" style="width: <?php echo $percentage;?>%;"><?php echo JText::_( 'COM_EASYDISCUSS_SOLVED' );?></div>
		<div class="bar bar-danger" style="width: <?php echo 100 - $percentage;?>%;"><?php echo JText::_( 'COM_EASYDISCUSS_UNRESOLVED' );?></div>
	</div>
	<?php } ?>
</div>

<hr />
<?php if( $posts ) { ?>
	<ul class="unstyled discuss-list featured clearfix" itemscope itemtype="http://schema.org/ItemList">
	<?php foreach( $posts as $post) { ?>
		<?php echo $this->loadTemplate( 'frontpage.post.php' , array( 'post' => $post ) ); ?>
	<?php } ?>
	</ul>
<?php } else { ?>
	<div class="empty"><?php echo JText::_('COM_EASYDISCUSS_ASSIGNED_NOT_FOUND');?></div>
<?php } ?>
