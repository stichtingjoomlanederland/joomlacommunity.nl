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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_DISPLAY'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'layout_darkmode', 'COM_ED_USE_DARK_MODE'); ?>
					<?php echo $this->html('settings.toggle', 'layout_rem', 'COM_ED_USE_REM_SIZE'); ?>
					<?php echo $this->html('settings.textbox', 'layout_list_limit', 'COM_EASYDISCUSS_LIST_LIMIT', '', array('size' => 7, 'postfix' => 'Posts'), '', '', 'text-center'); ?>
					<?php echo $this->html('settings.textbox', 'layout_daystostaynew', 'COM_EASYDISCUSS_NUMBER_OF_DAYS_A_POST_STAY_AS_NEW', '', array('size' => 7, 'postfix' => 'Days'), '', '', 'text-center'); ?>
					<?php echo $this->html('settings.toggle', 'layout_zero_as_plural', 'COM_EASYDISCUSS_SETTINGS_ZERO_AS_PLURAL'); ?>
					<?php echo $this->html('settings.toggle', 'layout_customcss', 'COM_ED_ATTACH_CUSTOM_CSS'); ?>
					<?php echo $this->html('settings.textbox', 'layout_wrapper_sfx', 'COM_EASYDISCUSS_WRAPPERCLASS_SFX'); ?>
					<?php echo $this->html('settings.toggle', 'main_copyright_link_back', 'COM_EASYDISCUSS_ENABLE_POWERED_BY'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_ED_3RD_PARTY_STYLESHEETS'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'layout_fontawesome', 'COM_ED_LOAD_FONTAWESOME'); ?>

					<?php echo $this->html('settings.toggle', 'layout_prism', 'COM_ED_LOAD_PRISM'); ?>
				</div>
			</div>
		</div>

		<div class="panel">
			<div class="o-form-horizontal">
				<?php echo $this->html('panel.head', 'COM_ED_SETTINGS_SCHEMA'); ?>

				<div class="panel-body">
					<div class="o-form-group">
						<div class="col-md-5 o-form-label">
							<?php echo $this->html('form.label', 'COM_ED_SETTINGS_SCHEMA_LOGO'); ?>
						</div>

						<div class="col-md-7">
							<div>
								<div class="ed-img-holder">
									<div class="ed-img-holder__remove" <?php echo ED::hasOverrideLogo('schema') ? '' : 'style="display: none;'; ?>>
										<a href="javascript:void(0);" class="o-btn o-btn--default-o o-btn--sm t-text--danger t-mb--sm" data-schema-logo-restore-default-button>
											<i class="fa fa-times"></i>&nbsp; <?php echo JText::_('COM_EASYDISCUSS_REMOVE'); ?>
										</a>
									</div>
									<img src="<?php echo ED::getLogo('schema'); ?>" width="60" />
								</div>
							</div>
							<div class="t-mt--sm">
								<input type="file" name="schema_logo" class="o-form-control" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
