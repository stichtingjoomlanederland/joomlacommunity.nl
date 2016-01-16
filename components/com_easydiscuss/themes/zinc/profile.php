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
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=true"></script>
<script type="text/javascript">
EasyDiscuss
.require()
.script( 'profile' )
.done(function($){

	$('.discussProfilePage').implement(
		'EasyDiscuss.Controller.Profile',
		{
			defaultTab: '<?php echo ucfirst( $viewType );?>'
		}
	);
});
</script>
<div class="discuss-profile discussProfilePage" data-id="<?php echo $profile->id;?>">
	<aside>
		<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
		<section class="profile-avatar<?php echo $profile->getRoleLabelClassname(); ?>">
			<img src="<?php echo $profile->getAvatar( false );?>" width="160" alt="<?php echo $this->escape( $profile->getName() );?>" class="avatar" style="border-radius: 50%" />
		</section>
		<?php } ?>

		<?php if ($system->config->get( 'main_ranking' )){ ?>
		<section class="profile-rank">
			<p><strong><?php echo DiscussHelper::getUserRanks( $profile->id ); ?></strong></p>
			<div class="discuss-rank">
				<div style="width: <?php echo DiscussHelper::getUserRankScore( $profile->id ); ?>%"></div>
			</div>
		</section>
		<?php } ?>

		<section class="profile-points">
			<p><strong><?php echo JText::_('COM_EASYDISCUSS_POINTS'); ?></strong></p>
			<div>
				<b style="font-size: 20px"><?php echo $profile->getPoints(); ?></b>
				<span class="muted"><?php echo JText::_('COM_EASYDISCUSS_POINTS'); ?></span>
			</div>

			<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=points&layout=history&id=' . $profile->id );?>"><?php echo JText::_( 'COM_EASYDISCUSS_VIEW_HISTORY' );?></a>
		</section>

		<section>
			<ul class="list-links discuss-profile-nav reset-ul">
				<?php if( $system->my->id == $profile->id ){ ?>
				<li>
					<a data-foundry-toggle="tab"  href="#subscriptions" class="tabSubscriptions profileTab" data-id="subscriptions">
						<i class="i i-rss"></i>
						<?php echo JText::_( 'COM_EASYDISCUSS_USER_EDIT_SUBSCRIPTIONS');?>
					</a>
				</li>
				<?php } ?>

				<?php if( $system->config->get( 'main_favorite' ) ){ ?>
				<li>
					<a data-foundry-toggle="tab" href="#favourites" class="tabFavourites profileTab" data-id="favourites">
						<i class="i i-heart"></i>
						<?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_TAB_FAVOURITES' );?>
						<span class="muted"><?php echo $profile->getTotalFavourites(); ?></span>
					</a>
				</li>
				<?php } ?>

				<li class="active">
					<a data-foundry-toggle="tab" href="#questions" class="tabQuestions profileTab" data-id="questions">
						<i class="i i-question"></i> 
						<?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_TAB_QUESTIONS' );?>
						<span class="muted"><?php echo $profile->getNumTopicPosted(); ?></span>
					</a>
				</li>

				<li>
					<a data-foundry-toggle="tab" href="#unresolved" class="tabUnresolved profileTab" data-id="unresolved">
						<i class="i i-flag"></i> 
						<?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_TAB_UNRESOLVED' );?>
						<span class="muted"><?php echo $profile->getNumTopicUnresolved(); ?></span>
					</a>
				</li>

				<li>
					<a data-foundry-toggle="tab" href="#replies" class="tabReplies profileTab" data-id="replies">
						<i class="i i-reply-all"></i> 
						<?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_TAB_REPLIES' );?>
						<span class="muted"><?php echo $profile->getNumTopicAnswered(); ?></span>
					</a>
				</li>

				<?php if( $system->config->get( 'main_master_tags' ) ){ ?>
				<li>
					<a data-foundry-toggle="tab" href="#tags" class="tabTags profileTab" data-id="tags">
						<i class="i i-tags"></i> 
						<?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_TAB_TAGS' );?>
						<span class="muted"><?php echo $profile->getTotalTags(); ?></span>
					</a>
				</li>
				<?php } ?>

				<?php if( $system->config->get( 'main_badges' ) ){ ?>
				<li>
					<a data-foundry-toggle="tab" href="#achievements" class="tabAchievements profileTab" data-id="achievements">
						<i class="i i-trophy"></i> 
						<?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_TAB_BADGES' );?>
						<span class="muted"><?php echo count( $badges );?></span>
					</a>
				</li>
				<?php } ?>

				<?php if( $easyblogExists && $system->config->get( 'integrations_easyblog_profile' ) ){ ?>
				<li>
					<a data-foundry-toggle="tab" href="#tabEasyBlog" class="tabEasyBlog profileTab" data-id="tabEasyBlog">
						<i class="i i-list-ul"></i> 
						<?php echo JText::_('COM_EASYDISCUSS_PROFILE_TAB_BLOGS'); ?>
						<?php if( $blogCount > 0 ){ ?>
						<span class="muted"><?php echo $blogCount;?></span>
						<?php } ?>
					</a>
				</li>
				<?php } ?>

				<?php if( $komentoExists && $system->config->get( 'integrations_komento_profile' ) ) { ?>
				<li>
					<a data-foundry-toggle="tab" href="#tabKomento" class="tabKomento profileTab" data-id="tabKomento">
						<i class="i i-comment-alt"></i> <?php echo JText::_('COM_EASYDISCUSS_PROFILE_TAB_COMMENTS'); ?>
						<?php if( $commentCount > 0 ){ ?>
						<span class="muted"><?php echo $commentCount;?></span>
						<?php } ?>
					</a>
				</li>
				<?php } ?>
			</ul>
		</section>

		<?php if( $system->config->get( 'layout_profile_showsocial') ) { ?>
		<?php if( $params->get( 'show_facebook' ) || $params->get( 'show_twitter' ) || $params->get( 'show_linkedin' ) || $params->get( 'show_skype' ) || $params->get( 'show_website' ) ){ ?>
		<section class="profile-social">
			<p><strong><?php echo JText::_( 'COM_EASYDISCUSS_SOCIAL_PROFILES' );?></strong></p>
			<ul class="list-links reset-ul">
				<?php if ($params->get( 'show_facebook' )) { ?>
				<li class="facebook">
					<a data-original-title="<?php echo JText::_('COM_EASYDISCUSS_FACEBOOK'); ?>" data-placement="top" rel="ed-tooltip" href="<?php echo DiscussStringHelper::escape($params->get( 'facebook' )); ?>" target="_blank">
						<i class="i i-facebook"></i> 
						<span><?php echo JText::_('COM_EASYDISCUSS_FACEBOOK'); ?></span>
					</a>
				</li>
				<?php } ?>
				<?php if ($params->get( 'show_twitter' )) { ?>
				<li class="twitter">
					<a data-original-title="<?php echo JText::_('COM_EASYDISCUSS_TWITTER'); ?>" data-placement="top" rel="ed-tooltip" href="<?php echo DiscussStringHelper::escape($params->get( 'twitter' )); ?>" target="_blank">
						<i class="i i-twitter"></i> 
						<span><?php echo JText::_('COM_EASYDISCUSS_TWITTER'); ?></span>
					</a>
				</li>
				<?php } ?>
				<?php if ($params->get( 'show_linkedin' )) { ?>
				<li class="linkedin">
					<a data-original-title="<?php echo JText::_('COM_EASYDISCUSS_LINKEDIN'); ?>" data-placement="top" rel="ed-tooltip" href="<?php echo DiscussStringHelper::escape($params->get( 'linkedin' )); ?>" target="_blank">
						<i class="i i-linkedin"></i> 
						<span><?php echo JText::_('COM_EASYDISCUSS_LINKEDIN'); ?></span>
					</a>
				</li>
				<?php } ?>
				<?php if ($params->get( 'show_skype' )) { ?>
				<li class="skype">
					<a href="skype:<?php echo DiscussStringHelper::escape( $params->get( 'skype' ) );?>?add" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_SKYPE' , true ); ?>" data-placement="top" rel="ed-tooltip">
						<img src="http://mystatus.skype.com/smallicon/<?php echo DiscussStringHelper::escape( $params->get( 'skype' ) );?>" style="border: none;" width="16" height="16" alt="<?php echo JText::_('COM_EASYDISCUSS_SKYPE' , true ); ?>" />
					</a>
				</li>
				<?php } ?>
				<?php if ($params->get( 'show_website' )) { ?>
				<li class="website">
					<a data-original-title="<?php echo JText::_('COM_EASYDISCUSS_WEBSITE'); ?>" data-placement="top" rel="ed-tooltip" href="<?php echo DiscussStringHelper::escape($params->get( 'website' )); ?>" target="_blank">
						<i class="i i-link"></i> 
						<span><?php echo JText::_('COM_EASYDISCUSS_WEBSITE'); ?></span>
					</a>
				</li>
				<?php } ?>
			</ul>
		</section>
		<?php } ?>
		<?php } ?>
	</aside>




	<article>
		<header>
			<h2 style="margin: 0 0 5px;">
				<?php echo DiscussStringHelper::escape($profile->getName());?>
			</h2>

			<?php if($system->config->get( 'layout_profile_roles' ) && $profile->getRole() ) { ?>
			<span><?php echo $this->escape($profile->getRole()); ?></span>
			<?php } ?>

			

			<?php if( $system->config->get( 'main_signature_visibility' ) && DiscussHelper::getHelper('ACL')->allowed('show_signature') ){ ?>
			<div><?php echo DiscussHelper::bbcodeHtmlSwitcher( $profile->getSignature( 'true' ), 'signature', false ); ?></div>
			<?php } ?>

			<p>
				<span><?php echo $this->loadTemplate( 'online.php' , array( 'user' => $profile ) ); ?></span>
				&nbsp;&middot;&nbsp;
				<span class="muted"><?php echo JText::_( 'COM_EASYDISCUSS_REGISTERED_ON' );?></span> <?php echo $profile->getDateJoined(); ?>
				&nbsp;&middot;&nbsp;
				<span class="muted"><?php echo JText::_( 'COM_EASYDISCUSS_LAST_SEEN_ON' );?></span> <?php echo $profile->getLastOnline(); ?>
			</p>

			<?php if( $profile->get( 'description' ) ) { ?>
			<p style="margin: 0 0 15px;"><?php echo DiscussHelper::bbcodeHtmlSwitcher( $profile->get( 'description' ), 'description', false ); ?></p>
			<?php } ?>

			<?php echo $this->loadTemplate( 'post.conversation.php' , array( 'userId' => $profile->id ) ); ?>

			<?php if( $system->config->get( 'main_rss' ) ){ ?>
			<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&id=' . $profile->id . '&format=feed' );?>" class="butt butt-default">
				<i class="i i-rss muted"></i>
				&nbsp;
				<?php echo JText::_( 'COM_EASYDISCUSS_USER_SUBSCRIBE_RSS' );?>
			</a>
			<?php } ?>

			<?php if( $system->my->id == $profile->id && $system->config->get( 'layout_avatarIntegration' ) != 'jomsocial' ){ ?>
			<a class="butt butt-default" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&layout=edit' );?>">
				<i class="i i-pencil muted"></i>
				&nbsp;
				<?php echo JText::_( 'COM_EASYDISCUSS_USER_EDIT_PROFILE');?>
			</a>
			<?php } ?>

			<?php if( $system->my->id != $profile->id && DiscussHelper::isSiteAdmin( $system->my->id ) ){ ?>
			<a class="butt butt-default" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=profile&task=disableUser&id=' . $profile->id );?>">
				<i class="i i-minus muted"></i>
				&nbsp;
				<?php echo JText::_( 'COM_EASYDISCUSS_DISABLE_USER');?>
			</a>
			<?php } ?>
		</header>

		<?php if( !empty( $profile->latitude) && !empty( $profile->longitude) ){ ?>
		<hr>
		<section class="profile-map">
			<?php if( !empty( $profile->location ) ) { ?>
			<p>
				<i class="i i-map-marker"></i>
				&nbsp;
				<?php echo $profile->location; ?>
			</p>
			<?php } ?>
			<script type="text/javascript">
			EasyDiscuss.require().script('legacy').done(function($){
				discuss.map.render( '<?php echo $this->escape( addslashes( $profile->location ) );?>' , '<?php echo $profile->latitude;?>' , '<?php echo $profile->longitude;?>' , 'user-map-area' );
			});
			</script>
			<div id="user-map-area" style="width: 100% !important;height: 130px !important;"></div>
		</section>
		<?php } ?>

		<div class="tab-content">
			<div class="tab-pane tabContents" id="subscriptions"></div>
			<div class="tab-pane tabContents active" id="questions">
				<?php if( count( $posts ) > 0 ) { ?>
					<?php echo $this->loadTemplate( 'profile.questions.php' , array( 'posts' => $posts ) ); ?>

					<?php echo $pagination; ?>
				<?php } ?>
			</div>
			<div class="tab-pane tabContents" id="unresolved">
				<?php if( count( $unresolved ) > 0 ) { ?>
					<?php echo $this->loadTemplate( 'profile.unresolved.php' , array( 'posts' => $unresolved ) ); ?>

					<?php echo $pagination; ?>
				<?php } ?>
			</div>
			<div class="tab-pane tabContents" id="replies">
				<?php if( count( $replies ) > 0 ) { ?>
					<?php echo $this->loadTemplate( 'profile.replies.php' , array( 'posts' => $replies ) ); ?>

					<?php echo $pagination; ?>
				<?php } ?>
			</div>
			<div class="tab-pane tabContents" id="tags"></div>
			<div class="tab-pane tabContents" id="achievements">
				<?php echo $this->loadTemplate( 'profile.badges.php' , array( 'badges' => $badges ) ); ?>
			</div>
			<div class="tab-pane tabContents" id="favourites"></div>
			<!-- Container for EasyBlog posts -->
			<div class="tab-pane tabContents" id="tabEasyBlog"></div>
			<!-- Container for Komento comments -->
			<div class="tab-pane tabContents" id="tabKomento"></div>
		</div>
	</article>
</div>

<div class="clear"></div>

<div class="discuss-profile discussProfilePage" data-id="<?php echo $profile->id;?>">
	<div class="row-fluid">
		<div class="discuss-profile-right">

			

		</div>
	</div>
</div>
<input id="profile-id" value="<?php echo $profile->id; ?>" type="hidden" />
