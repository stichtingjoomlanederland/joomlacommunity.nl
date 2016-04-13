<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_AKISMET_INTEGRATIONS'); ?>

			<div id="option01" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_AKISMET_INTEGRATIONS'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', ' antispam_akismet', $this->config->get('antispam_akismet')); ?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_AKISMET_API_KEY'); ?>
                        </div>
                        <div class="col-md-7">
							<input type="text" class="form-control" name="antispam_akismet_key" value="<?php echo $this->config->get('antispam_akismet_key');?>" size="60" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_FILTERING'); ?>

			<div id="option02" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_BAD_WORDS_FILTER'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', ' main_filterbadword', $this->config->get('main_filterbadword')); ?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_BAD_WORDS'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.textarea', 'main_filtertext', $this->config->get('main_filtertext')); ?>
							<div><?php echo JText::_( 'COM_EASYDISCUSS_REPLACE_BAD_WORDS_TIPS' ); ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
