<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="row-fluid">
	<div class="span12 panel-title">
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_WORKFLOW_TITLE' );?></h2>
		<p style="margin: 0 0 15px;"><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_WORKFLOW_DESC' );?></p>
	</div>
</div>


<div class="row-fluid">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_MAIN' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_TITLE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_TITLE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_MAIN_TITLE_DESC'); ?>"
							>
							<input type="text" name="main_title" class="input-full" size="60" value="<?php echo $this->config->get('main_title' );?>" />

						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_DESCRIPTION' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_DESCRIPTION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_MAIN_DESCRIPTION_DESC'); ?>"
						>
							<textarea name="main_description" class="input-full" cols="65" rows="5"><?php echo $this->config->get( 'main_description' ); ?></textarea>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SHOW_FRONTEND_STATISTICS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SHOW_FRONTEND_STATISTICS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SHOW_FRONTEND_STATISTICS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_frontend_statistics' , $this->config->get( 'main_frontend_statistics' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SHOW_BACKEND_STATISTICS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SHOW_BACKEND_STATISTICS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SHOW_BACKEND_STATISTICS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_backend_statistics' , $this->config->get( 'main_backend_statistics' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_EXCLUDE_GROUP_FRONTEND_STATISTICS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_EXCLUDE_GROUP_FRONTEND_STATISTICS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_EXCLUDE_GROUP_FRONTEND_STATISTICS_DESC'); ?>"
						>
							<input type="text" name="main_exclude_frontend_statistics" class="input-full" size="60" value="<?php echo $this->config->get( 'main_exclude_frontend_statistics' ); ?>" />
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option03">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_POST' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div id="option03" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_RSS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_RSS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_RSS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_rss' , $this->config->get( 'main_rss' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_PRIVATE_POSTINGS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_PRIVATE_POSTINGS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_PRIVATE_POSTINGS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_private_post' , $this->config->get( 'main_private_post' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_QNA' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_QNA' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_QNA_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_qna' , $this->config->get( 'main_qna' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ALLOW_REGISTERED_USER_CREATE_TAG' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ALLOW_REGISTERED_USER_CREATE_TAG' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ALLOW_REGISTERED_USER_CREATE_TAG_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_allowcreatetag' , $this->config->get( 'main_allowcreatetag' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_REDIRECTION_AFTER_POST' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_REDIRECTION_AFTER_POST' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_REDIRECTION_AFTER_POST_DESC'); ?>"
						>
							<?php
								$sortingType = array();
								$sortingType[] = JHTML::_('select.option', 'default', JText::_( 'COM_EASYDISCUSS_REDIRECTION_DEFAULT' ) );
								$sortingType[] = JHTML::_('select.option', 'home', JText::_( 'COM_EASYDISCUSS_REDIRECTION_HOME' ) );
								$sortingType[] = JHTML::_('select.option', 'mainCategory', JText::_( 'COM_EASYDISCUSS_REDIRECTION_ALL_CATEGORIES' ) );
								$sortingType[] = JHTML::_('select.option', 'currentCategory', JText::_( 'COM_EASYDISCUSS_REDIRECTION_CURRENT_CATEGORY' ) );
								$categorySortHTML = JHTML::_('select.genericlist', $sortingType, 'main_post_redirection', 'class="full-width" size="1" ', 'value', 'text', $this->config->get('main_post_redirection' , 'default' ) );
								echo $categorySortHTML;
							?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_PRINT_BUTTON' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_PRINT_BUTTON' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_PRINT_BUTTON_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_enable_print' , $this->config->get( 'main_enable_print' ) );?>
						</div>
					</div>

				</div>
			</div>
		</div>

		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option04">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_ADVANCE_POST' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div id="option04" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_LINK_NEW_WINDOW' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LINK_NEW_WINDOW' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_LINK_NEW_WINDOW_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_link_new_window' , $this->config->get( 'main_link_new_window' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_MODERATE_NEW_POST' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_MODERATE_NEW_POST' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_MODERATE_NEW_POST_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_moderatepost' , $this->config->get( 'main_moderatepost' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_MODERATION_THRESHOLD' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_MODERATION_THRESHOLD' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_MODERATION_THRESHOLD_DESC'); ?>"
						>
							<input type="text" name="moderation_threshold" class="input-mini center" value="<?php echo $this->config->get('moderation_threshold');?>" />
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SYNTAX_HIGHLIGHTER' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SYNTAX_HIGHLIGHTER' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SYNTAX_HIGHLIGHTER_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_syntax_highlighter' , $this->config->get( 'main_syntax_highlighter' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_AUTOLOCK_NEWPOST_ONLY' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_AUTOLOCK_NEWPOST_ONLY' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_AUTOLOCK_NEWPOST_ONLY_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_lock_newpost_only' , $this->config->get( 'main_lock_newpost_only' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_DAYSTOLOCK_REPLIED' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_DAYSTOLOCK_REPLIED' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_DAYSTOLOCK_REPLIED_DESC'); ?>"
						>
							<input type="text" name="main_daystolock_afterlastreplied" class="input-mini center" value="<?php echo $this->config->get('main_daystolock_afterlastreplied');?>" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_POST_MIN_LENGTH' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_POST_MIN_LENGTH' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_MAIN_POST_MIN_LENGTH_DESC'); ?>"
						>
							<input type="text" name="main_post_min_length" class="input-mini center" value="<?php echo $this->config->get('main_post_min_length' );?>" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_DAYSTOLOCK_CREATED' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_DAYSTOLOCK_CREATED' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_DAYSTOLOCK_CREATED_DESC'); ?>"
						>
							<input type="text" name="main_daystolock_aftercreated" class="input-mini center" value="<?php echo $this->config->get('main_daystolock_aftercreated');?>" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div><!--Left bar-->
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option05">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_SIMILAR_QUESTION' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div id="option05" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SIMILAR_QUESTION_ENABLE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SIMILAR_QUESTION_ENABLE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SIMILAR_QUESTION_ENABLE'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_similartopic' , $this->config->get( 'main_similartopic' ) );?>

						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SIMILAR_QUESTION_INCLUDE_PRIVATE_POSTS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SIMILAR_QUESTION_INCLUDE_PRIVATE_POSTS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SIMILAR_QUESTION_INCLUDE_PRIVATE_POSTS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_similartopic_privatepost' , $this->config->get( 'main_similartopic_privatepost' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SIMILAR_QUESTION_SEARCH_LIMIT' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SIMILAR_QUESTION_SEARCH_LIMIT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SIMILAR_QUESTION_SEARCH_LIMIT_DESC'); ?>"
						>
							<input type="text" name="main_similartopic_limit" class="input-mini center" value="<?php echo $this->config->get('main_similartopic_limit' , '5' );?>" />
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option06">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_WHOS_VIEWING' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div id="option06" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_WHOS_VIEWING' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_WHOS_VIEWING' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_WHOS_VIEWING_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_viewingpage' , $this->config->get( 'main_viewingpage' ) );?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option07">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_VIDEO_EMBEDDING' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div id="option07" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_VIDEO_WIDTH' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_VIDEO_WIDTH' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_VIDEO_WIDTH_DESC'); ?>"
						>
							<input type="text" class="input-mini" name="bbcode_video_width" value="<?php echo $this->config->get( 'bbcode_video_width' );?>" size="5" style="text-align:center;" />
							<?php echo JText::_( 'COM_EASYDISCUSS_PIXELS' ); ?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_VIDEO_HEIGHT' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_VIDEO_HEIGHT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_VIDEO_HEIGHT_DESC'); ?>"
						>
							<input type="text" class="input-mini" name="bbcode_video_height" value="<?php echo $this->config->get( 'bbcode_video_height' );?>" size="5" style="text-align:center;" />
							<?php echo JText::_( 'COM_EASYDISCUSS_PIXELS' ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option09">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_LIKES' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div id="option09" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_LIKES_DISCUSSIONS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_LIKES_DISCUSSIONS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_LIKES_DISCUSSIONS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_likes_discussions' , $this->config->get( 'main_likes_discussions' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_LIKES_REPLIES' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_LIKES_REPLIES' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_LIKES_REPLIES_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_likes_replies' , $this->config->get( 'main_likes_replies' ) );?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option10">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_FAVOURITES' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div id="option10" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_FAVOURITES_DISCUSSIONS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_FAVOURITES_DISCUSSIONS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_FAVOURITES_DISCUSSIONS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_favorite' , $this->config->get( 'main_favorite' ) );?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option11">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_RANKING' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div id="option11" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_RANKING' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_RANKING' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_RANKING_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_ranking' , $this->config->get( 'main_ranking' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_RANKING_CALCULATION' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_RANKING_CALCULATION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_RANKING_CALCULATION_DESC'); ?>"
						>
							<select name="main_ranking_calc_type" id="main_ranking_calc_type" class="full-width">
								<option value="posts" <?php echo ($this->config->get( 'main_ranking_calc_type' ) == 'posts') ? 'selected="selected"' : '' ?> ><?php echo JText::_('COM_EASYDISCUSS_RANKING_TYPE_POSTS'); ?></option>
								<option value="points" <?php echo ($this->config->get( 'main_ranking_calc_type' ) == 'points') ? 'selected="selected"' : '' ?>><?php echo JText::_('COM_EASYDISCUSS_RANKING_TYPE_POINTS'); ?></option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option12">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_LOGIN_PROVIDER' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div id="option12" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SELECT_LOGIN_PROVIDER' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SELECT_LOGIN_PROVIDER' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SELECT_LOGIN_PROVIDER_DESC'); ?>"
						>
							<select name="main_login_provider" class="full-width" >
								<option value="easysocial"<?php echo $this->config->get( 'main_login_provider' ) == 'easysocial' ? ' selected="selected"' : '';?>><?php echo JText::_( 'EasySocial' );?></option>
								<option value="joomla"<?php echo $this->config->get( 'main_login_provider' ) == 'joomla' ? ' selected="selected"' : '';?>><?php echo JText::_( 'Joomla' );?></option>
								<option value="jomsocial"<?php echo $this->config->get( 'main_login_provider' ) == 'jomsocial' ? ' selected="selected"' : '';?>><?php echo JText::_( 'JomSocial' );?></option>
								<option value="cb"<?php echo $this->config->get( 'main_login_provider' ) == 'cb' ? ' selected="selected"' : '';?>><?php echo JText::_( 'Community Builder' );?></option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
