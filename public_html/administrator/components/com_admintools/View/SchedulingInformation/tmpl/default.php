<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var $this \Akeeba\AdminTools\Admin\View\SchedulingInformation\Html */

// Protect from unauthorized access
defined('_JEXEC') or die();

?>

<h3>
	<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_RUN_FILESCANNER'); ?>
</h3>

<p>
	<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_HEADERINFO'); ?>
</p>

<fieldset>
	<legend><?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_CLICRON'); ?></legend>

		<p class="alert alert-info">
			<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_CLICRON_INFO'); ?>
			<br/>
			<a class="btn btn-mini btn-info" href="https://www.akeebabackup.com/documentation/admin-tools/php-file-scanner-cron.html" target="_blank">
				<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_GENERICREADDOC'); ?>
			</a>
		</p>
		<p>
			<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_GENERICUSECLI'); ?>
			<code>
				<?php echo $this->escape($this->croninfo->info->php_path); ?>

				<?php echo $this->escape($this->croninfo->cli->path); ?>

			</code>
		</p>
		<p>
			<span class="label label-important"><?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_CLIGENERICIMPROTANTINFO'); ?></span>
			<?php echo JText::sprintf('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_CLIGENERICINFO', $this->croninfo->info->php_path); ?>

		</p>
</fieldset>

<!--
<fieldset>
	<legend><?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_ALTCLICRON'); ?></legend>

		<p class="alert alert-info">
			<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_ALTCLICRON_INFO'); ?>
			<br/>
			<a class="btn btn-mini btn-info" href="https://www.akeebabackup.com/documentation/admin-tools/alternative-cron-script.html" target="_blank">
				<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_GENERICREADDOC'); ?>
			</a>
		</p>
		<?php if (!$this->croninfo->info->feenabled): ?>
			<p class="alert alert-error">
				<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_DISABLED'); ?>
			</p>
		<?php elseif (!trim($this->croninfo->info->secret)): ?>
			<p class="alert alert-error">
				<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_SECRET'); ?>
			</p>
		<?php else: ?>
			<p>
				<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_GENERICUSECLI'); ?>
				<code>
					<?php echo $this->escape($this->croninfo->info->php_path); ?>

					<?php echo $this->escape($this->croninfo->altcli->path); ?>

				</code>
			</p>
			<p>
				<span class="label label-important"><?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_CLIGENERICIMPROTANTINFO'); ?></span>
				<?php echo JText::sprintf('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_CLIGENERICINFO', $this->croninfo->info->php_path); ?>

			</p>
		<?php endif; ?>
</fieldset>
<!-- -->

<fieldset>
	<legend><?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTENDBACKUP'); ?></legend>

	<p class="alert alert-info">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTENDBACKUP_INFO'); ?>
		<br/>
		<a class="btn btn-mini btn-info" href="https://www.akeebabackup.com/documentation/admin-tools/php-file-scanner-frontend.html" target="_blank">
			<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_GENERICREADDOC'); ?>
		</a>
	</p>
	<?php if (!$this->croninfo->info->feenabled): ?>
		<p class="alert alert-error">
			<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_DISABLED'); ?>
		</p>
	<?php elseif (!trim($this->croninfo->info->secret)): ?>
		<p class="alert alert-error">
			<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_SECRET'); ?>
		</p>
	<?php else: ?>
		<p>
			<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTENDBACKUP_MANYMETHODS'); ?>
		</p>


		<ul id="abschedulingTabs" class="nav nav-tabs">
			<li>
				<a href="#absTabWebcron" data-toggle="tab">
					<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTENDBACKUP_TAB_WEBCRON'); ?>
				</a>
			</li>
			<li>
				<a href="#absTabWget" data-toggle="tab">
					<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTENDBACKUP_TAB_WGET'); ?>
				</a>
			</li>
			<li>
				<a href="#absTabCurl" data-toggle="tab">
					<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTENDBACKUP_TAB_CURL'); ?>
				</a>
			</li>
			<li>
				<a href="#absTabScript" data-toggle="tab">
					<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTENDBACKUP_TAB_SCRIPT'); ?>
				</a>
			</li>
			<li>
				<a href="#absTabUrl" data-toggle="tab">
					<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTENDBACKUP_TAB_URL'); ?>
				</a>
			</li>
		</ul>

		<div id="absTabContent" class="tab-content">
			<div class="tab-pane fade" id="absTabWebcron">
				<p>
					<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_WEBCRON'); ?>
				<table class="table table-striped" width="100%">
					<tr>
						<td></td>
						<td>
							<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_WEBCRON_INFO'); ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_WEBCRON_NAME'); ?>
						</td>
						<td>
							<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_WEBCRON_NAME_INFO'); ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_WEBCRON_TIMEOUT'); ?>
						</td>
						<td>
							<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_WEBCRON_TIMEOUT_INFO'); ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_WEBCRON_URL'); ?>
						</td>
						<td>
							<?php echo $this->escape($this->croninfo->info->root_url.'/'.$this->croninfo->frontend->path); ?>

						</td>
					</tr>
					<tr>
						<td>
							<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_WEBCRON_LOGIN'); ?>
						</td>
						<td>
							<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_WEBCRON_LOGINPASSWORD_INFO'); ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_WEBCRON_PASSWORD'); ?>
						</td>
						<td>
							<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_WEBCRON_LOGINPASSWORD_INFO'); ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_WEBCRON_EXECUTIONTIME'); ?>
						</td>
						<td>
							<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_WEBCRON_EXECUTIONTIME_INFO'); ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_WEBCRON_ALERTS'); ?>
						</td>
						<td>
							<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_WEBCRON_ALERTS_INFO'); ?>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_WEBCRON_THENCLICKSUBMIT'); ?>
						</td>
					</tr>
				</table>
				</p>
			</div>
			<div class="tab-pane fade" id="absTabWget">
				<p>
					<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_WGET'); ?>
					<code>
						wget --max-redirect=10000 "<?php echo $this->escape($this->croninfo->info->root_url.'/'.$this->croninfo->frontend->path); ?>" -O - 1>/dev/null 2>/dev/null
					</code>
				</p>
			</div>
			<div class="tab-pane fade" id="absTabCurl">
				<p>
					<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_CURL'); ?>
					<code>
						curl -L --max-redirs 1000 -v "<?php echo $this->escape($this->croninfo->info->root_url.'/'.$this->croninfo->frontend->path); ?>" 1>/dev/null 2>/dev/null
					</code>
				</p>
			</div>
			<div class="tab-pane fade" id="absTabScript">
				<p>
					<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_CUSTOMSCRIPT'); ?>
					<pre>
<?php echo '&lt;?php'; ?>


	$curl_handle=curl_init();
	curl_setopt($curl_handle, CURLOPT_URL, '<?php echo $this->escape($this->croninfo->info->root_url.'/'.$this->croninfo->frontend->path); ?>');
	curl_setopt($curl_handle,CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($curl_handle,CURLOPT_MAXREDIRS, 10000);
	curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER, 1);
	$buffer = curl_exec($curl_handle);
	curl_close($curl_handle);
	if (empty($buffer))
		echo "Sorry, the scan didn't work.";
	else
		echo $buffer;
<?php echo '?&gt;'; ?>

					</pre>
				</p>
			</div>
			<div class="tab-pane fade" id="absTabUrl">
				<p>
					<?php echo \JText::_('COM_ADMINTOOLS_LBL_SCHEDULINGINFORMATION_FRONTEND_RAWURL'); ?>
					<code>
						<?php echo $this->escape($this->croninfo->info->root_url); ?>/<?php echo $this->escape($this->croninfo->frontend->path); ?>

					</code>
				</p>
			</div>
		</div>

	<?php endif; ?>
</fieldset>