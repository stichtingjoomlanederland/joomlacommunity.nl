<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') || die;

/** @var  Akeeba\AdminTools\Admin\View\ControlPanel\Html $this For type hinting in the IDE */

$root      = realpath(JPATH_ROOT) ?: '';
$root      = trim($root);
$emptyRoot = empty($root);

?>
@include('admin:com_admintools/ControlPanel/needsipworkarounds')

@if (isset($this->jwarnings) && !empty($this->jwarnings))
	<div class="akeeba-block--failure">
		<h3>@lang('COM_ADMINTOOLS_ERR_CONTROLPANEL_JCONFIG')</h3>
		<p>{{ $this->jwarnings }}</p>
	</div>
@endif

{{-- Stuck database updates warning --}}
@if ($this->stuckUpdates)
	<div class="akeeba-block--failure">
		<p>
			@sprintf('COM_ADMINTOOLS_CPANEL_ERR_UPDATE_STUCK',
				$this->getContainer()->db->getPrefix(),
				'index.php?option=com_admintools&view=ControlPanel&task=forceUpdateDb'
			)
		</p>
	</div>
@endif

@if (isset($this->frontEndSecretWordIssue) && !empty($this->frontEndSecretWordIssue))
	<div class="akeeba-block--failure">
		<h3>@lang('COM_ADMINTOOLS_ERR_CONTROLPANEL_FESECRETWORD_HEADER')</h3>
		<p>@lang('COM_ADMINTOOLS_ERR_CONTROLPANEL_FESECRETWORD_INTRO')</p>
		<p>{{ $this->frontEndSecretWordIssue }}</p>
		<p>
			@lang('COM_ADMINTOOLS_ERR_CONTROLPANEL_FESECRETWORD_WHATTODO_JOOMLA')
			@sprintf('COM_ADMINTOOLS_ERR_CONTROLPANEL_FESECRETWORD_WHATTODO_COMMON', $this->newSecretWord)
		</p>
		<p>
			<a class="akeeba-btn--green akeeba-btn--big"
			   href="index.php?option=com_admintools&view=ControlPanel&task=resetSecretWord&@token()=1">
				<span class="akion-refresh"></span>
				@lang('COM_ADMINTOOLS_CONTROLPANEL_BTN_FESECRETWORD_RESET')
			</a>
		</p>
	</div>
@endif

{{-- Obsolete PHP version check --}}
@include('admin:com_admintools/ControlPanel/phpversion_warning', [
	'softwareName'  => 'Admin Tools',
	'minPHPVersion' => '7.2.0',
])

@if ($this->oldVersion)
	<div class="akeeba-block--warning">
		<strong>@lang('COM_ADMINTOOLS_ERR_CONTROLPANEL_OLDVERSION')</strong>
	</div>
@endif

@if ($emptyRoot)
	<div class="akeeba-block--failure">
		@lang('COM_ADMINTOOLS_LBL_CONTROLPANEL_EMPTYROOT')
	</div>
@endif

@if ($this->needsdlid)
	<div class="akeeba-block--success">
		<h3>
			@lang('COM_ADMINTOOLS_MSG_CONTROLPANEL_MUSTENTERDLID')
		</h3>
		<p>
			@sprintf('COM_ADMINTOOLS_LBL_CONTROLPANEL_NEEDSDLID', 'https://www.akeeba.com/download/official/add-on-dlid.html')
		</p>
		<form name="dlidform" action="index.php" method="post" class="akeeba-form--inline">
			<input type="hidden" name="option" value="com_admintools" />
			<input type="hidden" name="view" value="ControlPanel" />
			<input type="hidden" name="task" value="applydlid" />
			<input type="hidden" name="@token()" value="1" />
			<span>
				@lang('COM_ADMINTOOLS_MSG_CONTROLPANEL_PASTEDLID')
			</span>
			<input type="text" name="dlid"
				   placeholder="@lang('COM_ADMINTOOLS_LBL_JCONFIG_DOWNLOADID')"
				   class="akeeba-input--wide">
			<button type="submit" class="akeeba-btn--green">
				<span class="akion-checkmark-round"></span>
				@lang('COM_ADMINTOOLS_MSG_CONTROLPANEL_APPLYDLID')
			</button>
		</form>
	</div>
@endif

@if ($this->serverConfigEdited)
	<div class="akeeba-block--warning">
		<p>@lang('COM_ADMINTOOLS_CPANEL_SERVERCONFIGWARN')</p>

		<a href="index.php?option=com_admintools&view=ControlPanel&task=regenerateServerConfig"
		   class="akeeba-btn--green">
			@lang('COM_ADMINTOOLS_CPANEL_SERVERCONFIGWARN_REGENERATE')
		</a>
		<a href="index.php?option=com_admintools&view=ControlPanel&task=ignoreServerConfigWarn"
		   class="akeeba-btn--dark">
			@lang('COM_ADMINTOOLS_CPANEL_SERVERCONFIGWARN_IGNORE')
		</a>
	</div>
@endif
