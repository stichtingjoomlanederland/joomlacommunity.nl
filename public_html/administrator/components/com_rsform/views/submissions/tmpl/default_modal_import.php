<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2017 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;

JText::script('COM_RSFORM_PLEASE_UPLOAD_ONLY_CSV_FILES');
JText::script('COM_RSFORM_PLEASE_UPLOAD_A_FILE');
?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="span12">
			<fieldset class="form-horizontal">
                <div class="alert alert-error" id="importError" style="display: none;"></div>
                <div class="control-group">
                    <div class="controls">
                    <label class="checkbox" for="importSkipHeaders"><input type="checkbox" id="importSkipHeaders" name="import[skipHeaders]" value="1" checked="checked" /> <?php echo JText::_('COM_RSFORM_SKIP_HEADERS'); ?></label>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="importDelimiter"><?php echo JText::_('COM_RSFORM_IMPORT_DELIMITER'); ?></label>
                    <div class="controls">
                        <input type="text" id="importDelimiter" name="import[delimiter]" value="," />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="importEnclosure"><?php echo JText::_('COM_RSFORM_IMPORT_ENCLOSURE'); ?></label>
                    <div class="controls">
                        <input type="text" id="importEnclosure" name="import[enclosure]" value="&quot;" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="importFile"><?php echo JText::_('COM_RSFORM_IMPORT_FILE'); ?></label>
                    <div class="controls">
                        <input type="file" name="importFile" id="importFile" onchange="enableImportUpload();" />
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                    <button class="btn btn-large btn-primary" id="importFileButton" disabled onclick="Joomla.submitbutton('submissions.importcsv');" type="button"><?php echo JText::_('COM_RSFORM_UPLOAD'); ?></button>
                    </div>
                </div>
            </fieldset>
		</div>
	</div>
</div>

<script>
    function enableImportUpload()
    {
        var input = document.getElementById('importFile');
        var message = document.getElementById('importError');
        if (input.value.length == 0)
        {
            message.style.display = 'block';
            message.innerText = Joomla.JText._('COM_RSFORM_PLEASE_UPLOAD_A_FILE');
            return false;
        }

        var ext = input.value.substring(input.value.lastIndexOf('.') + 1).toLowerCase();

        if (ext !== 'csv')
        {
            message.style.display = 'block';
            message.innerText = Joomla.JText._('COM_RSFORM_PLEASE_UPLOAD_ONLY_CSV_FILES');
            return false;
        }

        message.style.display = 'none';
        document.getElementById('adminForm').setAttribute('enctype', 'multipart/form-data');
        document.getElementById('importFileButton').removeAttribute('disabled');
    }
</script>