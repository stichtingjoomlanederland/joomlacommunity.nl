<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
$cronlink = rtrim( JURI::root(), '/' ) . '/index.php?option=com_komento&task=clearCaptcha'; ?>
<script type="text/javascript">
Komento.require().script('admin.database').done(function($) {
	$('.populateDepth').implement('Komento.Controller.Database.DepthMaintenance');
	$('.fixStructure').implement('Komento.Controller.Database.FixStructure');
});
</script>

<div class="row">
	<div class="col-md-6">
		<fieldset class="panel">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_DATABASE_INFO' ); ?></div>
			<div class="panel-body">
				<?php echo JText::_( 'COM_KOMENTO_SETTINGS_DATABASE_CRON_LINK' ); ?>: <a href="<?php echo $cronlink; ?>" target="_blank"><?php echo $cronlink; ?></a>
			</div>
			<div class="panel-foot"><a href="http://stackideas.com/docs/komento/cronjobs/setting-up-cronjobs-in-cpanel.html" class="btn btn-success"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_DATABASE_SETUP_CRONJOB' ) ;?></a></div>
		</fieldset>

		<fieldset class="panel">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_DATABASE_MAINTENANCE' ); ?></div>
			<div class="panel-body">
				<table class="table table-options" cellspacing="1">
					<tbody>

						<!-- Show Activities Tab -->
						<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_DATABASE_CLEAR_CAPTCHA_ON_PAGE_LOAD', 'database_clearcaptchaonpageload' ); ?>

					</tbody>
				</table>
			</div>
		</fieldset>
	</div>

	<div class="col-md-6">
		<fieldset class="panel">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_DATABASE_UPDATE_POPULATE_DEPTH' ); ?></div>
			<div class="panel-body">
				<p class="alert alert-danger"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_DATABASE_UPDATE_POPULATE_DEPTH_WARNING' ); ?></p>
				<table class="admintable table populateDepth" cellspacing="1" style="margin: 0;">
					<tbody>

						<tr>
							<td width="300" class="key">
							</td>
							<td valign="top">
								<button type="button" class="btn btn-primary pull-right start"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_DATABASE_POPULATE_DEPTH_START' ); ?></button>
							</td>
						</tr>

						<tr class="statusWrapper" style="display: none;">
							<td width="300" class="key">
								<span><?php echo JText::_( 'COM_KOMENTO_SETTINGS_DATABASE_POPULATE_DEPTH_STATUS' ); ?></span>
							</td>
							<td>
								<span class="status"></span>
							</td>
						</tr>

						<tr class="totalWrapper" style="display: none;">
							<td width="300" class="key">
								<span><?php echo JText::_( 'COM_KOMENTO_SETTINGS_DATABASE_POPULATE_DEPTH_TOTAL_ARTICLES' ); ?></span>
							</td>
							<td>
								<span class="total"></span>
							</td>
						</tr>

						<tr class="countWrapper" style="display: none;">
							<td width="300" class="key">
								<span><?php echo JText::_( 'COM_KOMENTO_SETTINGS_DATABASE_POPULATE_DEPTH_COUNT_ARTICLES' ); ?></span>
							</td>
							<td>
								<span class="count"></span>
							</td>
						</tr>

					</tbody>
				</table>
			</div>
		</fieldset>

		<fieldset class="panel">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_DATABASE_UPDATE_FIX_STRUCTURE' ); ?></div>
			<div class="panel-body">
				<p><?php echo JText::_( 'COM_KOMENTO_SETTINGS_DATABASE_UPDATE_FIX_STRUCTURE_WARNING' ); ?></p>
				<table class="admintable table  table-options td-last-fluid fixStructure" cellspacing="1" style="margin: 0;">
					<tbody>
						<tr>
							<td width="300" class="key">
								<span><?php echo JText::_( 'COM_KOMENTO_SETTINGS_DATABASE_FIX_STRUCTURE_SELECT_COMPONENT' ); ?></span>
							</td>
							<td>
								<?php echo $this->getComponentSelection(); ?>
							</td>
						</tr>
						<tr>
							<td width="300" class="key">
								<span><?php echo JText::_( 'COM_KOMENTO_SETTINGS_DATABASE_FIX_STRUCTURE_SELECT_ARTICLE' ); ?></span>
							</td>
							<td>
								<?php echo $this->getArticleSelection(); ?>
							</td>
						</tr>

						<tr>
							<td width="300" class="key">
							</td>
							<td valign="top">
								<button type="button" class="btn btn-primary pull-right start"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_DATABASE_FIX_STRUCTURE_START' ); ?></button>
							</td>
						</tr>

						<tr class="statusWrapper" style="display: none;">
							<td width="300" class="key">
								<span><?php echo JText::_( 'COM_KOMENTO_SETTINGS_DATABASE_FIX_STRUCTURE_STATUS' ); ?></span>
							</td>
							<td>
								<span class="fixStatus"></span>
							</td>
						</tr>

						<tr class="totalWrapper" style="display: none;">
							<td width="300" class="key">
								<span><?php echo JText::_( 'COM_KOMENTO_SETTINGS_DATABASE_FIX_STRUCTURE_TOTAL_ARTICLES' ); ?></span>
							</td>
							<td>
								<span class="total"></span>
							</td>
						</tr>

						<tr class="countWrapper" style="display: none;">
							<td width="300" class="key">
								<span><?php echo JText::_( 'COM_KOMENTO_SETTINGS_DATABASE_FIX_STRUCTURE_COUNT_ARTICLES' ); ?></span>
							</td>
							<td>
								<span class="count"></span>
							</td>
						</tr>

					</tbody>
				</table>
			</div>
		</fieldset>
	</div>
</div>

