<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-lg-7">

			<div class="db-activity">
				<div class="db-activity-head">
					<b><?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_RECENT_ACTIVITIES');?></b>
				</div>

				<ul class="db-activity-filter list-unstyled">
					<li>
						<b><?php echo JText::_('COM_EASYDISCUSS_FILTERS');?>:</b>
					</li>
					<li class="active">
						<a href="#graphPosts" id="graphPosts-tab" role="tab" data-bp-toggle="tab"><?php echo JText::_('COM_EASYDISCUSS_FILTER_POSTS');?></a>
					</li>
				</ul>

				<div class="tab-content">
					<div role="tabpanel" class="tab-pane in active" id="graphPosts" aria-labelledby="graphPosts-tab">
						<div class="db-stream db-stream-graph">
							<canvas id="graph-area" />
						</div>
					</div>
				</div>
			</div>

			<div class="db-activity mt-20">
				<div class="db-activity-head">
					<b><?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_RECENT_ACTIVITIES');?></b>
				</div>

				<ul class="db-activity-filter list-unstyled">
					<li>
						<b><?php echo JText::_('COM_EASYDISCUSS_FILTERS');?>:</b>
					</li>
					<li class="active">
						<a href="#posts" id="posts-tab" role="tab" data-bp-toggle="tab"><?php echo JText::_('COM_EASYDISCUSS_FILTER_POSTS');?></a>
					</li>
					<li>
						<a href="#month" id="month-tab" role="tab" data-bp-toggle="tab"><?php echo JText::_('COM_EASYDISCUSS_FILTER_POSTS_MONTH');?></a>
					</li>
					<li>
						<a href="#category" id="category-tab" role="tab" data-bp-toggle="tab"><?php echo JText::_('COM_EASYDISCUSS_FILTER_POSTS_CATEGORY');?></a>
					</li>
				</ul>

				<div class="tab-content">
					<div role="tabpanel" class="tab-pane in active" id="posts" aria-labelledby="posts-tab">
						<div class="db-stream db-stream-graph db-stream--chart">
							<div id="canvas-holder">
								<canvas id="chart-area2" />
							</div>
							<div id="js-legend2" class="chart-legend"></div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="month" aria-labelledby="month-tab">
						<div class="db-stream db-stream-graph db-stream--chart">
							<div id="canvas-holder">
								<canvas id="chart-area3" />
							</div>
							<div id="js-legend3" class="chart-legend"></div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="category" aria-labelledby="category-tab">
						<div class="db-stream db-stream-graph db-stream--chart">
							<div id="canvas-holder">
								<canvas id="chart-area4" />
							</div>
							<div id="js-legend4" class="chart-legend"></div>
						</div>
					</div>
				</div>
				<!-- <div id="chartjs-tooltip" style="position: absolute;float: left;background-color: #cccccc;"></div> -->
			</div>

		</div>
		
		<div class="col-lg-5">

			<div class="db-sidebar">
				<div class="db-version">
					<div class="checking-updates" data-version-checks>
						<div class="o-media o-media--top">
							<div class="o-media__image">
								<div class="db-version__icon">
									<i class="fa fa-cog"></i>
									<i class="fa fa-thumbs-down"></i>
									<i class="fa fa-thumbs-up"></i>			
								</div>
								
							</div>
							<div class="o-media__body">
								<div class="o-grid">
									<div class="o-grid__cell">
										<b class="checking">
											<i class="fa fa-circle-o-notch fa-spin"></i> <?php echo JText::_('COM_EASYDISCUSS_CHECKING_FOR_UPDATES');?>
										</b>
										
										<b class="error-message">
											<?php echo JText::_('COM_EASYDISCUSS_ERROR_CONNECTING_TO_UPDATER'); ?>
										</b>

										<b class="latest">
											<?php echo JText::_('COM_EASYDISCUSS_SOFTWARE_IS_UP_TO_DATE');?>
										</b>

										<b class="requires-updating">
											<?php echo JText::_('COM_EASYDISCUSS_SOFTWARE_REQUIRES_UPDATING');?>

											
										</b>

										<div class="versions-meta">
											<div class="text-muted local-version"><?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_INSTALLED_VERSION');?>: <span data-local-version></span></div>
											<div class="text-muted latest-version"><?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_LATEST_VERSION');?>: <span data-online-version></span></div>
										</div>
									</div>
									<div class="o-grid__cell o-grid__cell--auto-size">
										<div class="requires-updating">
											<a href="<?php echo JURI::root();?>administrator/index.php?option=com_easydiscuss&controller=system&task=upgrade" class="btn btn-primary btn-sm"><?php echo JText::_('COM_EASYDISCUSS_UPDATE_NOW');?></a>
										</div>
									</div>
								</div>
								
							</div>
						</div>
						
					</div>
				</div>

				<div class="db-stats">
					<div class="row db-stats-grid text-center">
						<div class="col-md-4">
							<a class="db-stat-stamp" href="<?php echo JRoute::_('index.php?option=com_easydiscuss&view=posts');?>">
								<i class="fa fa-file-text-o"></i>
								<em><?php echo $totalPosts;?></em>
								<b><?php echo JText::_('COM_EASYDSICUSS_STATS_POSTS');?></b>
							</a>
						</div>
						<div class="col-md-4">
							<a class="db-stat-stamp" href="<?php echo JRoute::_('index.php?option=com_easydiscuss&view=categories');?>">
								<i class="fa fa-folder-open-o"></i>
								<em><?php echo $totalCategories;?></em>
								<b><?php echo JText::_('COM_EASYDISCUSS_STATS_CATEGORIES');?></b>
							</a>
						</div>
						<div class="col-md-4">
							<a class="db-stat-stamp" href="<?php echo JRoute::_('index.php?option=com_easydiscuss&view=tags');?>">
								<i class="fa fa-tags"></i>
								<em><?php echo $totalTags;?></em>
								<b><?php echo JText::_('COM_EASYDISCUSS_STATS_TAGS');?></b>
							</a>
						</div>
						<div class="col-md-4">
							<a class="db-stat-stamp" href="<?php echo JRoute::_('index.php?option=com_easydiscuss&view=users');?>">
								<i class="fa fa-user"></i>
								<em><?php echo $totalUsers;?></em>
								<b><?php echo JText::_('COM_EASYDISCUSS_STATS_USERS');?></b>
							</a>
						</div>
						<div class="col-md-4">
							<a class="db-stat-stamp" href="<?php echo JRoute::_('index.php?option=com_easydiscuss&view=roles');?>">
								<i class="fa fa-user-secret"></i>
								<em><?php echo $totalUserRoles;?></em>
								<b><?php echo JText::_('COM_EASYDISCUSS_STATS_ROLES');?></b>
							</a>
						</div>
						<div class="col-md-4">
							<a class="db-stat-stamp" href="<?php echo JRoute::_('index.php?option=com_easydiscuss&view=types');?>">
								<i class="fa fa-ticket"></i>
								<em><?php echo $totalTypes;?></em>
								<b><?php echo JText::_('COM_EASYDISCUSS_STATS_POST_TYPES');?></b>
							</a>
						</div>
					</div>
				</div>

				<div class="db-summary">
					<strong><?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_STAY_UPDATED');?></strong>
					<div>
						<i class="fa fa-facebook-square"></i>
						<span>
							<a href="https://facebook.com/StackIdeas" class="text-inherit"><?php echo JText::_('Like us on Facebook');?></a>
						</span>
					</div>
					<div>
						<i class="fa fa-twitter-square"></i>
						<span>
							<a href="https://twitter.com/StackIdeas" class="text-inherit"><?php echo JText::_('Follow us on Twitter');?></a>
						</span>
					</div>
					<div>
						<i class="fa fa-book"></i>
						<span>
							<a href="https://stackideas.com/docs/easydiscuss/administrators/welcome" class="text-inherit"><?php echo JText::_('COM_EASYDISCUSS_ABOUT_DOCS_SUPPORT');?></a>
						</span>
					</div>
					<div>
						<i class="fa fa-book"></i>
						<span>
							<a href="https://stackideas.com/forums" class="text-inherit"><?php echo JText::_('COM_EASYDISCUSS_ABOUT_FORUM_SUPPORT');?></a>
						</span>
					</div>
				</div>
			</div>

		</div>
	</div>


	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="view" value="discuss" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="discuss" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
