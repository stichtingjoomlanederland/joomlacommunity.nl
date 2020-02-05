<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

$urlIncludeFolders = addslashes(JUri::base() . 'index.php?option=com_akeeba&view=IncludeFolders&task=ajax');
$urlBrowser        = addslashes(JUri::base() . 'index.php?option=com_akeeba&view=Browser&processfolder=1&tmpl=component&folder=');
$this->json        = addcslashes($this->json, "'\\");
$js                = <<< JS

;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
// due to missing trailing semicolon and/or newline in their code.
akeeba.System.documentReady(function(){
	akeeba.System.params.AjaxURL                       = '$urlIncludeFolders';
	akeeba.Configuration.URLs['browser']               = '$urlBrowser';
	akeeba.Configuration.enablePopoverFor(document.querySelectorAll('[rel="popover"]'));
	var data = JSON.parse('{$this->json}');
	akeeba.Extradirs.render(data);
});

JS;

?>
@inlineJs($js)
@include('admin:com_akeeba/CommonTemplates/ErrorModal')
@include('admin:com_akeeba/CommonTemplates/FolderBrowser')
@include('admin:com_akeeba/CommonTemplates/ProfileName')

<div class="akeeba-container--primary">
    <div id="ak_list_container">
        <table id="ak_list_table" class="akeeba-table--striped--dynamic-line-editor">
            <thead>
            <tr>
                <!-- Delete -->
                <th width="50px">&nbsp;</th>
                <!-- Edit -->
                <th width="100px">&nbsp;</th>
                <!-- Directory path -->
                <th>
						<span rel="popover" data-original-title="@lang('COM_AKEEBA_INCLUDEFOLDER_LABEL_DIRECTORY')"
                              data-content="@lang('COM_AKEEBA_INCLUDEFOLDER_LABEL_DIRECTORY_HELP')">
							@lang('COM_AKEEBA_INCLUDEFOLDER_LABEL_DIRECTORY')
						</span>
                </th>
                <!-- Directory path -->
                <th>
						<span rel="popover" data-original-title="@lang('COM_AKEEBA_INCLUDEFOLDER_LABEL_VINCLUDEDIR')"
                              data-content="@lang('COM_AKEEBA_INCLUDEFOLDER_LABEL_VINCLUDEDIR_HELP')">
							@lang('COM_AKEEBA_INCLUDEFOLDER_LABEL_VINCLUDEDIR')
						</span>
                </th>
            </tr>
            </thead>
            <tbody id="ak_list_contents">
            </tbody>
        </table>
    </div>
</div>
