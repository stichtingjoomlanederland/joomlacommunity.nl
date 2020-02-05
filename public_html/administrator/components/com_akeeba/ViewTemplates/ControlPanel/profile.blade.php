<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var $this \Akeeba\Backup\Admin\View\ControlPanel\Html */

// Protect from unauthorized access
defined('_JEXEC') or die();

/**
 * Call this template with:
 * [
 * 	'returnURL' => 'index.php?......'
 * ]
 * to set up a custom return URL
 */
?>
<div class="akeeba-panel">
	<form action="index.php" method="post" name="switchActiveProfileForm" id="switchActiveProfileForm">
		<input type="hidden" name="option" value="com_akeeba" />
		<input type="hidden" name="view" value="ControlPanel" />
		<input type="hidden" name="task" value="SwitchProfile" />
		@if(isset($returnURL))
		<input type="hidden" name="returnurl" value="{{ $returnURL }}" />
		@endif
		<input type="hidden" name="@token(true)" value="1" />

	    <label>
			@lang('COM_AKEEBA_CPANEL_PROFILE_TITLE'): #{{ $this->profileid }}

		</label>
		@jhtml('select.genericlist', $this->profileList, 'profileid', 'onchange="document.forms.switchActiveProfileForm.submit()" class="advancedSelect"', 'value', 'text', $this->profileid)
		<button class="akeeba-btn akeeba-hidden-phone" onclick="this.form.submit(); return false;">
			<span class="akion-forward"></span>
			@lang('COM_AKEEBA_CPANEL_PROFILE_BUTTON')
		</button>
	</form>
</div>
