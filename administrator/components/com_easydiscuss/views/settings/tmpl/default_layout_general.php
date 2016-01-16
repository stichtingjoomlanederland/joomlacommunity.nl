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
<script type="text/javascript">

EasyDiscuss
.require()
.script('stylesheet' )
.done(function($){
	$( '.testCompile' ).implement( EasyDiscuss.Controller.Post.Stylesheet );
});

</script>

<div class="row-fluid">
	<div class="span12 panel-title">
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LAYOUT_GENERAL_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_LAYOUT_GENERAL_DESC' );?>
		</p>
	</div>
</div>

<div class="row-fluid ">
	<div class="span6">

		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_THEME' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_DISCUSSION_THEME' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_DISCUSSION_THEME' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_DISCUSSION_THEME_DESC'); ?>"
						>
							<?php echo $this->getThemes( $this->config->get('layout_site_theme', 'default') ); ?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_COMPILE_STYLESHEET' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_COMPILE_STYLESHEET' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_COMPILE_STYLESHEET_DESC'); ?>"
						>
							<?php
								$compileModes= array();
								$compileModes[] = JHTML::_('select.option', 'off'  , JText::_( 'COM_EASYDISCUSS_COMPILE_STYLESHEET_OFF' ) );
								$compileModes[] = JHTML::_('select.option', 'cache', JText::_( 'COM_EASYDISCUSS_COMPILE_STYLESHEET_CACHE' ) );
								$compileModes[] = JHTML::_('select.option', 'force' , JText::_( 'COM_EASYDISCUSS_COMPILE_STYLESHEET_FORCE' ) );
								echo JHTML::_('select.genericlist', $compileModes, 'layout_compile_mode', 'class="full-width" size="1"', 'value', 'text', $this->config->get('layout_compile_mode', 'cache') );
							?>
							<div class="notice">
								<p><strong><?php echo JText::_('COM_EASYDISCUSS_COMPILE_STYLESHEET_OFF'); ?></strong> - <?php echo JText::_('COM_EASYDISCUSS_COMPILE_STYLESHEET_OFF_DESC'); ?></p>
								<p><strong><?php echo JText::_('COM_EASYDISCUSS_COMPILE_STYLESHEET_CACHE'); ?></strong> - <?php echo JText::_('COM_EASYDISCUSS_COMPILE_STYLESHEET_CACHE_DESC'); ?></p>
								<p><strong><?php echo JText::_('COM_EASYDISCUSS_COMPILE_STYLESHEET_FORCE'); ?></strong> - <?php echo JText::_('COM_EASYDISCUSS_COMPILE_STYLESHEET_FORCE_DESC'); ?></p>
							</div>
						</div>
					</div>
					<div class="testCompile">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label><?php echo JText::_( 'COM_EASYDISCUSS_COMPILE_STYLESHEET_TESTER' ); ?></label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_TEST_COMPILE_STYLESHEET' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_TEST_COMPILE_STYLESHEET_DESC'); ?>"
						>
							<div class="">
								<select name="type" id="compileType" size="1" class="full-width">
									<option value="admin"><?php echo JText::_( 'COM_EASYDISCUSS_ADMIN' ); ?></option>
									<option value="site"><?php echo JText::_( 'COM_EASYDISCUSS_SITE' ); ?></option>
									<option value="module"><?php echo JText::_( 'COM_EASYDISCUSS_MODULE' ); ?></option>
								</select>
							</div>

							<button type="button" class="btn compileButton"><?php echo JText::_( 'COM_EASYDISCUSS_TEST_COMPILE' );?></button>
							<div class="compileResult compile-result"><!-- text-error and text-success -->
							</div>
						</div>
					</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_CDN_PATH' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_CDN_PATH' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_CDN_PATH_DESC'); ?>"
						>
							<?php
								$cdnOption = array();
								$cdnOption[] = JHTML::_('select.option', 'name', JText::_( 'COM_EASYDISCUSS_CDN_PATH_RELATIVE' ) );
								$cdnOption[] = JHTML::_('select.option', 'username', JText::_( 'COM_EASYDISCUSS_CDN_PATH_ABSOLUTE' ) );
								$showdet = JHTML::_('select.genericlist', $cdnOption, 'layout_compile_external_asset_path_type', 'class="full-width" size="1" ', 'value', 'text', $this->config->get('layout_compile_external_asset_path_type' , 'relative' ) );
								echo $showdet;
							?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option02">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_DISPLAY' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option02" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_LIST_LIMIT' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LIST_LIMIT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_LIST_LIMIT_DESC'); ?>"
						>
							<input type="text" name="layout_list_limit" value="<?php echo $this->config->get('layout_list_limit' );?>" size="5" style="text-align:center;" class="input-mini" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_REPLIES_LIST_LIMIT' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_REPLIES_LIST_LIMIT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_REPLIES_LIST_LIMIT_DESC'); ?>"
						>
							<input type="text" name="layout_replies_list_limit" value="<?php echo $this->config->get('layout_replies_list_limit' );?>" size="5" style="text-align:center;" class="input-mini" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SHOW_ONLINE_STATE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SHOW_ONLINE_STATE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SHOW_ONLINE_STATE_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_user_online' , $this->config->get( 'layout_user_online' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_REPLIES_ENABLE_PAGINATION' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_REPLIES_ENABLE_PAGINATION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_REPLIES_ENABLE_PAGINATION_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_replies_pagination' , $this->config->get( 'layout_replies_pagination' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_ZERO_AS_PLURAL' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_ZERO_AS_PLURAL' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_ZERO_AS_PLURAL_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_zero_as_plural' , $this->config->get( 'layout_zero_as_plural' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_DATE_FORMAT' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_DATE_FORMAT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_DATE_FORMAT_DESC'); ?>"
						>
							<input type="text" name="layout_dateformat"  style="width: 150px;" value="<?php echo $this->config->get('layout_dateformat' , '%b %d, %Y' );?>" />
							<br/>
							<a href="http://php.net/manual/en/function.strftime.php" target="_blank" class="extra_text"><?php echo JText::_('COM_EASYDISCUSS_DATE_FORMAT'); ?></a>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_AUTO_MINIMISE_POST_IF_HIT_MINIMUM_VOTE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_AUTO_MINIMISE_POST_IF_HIT_MINIMUM_VOTE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_AUTO_MINIMISE_POST_IF_HIT_MINIMUM_VOTE_DESC'); ?>"
						>
							<input type="text" name="layout_autominimisepost"  style="width: 80px;" value="<?php echo $this->config->get('layout_autominimisepost' , '5' );?>" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_NUMBER_OF_DAYS_A_POST_STAY_AS_NEW' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_NUMBER_OF_DAYS_A_POST_STAY_AS_NEW' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_NUMBER_OF_DAYS_A_POST_STAY_AS_NEW_DESC'); ?>"
						>
							<input type="text" name="layout_daystostaynew"  style="width: 80px;" value="<?php echo $this->config->get('layout_daystostaynew' , '7' );?>" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_DISPLAY_NAME_FORMAT' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_DISPLAY_NAME_FORMAT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_DISPLAY_NAME_FORMAT_DESC'); ?>"
						>
							<?php
								$nameFormat = array();
								$nameFormat[] = JHTML::_('select.option', 'name', JText::_( 'COM_EASYDISCUSS_DISPLAY_NAME_FORMAT_REAL_NAME' ) );
								$nameFormat[] = JHTML::_('select.option', 'username', JText::_( 'COM_EASYDISCUSS_DISPLAY_NAME_FORMAT_USERNAME' ) );
								$nameFormat[] = JHTML::_('select.option', 'nickname', JText::_( 'COM_EASYDISCUSS_DISPLAY_NAME_FORMAT_NICKNAME' ) );
								$showdet = JHTML::_('select.genericlist', $nameFormat, 'layout_nameformat', 'class="full-width" size="1" ', 'value', 'text', $this->config->get('layout_nameformat' , 'name' ) );
								echo $showdet;
							?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_INTROTEXT' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_INTROTEXT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_INTROTEXT_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_enableintrotext' , $this->config->get( 'layout_enableintrotext' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_INTROTEXT_LENGTH' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_INTROTEXT_LENGTH' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_INTROTEXT_LENGTH_DESC'); ?>"
						>
							<input type="text" name="layout_introtextlength"  style="width: 80px;" value="<?php echo $this->config->get('layout_introtextlength' , '200' );?>" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_WRAPPERCLASS_SFX' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_WRAPPERCLASS_SFX' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_WRAPPERCLASS_SFX_DESC'); ?>"
						>
							<input type="text" name="layout_wrapper_sfx"  style="width: 150px;" value="<?php echo $this->config->get('layout_wrapper_sfx' , '' );?>" />
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_FRONTPAGE_SORTING_TAB' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_FRONTPAGE_SORTING_TAB' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_FRONTPAGE_SORTING_TAB_DESC'); ?>"
						>
							<?php
								$filterFormat = array();
								$filterFormat[] = JHTML::_('select.option', 'all', JText::_( 'COM_EASYDISCUSS_FRONTPAGE_SORTING_BY_ALL' ) );
								$filterFormat[] = JHTML::_('select.option', 'mine', JText::_( 'COM_EASYDISCUSS_FRONTPAGE_SORTING_BY_MINE' ) );

								$showdet = JHTML::_('select.genericlist', $filterFormat, 'layout_frontpage_sorting', 'class="full-width" size="1" ', 'value', 'text', $this->config->get('layout_frontpage_sorting' , 'all' ) );
								echo $showdet;
							?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_REPLIES_SORTING_TAB' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_REPLIES_SORTING_TAB' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_REPLIES_SORTING_TAB_DESC'); ?>"
						>
							<?php
								$filterFormat = array();
								$filterFormat[] = JHTML::_('select.option', 'oldest', JText::_( 'COM_EASYDISCUSS_REPLIES_SORTING_BY_OLDEST' ) );
								$filterFormat[] = JHTML::_('select.option', 'latest', JText::_( 'COM_EASYDISCUSS_REPLIES_SORTING_BY_LATEST' ) );
								$filterFormat[] = JHTML::_('select.option', 'voted', JText::_( 'COM_EASYDISCUSS_REPLIES_SORTING_BY_VOTED' ) );
								$filterFormat[] = JHTML::_('select.option', 'likes', JText::_( 'COM_EASYDISCUSS_REPLIES_SORTING_BY_LIKES' ) );
								$showdet = JHTML::_('select.genericlist', $filterFormat, 'layout_replies_sorting', 'class="full-width" size="1" ', 'value', 'text', $this->config->get('layout_replies_sorting' , 'latest' ) );
								echo $showdet;
							?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_BOARD_STATISTICS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_BOARD_STATISTICS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_BOARD_STATISTICS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_board_stats' , $this->config->get( 'layout_board_stats' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SIGNATURE_ENABLE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SIGNATURE_ENABLE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SIGNATURE_ENABLE_NOTICE'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_signature_visibility' , $this->config->get( 'main_signature_visibility' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_RESPONSIVE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_RESPONSIVE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_RESPONSIVE_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_responsive' , $this->config->get( 'main_responsive' ) );?>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option03">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_FILTERS' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option03" class="accordion-body collapse in">
				<div class="wbody">

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_FILTER_NEW' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_FILTER_NEW' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_FILTER_NEW_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_enablefilter_new' , $this->config->get( 'layout_enablefilter_new' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_FILTER_UNRESOLVED' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_FILTER_UNRESOLVED' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_FILTER_UNRESOLVED_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_enablefilter_unresolved' , $this->config->get( 'layout_enablefilter_unresolved' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_FILTER_UNANSWERED' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_FILTER_UNANSWERED' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_FILTER_UNANSWERED_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_enablefilter_unanswered' , $this->config->get( 'layout_enablefilter_unanswered' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_FILTER_RESOLVED' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_FILTER_RESOLVED' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_FILTER_RESOLVED_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_enablefilter_resolved' , $this->config->get( 'layout_enablefilter_resolved' ) );?>
						</div>
					</div>

				</div>
			</div>
		</div>


		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#editor">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_EDITING_AREA' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div id="editor" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_DISCUSSION_EDITOR' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_DISCUSSION_EDITOR' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_DISCUSSION_EDITOR_DESC'); ?>"
						>
							<?php echo $this->getEditorList( $this->config->get('layout_editor') ); ?>
						</div>
					</div>
 					<div class="si-form-row">
 						<div class="span5 form-row-label">
 							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_DISCUSSION_REPLY_EDITOR' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_DISCUSSION_REPLY_EDITOR' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_DISCUSSION_REPLY_EDITOR_DESC'); ?>"
						>
							<?php echo $this->getEditorList( $this->config->get('layout_reply_editor'), 'layout_reply_editor' ); ?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_DISCUSSION_CATEGORY_SELECTION_TYPE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_DISCUSSION_CATEGORY_SELECTION_TYPE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_DISCUSSION_CATEGORY_SELECTION_TYPE_DESC'); ?>"
						>
							<?php echo $this->getCategorySelection( $this->config->get('layout_category_selection'), 'layout_category_selection' ); ?>

							<div class="notice">
								<?php echo JText::_('COM_EASYDISCUSS_DISCUSSION_CATEGORY_SELECTION_TYPE_NOTE'); ?>
							</div>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POST_TYPES' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POST_TYPES' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_POST_TYPES_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'layout_post_types' , $this->config->get( 'layout_post_types' ) );?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
