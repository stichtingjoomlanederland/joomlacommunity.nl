<?php
/**
 * @package Akeeba
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @since 3.0
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

JHtml::_('behavior.modal');

/** @var  AkeebaViewConfig  $this */
?>

<?php
// Configuration Wizard prompt
if (!\Akeeba\Engine\Factory::getConfiguration()->get('akeeba.flag.confwiz', 0))
{
	echo $this->loadAnyTemplate('admin:com_akeeba/config/confwiz_modal');
}

if (version_compare(JVERSION, '3.0.0', 'ge'))
{
	echo $this->loadAnyTemplate('admin:com_akeeba/config/dialogs_3x');
}
else
{
	echo $this->loadAnyTemplate('admin:com_akeeba/config/dialogs_25');
}
?>

<form name="adminForm" id="adminForm" method="post" action="index.php" class="form-horizontal form-horizontal-wide">

<div id="dialog" title="<?php echo JText::_('COM_AKEEBA_CONFIG_UI_BROWSER_TITLE') ?>">
</div>

<div >
	<?php if($this->securesettings == 1): ?>
	<div class="alert alert-success">
		<?php echo JText::_('COM_AKEEBA_CONFIG_UI_SETTINGS_SECURED'); ?>
	</div>
	<div class="ak_clr"></div>
	<?php elseif($this->securesettings == 0): ?>
	<div class="alert alert-error">
		<?php echo JText::_('COM_AKEEBA_CONFIG_UI_SETTINGS_NOTSECURED'); ?>
	</div>
	<div class="ak_clr"></div>
	<?php endif; ?>

	<div class="alert alert-info">
		<strong><?php echo JText::_('COM_AKEEBA_CPANEL_PROFILE_TITLE'); ?></strong>:
		#<?php echo $this->profileid; ?> <?php echo $this->profilename; ?>
	</div>

	<div class="alert">
		<?php echo JText::_('COM_AKEEBA_CONFIG_WHERE_ARE_THE_FILTERS'); ?>
	</div>
</div>

<div class="well">
	<h4>
		<?php echo JText::_('COM_AKEEBA_PROFILES_LABEL_DESCRIPTION') ?>
	</h4>

	<div class="control-group">
		<label class="control-label" for="profilename" rel="popover"
			data-original-title="<?php echo JText::_('COM_AKEEBA_PROFILES_LABEL_DESCRIPTION') ?>"
			data-content="<?php echo JText::_('COM_AKEEBA_PROFILES_LABEL_DESCRIPTION_TOOLTIP') ?>">
			<?php echo JText::_('COM_AKEEBA_PROFILES_LABEL_DESCRIPTION') ?>
		</label>
		<div class="controls">
			<input type="text" name="profilename" id="profilename" value="<?php echo $this->escape($this->profilename); ?>" />
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="quickicon" rel="popover"
			   data-original-title="<?php echo JText::_('COM_AKEEBA_CONFIG_QUICKICON_LABEL') ?>"
			   data-content="<?php echo JText::_('COM_AKEEBA_CONFIG_QUICKICON_DESC') ?>">
			<?php echo JText::_('COM_AKEEBA_CONFIG_QUICKICON_LABEL') ?>
		</label>
		<div class="controls">
			<input type="checkbox" name="quickicon" id="quickicon" <?php echo $this->quickicon ? 'checked="checked"' : ''; ?>/>
		</div>
	</div>
</div>

<input type="hidden" name="option" value="com_akeeba" />
<input type="hidden" name="view" value="config" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken()?>" value="1" />

<!-- This div contains dynamically generated user interface elements -->
<div id="akeebagui">
</div>

</form>
<script type="text/javascript" language="javascript">
	akeeba.jQuery(document).ready(function($){
		// Push some translations
        akeeba.Configuration.translations['UI-BROWSE'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_CONFIG_UI_BROWSE')) ?>';
        akeeba.Configuration.translations['UI-CONFIG'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_CONFIG_UI_CONFIG')) ?>';
        akeeba.Configuration.translations['UI-REFRESH'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_CONFIG_UI_REFRESH')) ?>';
        akeeba.Configuration.translations['UI-FTPBROWSER-TITLE'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_CONFIG_UI_FTPBROWSER_TITLE')) ?>';
        akeeba.Configuration.translations['UI-ROOT'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_FILEFILTERS_LABEL_UIROOT')) ?>';
        akeeba.Configuration.translations['UI-TESTFTP-OK'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_CONFIG_DIRECTFTP_TEST_OK')) ?>';
        akeeba.Configuration.translations['UI-TESTFTP-FAIL'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_CONFIG_DIRECTFTP_TEST_FAIL')) ?>';
        akeeba.Configuration.translations['UI-TESTSFTP-OK'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_CONFIG_DIRECTSFTP_TEST_OK')) ?>';
        akeeba.Configuration.translations['UI-TESTSFTP-FAIL'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_CONFIG_DIRECTSFTP_TEST_FAIL')) ?>';

        // Push some custom URLs
        akeeba.Configuration.URLs['browser'] = '<?php echo AkeebaHelperEscape::escapeJS('index.php?view=browser&tmpl=component&processfolder=1&folder=') ?>';
        akeeba.Configuration.URLs['ftpBrowser'] = '<?php echo AkeebaHelperEscape::escapeJS('index.php?option=com_akeeba&view=ftpbrowser') ?>';
        akeeba.Configuration.URLs['sftpBrowser'] = '<?php echo AkeebaHelperEscape::escapeJS('index.php?option=com_akeeba&view=sftpbrowser') ?>';
        akeeba.Configuration.URLs['testFtp'] = '<?php echo AkeebaHelperEscape::escapeJS('index.php?option=com_akeeba&view=config&task=testftp') ?>';
        akeeba.Configuration.URLs['testSftp'] = '<?php echo AkeebaHelperEscape::escapeJS('index.php?option=com_akeeba&view=config&task=testsftp') ?>';
        akeeba.Configuration.URLs['dpeauthopen'] = '<?php echo AkeebaHelperEscape::escapeJS('index.php?option=com_akeeba&view=config&task=dpeoauthopen&format=raw') ?>';
        akeeba.Configuration.URLs['dpecustomapi'] = '<?php echo AkeebaHelperEscape::escapeJS('index.php?option=com_akeeba&view=config&task=dpecustomapi&format=raw') ?>';
        akeeba.System.params.AjaxURL = akeeba.Configuration.URLs['dpecustomapi'];

		// Load the configuration UI data in a way that doesn't let Safari screw up password fields
		var data = JSON.parse("<?php echo $this->json; ?>");

		setTimeout(function(){
            akeeba.Configuration.parseConfigData(data);

			// Work around Chrome which blatantly ignores autocomplete=off in the ANGIE password field (FOR CRYING OUT LOUD!)
			setTimeout('akeeba.Configuration.restoreDefaultPasswords();', 1000);

			// Enable popovers
			akeeba.jQuery('[rel="popover"]').popover({
				trigger: 'manual',
				animate: false,
				html: true,
				placement: 'bottom',
				template: '<div class="popover akeeba-bootstrap-popover" onmouseover="akeeba.jQuery(this).mouseleave(function() {akeeba.jQuery(this).hide(); });"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
			})
				.click(function(e) {
					e.preventDefault();
				})
				.mouseenter(function(e) {
					akeeba.jQuery('div.popover').remove();
					akeeba.jQuery(this).popover('show');
				});
		}, 10);

        // Initialise hooks used by the definition INI files
        akeeba_directftp_init_browser = function()
        {
            akeeba.Configuration.FtpBrowser.initialise('engine.archiver.directftp.initial_directory', 'engine.archiver.directftp')
        };

        // Initialise hooks used by the definition INI files
        akeeba_directftp_init_browser = function()
        {
            akeeba.Configuration.FtpBrowser.initialise('engine.archiver.directftp.initial_directory', 'engine.archiver.directftp')
        };

        akeeba_postprocftp_init_browser = function()
        {
            akeeba.Configuration.FtpBrowser.initialise('engine.postproc.ftp.initial_directory', 'engine.postproc.ftp')
        };

        akeeba_directsftp_init_browser = function()
        {
            akeeba.Configuration.SftpBrowser.initialise('engine.archiver.directsftp.initial_directory', 'engine.archiver.directsftp')
        };

        akeeba_postprocsftp_init_browser = function()
        {
            akeeba.Configuration.SftpBrowser.initialise('engine.postproc.sftp.initial_directory', 'engine.postproc.sftp')
        };

        directftp_test_connection = function()
        {
            akeeba.Configuration.FtpTest.testConnection('engine.archiver.directftp.ftp_test','engine.archiver.directftp');
        };

        postprocftp_test_connection = function()
        {
            akeeba.Configuration.FtpTest.testConnection('engine.postproc.ftp.ftp_test','engine.postproc.ftp');
        };

        directsftp_test_connection = function()
        {
            akeeba.Configuration.SftpTest.testConnection('engine.archiver.directsftp.sftp_test','engine.archiver.directsftp');
        };

        postprocsftp_test_connection = function()
        {
            akeeba.Configuration.SftpTest.testConnection('engine.postproc.sftp.sftp_test','engine.postproc.sftp');
        };

		// Create the dialog
		$("#dialog").dialog({
			autoOpen: false,
			closeOnEscape: false,
			height: 400,
			width: 640,
			hide: 'slide',
			modal: true,
			position: 'center',
			show: 'slide'
		});

		// Create an AJAX error trap
        akeeba.System.params.errorCallback = function( message ) {
			var dialog_element = new Element('div');
			var dlgHead = new Element('h3');
			dlgHead.set('html','<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_CONFIG_UI_AJAXERRORDLG_TITLE')) ?>');
			dlgHead.inject(dialog_element);
			var dlgPara = new Element('p');
			dlgPara.set('html','<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_CONFIG_UI_AJAXERRORDLG_TEXT')) ?>');
			dlgPara.inject(dialog_element);
			var dlgPre = new Element('pre');
			dlgPre.set('html', message);
			dlgPre.inject(dialog_element);
			SqueezeBox.open(new Element(dialog_element), {
				handler:	'adopt',
				size:		{x: 600, y: 400}
			});
		};

        akeeba.Configuration.onBrowser = function( folder, element )
		{
			// Close dialog callback (user confirmed the new folder)
			akeeba_browser_callback = function( myFolder ) {
				$(element).val( myFolder );
				SqueezeBox.close();
			};

			// URL to load the browser
			var browserSrc = '<?php echo AkeebaHelperEscape::escapeJS(JUri::base().'index.php?option=com_akeeba&view=browser&tmpl=component&processfolder=1&folder=') ?>';
			browserSrc = browserSrc + encodeURIComponent(folder);

			SqueezeBox.open(browserSrc, {
				handler:	'iframe',
				size:		{x: 600, y: 400}
			});
		};

		// Enable popovers
		akeeba.jQuery('[rel="popover"]').popover({
			trigger: 'manual',
			animate: false,
			html: true,
			placement: 'bottom',
			template: '<div class="popover akeeba-bootstrap-popover" onmouseover="akeeba.jQuery(this).mouseleave(function() {akeeba.jQuery(this).hide(); });"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
		})
		.click(function(e) {
			e.preventDefault();
		})
		.mouseenter(function(e) {
			akeeba.jQuery('div.popover').remove();
			akeeba.jQuery(this).popover('show');
		});

	});
</script>