<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 *
 * @since 3.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var  $this  AkeebaViewTransfer */

$translations = [
	'UI-BROWSE'	=> JText::_('CONFIG_UI_BROWSE'),
	'UI-CONFIG'	=> JText::_('CONFIG_UI_CONFIG'),
	'UI-REFRESH'	=> JText::_('CONFIG_UI_REFRESH'),
	'UI-FTPBROWSER-TITLE'	=> JText::_('CONFIG_UI_FTPBROWSER_TITLE'),
	'UI-ROOT'	=> JText::_('FILTERS_LABEL_UIROOT'),
	'UI-TESTFTP-OK'	=> JText::_('CONFIG_DIRECTFTP_TEST_OK'),
	'UI-TESTFTP-FAIL'	=> JText::_('CONFIG_DIRECTFTP_TEST_FAIL'),
	'UI-TESTSFTP-OK'	=> JText::_('CONFIG_DIRECTSFTP_TEST_OK'),
	'UI-TESTSFTP-FAIL'	=> JText::_('CONFIG_DIRECTSFTP_TEST_FAIL'),
];

$js = <<< JS
akeeba.jQuery(document).ready(function(){
	// AJAX URL endpoint
	akeeba_ajax_url = 'index.php?option=com_akeeba&view=transfer&format=raw';

	// Last results of new site URL processing
	akeeba.Transfer.lastUrl = '{$this->newSiteUrl}';
	akeeba.Transfer.lastResult = '{$this->newSiteUrlResult}';

	// Initialise the translations
	akeeba.Transfer.translations['UI-BROWSE'] = '{$translations['UI-BROWSE']}';
	akeeba.Transfer.translations['UI-CONFIG'] = '{$translations['UI-CONFIG']}';
	akeeba.Transfer.translations['UI-REFRESH'] = '{$translations['UI-REFRESH']}';
	akeeba.Transfer.translations['UI-FTPBROWSER-TITLE'] = '{$translations['UI-FTPBROWSER-TITLE']}';
	akeeba.Transfer.translations['UI-ROOT'] = '{$translations['UI-ROOT']}';
	akeeba.Transfer.translations['UI-TESTFTP-OK'] = '{$translations['UI-TESTFTP-OK']}';
	akeeba.Transfer.translations['UI-TESTFTP-FAIL'] = '{$translations['UI-TESTFTP-FAIL']}';
	akeeba.Transfer.translations['UI-TESTSFTP-OK'] = '{$translations['UI-TESTSFTP-OK']}';
	akeeba.Transfer.translations['UI-TESTSFTP-FAIL'] = '{$translations['UI-TESTSFTP-FAIL']}';

	// Auto-process URL change event
	if (akeeba.jQuery('#akeeba-transfer-url').val())
	{
		akeeba.Transfer.onUrlChange();
	}
});
JS;

JFactory::getDocument()->addScriptDeclaration($js);

echo $this->loadAnyTemplate('admin:com_akeeba/transfer/default_dialogs');
echo $this->loadAnyTemplate('admin:com_akeeba/transfer/default_prerequisites');

if (empty($this->latestBackup))
{
	return;
}

echo $this->loadAnyTemplate('admin:com_akeeba/transfer/default_remoteconnection');
echo $this->loadAnyTemplate('admin:com_akeeba/transfer/default_manualtransfer');
echo $this->loadAnyTemplate('admin:com_akeeba/transfer/default_upload');