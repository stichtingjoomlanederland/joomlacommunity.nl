<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="discuss-profile" data-profile-item data-id="<?php echo $profile->id;?>">
	<div class="fd-cf">
		<div class="discuss-profile-left">
			<div class="discuss-user">

				<div class="discuss-avatar<?php echo ($this->config->get('layout_avatar')) ? ' avatar-large' : '' ; ?>">
					<?php if ($this->config->get('layout_avatar')) { ?>
						<img src="<?php echo $profile->getAvatar( false );?>" width="160" alt="<?php echo $this->escape( $profile->getName() );?>" />
					<?php } else { ?>
						<?php echo $this->escape($profile->getName());?>
					<?php } ?>

					<?php if ($this->config->get('layout_profile_roles') && $profile->getRole()) { ?>
					<div class="discuss-role-title <?php echo $profile->getRoleLabelClassname(); ?>"><?php echo $this->escape($profile->getRole()); ?></div>
					<?php } ?>
				</div>

				<?php echo $this->output('site/widgets/online.status', array('user' => $profile)); ?>

				<?php echo $this->output('site/widgets/pm', array('userId' => $profile->id)); ?>

				<?php echo $this->output('site/widgets/ranks.title', array('userId' => $profile->id)); ?>

				<?php if ($this->my->id == $profile->id && ($this->config->get('layout_avatarIntegration') != 'jomsocial' && $this->config->get('layout_avatarIntegration') != 'easysocial')) { ?>
				<div class="mt-10 mb-15">
					<a class="btn btn-small" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&layout=edit' );?>">
						<i class="icon-edit"></i> <?php echo JText::_('COM_EASYDISCUSS_USER_EDIT_PROFILE');?>
					</a>
				</div>
				<?php } ?>

				<?php if( $this->my->id != $profile->id && DiscussHelper::isSiteAdmin( $this->my->id ) ){ ?>
				<div class="mt-10 mb-15">
					<a class="btn-danger btn btn-small" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=profile&task=disableUser&id=' . $profile->id );?>">
						<i class="icon-ban-circle"></i> <?php echo JText::_( 'COM_EASYDISCUSS_DISABLE_USER');?>
					</a>
				</div>
				<?php } ?>

				<hr />

				<div class="discuss-user-point mt-10 mb-15">
					<div class="fs-12"><strong><?php echo JText::_('COM_EASYDISCUSS_POINTS'); ?></strong></div>
					<div class="widget-body">
						<span class="point-value mt-5"><?php echo $profile->getPoints(); ?></span>
						<small><?php echo JText::_('COM_EASYDISCUSS_POINTS'); ?></small>
					</div>

					<div class="small">
						<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=points&layout=history&id=' . $profile->id );?>"><?php echo JText::_( 'COM_EASYDISCUSS_VIEW_HISTORY' );?></a>
					</div>
				</div>
				<?php if( $this->config->get( 'layout_profile_showsocial') ) { ?>
					<?php if( $params->get( 'show_facebook' ) || $params->get( 'show_twitter' ) || $params->get( 'show_linkedin' ) || $params->get( 'show_skype' ) || $params->get( 'show_website' ) ){ ?>
					<hr/>
					<div class="discuss-user-social">
						<div class="fs-12"><strong><?php echo JText::_( 'COM_EASYDISCUSS_SOCIAL_PROFILES' );?></strong></div>
						<div class="mt-10">
							<ul class="unstyled">
								<?php if ($params->get( 'show_facebook' )) { ?>
								<li class="facebook"><a data-original-title="<?php echo JText::_('COM_EASYDISCUSS_FACEBOOK'); ?>" data-placement="top" rel="ed-tooltip" href="<?php echo DiscussStringHelper::escape($params->get( 'facebook' )); ?>" target="_blank"><i class="icon-ed-fb"></i> <span><?php echo JText::_('COM_EASYDISCUSS_FACEBOOK'); ?></span></a></li>
								<?php } ?>

								<?php if ($params->get( 'show_twitter' )) { ?>
								<li class="twitter">
									<a data-original-title="<?php echo JText::_('COM_EASYDISCUSS_TWITTER'); ?>" data-placement="top" rel="ed-tooltip" href="<?php echo DiscussStringHelper::escape($params->get( 'twitter' )); ?>" target="_blank">
										<i class="icon-ed-twitter"></i> <span><?php echo JText::_('COM_EASYDISCUSS_TWITTER'); ?></span>
									</a>
								</li>
								<?php } ?>

								<?php if ($params->get( 'show_linkedin' )) { ?>
								<li class="linkedin"><a data-original-title="<?php echo JText::_('COM_EASYDISCUSS_LINKEDIN'); ?>" data-placement="top" rel="ed-tooltip" href="<?php echo DiscussStringHelper::escape($params->get( 'linkedin' )); ?>" target="_blank"><i class="icon-ed-linkedin"></i> <span><?php echo JText::_('COM_EASYDISCUSS_LINKEDIN'); ?></span></a></li>
								<?php } ?>
								<?php if ($params->get( 'show_skype' )) { ?>
								<li class="skype">
									<a href="skype:<?php echo DiscussStringHelper::escape( $params->get( 'skype' ) );?>?add" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_SKYPE' , true ); ?>" data-placement="top" rel="ed-tooltip">
										<img src="http://mystatus.skype.com/smallicon/<?php echo DiscussStringHelper::escape( $params->get( 'skype' ) );?>" style="border: none;" width="16" height="16" alt="<?php echo JText::_('COM_EASYDISCUSS_SKYPE' , true ); ?>" />
									</a>
								</li>
								<?php } ?>
								<?php if ($params->get( 'show_website' )) { ?>
								<li class="website"><a data-original-title="<?php echo JText::_('COM_EASYDISCUSS_WEBSITE'); ?>" data-placement="top" rel="ed-tooltip" href="<?php echo DiscussStringHelper::escape($params->get( 'website' )); ?>" target="_blank"><i class="icon-ed-website"></i> <span><?php echo JText::_('COM_EASYDISCUSS_WEBSITE'); ?></span></a></li>
								<?php } ?>
							</ul>
						</div>
					</div>
					<?php } ?>
				<?php } ?>
				<hr/>
				<ul class="unstyled discuss-profile-nav">
					<li class="divider-horizontal"></li>
					<?php if( $this->my->id == $profile->id ){ ?>
						<li>
							<a data-foundry-toggle="tab"  href="#subscriptions" class="tabSubscriptions profileTab" data-id="subscriptions">
								<i class="icon-rss"></i> <?php echo JText::_( 'COM_EASYDISCUSS_USER_EDIT_SUBSCRIPTIONS');?>
							</a>
						</li>
					<?php } ?>

					<?php if( $this->config->get( 'main_favorite' ) ){ ?>
					<li>
						<a data-foundry-toggle="tab" href="#favourites" class="tabFavourites profileTab" data-id="favourites">
							<i class="icon-heart"></i> <?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_TAB_FAVOURITES' );?>
							<span class="label label-important label-notification"><?php echo $profile->getTotalFavourites(); ?></span>
						</a>
					</li>
					<?php } ?>

					<li class="active">
						<a data-foundry-toggle="tab" href="#questions" class="tabQuestions profileTab" data-id="questions">
							<i class="icon-columns"></i> <?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_TAB_QUESTIONS' );?>
							<span class="label label-important label-notification"><?php echo $profile->getNumTopicPosted(); ?></span>
						</a>
					</li>
					<?php if($this->config->get('main_qna')) { ?>
					<li>
						<a data-foundry-toggle="tab" href="#unresolved" class="tabUnresolved profileTab" data-id="unresolved">
							<i class="icon-exclamation-sign"></i> <?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_TAB_UNRESOLVED' );?>
							<span class="label label-important label-notification"><?php echo $profile->getNumTopicUnresolved(); ?></span>
						</a>
					</li>
					<?php } ?>
					<li>
						<a data-foundry-toggle="tab" href="#replies" class="tabReplies profileTab" data-id="replies">
							<i class="icon-comments"></i> <?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_TAB_REPLIES' );?>
							<span class="label label-important label-notification"><?php echo $profile->getNumTopicAnswered(); ?></span>
						</a>
					</li>

					<?php if( $this->config->get( 'main_master_tags' ) ){ ?>
						<li>
							<a data-foundry-toggle="tab" href="#tags" class="tabTags profileTab" data-id="tags">
								<i class="icon-tags"></i> <?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_TAB_TAGS' );?>
								<span class="label label-important label-notification"><?php echo $profile->getTotalTags(); ?></span>
							</a>
						</li>
					<?php } ?>

					<?php if( $this->config->get( 'main_badges' ) ){ ?>
					<li>
						<a data-foundry-toggle="tab" href="#achievements" class="tabAchievements profileTab" data-id="achievements">
							<i class="icon-trophy"></i> <?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_TAB_BADGES' );?>
							<span class="label label-important label-notification"><?php echo count( $badges );?></span>
						</a>
					</li>
					<?php } ?>

					<?php if( $easyblogExists && $this->config->get( 'integrations_easyblog_profile' ) ){ ?>
					<li>
						<a data-foundry-toggle="tab" href="#tabEasyBlog" class="tabEasyBlog profileTab" data-id="tabEasyBlog">
							<i class="icon-list-ul"></i> <?php echo JText::_('COM_EASYDISCUSS_PROFILE_TAB_BLOGS'); ?>
							<?php if( $blogCount > 0 ){ ?>
							<span class="label label-important label-notification"><?php echo $blogCount;?></span>
							<?php } ?>
						</a>
					</li>
					<?php } ?>

					<?php if( $komentoExists && $this->config->get( 'integrations_komento_profile' ) ) { ?>
					<li>
						<a data-foundry-toggle="tab" href="#tabKomento" class="tabKomento profileTab" data-id="tabKomento">
							<i class="icon-comment-alt"></i> <?php echo JText::_('COM_EASYDISCUSS_PROFILE_TAB_COMMENTS'); ?>
							<?php if( $commentCount > 0 ){ ?>
							<span class="label label-important label-notification"><?php echo $commentCount;?></span>
							<?php } ?>
						</a>
					</li>
					<?php } ?>

				</ul>
			</div>
		</div>

		<div class="discuss-profile-right">
			<div class="fd-cf">
				<div>
					<?php if( $this->config->get( 'main_rss' ) ){ ?>
						<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&id=' . $profile->id . '&format=feed' );?>" class="via-feed pull-right"><i class="icon-ed-rss"></i> <?php echo JText::_( 'COM_EASYDISCUSS_USER_SUBSCRIBE_RSS' );?></a>
					<?php } ?>
					
					<h2><?php echo ED::string()->escape($profile->getName());?></h2>

					<?php if ($this->config->get('main_signature_visibility') && $this->acl->allowed('show_signature')) { ?>
						<?php echo $this->output('site/widgets/signature', array('profile' => $profile)); ?>
					<?php } ?>
					<hr />
					
					<p class="profile-desp">
						<i class="icon-user mr-5"></i>
						<?php echo JText::_('COM_EASYDISCUSS_REGISTERED_ON');?> - <?php echo $profile->getDateJoined(); ?>

						<i class="icon-signin ml-20 mr-5"></i>
						<?php echo JText::_('COM_EASYDISCUSS_LAST_SEEN_ON' );?> - <?php echo $profile->getLastOnline(); ?>
					</p>
				</div>

				<?php if( !empty( $profile->latitude) && !empty( $profile->longitude) ){ ?>
				<div class="discuss-user-map mt-5 mb-15">
					<?php if( !empty( $profile->location ) ) { ?>
					<div class="mb-5">
						<i class="icon-map-marker mr-5"></i><?php echo $profile->location; ?>
					</div>
					<?php } ?>
					<script type="text/javascript">
					EasyDiscuss.ready(function($){
						discuss.map.render( '<?php echo $this->escape( addslashes( $profile->location ) );?>' , '<?php echo $profile->latitude;?>' , '<?php echo $profile->longitude;?>' , 'user-map-area' );
					});
					</script>
					<div id="user-map-area" style="width: 100% !important;height: 130px !important;"></div>

				</div>
				<?php } ?>

				<?php if( $profile->get( 'description' ) ) { ?>

				<div class="user-intro mt-5 mb-10"><?php echo DiscussHelper::bbcodeHtmlSwitcher( $profile->get( 'description' ), 'description', false ); ?></div>
				<?php } ?>
			</div>

			<hr />

			<div class="tab-content">
				<div class="tab-pane tabContents" id="subscriptions"></div>

				<div class="tab-pane tabContents active" id="questions">
					<?php if ($posts) { ?>
						<?php echo $this->output('site/profile/questions', array('posts' => $posts)); ?>
						<?php echo $pagination; ?>
					<?php } ?>
				</div>

				<div class="tab-pane tabContents" id="unresolved">
					<?php if ($unresolved) { ?>
						<?php echo $this->output('site/profile/unresolved', array('posts' => $unresolved)); ?>

						<?php echo $pagination; ?>
					<?php } ?>
				</div>

				<div class="tab-pane tabContents" id="replies">
					<?php if ($replies) { ?>
						<?php echo $this->output('site/profile/replies', array('posts' => $replies)); ?>

						<?php echo $pagination; ?>
					<?php } ?>
				</div>

				<div class="tab-pane tabContents" id="tags">
				</div>

				<div class="tab-pane tabContents" id="achievements">
					<?php echo $this->output('site/profile/badges', array('badges' => $badges)); ?>
				</div>

				<div class="tab-pane tabContents" id="favourites">
				</div>

				<div class="tab-pane tabContents" id="tabEasyBlog">
				</div>

				<div class="tab-pane tabContents" id="tabKomento">
				</div>
			</div>
		</div>
	</div>
</div>
<input id="profile-id" value="<?php echo $profile->id; ?>" type="hidden" />