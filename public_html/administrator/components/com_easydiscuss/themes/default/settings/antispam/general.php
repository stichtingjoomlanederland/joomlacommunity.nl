<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
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
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_filterbadword', 'COM_EASYDISCUSS_ENABLE_BAD_WORDS_FILTER'); ?>	
					<?php echo $this->html('settings.textarea', 'main_filtertext', 'COM_EASYDISCUSS_BAD_WORDS', '', array(), 'COM_EASYDISCUSS_REPLACE_BAD_WORDS_TIPS'); ?>
					<?php echo $this->html('settings.textbox', 'antispam_minimum_title', 'COM_EASYDISCUSS_ANTI_SPAM_MINIMUM_TITLE', '', array('size' => 7, 'postfix' => 'Characters'), '', '', 'text-center'); ?>
					<?php echo $this->html('settings.textbox', 'main_post_min_length', 'COM_EASYDISCUSS_MAIN_POST_MIN_LENGTH', '', array('size' => 7, 'postfix' => 'COM_EASYDISCUSS_CHARACTERS'), '', '', 'text-center'); ?>
					<?php echo $this->html('settings.toggle', 'main_post_title_limit', 'COM_ED_ENFORCE_TITLE_MAX_CHARS', '', 'data-max-title-option'); ?>
					<?php echo $this->html('settings.textbox', 'main_post_title_chars', 'COM_ED_MAX_TITLE_CHARS', '', array('size' => 7, 'postfix' => 'COM_EASYDISCUSS_CHARACTERS'), '', '', 'text-center'); ?>
					<?php echo $this->html('settings.toggle', 'antispam_disallow_editing', 'COM_ED_ANTISPAM_DISALLOW_EDITING'); ?>
					<?php echo $this->html('settings.textbox', 'antispam_disallow_editing_days', 'COM_ED_ANTISPAM_DISALLOW_EDITING_DAYS', '', array('size' => 7, 'postfix' => 'Days'), '', '', 'text-center'); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_ED_HONEYPOT_TRAP'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'antispam_honeypot_posts', 'COM_ED_HONEYPOT_POSTS'); ?>
					<?php echo $this->html('settings.toggle', 'antispam_honeypot_replies', 'COM_ED_HONEYPOT_REPLIES'); ?>
					<?php echo $this->html('settings.toggle', 'antispam_honeypot_comments', 'COM_ED_HONEYPOT_COMMENTS'); ?>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_AKISMET_INTEGRATIONS', '', '/docs/easydiscuss/administrators/configuration/akismet-anti-spam'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'antispam_akismet', 'COM_EASYDISCUSS_AKISMET_INTEGRATIONS'); ?>
					<?php echo $this->html('settings.textbox', 'antispam_akismet_key', 'COM_EASYDISCUSS_AKISMET_API_KEY'); ?>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_ED_CLEANTALK_INTEGRATIONS', '', '/docs/easydiscuss/administrators/configuration/cleantalk-anti-spam'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'antispam_cleantalk', 'COM_ED_CLEANTALK_INTEGRATIONS'); ?>
					<?php echo $this->html('settings.textbox', 'antispam_cleantalk_key', 'COM_ED_CLEANTALK_API_KEY'); ?>
				</div>
			</div>
		</div>
	</div>
</div>