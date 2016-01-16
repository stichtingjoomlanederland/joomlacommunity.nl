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
						<h6><?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_FACEEBOOK' ); ?></h6>
						<!-- <i class="icon-chevron-down"></i> -->
						</a>
					</div>

					<div id="option01" class="accordion-body collapse in">
						<div class="wbody">
							<div class="si-form-row">
								<div class="span5 form-row-label">
									<label>
										<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_AUTOPOST' ); ?>
									</label>
								</div>
								<div class="span7"
									rel="ed-popover"
									data-placement="left"
									data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_AUTOPOST' ); ?>"
									data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_AUTOPOST'); ?>"
								>
									<?php echo $this->renderCheckbox( 'main_autopost_facebook' , $this->config->get( 'main_autopost_facebook' ) ); ?>
								</div>
							</div>
							<div class="si-form-row">
								<div class="span5 form-row-label">
									<label>
										<?php echo JText::_( 'COM_EASYDISCUSS_FB_AUTOPOST_APP_ID' ); ?>
									</label>
								</div>
								<div class="span7"
									rel="ed-popover"
									data-placement="left"
									data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_FB_AUTOPOST_APP_ID' ); ?>"
									data-content="<?php echo JText::_('COM_EASYDISCUSS_FB_AUTOPOST_APP_ID'); ?>"
								>
									<input type="text" name="main_autopost_facebook_id" id="main_autopost_facebook_id" value="<?php echo $this->config->get( 'main_autopost_facebook_id' );?>" class="input" style="width:200px" />
								</div>
							</div>
							<div class="si-form-row">
								<div class="span5 form-row-label">
									<label>
										<?php echo JText::_( 'COM_EASYDISCUSS_FB_AUTOPOST_APP_SECRET' ); ?>
									</label>
								</div>
								<div class="span7"
									rel="ed-popover"
									data-placement="left"
									data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_FB_AUTOPOST_APP_SECRET' ); ?>"
									data-content="<?php echo JText::_('COM_EASYDISCUSS_FB_AUTOPOST_APP_SECRET'); ?>"
								>
									<input type="text" name="main_autopost_facebook_secret" id="main_autopost_facebook_secret" value="<?php echo $this->config->get( 'main_autopost_facebook_secret' );?>" class="input" style="width:300px" />
								</div>
							</div>
							<div class="si-form-row">
								<div class="span5 form-row-label">
									<label>
										<?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_SIGN_IN' ); ?>
									</label>
								</div>
								<div class="span7"
									rel="ed-popover"
									data-placement="left"
									data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_SIGN_IN' ); ?>"
									data-content="<?php echo JText::_('COM_EASYDISCUSS_AUTOPOST_SIGN_IN'); ?>"
								>
									<?php if( $this->associated ){ ?>
										<a href="<?php echo JRoute::_( 'index.php?option=com_easydiscuss&controller=autoposting&task=revoke&type=facebook');?>">
											<?php echo JText::_( 'Revoke access' );?>
										</a>
									<?php } else { ?>
									<div>
										<a href="<?php echo JRoute::_( 'index.php?option=com_easydiscuss&controller=autoposting&task=request&type=facebook');?>"><img src="<?php echo JURI::root();?>media/com_easydiscuss/images/facebook_signon.png" /></a>
									</div>
									<?php } ?>
								</div>
							</div>
							<div class="si-form-row">
								<div class="span5 form-row-label">
									<label>
										<?php echo JText::_( 'COM_EASYDISCUSS_FB_AUTOPOST_STEP_2_PAGE_ID' ); ?>
									</label>
								</div>
								<div class="span7"
									rel="ed-popover"
									data-placement="left"
									data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_FB_AUTOPOST_STEP_2_PAGE_ID' ); ?>"
									data-content="<?php echo JText::_('COM_EASYDISCUSS_FB_AUTOPOST_STEP_2_PAGE_ID'); ?>"
								>
									<input type="text" id="page-id" name="main_autopost_facebook_page_id" value="<?php echo $this->config->get( 'main_autopost_facebook_page_id' );?>" class="input" style="width:300px" />
									<div class="small"><?php echo JText::_( 'Separate each page id with a comma. E.g: 123456789,123456780' );?></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<input type="hidden" name="step" value="completed" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="layout" value="facebook" />
	<input type="hidden" name="controller" value="autoposting" />
	<input type="hidden" name="option" value="com_easydiscuss" />
	</form>
</div>
