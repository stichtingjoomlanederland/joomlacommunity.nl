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
<div class="adminform-body">
	<form name="adminForm" id="adminForm" action="index.php" method="post" class="adminForm">
		<div class="row-fluid">
			<div class="span6">
				<div class="widget accordion-group">
					<div class="whead accordion-heading">
						<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
						<h6><?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_TWITTER' ); ?></h6>
						<!-- <i class="icon-chevron-down"></i> -->
						</a>
					</div>

					<div id="option01" class="accordion-body collapse in">
						<div class="wbody">
							<div class="si-form-row">
								<div class="span5 form-row-label">
									<label for="main_autopost_twitter">
										<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_AUTOPOST' );?>
									</label>
								</div>
								<div class="span7"
									rel="ed-popover"
									data-placement="left"
									data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_AUTOPOST' ); ?>"
									data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_AUTOPOST'); ?>"
								>
									<?php echo $this->renderCheckbox( 'main_autopost_twitter' , $this->config->get( 'main_autopost_twitter' ) ); ?>
								</div>
							</div>
							<div class="si-form-row">
								<div class="span5 form-row-label">
									<label for="main_autopost_twitter_id">
										<?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_TWITTER_CONSUMER_KEY' );?>
									</label>
								</div>
								<div class="span7"
									rel="ed-popover"
									data-placement="left"
									data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_TWITTER_CONSUMER_KEY' ); ?>"
									data-content="<?php echo JText::_('COM_EASYDISCUSS_AUTOPOST_TWITTER_CONSUMER_KEY'); ?>"
								>
									<input type="text" name="main_autopost_twitter_id" id="main_autopost_twitter_id" value="<?php echo $this->config->get( 'main_autopost_twitter_id' );?>" />
								</div>
							</div>
							<div class="si-form-row">
								<div class="span5 form-row-label">
									<label for="main_autopost_twitter_secret">
										<?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_TWITTER_CONSUMER_SECRET' );?>
									</label>
								</div>
								<div class="span7"
									rel="ed-popover"
									data-placement="left"
									data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_TWITTER_CONSUMER_SECRET' ); ?>"
									data-content="<?php echo JText::_('COM_EASYDISCUSS_AUTOPOST_TWITTER_CONSUMER_SECRET'); ?>"
								>
									<input type="text" name="main_autopost_twitter_secret" id="main_autopost_twitter_secret" value="<?php echo $this->config->get( 'main_autopost_twitter_secret' );?>" />
								</div>
							</div>
							<div class="si-form-row">
								<div class="span5 form-row-label">
									<label for="main_autopost_twitter_secret">
										<?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_SIGN_IN' );?>
									</label>
								</div>
								<div class="span7">
									<?php if( $this->associated ){ ?>
									<a href="<?php echo JRoute::_( 'index.php?option=com_easydiscuss&controller=autoposting&task=revoke&type=twitter');?>"><?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_REVOKE_ACCCESS' );?></a>
									<?php } else { ?>
									<div>
										<a href="<?php echo JRoute::_( 'index.php?option=com_easydiscuss&controller=autoposting&task=request&type=twitter');?>"><img src="<?php echo JURI::root();?>media/com_easydiscuss/images/twitter_signon.png" /></a>
									</div>
									<?php } ?>
								</div>
							</div>
							<div class="si-form-row">
								<div class="span5 form-row-label">
									<label for="main_autopost_twitter_message">
										<?php echo JText::_( 'COM_EASYDISCUSS_TWITTER_AUTOPOST_POST_MESSAGE' );?>
									</label>
								</div>
								<div class="span7"
									rel="ed-popover"
									data-placement="left"
									data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_TWITTER_AUTOPOST_POST_MESSAGE' ); ?>"
									data-content="<?php echo JText::_('COM_EASYDISCUSS_TWITTER_AUTOPOST_POST_MESSAGE'); ?>"
								>
									<textarea name="main_autopost_twitter_message"><?php echo $this->config->get( 'main_autopost_twitter_message' );?></textarea>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<input type="hidden" name="step" value="completed" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="layout" value="twitter" />
	<input type="hidden" name="controller" value="autoposting" />
	<input type="hidden" name="option" value="com_easydiscuss" />
	</form>
</div>
