<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
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

<div class="akeeba-block--failure" style="<?php echo $this->escape($warningStyle); ?>" id="youhavebeenwarnednottodothat">
	<h4>
		<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ALREADYCONFIGURED_HEAD'); ?>
	</h4>
	<p></p>
	<p>
		<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ALREADYCONFIGURED_BODY'); ?>
	</p>
	<p></p>
	<p>
		<a href="index.php?option=com_admintools" class="akeeba-btn--green--large">
			<span class="akion-ios-home"></span>
            <?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ALREADYCONFIGURED_BTN_NO'); ?>
		</a>

		<a onclick="admintools.QuickStart.youWantToBreakYourSite(); return false;"
			class="akeeba-btn--red--small">
			<span class="akion-alert-circled"></span>
			<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ALREADYCONFIGURED_BTN_YES'); ?>
		</a>
	</p>
</div>

<form name="adminForm" id="adminForm" action="index.php" method="post"
	  class="akeeba-form--horizontal"
	  style="<?php echo $this->escape($formStyle); ?>">

	<div class="akeeba-block--info" style="<?php echo $this->escape($formStyle); ?>">
		<p>
			<?php echo $this->escape(JText::sprintf('COM_ADMINTOOLS_QUICKSTART_INTRO', 'https://www.akeebabackup.com/documentation/admin-tools.html')); ?>

		</p>
	</div>

	<div class="akeeba-block--failure" style="<?php echo $this->escape($warningStyle); ?>">
		<h1>
			<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ALREADYCONFIGURED_NOSUPPORT_HEAD'); ?>
		</h1>
		<p>
			<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ALREADYCONFIGURED_NOSUPPORT_BODY'); ?>
		</p>
	</div>

    <div class="akeeba-panel--primary">
        <header class="akeeba-block-header">
            <h3><?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_HEAD_ADMINSECURITY'); ?></h3>
        </header>

        <div class="akeeba-form-group">
            <label for="adminpw"
                   rel="akeeba-sticky-tooltip"
                   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ADMINPW'); ?>"
                   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ADMINPW_TIP'); ?>">
                <?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ADMINPW'); ?>
            </label>

            <input type="text" size="20" name="adminpw" value="<?php echo $this->escape($this->wafconfig['adminpw']); ?>"/>
        </div>

        <div class="akeeba-form-group">
            <label for="admin_username"
                   rel="akeeba-sticky-tooltip"
                   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_TITLE_ADMINPW'); ?>"
                   data-content="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ADMINISTRATORPASSORD_INFO'); ?>">
                <?php echo \JText::_('COM_ADMINTOOLS_TITLE_ADMINPW'); ?>
            </label>

            <div>
                <input type="text" name="admin_username" id="admin_username" value="<?php echo $this->escape($this->admin_username); ?>" autocomplete="off"
                    placeholder="<?php echo \JText::_('COM_ADMINTOOLS_LBL_ADMINPASSWORD_USERNAME'); ?>"
                    />
                <input type="text" name="admin_password" id="admin_password" value="<?php echo $this->escape($this->admin_password); ?>" autocomplete="off"
                       placeholder="<?php echo \JText::_('COM_ADMINTOOLS_LBL_ADMINPASSWORD_PASSWORD'); ?>"
                    />
            </div>
        </div>

        <div class="akeeba-form-group">
            <label for="emailonadminlogin"
                   rel="akeeba-sticky-tooltip"
                   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ADMINLOGINEMAIL_LBL'); ?>"
                   data-content="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ADMINLOGINEMAIL_DESC'); ?>">
                <?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ADMINLOGINEMAIL_LBL'); ?>
            </label>
            
            <input type="text" size="20" name="emailonadminlogin" id="emailonadminlogin"
                   value="<?php echo $this->escape($this->wafconfig['emailonadminlogin']); ?>" >
        </div>

        <div class="akeeba-form-group">
            <label for="ipwl"
                   rel="akeeba-sticky-tooltip"
                   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_IPWL'); ?>"
                   data-content="<?php echo $this->escape(JText::sprintf('COM_ADMINTOOLS_QUICKSTART_WHITELIST_DESC', $this->myIp)); ?>"
                >
                <?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_IPWL'); ?>
            </label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'ipwl', $this->wafconfig['ipwl']); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="nonewadmins"
                   rel="akeeba-sticky-tooltip"
                   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NONEWADMINS'); ?>"
                   data-content="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_NONEWADMINS_DESC'); ?>">
                <?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NONEWADMINS'); ?>
            </label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'nonewadmins', $this->wafconfig['nonewadmins']); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="nofesalogin"
                   rel="akeeba-sticky-tooltip"
                   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NOFESALOGIN'); ?>"
                   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NOFESALOGIN_TIP'); ?>">
                <?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NOFESALOGIN'); ?>
            </label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'nofesalogin', $this->wafconfig['nofesalogin']); ?>
        </div>
    </div>

    <div class="akeeba-panel--primary">
        <header class="akeeba-block-header">
	        <h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_BASIC'); ?></h3>
        </header>

        <div class="akeeba-form-group">
            <label for="enablewaf"
                   rel="akeeba-sticky-tooltip"
                   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ENABLEWAF_LBL'); ?>"
                   data-content="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ENABLEWAF_DESC'); ?>"
                >
                <?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ENABLEWAF_LBL'); ?>
            </label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'enablewaf', 1); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="ipworkarounds"
                   rel="akeeba-sticky-tooltip"
                   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_OPT_IPWORKAROUNDS'); ?>"
                   data-content="<?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_IPWORKAROUNDS_TIP'); ?>"
                >
                <?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_OPT_IPWORKAROUNDS'); ?>
            </label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'ipworkarounds', 1); ?>

            <div class="help-block">
                <?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_IPWORKAROUNDS_RECOMMENDED'); ?>
                <span class="akeeba-label--grey" id="ipWorkaroundsRecommendedSetting">
                    <?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_IPWORKAROUNDS_RECOMMENDED_WAIT'); ?>
                </span>
            </div>
        </div>

        <div class="akeeba-form-group">
            <label for="autoban"
                   rel="akeeba-sticky-tooltip"
                   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_AUTOBAN_LBL'); ?>"
                   data-content="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_AUTOBAN_DESC'); ?>"
                >
                <?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_AUTOBAN_LBL'); ?>
            </label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'autoban', 1); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="autoblacklist"
                   rel="akeeba-sticky-tooltip"
                   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_AUTOBLACKLIST_LBL'); ?>"
                   data-content="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_AUTOBLACKLIST_DESC'); ?>"
                >
                <?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_AUTOBLACKLIST_LBL'); ?>
            </label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'autoblacklist', 1); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="emailbreaches"
                   rel="akeeba-sticky-tooltip"
                   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILBREACHES'); ?>"
                   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILBREACHES_TIP'); ?>">
                <?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILBREACHES'); ?>
            </label>

            <input type="text" size="20" name="emailbreaches" value="<?php echo $this->escape($this->wafconfig['emailbreaches']); ?>">
        </div>
    </div>

    <div class="akeeba-panel--primary">
        <header class="akeeba-block-header">
	        <h3><?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_HEAD_ADVANCEDSECURITY'); ?></h3>
        </header>

        <div class="akeeba-form-group">
            <label for="bbhttpblkey"
                   rel="akeeba-sticky-tooltip"
                   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_BBHTTPBLKEY'); ?>"
                   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_BBHTTPBLKEY_TIP'); ?>">
                <?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_BBHTTPBLKEY'); ?>
            </label>

            <input type="text" size="45" name="bbhttpblkey" value="<?php echo $this->escape($this->wafconfig['bbhttpblkey']); ?>"/>
        </div>

        <div class="akeeba-form-group">
            <label for="htmaker"
                   rel="akeeba-sticky-tooltip"
                   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_HTMAKER_LBL'); ?>"
                   data-content="<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_HTMAKER_DESC'); ?>"
                >
                <?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_HTMAKER_LBL'); ?>
            </label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'htmaker', 1); ?>
        </div>
    </div>

    <div class="akeeba-panel--primary">
        <header class="akeeba-block-header">
	        <h3><?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_HEAD_ALMOSTTHERE'); ?></h3>
        </header>

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
    </div>

	<div class="akeeba-block--failure" style="<?php echo $this->escape($warningStyle); ?>">
		<h1>
			<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ALREADYCONFIGURED_NOSUPPORT_HEAD'); ?>
		</h1>
		<p>
			<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ALREADYCONFIGURED_NOSUPPORT_BODY'); ?>
		</p>
	</div>

	<div class="form-actions" style="<?php echo $this->escape($formStyle); ?>">
		<button type="submit" class="akeeba-btn--primary">
			<?php echo \JText::_('JSAVE'); ?>
		</button>
	</div>

	<div style="<?php echo $this->escape($warningStyle); ?>">
		<button type="submit" class="akeeba-btn--red">
			<span class="akion-alert-circled"></span>
			<?php echo \JText::_('JSAVE'); ?>
		</button>

		<a href="index.php?option=com_admintools"
		   class="akeeba-btn--green--large">
			<span class="akion-ios-home"></span>
			<strong>
				<?php echo \JText::_('COM_ADMINTOOLS_QUICKSTART_ALREADYCONFIGURED_BTN_NO'); ?>
			</strong>
		</a>
	</div>

    <input type="hidden" name="option" value="com_admintools"/>
    <input type="hidden" name="view" value="QuickStart"/>
    <input type="hidden" name="task" value="commit"/>
    <input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
    <input type="hidden" name="detectedip" id="detectedip" value=""/>
</form>
