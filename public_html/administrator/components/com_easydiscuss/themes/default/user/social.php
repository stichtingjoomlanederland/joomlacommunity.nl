<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div id="social" class="tab-pane">
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_USER_SOCIAL_PROFILES'); ?>
				
				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
	                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_FACEBOOK'); ?>
	                        </div>
	                        <div class="col-md-7">
								<input type="text" class="form-control" id="facebook" name="facebook" size="55" maxlength="255" value="<?php echo $this->escape($userparams->get('facebook')); ?>" />
							</div>							
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
	                        </div>
	                        <div class="col-md-7">
						        <div class="o-checkbox">
						            <input type="checkbox" value="1" id="show_facebook" name="show_facebook" <?php echo $userparams->get('show_facebook') ? ' checked="1"' : ''; ?>>
						            <label for="show_facebook">
						                <?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
						            </label>
						        </div>
							</div>							
						</div>						

						<div class="form-group">
							<div class="col-md-5 control-label">
	                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_TWITTER'); ?>
	                        </div>
	                        <div class="col-md-7">
								<input type="text" class="form-control" id="twitter" name="twitter" size="55" maxlength="255" value="<?php echo $this->escape($userparams->get('twitter')); ?>" />
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
	                        </div>
	                        <div class="col-md-7">
						        <div class="o-checkbox">
						            <input type="checkbox" value="1" id="show_twitter" name="show_twitter" <?php echo $userparams->get('show_twitter') ? ' checked="1"' : ''; ?>>
						            <label for="show_twitter">
						                <?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
						            </label>
						        </div>
							</div>							
						</div>						

						<div class="form-group">
							<div class="col-md-5 control-label">
	                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_LINKEDIN'); ?>
	                        </div>
	                        <div class="col-md-7">
								<input type="text" class="form-control" id="linkedin" name="linkedin" size="55" maxlength="255" value="<?php echo $this->escape($userparams->get('linkedin')); ?>" />
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
	                        </div>
	                        <div class="col-md-7">
						        <div class="o-checkbox">
						            <input type="checkbox" value="1" id="show_linkedin" name="show_linkedin" <?php echo $userparams->get('show_linkedin') ? ' checked="1"' : ''; ?>>
						            <label for="show_linkedin">
						                <?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
						            </label>
						        </div>
							</div>							
						</div>						

						<div class="form-group">
							<div class="col-md-5 control-label">
	                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SKYPE'); ?>
	                        </div>
	                        <div class="col-md-7">
								<input type="text" class="form-control" id="skype" name="skype" size="55" maxlength="255" value="<?php echo $this->escape($userparams->get('skype')); ?>" />
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
	                        </div>
	                        <div class="col-md-7">
						        <div class="o-checkbox">
						            <input type="checkbox" value="1" id="show_skype" name="show_skype" <?php echo $userparams->get('show_skype') ? ' checked="1"' : ''; ?>>
						            <label for="show_skype">
						                <?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
						            </label>
						        </div>
							</div>							
						</div>						

						<div class="form-group">
							<div class="col-md-5 control-label">
	                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_WEBSITE'); ?>
	                        </div>
	                        <div class="col-md-7">
								<input type="text" class="form-control" id="website" name="website" size="55" maxlength="255" value="<?php echo $this->escape($userparams->get('website')); ?>" />
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
	                        </div>
	                        <div class="col-md-7">
						        <div class="o-checkbox">
						            <input type="checkbox" value="1" id="show_website" name="show_website" <?php echo $userparams->get('show_website') ? ' checked="1"' : ''; ?>>
						            <label for="show_website">
						                <?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
						            </label>
						        </div>
							</div>							
						</div>						

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
