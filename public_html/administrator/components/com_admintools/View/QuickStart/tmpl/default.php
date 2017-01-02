<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var Akeeba\AdminTools\Admin\View\QuickStart\Html $this */
use Akeeba\AdminTools\Admin\Helper\Select;

defined('_JEXEC') or die;

JHtml::_('formbehavior.chosen');

$jNo  = JText::_('JNO');
$jYes = JText::_('JYES');

$formStyle    = $this->isFirstRun ? '' : 'display: none';
$warningStyle = $this->isFirstRun ? 'display: none' : '';
?>

<div class="alert alert-error" style="<?php echo $this->escape($warningStyle); ?>" id="youhavebeenwarnednottodothat">
	<h4>
		<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ALREADYCONFIGURED_HEAD'); ?>
	</h4>
	<p></p>
	<p>
		<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ALREADYCONFIGURED_BODY'); ?>
	</p>
	<p></p>
	<p>
		<a href="index.php?option=com_admintools"
		   class="btn btn-large btn-success">
			<span class="icon icon-home"></span>
			<strong>
				<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ALREADYCONFIGURED_BTN_NO'); ?>
			</strong>
		</a>
		&nbsp;&nbsp;&nbsp;
		<a onclick="admintools.QuickStart.youWantToBreakYourSite(); return false;"
			class="btn btn-mini btn-danger"
		>
			<span class="icon icon-white icon-warning"></span>
			<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ALREADYCONFIGURED_BTN_YES'); ?>
		</a>
	</p>
</div>

<form name="adminForm" id="adminForm" action="index.php" method="post"
	  class="form form-horizontal form-horizontal-wide"
	  style="<?php echo $this->escape($formStyle); ?>"
>
	<div class="alert alert-info" style="<?php echo $this->escape($formStyle); ?>">
		<p>
			<?php echo $this->escape(JText::sprintf('COM_ADMINTOOLS_QUICKSTART_INTRO', 'https://www.akeebabackup.com/documentation/admin-tools.html')); ?>

		</p>
	</div>

	<div class="alert alert-error" style="<?php echo $this->escape($warningStyle); ?>">
		<h1>
			<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ALREADYCONFIGURED_NOSUPPORT_HEAD'); ?>
		</h1>
		<p>
			<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ALREADYCONFIGURED_NOSUPPORT_BODY'); ?>
		</p>
	</div>

	<input type="hidden" name="option" value="com_admintools"/>
	<input type="hidden" name="view" value="QuickStart"/>
	<input type="hidden" name="task" value="commit"/>
	<input type="hidden" name="<?php echo $this->escape(JFactory::getSession()->getFormToken()); ?>" value="1"/>
	<input type="hidden" name="detectedip" id="detectedip" value=""/>

	<h2><?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_HEAD_ADMINSECURITY'); ?></h2>

	<div class="control-group">
		<label class="control-label" for="adminpw"
			   rel="popover"
			   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ADMINPW'); ?>"
			   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ADMINPW_TIP'); ?>">
			<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ADMINPW'); ?>
		</label>

		<div class="controls">
			<input type="text" size="20" name="adminpw" value="<?php echo $this->escape($this->wafconfig['adminpw']); ?>"/>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="admin_username"
			   rel="popover"
			   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_TITLE_ADMINPW'); ?>"
			   data-content="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ADMINISTRATORPASSORD_INFO'); ?>">
			<?php echo \JText::_('COM_ADMINTOOLS_TITLE_ADMINPW'); ?>
		</label>

		<div class="controls">
			<input type="text" name="admin_username" id="admin_username" value="<?php echo $this->escape($this->admin_username); ?>" autocomplete="off"
				placeholder="<?php echo \JText::_('COM_ADMINTOOLS_LBL_ADMINPASSWORD_USERNAME'); ?>"
				/>
			<input type="text" name="admin_password" id="admin_password" value="<?php echo $this->escape($this->admin_password); ?>" autocomplete="off"
				   placeholder="<?php echo \JText::_('COM_ADMINTOOLS_LBL_ADMINPASSWORD_PASSWORD'); ?>"
				/>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="emailonadminlogin"
			   rel="popover"
			   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ADMINLOGINEMAIL_LBL'); ?>"
			   data-content="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ADMINLOGINEMAIL_DESC'); ?>">
			<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ADMINLOGINEMAIL_LBL'); ?>
		</label>

		<div class="controls">
			<input type="text" size="20" name="emailonadminlogin"
				   value="<?php echo $this->escape($this->wafconfig['emailonadminlogin']); ?>" >
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="ipwl"
			   rel="popover"
			   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_IPWL'); ?>"
			   data-content="<?php echo $this->escape(JText::sprintf('COM_ADMINTOOLS_QUICKSTART_WHITELIST_DESC', $this->myIp)); ?>"
			>
			<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_IPWL'); ?>
		</label>

		<div class="controls">
			<?php echo Select::booleanlist('ipwl', array(), $this->wafconfig['ipwl']); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="nonewadmins"
			   rel="popover"
			   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NONEWADMINS'); ?>"
			   data-content="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_NONEWADMINS_DESC'); ?>">
			<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NONEWADMINS'); ?>
		</label>

		<div class="controls">
			<?php echo Select::booleanlist('nonewadmins', array(), $this->wafconfig['nonewadmins']); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="nofesalogin"
			   rel="popover"
			   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NOFESALOGIN'); ?>"
			   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NOFESALOGIN_TIP'); ?>">
			<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NOFESALOGIN'); ?>
		</label>

		<div class="controls">
			<?php echo Select::booleanlist('nofesalogin', array(), $this->wafconfig['nofesalogin']); ?>

		</div>
	</div>

	<h2><?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_BASIC'); ?></h2>


	<div class="control-group">
		<label class="control-label" for="enablewaf"
			   rel="popover"
			   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ENABLEWAF_LBL'); ?>"
			   data-content="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ENABLEWAF_DESC'); ?>"
			>
			<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ENABLEWAF_LBL'); ?>
		</label>

		<div class="controls">
			<?php echo Select::booleanlist('enablewaf', array(), 1); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="ipworkarounds"
			   rel="popover"
			   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_OPT_IPWORKAROUNDS'); ?>"
			   data-content="<?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_IPWORKAROUNDS_TIP'); ?>"
			>
			<?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_OPT_IPWORKAROUNDS'); ?>
		</label>

		<div class="controls">
			<?php echo Select::booleanlist('ipworkarounds', array(), 1); ?>

			<div class="help-block">
				<?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_IPWORKAROUNDS_RECOMMENDED'); ?>
				<span class="label label-default" id="ipWorkaroundsRecommendedSetting">
					<?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_IPWORKAROUNDS_RECOMMENDED_WAIT'); ?>
				</span>
			</div>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="autoban"
			   rel="popover"
			   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_AUTOBAN_LBL'); ?>"
			   data-content="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_AUTOBAN_DESC'); ?>"
			>
			<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_AUTOBAN_LBL'); ?>
		</label>

		<div class="controls">
			<?php echo Select::booleanlist('autoban', array(), 1); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="autoblacklist"
			   rel="popover"
			   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_AUTOBLACKLIST_LBL'); ?>"
			   data-content="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_AUTOBLACKLIST_DESC'); ?>"
			>
			<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_AUTOBLACKLIST_LBL'); ?>
		</label>

		<div class="controls">
			<?php echo Select::booleanlist('autoblacklist', array(), 1); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="emailbreaches"
			   rel="popover"
			   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILBREACHES'); ?>"
			   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILBREACHES_TIP'); ?>">
			<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILBREACHES'); ?>
		</label>

		<div class="controls">
			<input type="text" size="20" name="emailbreaches" value="<?php echo $this->escape($this->wafconfig['emailbreaches']); ?>">
		</div>
	</div>

	<h2><?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_HEAD_ADVANCEDSECURITY'); ?></h2>

	<div class="control-group">
		<label class="control-label" for="bbhttpblkey"
			   rel="popover"
			   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_BBHTTPBLKEY'); ?>"
			   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_BBHTTPBLKEY_TIP'); ?>">
			<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_BBHTTPBLKEY'); ?>
		</label>

		<div class="controls">
			<input type="text" size="45" name="bbhttpblkey" value="<?php echo $this->escape($this->wafconfig['bbhttpblkey']); ?>"/>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="htmaker"
			   rel="popover"
			   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_HTMAKER_LBL'); ?>"
			   data-content="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_HTMAKER_DESC'); ?>"
			>
			<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_HTMAKER_LBL'); ?>
		</label>

		<div class="controls">
			<?php echo Select::booleanlist('htmaker', array(), 1); ?>

		</div>
	</div>

	<h2><?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_HEAD_ALMOSTTHERE'); ?></h2>

	<p>
		<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ALMOSTTHERE_INTRO'); ?>
	</p>
	<ul>
		<li>
			<a href="http://akee.ba/lockedout">http://akee.ba/lockedout</a>
		</li>
		<li>
			<a href="http://akee.ba/500htaccess">http://akee.ba/500htaccess</a>
		</li>
		<li>
			<a href="http://akee.ba/adminpassword">http://akee.ba/adminpassword</a>
		</li>
		<li>
			<a href="http://akee.ba/lockedout">http://akee.ba/403edituser</a>
		</li>
	</ul>
	<p>
		<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ALMOSTTHERE_OUTRO'); ?>
	</p>

	<div class="alert alert-error" style="<?php echo $this->escape($warningStyle); ?>">
		<h1>
			<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ALREADYCONFIGURED_NOSUPPORT_HEAD'); ?>
		</h1>
		<p>
			<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ALREADYCONFIGURED_NOSUPPORT_BODY'); ?>
		</p>
	</div>

	<div class="form-actions" style="<?php echo $this->escape($formStyle); ?>">
		<button type="submit" class="btn btn-primary">
			<?php echo \JText::_('JSAVE'); ?>
		</button>
	</div>

	<div class="form-actions" style="<?php echo $this->escape($warningStyle); ?>">
		<button type="submit" class="btn btn-danger">
			<span class="icon icon-white icon-warning"></span>
			<?php echo \JText::_('JSAVE'); ?>
		</button>

		<a href="index.php?option=com_admintools"
		   class="btn btn-large btn-success">
			<span class="icon icon-home"></span>
			<strong>
				<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ALREADYCONFIGURED_BTN_NO'); ?>
			</strong>
		</a>
	</div>
</form>