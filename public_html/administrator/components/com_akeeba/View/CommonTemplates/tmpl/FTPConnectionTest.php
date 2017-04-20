<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();
?>
<?php /* (S)FTP connection test */ ?>
<div class="modal fade" id="testFtpDialog" tabindex="-1" role="dialog" aria-labelledby="testFtpDialogLabel"
     aria-hidden="true" style="display:none;">

    <div class="modal-header">
        <h4 class="modal-title" id="testFtpDialogLabel">
        </h4>
    </div>
    <div class="modal-body" id="testFtpDialogBody">
        <div class="alert alert-success" id="testFtpDialogBodyOk"></div>
        <div class="alert alert-danger" id="testFtpDialogBodyFail"></div>
    </div>

</div>
