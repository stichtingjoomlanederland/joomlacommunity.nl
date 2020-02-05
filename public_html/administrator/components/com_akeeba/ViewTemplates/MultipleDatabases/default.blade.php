<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

$ajaxUrl    = addslashes(JUri::base() . 'index.php?option=com_akeeba&view=MultipleDatabases&task=ajax');
$loadingUrl = addslashes($this->container->template->parsePath('media://com_akeeba/icons/loading.gif'));
$this->json = addcslashes($this->json, "'\\");
$js         = <<< JS

;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
// due to missing trailing semicolon and/or newline in their code.
akeeba.System.documentReady(function(){
    akeeba.System.params.AjaxURL = '$ajaxUrl';
	akeeba.Multidb.loadingGif = '$loadingUrl';
	var data = JSON.parse('{$this->json}');
    akeeba.Multidb.render(data);
});

JS;

?>
@inlineJs($js)
<div id="akEditorDialog" tabindex="-1" role="dialog" aria-labelledby="akEditorDialogLabel" aria-hidden="true"
     style="display:none;">
    <div class="akeeba-renderer-fef">
        <div class="akeeba-panel--primary">
            <header class="akeeba-block-header">
                <h3 id="akEditorDialogLabel">
                    @lang('COM_AKEEBA_FILEFILTERS_EDITOR_TITLE')
                </h3>
            </header>

            <div id="akEditorDialogBody">
                <form class="akeeba-form--horizontal" id="ak_editor_table">
                    <div class="akeeba-form-group">
                        <label class="control-label" for="ake_driver">@lang('COM_AKEEBA_MULTIDB_GUI_LBL_DRIVER')</label>
                        <select id="ake_driver">
                            <option value="mysqli">MySQLi</option>
                            <option value="mysql">MySQL (old)</option>
                            <option value="pdomysql">PDO MySQL</option>
                        </select>
                    </div>

                    <div class="akeeba-form-group">
                        <label for="ake_host">@lang('COM_AKEEBA_MULTIDB_GUI_LBL_HOST')</label>
                        <input id="ake_host" type="text" size="40" />
                    </div>

                    <div class="akeeba-form-group">
                        <label for="ake_port">@lang('COM_AKEEBA_MULTIDB_GUI_LBL_PORT')</label>
                        <input id="ake_port" type="text" size="10" />
                    </div>

                    <div class="akeeba-form-group">
                        <label for="ake_username">@lang('COM_AKEEBA_MULTIDB_GUI_LBL_USERNAME')</label>
                        <input id="ake_username" type="text" size="40" />
                    </div>

                    <div class="akeeba-form-group">
                        <label for="ake_password">@lang('COM_AKEEBA_MULTIDB_GUI_LBL_PASSWORD')</label>
                        <input id="ake_password" type="password" size="40" />
                    </div>

                    <div class="akeeba-form-group">
                        <label for="ake_database">@lang('COM_AKEEBA_MULTIDB_GUI_LBL_DATABASE')</label>
                        <input id="ake_database" type="text" size="40" />
                    </div>

                    <div class="akeeba-form-group">
                        <label for="ake_prefix">@lang('COM_AKEEBA_MULTIDB_GUI_LBL_PREFIX')</label>
                        <input id="ake_prefix" type="text" size="10" />
                    </div>

                    <div class="akeeba-form-group--pull-right">
                        <div class="akeeba-form-group--actions">
                            <button type="button" class="akeeba-btn--dark" id="akEditorBtnDefault">
                                <span class="akion-ios-pulse-strong"></span>
                                @lang('COM_AKEEBA_MULTIDB_GUI_LBL_TEST')
                            </button>

                            <button type="button" class="akeeba-btn--primary" id="akEditorBtnSave">
                                <span class="akion-checkmark"></span>
                                @lang('COM_AKEEBA_MULTIDB_GUI_LBL_SAVE')
                            </button>

                            <button type="button" class="akeeba-btn--orange" id="akEditorBtnCancel">
                                <span class="akion-close"></span>
                                @lang('COM_AKEEBA_MULTIDB_GUI_LBL_CANCEL')
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('admin:com_akeeba/CommonTemplates/ErrorModal')
@include('admin:com_akeeba/CommonTemplates/ProfileName')

<div class="akeeba-panel--information">
    <div id="ak_list_container">
        <table id="ak_list_table" class="akeeba-table--striped--dynamic-line-editor">
            <thead>
            <tr>
                <th width="40px">&nbsp;</th>
                <th width="40px">&nbsp;</th>
                <th>@lang('COM_AKEEBA_MULTIDB_LABEL_HOST')</th>
                <th>@lang('COM_AKEEBA_MULTIDB_LABEL_DATABASE')</th>
            </tr>
            </thead>
            <tbody id="ak_list_contents">
            </tbody>
        </table>
    </div>
</div>
