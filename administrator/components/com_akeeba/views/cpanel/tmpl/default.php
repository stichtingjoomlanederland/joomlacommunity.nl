<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 *
 * @since 1.3
 *
 * The main page of the Akeeba Backup component is where all the fun takes place :)
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Engine\Platform;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Util\Comconfig;

Platform::getInstance()->load_version_defines();
$lang = JFactory::getLanguage();
$icons_root = JUri::base().'components/com_akeeba/assets/images/';

JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen');

$script = <<<JS

;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
// due to missing trailing semicolon and/or newline in their code.
(function($){
	if ({$this->desktop_notifications})
	{
		akeeba.System.notification.askPermission();
	}

	$(document).ready(function(){
		$('#btnchangelog').click(showChangelog);
	});

	function showChangelog()
	{
		var akeebaChangelogElement = $('#akeeba-changelog').clone().appendTo('body').attr('id', 'akeeba-changelog-clone');

		SqueezeBox.fromElement(
			document.getElementById('akeeba-changelog-clone'), {
				handler: 'adopt',
				size: {
					x: 550,
					y: 500
				}
			}
		);
	}
})(akeeba.jQuery);

JS;
JFactory::getDocument()->addScriptDeclaration($script,'text/javascript');

?>

<?php
// Configuration Wizard prompt
if (!\Akeeba\Engine\Factory::getConfiguration()->get('akeeba.flag.confwiz', 0))
{
	echo $this->loadAnyTemplate('admin:com_akeeba/config/confwiz_modal');
}
?>

<?php if (!empty($this->frontEndSecretWordIssue)): ?>
<div class="alert alert-danger">
	<h3><?php echo JText::_('COM_AKEEBA_CPANEL_ERR_FESECRETWORD_HEADER'); ?></h3>
	<p><?php echo JText::_('COM_AKEEBA_CPANEL_ERR_FESECRETWORD_INTRO'); ?></p>
	<p><?php echo $this->frontEndSecretWordIssue ?></p>
	<p>
		<?php echo JText::_('COM_AKEEBA_CPANEL_ERR_FESECRETWORD_WHATTODO_JOOMLA'); ?>
		<?php echo JText::sprintf('COM_AKEEBA_CPANEL_ERR_FESECRETWORD_WHATTODO_COMMON', $this->newSecretWord); ?>
	</p>
	<p>
		<a class="btn btn-success btn-large"
			href="index.php?option=com_akeeba&view=cpanel&task=resetSecretWord&<?php echo JFactory::getSession()->getToken() ?>=1">
			<span class="icon icon-white icon-refresh"></span>
			<?php echo JText::_('COM_AKEEBA_CPANEL_BTN_FESECRETWORD_RESET'); ?>
		</a>
	</p>
</div>
<?php endif; ?>

<?php
// Obsolete PHP version check
if (version_compare(PHP_VERSION, '5.3.3', 'lt')):
	JLoader::import('joomla.utilities.date');
	$akeebaCommonDatePHP = new JDate('2014-08-14 00:00:00', 'GMT');
	$akeebaCommonDateObsolescence = new JDate('2015-05-14 00:00:00', 'GMT');
	?>
	<div id="phpVersionCheck" class="alert alert-warning">
		<h3><?php echo JText::_('AKEEBA_COMMON_PHPVERSIONTOOOLD_WARNING_TITLE'); ?></h3>
		<p>
			<?php echo JText::sprintf(
				'AKEEBA_COMMON_PHPVERSIONTOOOLD_WARNING_BODY',
				PHP_VERSION,
				$akeebaCommonDatePHP->format(JText::_('DATE_FORMAT_LC1')),
				$akeebaCommonDateObsolescence->format(JText::_('DATE_FORMAT_LC1')),
				'5.5'
			);
			?>
		</p>
	</div>
<?php endif; ?>

<?php if (!$this->fixedpermissions): ?>
<div id="notfixedperms" class="alert alert-error">
	<h3><?php echo JText::_('AKEEBA_CPANEL_WARN_WARNING') ?></h3>
	<p><?php echo JText::_('AKEEBA_CPANEL_WARN_PERMS_L1') ?></p>
	<p><?php echo JText::_('AKEEBA_CPANEL_WARN_PERMS_L2') ?></p>
	<ol>
		<li><?php echo JText::_('AKEEBA_CPANEL_WARN_PERMS_L3A') ?></li>
		<li><?php echo JText::_('AKEEBA_CPANEL_WARN_PERMS_L3B') ?></li>
	</ol>
	<p><?php echo JText::_('AKEEBA_CPANEL_WARN_PERMS_L4') ?></p>
</div>
<?php endif; ?>

<?php if(!version_compare(PHP_VERSION, '5.3.0', 'ge') && Comconfig::getValue('displayphpwarning', 1)): ?>
<div class="alert">
	<a class="close" data-dismiss="alert" href="#">×</a>
	<p><strong><?php echo JText::_('COM_AKEEBA_CONFIG_LBL_OUTDATEDPHP_HEADER') ?></strong><br/>
	<?php echo JText::sprintf('COM_AKEEBA_CONFIG_LBL_OUTDATEDPHP_BODY', PHP_VERSION) ?>
	</p>

	<p>
		<a class="btn btn-small btn-primary" href="index.php?option=com_akeeba&view=cpanel&task=disablephpwarning&<?php echo JFactory::getSession()->getFormToken() ?>=1">
			<?php echo JText::_('COM_AKEEBA_CONFIG_LBL_OUTDATEDPHP_BUTTON'); ?>
		</a>
	</p>
</div>
<?php endif; ?>

<?php if($this->needsdlid): ?>
<div class="alert alert-success">
	<h3>
		<?php echo JText::_('COM_AKEEBA_CPANEL_MSG_MUSTENTERDLID') ?>
	</h3>
	<p>
		<?php echo JText::sprintf('COM_AKEEBA_LBL_CPANEL_NEEDSDLID','https://www.akeebabackup.com/instructions/1435-akeeba-backup-download-id.html'); ?>
	</p>
	<form name="dlidform" action="index.php" method="post" class="form-inline">
		<input type="hidden" name="option" value="com_akeeba" />
		<input type="hidden" name="view" value="cpanel" />
		<input type="hidden" name="task" value="applydlid" />
		<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken()?>" value="1" />
		<span>
			<?php echo JText::_('COM_AKEEBA_CPANEL_MSG_PASTEDLID') ?>
		</span>
		<input type="text" name="dlid" placeholder="<?php echo JText::_('CONFIG_DOWNLOADID_LABEL')?>" class="input-xlarge">
		<button type="submit" class="btn btn-success">
			<span class="icon icon-<?php echo version_compare(JVERSION, '3.0.0', 'ge') ? 'checkbox' : 'ok icon-white' ?>"></span>
			<?php echo JText::_('COM_AKEEBA_CPANEL_MSG_APPLYDLID') ?>
		</button>
	</form>
</div>
<?php elseif ($this->needscoredlidwarning): ?>
<div class="alert alert-danger">
	<?php echo JText::sprintf('COM_AKEEBA_LBL_CPANEL_NEEDSUPGRADE','https://www.akeebabackup.com/videos/1212-akeeba-backup-core/1617-abtc03-upgrade-core-professional.html'); ?>
</div>
<?php endif; ?>

<div id="updateNotice"></div>

<div id="cpanel" class="row-fluid">
	<div class="span8">
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-formstyle-reset form-inline">
			<input type="hidden" name="option" value="com_akeeba" />
			<input type="hidden" name="view" value="cpanel" />
			<input type="hidden" name="task" value="switchprofile" />
			<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken()?>" value="1" />
			<label>
				<?php echo JText::_('CPANEL_PROFILE_TITLE'); ?>: #<?php echo $this->profileid; ?>
			</label>
			<?php echo JHTML::_('select.genericlist', $this->profilelist, 'profileid', 'onchange="document.forms.adminForm.submit()" class="advancedSelect"', 'value', 'text', $this->profileid); ?>
			<button class="btn hidden-phone" onclick="this.form.submit(); return false;">
				<span class="icon-retweet"></span>
				<?php echo JText::_('CPANEL_PROFILE_BUTTON'); ?>
			</button>
		</form>

		<?php if(!empty($this->quickIconProfiles)):
		$token = JFactory::getSession()->getToken();
		?>
		<h3><?php echo JText::_('COM_AKEEBA_CPANEL_HEADER_QUICKBACKUP'); ?></h3>

		<?php foreach($this->quickIconProfiles as $qiProfile): ?>
		<div class="icon">
			<a href="index.php?option=com_akeeba&view=backup&autostart=1&profileid=<?php echo (int) $qiProfile->id ?>&<?php echo $token ?>=1">
				<div class="ak-icon ak-icon-backup">&nbsp;</div>
				<span>
					<?php echo htmlentities($qiProfile->description) ?>
				</span>
			</a>
		</div>
		<?php endforeach; ?>

		<div class="ak_clr"></div>
		<?php endif; ?>

		<h3><?php echo JText::_('CPANEL_HEADER_BASICOPS'); ?></h3>

		<div class="icon">
			<a href="index.php?option=com_akeeba&view=backup">
				<div class="ak-icon ak-icon-backup">&nbsp;</div>
				<span><?php echo JText::_('BACKUP'); ?></span>
			</a>
		</div>

		<div class="icon">
			<a href="index.php?option=com_akeeba&view=transfer">
				<div class="ak-icon ak-icon-stw">&nbsp;</div>
				<span><?php echo JText::_('COM_AKEEBA_TRANSFER');?></span>
			</a>
		</div>

		<div class="icon">
			<a href="index.php?option=com_akeeba&view=buadmin">
				<div class="ak-icon ak-icon-manage">&nbsp;</div>
				<span><?php echo JText::_('BUADMIN'); ?></span>
			</a>
		</div>

		<div class="icon">
			<a href="index.php?option=com_akeeba&view=config">
				<div class="ak-icon ak-icon-configuration">&nbsp;</div>
				<span><?php echo JText::_('CONFIGURATION'); ?></span>
			</a>
		</div>

		<div class="icon">
			<a href="index.php?option=com_akeeba&view=profiles">
				<div class="ak-icon ak-icon-profiles">&nbsp;</div>
				<span><?php echo JText::_('PROFILES'); ?></span>
			</a>
		</div>

		<div class="ak_clr"></div>

		<h3><?php echo JText::_('COM_AKEEBA_CPANEL_HEADER_TROUBLESHOOTING'); ?></h3>

		<div class="icon">
			<a href="index.php?option=com_akeeba&view=log">
				<div class="ak-icon ak-icon-viewlog">&nbsp;</div>
				<span><?php echo JText::_('VIEWLOG'); ?></span>
			</a>
		</div>

		<?php if (AKEEBA_PRO): ?>
		<div class="icon">
			<a href="index.php?option=com_akeeba&view=alices">
				<div class="ak-icon ak-icon-viewlog">&nbsp;</div>
				<span><?php echo JText::_('AKEEBA_ALICE'); ?></span>
			</a>
		</div>
		<?php endif; ?>

		<div class="ak_clr"></div>

		<h3><?php echo JText::_('COM_AKEEBA_CPANEL_HEADER_ADVANCED'); ?></h3>

		<div class="icon">
			<a href="index.php?option=com_akeeba&view=schedule">
				<div class="ak-icon ak-icon-scheduling">&nbsp;</div>
				<span><?php echo JText::_('AKEEBA_SCHEDULE'); ?></span>
			</a>
		</div>

		<?php if (AKEEBA_PRO): ?>
		<div class="icon">
			<a href="index.php?option=com_akeeba&view=discover">
				<div class="ak-icon ak-icon-import">&nbsp;</div>
				<span><?php echo JText::_('DISCOVER'); ?></span>
			</a>
		</div>

		<div class="icon">
			<a href="index.php?option=com_akeeba&view=s3import">
				<div class="ak-icon ak-icon-s3import">&nbsp;</div>
				<span><?php echo JText::_('S3IMPORT'); ?></span>
			</a>
		</div>
		<?php endif; ?>

		<div class="ak_clr"></div>

		<h3><?php echo JText::_('COM_AKEEBA_CPANEL_HEADER_INCLUDEEXCLUDE'); ?></h3>

		<?php if (AKEEBA_PRO): ?>
		<div class="icon">
			<a href="index.php?option=com_akeeba&view=multidb">
				<div class="ak-icon ak-icon-multidb">&nbsp;</div>
				<span><?php echo JText::_('MULTIDB'); ?></span>
			</a>
		</div>

		<div class="icon">
			<a href="index.php?option=com_akeeba&view=eff">
				<div class="ak-icon ak-icon-extradirs">&nbsp;</div>
				<span><?php echo JText::_('EXTRADIRS'); ?></span>
			</a>
		</div>
		<?php endif; ?>

		<div class="icon">
			<a href="index.php?option=com_akeeba&view=fsfilter">
				<div class="ak-icon ak-icon-fsfilter">&nbsp;</div>
				<span><?php echo JText::_('FSFILTERS'); ?></span>
			</a>
		</div>

		<div class="icon">
			<a href="index.php?option=com_akeeba&view=dbef">
				<div class="ak-icon ak-icon-dbfilter">&nbsp;</div>
				<span><?php echo JText::_('DBEF'); ?></span>
			</a>
		</div>

		<?php if (AKEEBA_PRO): ?>
			<div class="icon">
				<a href="index.php?option=com_akeeba&view=regexfsfilter">
					<div class="ak-icon ak-icon-regexfiles">&nbsp;</div>
					<span><?php echo JText::_('REGEXFSFILTERS'); ?></span>
				</a>
			</div>

			<div class="icon">
				<a href="index.php?option=com_akeeba&view=regexdbfilter">
					<div class="ak-icon ak-icon-regexdb">&nbsp;</div>
					<span><?php echo JText::_('REGEXDBFILTERS'); ?></span>
				</a>
			</div>
		<?php endif; ?>

		<div class="ak_clr"></div>

	</div>

	<div class="span4">

		<h3><?php echo JText::_('CPANEL_LABEL_STATUSSUMMARY')?></h3>
		<div>
			<?php echo $this->statuscell ?>

			<?php $quirks = Factory::getConfigurationChecks()->getDetailedStatus(); ?>
			<?php if(!empty($quirks)): ?>
			<div>
				<?php echo $this->detailscell ?>
			</div>
			<hr/>
			<?php endif; ?>

			<?php if(!defined('AKEEBA_PRO')) { $show_donation = 1; } else { $show_donation = (AKEEBA_PRO != 1); } ?>
			<p class="ak_version">
				<?php echo JText::_('AKEEBA').' '.($show_donation?'Core':'Professional ').' '.AKEEBA_VERSION.' ('.AKEEBA_DATE.')' ?>
			</p>
			<!-- CHANGELOG :: BEGIN -->
			<?php if($show_donation): ?>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_s-xclick" />
				<input type="hidden" name="hosted_button_id" value="10903325" />
				<a href="#" id="btnchangelog" class="btn btn-info btn-small">CHANGELOG</a>
				<input type="submit" class="btn btn-inverse btn-small" value="Donate via PayPal" />
				<!--<input class="btn" type="image" src="https://www.paypal.com/en_GB/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online." style="border: none !important; width: 92px; height 26px;" />-->
				<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
			<?php else: ?>
			<a href="#" id="btnchangelog" class="btn btn-info btn-small">CHANGELOG</a>
			<?php endif; ?>
			<div style="display:none;">
				<div id="akeeba-changelog">
					<?php
					require_once dirname(__FILE__).'/coloriser.php';
					echo AkeebaChangelogColoriser::colorise(JPATH_COMPONENT_ADMINISTRATOR.'/CHANGELOG.php');
					?>
				</div>
			</div>
			<!-- CHANGELOG :: END -->

			<a href="index.php?option=com_akeeba&view=update&task=force" class="btn btn-inverse btn-small">
				<?php echo JText::_('COM_AKEEBA_CPANEL_MSG_RELOADUPDATE'); ?>
			</a>
		</div>

		<h3><?php echo JText::_('BACKUP_STATS') ?></h3>
		<div><?php echo $this->statscell ?></div>

	</div>
</div>

<div class="row-fluid footer">
	<div class="span12">
		<p style="height: 6em">
			<?php echo JText::sprintf('Copyright &copy;2006-%s <a href="http://www.akeebabackup.com">Akeeba Ltd</a>. All Rights Reserved.', date('Y')); ?><br/>
			Akeeba Backup is Free Software and is distributed under the terms of the <a href="http://www.gnu.org/licenses/gpl-3.0.html">GNU General Public License</a>, version 3 or - at your option - any later version.
			<?php if(AKEEBA_PRO != 1): ?>
			<br/>If you use Akeeba Backup Core, please post a rating and a review at the <a href="http://extensions.joomla.org/extensions/extension/access-a-security/site-security/akeeba-backup">Joomla! Extensions Directory</a>.
			<?php endif; ?>
			<br/><br/>
			<strong><?php echo JText::_('TRANSLATION_CREDITS')?></strong>:
			<em><?php echo JText::_('TRANSLATION_LANGUAGE') ?></em> &bull;
			<a href="<?php echo JText::_('TRANSLATION_AUTHOR_URL') ?>"><?php echo JText::_('TRANSLATION_AUTHOR') ?></a>
		</p>
	</div>
</div>

<?php
if($this->statsIframe)
{
    echo $this->statsIframe;
}
?>

<script type="text/javascript">
	(function($) {
		$(document).ready(function(){
			<?php if (!$this->needsdlid): ?>
			$.ajax('index.php?option=com_akeeba&view=cpanel&task=updateinfo&tmpl=component', {
				success: function(msg, textStatus, jqXHR)
				{
					// Get rid of junk before and after data
					var match = msg.match(/###([\s\S]*?)###/);
					data = match[1];

					if (data.length)
					{
						$('#updateNotice').html(data);
					}
				}
			});
			<?php endif; ?>

		});
	})(akeeba.jQuery);
</script>
