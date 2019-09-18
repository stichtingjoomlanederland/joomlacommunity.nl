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
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_ANTI_SPAM_GENERAL'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_filterbadword', 'COM_EASYDISCUSS_ENABLE_BAD_WORDS_FILTER'); ?>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_BAD_WORDS'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.textarea', 'main_filtertext', $this->config->get('main_filtertext'), 10); ?>
							<div style="margin-top: 10px;"><?php echo JText::_( 'COM_EASYDISCUSS_REPLACE_BAD_WORDS_TIPS' ); ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_AKISMET_INTEGRATIONS'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'antispam_akismet', 'COM_EASYDISCUSS_AKISMET_INTEGRATIONS'); ?>
					<?php echo $this->html('settings.textbox', 'antispam_akismet_key', 'COM_EASYDISCUSS_AKISMET_API_KEY'); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_ED_CLEANTALK_INTEGRATIONS'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'antispam_cleantalk', 'COM_ED_CLEANTALK_INTEGRATIONS'); ?>
					<?php echo $this->html('settings.textbox', 'antispam_cleantalk_key', 'COM_ED_CLEANTALK_API_KEY'); ?>
				</div>
			</div>
		</div>
	</div>	
</div>