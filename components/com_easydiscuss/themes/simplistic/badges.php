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
EasyDiscuss.ready(function($){

	// Add active class to the first filter.
	$( '.badgeFilters' ).children( ':first' ).addClass( 'active' );

	$( '.filterAll' ).bind( 'click' , function(){
		$( '.badgeFilters' ).children().removeClass( 'active' );
		$( this ).parent().addClass( 'active' );
		$( '.badgeList' ).children().show();
	});

	$( '.filterMine' ).bind( 'click' , function(){
		$( '.badgeFilters' ).children().removeClass( 'active' );
		$( this ).parent().addClass( 'active' );
		$( '.badgeList' ).children().hide();
		$( '.badgeList' ).children( '.is-earned' ).show();
	});
});
</script>
<div class="fd-cf">
	<h2 class="discuss-component-title pull-left"><?php echo JText::_('COM_EASYDISCUSS_BADGES'); ?></h2>

	<div class="discuss-filter pull-right">
		<ul class="nav nav-pills badgeFilters">
			<li class="badge-all"><a href="javascript:void(0);" class="filterAll"><?php echo JText::_( 'COM_EASYDISCUSS_ALL_BADGES' );?></a></li>
			<?php if( $system->my->id > 0 ){ ?>
			<li class="badge-mine"><a href="javascript:void(0);" class="filterMine"><?php echo JText::_( 'COM_EASYDISCUSS_MY_BADGES' );?></a></li>
			<?php } ?>
		</ul>
	</div>
</div>
<hr />

<div class="tab-content">
	<?php if( $badges ){ ?>
		<ul class="unstyled clearfix discuss-badges-list badgeList">
		<?php foreach( $badges as $badge ){ ?>
			<li class="badge-row<?php echo $badge->achieved( $system->my->id ) ? ' is-earned' : '';?>">
				<div class="discuss-item<?php echo $badge->achieved( $system->my->id ) ? ' is-earned' : '';?>">
					<div class="discuss-status">
						<i class="icon-ed-earned" rel="ed-tooltip" data-placement="top" data-original-title="Earned!" ></i>
					</div>
					<div class="discuss-item-left">
						<div class="discuss-avatar avatar-medium mb-10">
							<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=badges&layout=listings&id=' . $badge->get( 'id' ) );?>" ><img src="<?php echo $badge->getAvatar();?>" width="48" height="48" alt="<?php echo $this->escape( $badge->get( 'title' ) );?>" /></a>
						</div>
					</div>

					<div class="discuss-item-right">
						<div class="discuss-story">
							<div class="discuss-story-bd clearfix">
								<div class="ph-10">
									<div class="badges-name">
										<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=badges&layout=listings&id=' . $badge->get( 'id' ) );?>" ><?php echo JText::_( $badge->title );?></a>
										<?php if( $badge->achieved( $system->my->id ) ){ ?>
										<i class="checked pos-a atr" title="<?php echo JText::_( 'COM_EASYDISCUSS_ACHIEVED');?>"></i>
										<?php }?>
									</div>

									<div class="badges-guide"><?php echo JText::_( 'COM_EASYDISCUSS_EARN_THIS_BADGE_WHEN' );?>:</div>
									<div class="badges-desp"><?php echo $badge->get( 'description' );?></div>

									<div class="badges-status">
										<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=badges&layout=listings&id=' . $badge->id ); ?>">
											<?php echo JText::sprintf( 'COM_EASYDISCUSS_BADGE_TOTAL_ACHIEVERS' , $badge->getTotalAchievers() );?>
										</a>
									</div>
								</div>
							</div>

							<div class="discuss-story-ft clearfix hide">
								<div class="discuss-action-options">

								</div>
							</div>
						</div>
					</div>
				</div>
			</li>
		<?php } ?>
		</ul>
	<?php } else { ?>
		<div class="small"><?php echo JText::_( 'COM_EASYDISCUSS_NO_BADGES_CREATED' ); ?></div>
	<?php } ?>
</div>
