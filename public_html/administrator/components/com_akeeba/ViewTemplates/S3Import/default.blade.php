<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var \Akeeba\Backup\Admin\View\S3Import\Html $this */

// Work around Safari which ignores autocomplete=off (FOR CRYING OUT LOUD!)
$s3AccessEscaped = addslashes($this->s3access);
$s3SecretEscaped = addslashes($this->s3secret);
$js = <<< JS

;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
// due to missing trailing semicolon and/or newline in their code.
akeeba.System.documentReady(function() {
	setTimeout(function(){
		document.getElementById('s3access').value = '$s3AccessEscaped';
		document.getElementById('s3secret').value = '$s3SecretEscaped';
	}, 500);
});

JS;

?>
@inlineJs($js)
<form action="index.php" method="post" name="adminForm" id="adminForm">

    <div class="akeeba-hidden-fields-container">
        <input type="hidden" name="option" value="com_akeeba" />
        <input type="hidden" name="view" value="S3Import" />
        <input type="hidden" name="task" value="display" />
        <input type="hidden" id="ak_s3import_folder" name="folder" value="{{{ $this->root }}}" />
    </div>

    <div class="akeeba-panel--information">
        <div class="akeeba-form--inline">
            <div class="akeeba-form-group">
                <input type="text" size="40" name="s3access" id="s3access"
                       value="{{{ $this->s3access }}}"
                       placeholder="@lang('COM_AKEEBA_CONFIG_S3ACCESSKEY_TITLE')" />
            </div>

            <div class="akeeba-form-group">
                <input type="password" size="40" name="s3secret" id="s3secret"
                       value="{{{ $this->s3secret }}}"
                       placeholder="@lang('COM_AKEEBA_CONFIG_S3SECRETKEY_TITLE')" />
            </div>

            @if(empty($this->buckets))
                <div class="akeeba-form-group">
                    <button class="akeeba-btn--primary" type="submit" onclick="ak_s3import_resetroot();">
                        <span class="akion-wifi"></span>
                        @lang('COM_AKEEBA_S3IMPORT_LABEL_CONNECT')
                    </button>
                </div>
            @else
                <div class="akeeba-form-group">
                    {{ $this->bucketSelect }}
                </div>

                <div class="akeeba-form-group">
                    <button class="akeeba-btn--primary" type="submit" onclick="ak_s3import_resetroot();">
                        <span class="akion-folder"></span>
                        @lang('COM_AKEEBA_S3IMPORT_LABEL_CHANGEBUCKET')
                    </button>
                </div>
            @endif
        </div>
    </div>

    <div class="akeeba-panel--information">
        <div id="ak_crumbs_container">
            <ul class="breadcrumb">
                <li>
                    <a href="javascript:ak_s3import_chdir('');">&lt;root&gt;</a>
                    <span class="divider">/</span>
                </li>

                @if(!empty($this->crumbs))
					<?php $runningCrumb = ''; $i = 0; ?>
                    @foreach($this->crumbs as $crumb)
						<?php $runningCrumb .= $crumb . '/'; $i++; ?>
                        <li>
                            <a href="javascript:ak_s3import_chdir('{{ addslashes($runningCrumb) }}');">
                                {{{ $crumb }}}
                            </a>
                            @if($i < count($this->crumbs))
                                <span class="divider">/</span>
                            @endif
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
    </div>

    <div class="akeeba-container--50-50">
        <div>
            <div id="ak_folder_container" class="akeeba-panel--primary">
                <header class="akeeba-block-header">
                    <h3>
                        @lang('COM_AKEEBA_FILEFILTERS_LABEL_DIRS')
                    </h3>
                </header>

                <div id="folders">
                    @if(!empty($this->contents['folders']))
                        @foreach($this->contents['folders'] as $name => $record)
                            <div class="folder-container"
                                 onclick="ak_s3import_chdir('{{ addslashes($record['prefix']) }}');">
                                <span class="folder-icon-container">
                                    <span class="akion-ios-folder"></span>
                                </span>
                                <span class="folder-name">
                                    {{{ rtrim($name, '/') }}}
                                </span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div>
            <div id="ak_files_container" class="akeeba-panel--primary">
                <header class="akeeba-block-header">
                    <h3>
                        @lang('COM_AKEEBA_FILEFILTERS_LABEL_FILES')
                    </h3>
                </header>
                <div id="files">
                    @if(!empty($this->contents['files']))
                        @foreach($this->contents['files'] as $name => $record)
                            <div class="file-container"
                                 onclick="window.location='index.php?option=com_akeeba&view=S3Import&task=dltoserver&part=-1&frag=-1&layout=downloading&file={{{ $name }}}';">
                                <span class="file-icon-container">
                                    <span class="akion-document"></span>
                                </span>
                                <span class="file-name file-clickable">
                                    {{{ basename($record['name']) }}}
                                </span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</form>
