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
<header>
	<h2><?php echo JText::_('COM_EASYDISCUSS_BADGES'); ?></h2>
</header>

<article id="dc_badges">
	<div class="discuss-filter filter-badges">
		<ul class="reset-ul float-li clearfix badgeFilters">
			<li class="badge-all">
				<a href="javascript:void(0);" class="butt butt-default filterAll"><?php echo JText::_( 'COM_EASYDISCUSS_ALL_BADGES' );?></a>
			</li>
			<?php if( $system->my->id > 0 ){ ?>
			<li class="badge-mine">
				<a href="javascript:void(0);" class="butt butt-default filterMine"><?php echo JText::_( 'COM_EASYDISCUSS_MY_BADGES' );?></a>
			</li>
			<?php } ?>
		</ul>
	</div>

	<div class="tab-content">
		<?php if( $badges ){ ?>
			<ul class="discuss-grid grid-badges reset-ul float-li clearfix badgeList">
			<?php foreach( $badges as $badge ){ ?>
				<li class="badge-item<?php echo $badge->achieved( $system->my->id ) ? ' is-earned' : '';?>">
					<div>
							<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=badges&layout=listings&id=' . $badge->get( 'id' ) );?>" class="discuss-avatar avatar-badge">
								<img src="<?php echo $badge->getAvatar();?>" alt="<?php echo $this->escape( $badge->get( 'title' ) );?>" />
							</a>

							<div>
								<div class="badge-name">
									<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=badges&layout=listings&id=' . $badge->get( 'id' ) );?>" >
										<b><?php echo JText::_( $badge->title );?></b>
									</a>
									<?php if( $badge->achieved( $system->my->id ) ){ ?>
									<small class="muted"><?php echo JText::_( 'COM_EASYDISCUSS_ACHIEVED');?></small>
									<?php }?>
								</div>

								<div class="muted"><?php echo JText::_( 'COM_EASYDISCUSS_EARN_THIS_BADGE_WHEN' );?>:</div>
								<div class="badge-brief"><?php echo $badge->get( 'description' );?></div>

								<div class="badge-status">
									<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=badges&layout=listings&id=' . $badge->id ); ?>">
										<?php echo JText::sprintf( 'COM_EASYDISCUSS_BADGE_TOTAL_ACHIEVERS' , $badge->getTotalAchievers() );?>
									</a>
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
</article>