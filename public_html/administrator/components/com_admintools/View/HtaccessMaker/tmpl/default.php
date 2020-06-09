<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var $this \Akeeba\AdminTools\Admin\View\HtaccessMaker\Html */

// Protect from unauthorized access
defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Helper\Select;

$config = $this->htconfig;

?>
<div class="akeeba-block--info">
	<p>
		<strong>
			<?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_WILLTHISWORK'); ?>
		</strong>
	</p>
	<p>
		<?php if ($this->isSupported == 0): ?>
			<?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_WILLTHISWORK_NO'); ?>
		<?php elseif ($this->isSupported == 1): ?>
			<?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_WILLTHISWORK_YES'); ?>
		<?php else: ?>
			<?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_WILLTHISWORK_MAYBE'); ?>
		<?php endif; ?>
	</p>
</div>

<div class="akeeba-block--warning">
	<h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_WARNING'); ?></h3>

	<p><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_WARNTEXT'); ?></p>

	<p><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_TUNETEXT'); ?></p>
</div>

<form name="adminForm" id="adminForm" action="index.php" method="post" class="akeeba-form--horizontal">
<!-- ======================================================================= -->
    <div class="akeeba-panel--primary">
        <header class="akeeba-block-header">
            <h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_BASICSEC'); ?></h3>
        </header>

        <div class="akeeba-form-group">
            <label for="nodirlists"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_NODIRLISTS'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'nodirlists', $config->nodirlists); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="fileinj"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_FILEINJ'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'fileinj', $config->fileinj); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="phpeaster"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_PHPEASTER'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'phpeaster', $config->phpeaster); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="leftovers"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_LEFTOVERS'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'leftovers', $config->leftovers); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="clickjacking"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_CLICKJACKING'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'clickjacking', $config->clickjacking); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="reducemimetyperisks"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_REDUCEMIMETYPERISKS'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'reducemimetyperisks', $config->reducemimetyperisks); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="reflectedxss"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_REFLECTEDXSS'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'reflectedxss', $config->reflectedxss); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="noserversignature"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_NOSERVERSIGNATURE'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'noserversignature', $config->noserversignature); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="notransform"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_NOTRANSFORM'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'notransform', $config->notransform); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="nohoggers"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_NOHOGGERS'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'nohoggers', $config->nohoggers); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="hoggeragents"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_HOGGERAGENTS'); ?></label>

            <textarea rows="10" name="hoggeragents" id="hoggeragents"><?php echo implode("\n", $config->hoggeragents); ?></textarea>
        </div>
    </div>
    <!-- ======================================================================= -->
    <div class="akeeba-panel--primary">
        <header class="akeeba-block-header">
            <h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_SERVERPROT'); ?></h3>
        </header>

        <h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_SERVERPROT_TOGGLES'); ?></h3>

        <div class="akeeba-form-group">
            <label for="backendprot"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_BACKENDPROT'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'backendprot', $config->backendprot); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="frontendprot"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_FRONTENDPROT'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'frontendprot', $config->frontendprot); ?>
        </div>

        <h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_SERVERPROT_FINETUNE'); ?></h3>

        <div class="akeeba-form-group">
            <label for="bepexdirs"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_BEPEXDIRS'); ?></label>

            <textarea cols="80" rows="10" name="bepexdirs"
                      id="bepexdirs"><?php echo $this->escape(implode("\n", $config->bepexdirs)); ?></textarea>
        </div>

        <div class="akeeba-form-group">
            <label for="bepextypes"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_BEPEXTYPES'); ?></label>

            <textarea cols="80" rows="10" name="bepextypes"
                      id="bepextypes"><?php echo $this->escape(implode("\n", $config->bepextypes)); ?></textarea>
        </div>

        <div class="akeeba-form-group">
            <label for="fepexdirs"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_FEPEXDIRS'); ?></label>

            <textarea cols="80" rows="10" name="fepexdirs"
                      id="fepexdirs"><?php echo $this->escape(implode("\n", $config->fepexdirs)); ?></textarea>
        </div>
        <div class="akeeba-form-group">
            <label for="fepextypes"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_FEPEXTYPES'); ?></label>

            <textarea cols="80" rows="10" name="fepextypes"
                      id="fepextypes"><?php echo $this->escape(implode("\n", $config->fepextypes)); ?></textarea>
        </div>

        <h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_SERVERPROT_EXCEPTIONS'); ?></h3>

        <div class="akeeba-form-group">
            <label for="exceptionfiles"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_EXCEPTIONFILES'); ?></label>

            <textarea cols="80" rows="10" name="exceptionfiles"
                      id="exceptionfiles"><?php echo $this->escape(implode("\n", $config->exceptionfiles)); ?></textarea>
        </div>

        <div class="akeeba-form-group">
            <label for="exceptiondirs"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_EXCEPTIONDIRS'); ?></label>

            <textarea cols="80" rows="10" name="exceptiondirs"
                      id="exceptiondirs"><?php echo $this->escape(implode("\n", $config->exceptiondirs)); ?></textarea>
        </div>

        <div class="akeeba-form-group">
            <label for="fullaccessdirs"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_FULLACCESSDIRS'); ?></label>

            <textarea cols="80" rows="10" name="fullaccessdirs"
                      id="fullaccessdirs"><?php echo $this->escape(implode("\n", $config->fullaccessdirs)); ?></textarea>
        </div>
    </div>

    <!-- ======================================================================= -->
    <div class="akeeba-panel--primary">
        <header class="akeeba-block-header">
            <h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_CUSTOM'); ?></h3>
        </header>

        <div class="akeeba-form-group">
            <label for="custhead"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_CUSTHEAD'); ?></label>

            <textarea cols="80" rows="10" name="custhead" id="custhead"><?php echo $this->escape($config->custhead); ?></textarea>
        </div>

        <div class="akeeba-form-group">
            <label for="custfoot"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_CUSTFOOT'); ?></label>

            <textarea cols="80" rows="10" name="custfoot" id="custfoot"><?php echo $this->escape($config->custfoot); ?></textarea>
        </div>
    </div>
    <!-- ======================================================================= -->
    <div class="akeeba-panel--primary">
        <header class="akeeba-block-header">
            <h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_OPTUTIL'); ?></h3>
        </header>

        <div class="akeeba-form-group">
            <label for="fileorder"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_FILEORDER'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'fileorder', $config->fileorder); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="exptime"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_EXPTIME'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'exptime', $config->exptime); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="autocompress"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_AUTOCOMPRESS'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'autocompress', $config->autocompress); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="forcegzip"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_FORCEGZIP'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'forcegzip', $config->forcegzip); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="autoroot"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_AUTOROOT'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'autoroot', $config->autoroot); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="wwwredir"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_WWWREDIR'); ?></label>

        <?php
            $value = $config->wwwredir;
            $attribs = null;

            if (!$this->enableRedirects)
            {
                $value = 0;
                $attribs = ['disabled' => 'disabled'];
            }

            echo Select::wwwredirs('wwwredir', $attribs, $value);
        ?>
        </div>

        <div class="akeeba-form-group">
            <label for="olddomain"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_OLDDOMAIN'); ?></label>

            <input type="text" name="olddomain" id="olddomain" value="<?php echo $this->escape($config->olddomain); ?>">
        </div>

        <div class="akeeba-form-group">
            <label for="httpsurls"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_HTTPSURLS'); ?></label>

            <textarea cols="80" rows="10" name="httpsurls"
                      id="httpsurls"><?php echo $this->escape(implode("\n", $config->httpsurls)); ?></textarea>
        </div>

        <div class="akeeba-form-group">
            <label for="hstsheader"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_HSTSHEADER'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'hstsheader', $config->hstsheader); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="notracetrack"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_NOTRACETRACK'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'notracetrack', $config->notracetrack); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="cors"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_CORS'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'cors', $config->cors); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="utf8charset"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_UTF8CHARSET'); ?></label>

            <div>
                <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'utf8charset', $config->utf8charset); ?>
            </div>
        </div>

        <div class="akeeba-form-group">
            <label for="etagtype"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_ETAGTYPE'); ?></label>

            <?php echo Select::etagtype('etagtype', array('class' => 'input-medium'), $config->etagtype); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="referrerpolicy"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_REFERERPOLICY'); ?></label>

			<?php echo Select::referrerpolicy('referrerpolicy', array(), $config->referrerpolicy); ?>
        </div>
    </div>

    <div class="akeeba-panel--primary">
        <header class="akeeba-block-header">
            <h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_SYSCONF'); ?></h3>
        </header>

        <div class="akeeba-form-group">
            <label for="httpshost"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_HTTPSHOST'); ?></label>

            <input type="text" name="httpshost" id="httpshost" value="<?php echo $this->escape($config->httpshost); ?>">
        </div>

        <div class="akeeba-form-group">
            <label for="httphost"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_HTTPHOST'); ?></label>

            <input type="text" name="httphost" id="httphost" value="<?php echo $this->escape($config->httphost); ?>">
        </div>

        <div class="akeeba-form-group">
            <label for="symlinks"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_SYMLINKS'); ?></label>

            <?php echo Select::symlinks('symlinks', array('class' => 'input-medium'), $config->symlinks); ?>
        </div>

        <div class="akeeba-form-group">
            <label for="rewritebase"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_REWRITEBASE'); ?></label>

            <input type="text" name="rewritebase" id="rewritebase" value="<?php echo $this->escape($config->rewritebase); ?>">
        </div>
    </div>

    <input type="hidden" name="option" value="com_admintools"/>
    <input type="hidden" name="view" value="HtaccessMaker"/>
    <input type="hidden" name="task" value="save"/>
    <input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
</form>
