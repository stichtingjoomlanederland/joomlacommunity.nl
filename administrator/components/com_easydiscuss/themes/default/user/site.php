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
<div id="site" class="tab-pane">
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_USER_TAB_SITE'); ?>		
				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
	                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SITE_URL'); ?>
	                        </div>
	                        <div class="col-md-7">
								<input type="text" class="form-control" id="siteUrl" name="siteUrl" size="55" maxlength="255" value="<?php echo $this->escape($siteDetails->get('siteUrl')); ?>" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
	                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SITE_USERNAME'); ?>
	                        </div>
	                        <div class="col-md-7">
								<input type="text" class="form-control" id="siteUsername" name="siteUsername" size="55" maxlength="255" value="<?php echo $this->escape($siteDetails->get('siteUsername')); ?>" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
	                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SITE_PASSWORD'); ?>
	                        </div>
	                        <div class="col-md-7">
								<input type="text" class="form-control" id="sitePassword" name="sitePassword" size="55" maxlength="255" value="<?php echo $this->escape($siteDetails->get('sitePassword')); ?>" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
	                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_FTP_URL'); ?>
	                        </div>
	                        <div class="col-md-7">
								<input type="text" class="form-control" id="ftpUrl" name="ftpUrl" size="55" maxlength="255" value="<?php echo $this->escape($siteDetails->get('ftpUrl')); ?>" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
	                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_FTP_USERNAME'); ?>
	                        </div>
	                        <div class="col-md-7">
								<input type="text" class="form-control" id="ftpUsername" name="ftpUsername" size="55" maxlength="255" value="<?php echo $this->escape($siteDetails->get('ftpUsername')); ?>" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
	                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_FTP_PASSWORD'); ?>
	                        </div>
	                        <div class="col-md-7">
								<input type="text" class="form-control" id="ftpPassword" name="ftpPassword" size="55" maxlength="255" value="<?php echo $this->escape($siteDetails->get('ftpPassword')); ?>" />		
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
	                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_OPTIONAL'); ?>
	                        </div>
	                        <div class="col-md-7">
								<textarea type="text" class="form-control" id="optional" name="optional" size="55" maxlength="255"/><?php echo $this->escape($siteDetails->get('optional')); ?></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

