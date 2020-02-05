<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

$ajaxUrl    = addslashes('index.php?option=com_akeeba&view=FileFilters&task=ajax');
$loadingUrl = addslashes($this->container->template->parsePath('media://com_akeeba/icons/loading.gif'));
$json       = addcslashes($this->json, "'");
$js         = <<< JS

;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
// due to missing trailing semicolon and/or newline in their code.
akeeba.System.documentReady(function() {
    akeeba.System.params.AjaxURL = '$ajaxUrl';
    akeeba.Fsfilters.loadingGif = '$loadingUrl';

	// Bootstrap the page display
	var data = JSON.parse('{$json}');
    akeeba.Fsfilters.renderTab(data);
});

JS;

?>
@inlineJs($js)
@include('admin:com_akeeba/CommonTemplates/ErrorModal')
@include('admin:com_akeeba/CommonTemplates/ProfileName')

<div class="akeeba-form--inline akeeba-panel--info">
    <div class="akeeba-form-group">
        <label>@lang('COM_AKEEBA_FILEFILTERS_LABEL_ROOTDIR')</label>
        {{ $this->root_select }}
    </div>
    <div id="addnewfilter" class="akeeba-form-group--actions">
        <label>
            @lang('COM_AKEEBA_FILEFILTERS_LABEL_ADDNEWFILTER')
        </label>
        <button class="akeeba-btn--grey"
                onclick="akeeba.Fsfilters.addNew('directories'); return false;">@lang('COM_AKEEBA_FILEFILTERS_TYPE_DIRECTORIES')</button>
        <button class="akeeba-btn--grey"
                onclick="akeeba.Fsfilters.addNew('skipfiles'); return false;">@lang('COM_AKEEBA_FILEFILTERS_TYPE_SKIPFILES')</button>
        <button class="akeeba-btn--grey"
                onclick="akeeba.Fsfilters.addNew('skipdirs'); return false;">@lang('COM_AKEEBA_FILEFILTERS_TYPE_SKIPDIRS')</button>
        <button class="akeeba-btn--grey"
                onclick="akeeba.Fsfilters.addNew('files'); return false;">@lang('COM_AKEEBA_FILEFILTERS_TYPE_FILES')</button>
    </div>
</div>

<form id="ak_roots_container_tab" class="akeeba-panel--primary">
    <div id="ak_list_container">
        <table id="ak_list_table" class="akeeba-table--striped">
            <thead>
            <tr>
                <td width="250px">@lang('COM_AKEEBA_FILEFILTERS_LABEL_TYPE')</td>
                <td>@lang('COM_AKEEBA_FILEFILTERS_LABEL_FILTERITEM')</td>
            </tr>
            </thead>
            <tbody id="ak_list_contents">
            </tbody>
        </table>
    </div>
</form>
