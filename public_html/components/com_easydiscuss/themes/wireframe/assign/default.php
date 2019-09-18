<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<h2 class="ed-page-title"><?php echo JText::_('COM_EASYDISCUSS_ASSIGNED_MY_POST'); ?></h2>

<div class="ed-assigned-post">

    <div class="ed-chart-assigned-post t-lg-mb--lg">
        <div class="o-row">
            <div class="o-col o-col--5">
                <div class="ed-chart-wrapper">
                    <canvas id="assign-chart" /></canvas>
                </div>
            </div>

            <div class="o-col">
                <div class="o-row">
                    <div class="o-col">
                       <div class="ed-pie-chart-assigned">
                           <canvas id="total-assigned-post-area" /></canvas>
                           <div class="ed-pie-chart-assigned__info">
                                <div class="ed-pie-chart-assigned__no"><?php echo $totalAssignedData; ?></div>
                                <div class="ed-pie-chart-assigned__txt"><?php echo JText::_('COM_EASYDISCUSS_GRAPH_POST_ASSIGNED') ?></div>
                           </div>
                       </div>
                    </div>
                    <div class="o-col">
                        <div class="ed-pie-chart-assigned">
                            <canvas id="total-solved-post-area" /></canvas>
                            <div class="ed-pie-chart-assigned__info">
                                <div class="ed-pie-chart-assigned__no"><?php echo $totalResolvedData; ?></div>
                                <div class="ed-pie-chart-assigned__txt"><?php echo JText::_('COM_EASYDISCUSS_GRAPH_POST_RESOLVED') ?></div>
                            </div>
                       </div>
                    </div>
                    <div class="o-col">
                        <div class="ed-pie-chart-assigned">
                            <canvas id="total-completed-post-area" /></canvas>
                            <div class="ed-pie-chart-assigned__info">
                                <div class="ed-pie-chart-assigned__no"><?php echo $completedPercentage; ?>%</div>
                                <div class="ed-pie-chart-assigned__txt"><?php echo JText::_('COM_EASYDISCUSS_GRAPH_POST_COMPLETED') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="ed-list">
    	<?php if ($posts) { ?>
    		<?php foreach ($posts as $post) { ?>
		        <div class="ed-post-item">
		            <div class="ed-post-item__hd">
		                <div class="o-row">
		                    <div class="o-col">
		                        <h2 class="ed-post-item__title t-lg-mt--md t-lg-mb--md">
		                        	<a href="<?php echo $post->getPermalink();?>"><?php echo $post->getTitle();?></a>
		                        </h2>

				                <?php if ($post->getTags()) { ?>
					                <ol class="g-list-inline ed-post-meta-tag">
					                	<?php foreach ($post->getTags() as $tag) { ?>
					                    	<li><a href="<?php echo EDR::getTagRoute($tag->id); ?>"><i class="fa fa-tag"></i> <?php echo $tag->title; ?></a></li>
					                    <?php } ?>
					                </ol>
			 					<?php } ?>

			 					<?php if ($this->config->get('main_qna')) { ?>
		                        <ol class="g-list-inline ed-post-item__post-meta">
		                            <li>
		                            	<span class="o-label o-label--<?php echo ($post->isresolve) ? 'success' : 'danger';?>-o">
		                            		<?php echo ($post->isresolve) ? JText::_('COM_EASYDISCUSS_RESOLVED') : JText::_('COM_EASYDISCUSS_UNRESOLVED');?>
                                        </span>
		                            </li>
		                        </ol>
		                        <?php } ?>
		                    </div>
		                </div>

		        		<?php if ($post->isFeatured() || $post->isLocked()) { ?>
			                <div class="ed-post-item__status">
			                	<?php if ($post->isFeatured()) { ?>
			                    <i class="fa fa-star"></i>
			                    <?php } ?>

			                    <?php if ($post->isLocked()) { ?>
			                    <i class="fa fa-lock"></i>
			                    <?php } ?>
			                </div>
		                <?php } ?>
		            </div>

		            <div class="ed-post-item__ft t-bdt-no">
		                <ol class="g-list-inline g-list-inline--dashed">
		                    <li><span class=""><?php echo $post->getDuration(); ?></span></li>
		                    <li><a class="" href="<?php echo EDR::getCategoryRoute($post->getCategory()->id); ?>"><?php echo JText::_($post->getCategory()->title); ?></a></li>

		                    <?php if ($post->getLastReplier()) { ?>
			                    <li class="current">
			                        <div class="">
					                    <span><?php echo JText::_('COM_EASYDISCUSS_LAST_REPLIER'); ?>: </span>
					                    <?php if (!$post->isLastReplyAnonymous()) { ?>
					                        <?php echo $this->html('user.avatar', $post->getLastReplier(), array('rank' => false, 'size' => 'sm')); ?>
					                    <?php } else { ?>
					                        <?php echo $this->html('user.anonymous', $post->getOwner(), $post->isAnonymous()); ?>
					                    <?php } ?> 
			                        </div>
			                    </li>
							<?php } ?>
		                </ol>
		            </div>
		        </div>
        	<?php } ?>
        <?php } else { ?>
			<div class="empty o-alert o-alert--info-o"><?php echo JText::_('COM_EASYDISCUSS_ASSIGNED_NOT_FOUND');?></div>
		<?php } ?>
    </div>
</div>

<?php if (isset($pagination)) { ?>
    <div class="ed-pagination">
        <?php echo $pagination->getPagesLinks();?>
    </div>
<?php } ?>
