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
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_MAIN_MODERATION'); ?>

            <div class="panel-body">
                <div class="form-horizontal">

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_MODERATE_NEW_POST'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'main_moderatepost', $this->config->get('main_moderatepost')); ?>
                        </div>
                    </div>
                </div>
            </div>

		</div>
	</div>

	<div class="col-md-6">
        <div class="panel">
            <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_MODERATION_THRESHOLD'); ?>

            <div class="panel-body">
            <?php echo $this->html('panel.info', 'COM_EASYDISCUSS_SETTINGS_MODERATION_THRESHOLD_INFO'); ?>
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_MODERATION_THRESHOLD'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'main_moderation_automated', $this->config->get('main_moderation_automated')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_MODERATION_THRESHOLD'); ?>
                        </div>
                        <div class="col-md-7">
                            <input type="text" name="moderation_threshold" class="form-control form-control-sm text-center" value="<?php echo $this->config->get('moderation_threshold');?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>