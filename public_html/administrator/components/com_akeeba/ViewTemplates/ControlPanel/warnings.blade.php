<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var $this \Akeeba\Backup\Admin\View\ControlPanel\Html */

// Protect from unauthorized access
defined('_JEXEC') or die();

$cloudFlareTestFile = 'CLOUDFLARE::' . $this->getContainer()->template->parsePath('media://com_akeeba/js/ControlPanel.min.js');
$cloudFlareTestFile .= '?' . $this->getContainer()->mediaVersion;

?>
{{-- Configuration Wizard pop-up --}}
@if($this->promptForConfigurationWizard)
    @include('admin:com_akeeba/Configuration/confwiz_modal')
@endif

{{-- Stuck database updates warning --}}
@if ($this->stuckUpdates)
    <div class="akeeba-block--warning">
        <p>
            @sprintf('COM_AKEEBA_CPANEL_ERR_UPDATE_STUCK', $this->getContainer()->db->getPrefix(), 'index.php?option=com_akeeba&view=ControlPanel&task=forceUpdateDb')
        </p>
    </div>
@endif

{{-- mbstring warning --}}
@unless($this->checkMbstring)
    <div class="akeeba-block--warning">
        @sprintf('COM_AKEEBA_CPANL_ERR_MBSTRING', PHP_VERSION)
    </div>
@endunless

{{-- Front-end backup secret word reminder --}}
@unless(empty($this->frontEndSecretWordIssue))
    <div class="akeeba-block--failure">
        <h3>@lang('COM_AKEEBA_CPANEL_ERR_FESECRETWORD_HEADER')</h3>
        <p>@lang('COM_AKEEBA_CPANEL_ERR_FESECRETWORD_INTRO')</p>
        <p>{{ $this->frontEndSecretWordIssue }}</p>
        <p>
            @lang('COM_AKEEBA_CPANEL_ERR_FESECRETWORD_WHATTODO_JOOMLA')
            @sprintf('COM_AKEEBA_CPANEL_ERR_FESECRETWORD_WHATTODO_COMMON', $this->newSecretWord)
        </p>
        <p>
            <a class="akeeba-btn--green akeeba-btn--big"
               href="index.php?option=com_akeeba&view=ControlPanel&task=resetSecretWord&@token(true)=1">
                <span class="akion-refresh"></span>
                @lang('COM_AKEEBA_CPANEL_BTN_FESECRETWORD_RESET')
            </a>
        </p>
    </div>
@endunless

{{-- Old PHP version reminder --}}
@include('admin:com_akeeba/ControlPanel/warning_phpversion')

{{-- Wrong media directory permissions --}}
@unless($this->areMediaPermissionsFixed)
    <div id="notfixedperms" class="akeeba-block--failure">
        <h3>@lang('COM_AKEEBA_CONTROLPANEL_WARN_WARNING')</h3>
        <p>@lang('COM_AKEEBA_CONTROLPANEL_WARN_PERMS_L1')</p>
        <p>@lang('COM_AKEEBA_CONTROLPANEL_WARN_PERMS_L2')</p>
        <ol>
            <li>@lang('COM_AKEEBA_CONTROLPANEL_WARN_PERMS_L3A')</li>
            <li>@lang('COM_AKEEBA_CONTROLPANEL_WARN_PERMS_L3B')</li>
        </ol>
        <p>@lang('COM_AKEEBA_CONTROLPANEL_WARN_PERMS_L4')</p>
    </div>
@endunless

{{-- You need to enter your Download ID --}}
@if($this->needsDownloadID)
    <div class="akeeba-block--warning">
        <h3>
            @lang('COM_AKEEBA_CPANEL_MSG_MUSTENTERDLID')
        </h3>
        <p>
            @sprintf('COM_AKEEBA_LBL_CPANEL_NEEDSDLID','https://www.akeebabackup.com/download/official/add-on-dlid.html')
        </p>
        <form name="dlidform" action="index.php" method="post" class="akeeba-form--inline">
            <input type="hidden" name="option" value="com_akeeba" />
            <input type="hidden" name="view" value="ControlPanel" />
            <input type="hidden" name="task" value="applydlid" />
            <input type="hidden" name="@token(true)" value="1" />
            <div class="akeeba-form-group">
                <label for="dlid">@lang('COM_AKEEBA_CPANEL_MSG_PASTEDLID')</label>
                <input type="text" name="dlid" placeholder="<?php echo JText::_('COM_AKEEBA_CONFIG_DOWNLOADID_LABEL')?>"
                       class="akeeba-input--wide">

                <button type="submit" class="akeeba-btn--green">
                    <span class="akion-checkmark-round"></span>
                    @lang('COM_AKEEBA_CPANEL_MSG_APPLYDLID')
                </button>
            </div>
        </form>
    </div>
@endif

{{-- You have CORE; you need to upgrade, not just enter a Download ID --}}
@if($this->coreWarningForDownloadID)
    <div class="akeeba-block--warning">
        @sprintf('COM_AKEEBA_LBL_CPANEL_NEEDSUPGRADE','https://www.akeebabackup.com/videos/1212-akeeba-backup-core/1617-abtc03-upgrade-core-professional.html')
    </div>
@endif

{{-- Warn about CloudFlare Rocket Loader --}}
<div class="akeeba-block--failure" style="display: none;" id="cloudFlareWarn">
    <h3><?php echo JText::_('COM_AKEEBA_CPANEL_MSG_CLOUDFLARE_WARN')?></h3>
    <p><?php echo JText::sprintf('COM_AKEEBA_CPANEL_MSG_CLOUDFLARE_WARN1', 'https://support.cloudflare.com/hc/en-us/articles/200169456-Why-is-JavaScript-or-jQuery-not-working-on-my-site-')?></p>
</div>
<?php
/**
 * DO NOT USE INLINE JAVASCRIPT FOR THIS SCRIPT. DO NOT REMOVE THE ATTRIBUTES.
 *
 * This is a specialised test which looks for CloudFlare's completely broken RocketLoader feature and warns the user
 * about it.
 */
?>
<script type="text/javascript" data-cfasync="true">
    var test = localStorage.getItem('<?php echo $cloudFlareTestFile?>');
    if (test)
    {
        document.getElementById("cloudFlareWarn").style.display = "block";
    }
</script>
