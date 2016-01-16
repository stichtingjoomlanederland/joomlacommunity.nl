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

$dateContainer = '';

?>
<div class="fd-cf">
<h2 class="discuss-component-title pull-left">
	<?php echo JText::_( 'COM_EASYDISCUSS_POINTS_HISTORY' );?>
</h2>
</div>
<hr>

<?php if( $history ){ ?>
	<?php foreach( $history as $item ){ ?>
		<div class="notification-day">
			<?php if( $dateContainer != $item->created ){ ?>
				<?php $dateContainer = $item->created; ?>
				<div class="day-seperator discuss-post-title"><strong><?php echo $dateContainer; ?></strong></div>
			<?php } ?>
			<ul class="unstyled points-history-list">
				<li class="type-likes-discussion">
					<span>
						<?php ?>
						<span class="badge <?php echo $item->class; ?>"><?php echo $item->points ?></span> <?php echo $item->title; ?>
					</span>
				</li>
			</ul>
		</div>
	<?php } ?>
<?php } ?>
