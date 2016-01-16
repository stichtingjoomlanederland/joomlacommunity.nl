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
.script( 'favourites' )
.done(function($){

	$( '.layoutList' ).bind( 'click' , function(){
		$( '.discuss-list' ).removeClass("discuss-list-grid")
	});

	$( '.layoutColumn' ).bind( 'click' , function(){
		$( '.discuss-list' ).addClass("discuss-list-grid")
	});

	$( '.viewFavourites' ).implement( EasyDiscuss.Controller.Post.Favourites );
});

</script>
<div class="fd-cf">
	<h2 class="discuss-component-title pull-left"><?php echo JText::_('COM_EASYDISCUSS_FAVOURITES_PAGE_HEADING'); ?></h2>
	<div class="btn-group mr-5 mt-20 hide-phone pull-right" data-toggle="buttons-radio">
		<button type="button" class="btn btn-medium layoutList" ><i class="icon-th-list"></i></button>
		<button type="button" class="btn btn-medium layoutColumn active"><i class="icon-th-large"></i></button>
	</div>
</div>

<hr />

<div class="discuss-favourites-list mt-15">
	<?php if( $posts ){ ?>
	<ul class="unstyled discuss-list discuss-list-grid" itemscope itemtype="http://schema.org/ItemList">
		<?php foreach( $posts as $post ){ ?>
		<li class="discussItem<?php echo $post->id; ?>">
			<div class="discuss-item is-resolved is-read">

				<div class="discuss-favors viewFavourites" data-postid="<?php echo $post->id;?>">
				<a href="javascript:void(0);" class="pull-right btn btn-danger btn-mini btn-remove btnRemove " rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_FAVOURITE_BUTTON_UNFAVOURITE' , true );?>">
					<i></i>
				</a>
				</div>

				<div class="discuss-status ">
					<?php if( $post->isresolve ){ ?>
						<!-- <i class="icon-ed-resolved " rel="ed-tooltip" data-placement="top" data-original-title="Resolved"></i> -->
					<?php } ?>
				</div>

				<div class="discuss-item-right">
					<div class="discuss-story">
						<div class="discuss-story-hd">
							<div class="ph-20">
								<a class="xbreak-word" href="<?php echo DiscussRouter::getPostRoute( $post->id );?>">
									<h2 class="discuss-post-title" itemprop="name">
										<?php echo $post->title; ?>
									</h2>
								</a>
							</div>

						</div><!-- hd -->
						<div class="discuss-story-bd">
							<div class="ph-20">
								<div class="small">
									<?php echo $post->duration; ?>
									<time datetime="<?php echo $this->formatDate( '%Y-%m-%d' , $post->created ); ?>"></time>
								</div>
								<div class="small mt-10">
									<span><?php echo JText::_( 'COM_EASYDISCUSS_POSTED_BY' );?></span>
									<span class="discuss-avatar avatar-small">
										<a href="<?php echo $post->user->getLink();?>">
											<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
											<img src="<?php echo $post->user->getAvatar();?>" alt="<?php echo $this->escape( $post->user->getName() );?>" />
											<?php } else { ?>
											<?php echo $this->escape( $post->user->getName() );?>
											<?php } ?>
										</a>
									</span>
								</div>

							</div>
						</div>

						<div class="discuss-story-ft clearfix">

							<div class="discuss-action-options">

								<div class="pull-right">


								</div><!-- pull-right -->

								<div class="discuss-statistic pull-left">
								<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>" rel="ed-tooltip" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_STAT_TOTAL_REPLIES' , true );?>">
									<i class="icon-comments"></i> <?php echo $post->totalreplies;?>
									</a>
								<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>" rel="ed-tooltip" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_STAT_TOTAL_HITS' , true );?>">
									<i class="icon-bar-chart"></i> <?php echo $post->hits; ?>
									</a>
								<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>" rel="ed-tooltip" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_STAT_TOTAL_FAVOURITES' , true ); ?>">
									<i class="icon-heart"></i> <?php echo $post->totalFavourites ?>
								</a>
								<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>" rel="ed-tooltip" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_STAT_TOTAL_VOTES' , true ); ?>">
									<i class="icon-thumbs-up"></i> <?php echo $post->sum_totalvote; ?>
									</a>

								</div><!-- discuss-statistic -->
							</div>
							<!-- discuss-action-options -->
						</div>
					</div>
				</div>
			</div>
		</li>
		<?php } ?>
	</ul>

	<?php } else { ?><!-- end foreach -->
		<div class="empty">
			<?php echo JText::_( 'COM_EASYDISCUSS_NO_FAVOURITE_POSTS_YET' ); ?>
		</div>
	<?php } ?>
</div>
