<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var $this \Akeeba\AdminTools\Admin\View\HtaccessMaker\Html */

// Protect from unauthorized access
defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Helper\Select;

$config = $this->htconfig;

?>
<div class="alert alert-info">
	<p>
		<span class="icon icon-question-sign"></span>
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

<div class="alert">
	<h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_WARNING'); ?></h3>

	<p><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_WARNTEXT'); ?></p>

	<p><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_TUNETEXT'); ?></p>
</div>

<form name="adminForm" id="adminForm" action="index.php" method="post" class="form form-horizontal form-horizontal-wide">
<input type="hidden" name="option" value="com_admintools"/>
<input type="hidden" name="view" value="HtaccessMaker"/>
<input type="hidden" name="task" value="save"/>
<input type="hidden" name="<?php echo $this->escape(JFactory::getSession()->getFormToken()); ?>" value="1"/>

<!-- ======================================================================= -->
<fieldset>
	<legend><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_BASICSEC'); ?></legend>

	<div class="control-group">
		<label class="control-label" for="nodirlists"
			   class="control-label"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_NODIRLISTS'); ?></label>

		<div class="controls">
			<?php echo Select::booleanlist('nodirlists', array('class' => 'input-small'), $config->nodirlists); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="fileinj"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_FILEINJ'); ?></label>

		<div class="controls">
			<?php echo Select::booleanlist('fileinj', array('class' => 'input-small'), $config->fileinj); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="phpeaster"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_PHPEASTER'); ?></label>

		<div class="controls">
			<?php echo Select::booleanlist('phpeaster', array('class' => 'input-small'), $config->phpeaster); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="leftovers"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_LEFTOVERS'); ?></label>

		<div class="controls">
			<?php echo Select::booleanlist('leftovers', array('class' => 'input-small'), $config->leftovers); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="clickjacking"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_CLICKJACKING'); ?></label>

		<div class="controls">
			<?php echo Select::booleanlist('clickjacking', array('class' => 'input-small'), $config->clickjacking); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="reducemimetyperisks"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_REDUCEMIMETYPERISKS'); ?></label>

		<div class="controls">
			<?php echo Select::booleanlist('reducemimetyperisks', array('class' => 'input-small'), $config->reducemimetyperisks); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="reflectedxss"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_REFLECTEDXSS'); ?></label>

		<div class="controls">
			<?php echo Select::booleanlist('reflectedxss', array('class' => 'input-small'), $config->reflectedxss); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="noserversignature"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_NOSERVERSIGNATURE'); ?></label>

		<div class="controls">
			<?php echo Select::booleanlist('noserversignature', array('class' => 'input-small'), $config->noserversignature); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="notransform"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_NOTRANSFORM'); ?></label>

		<div class="controls">
			<?php echo Select::booleanlist('notransform', array('class' => 'input-small'), $config->notransform); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="nohoggers"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_NOHOGGERS'); ?></label>

		<div class="controls">
			<?php echo Select::booleanlist('nohoggers', array('class' => 'input-small'), $config->nohoggers); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label"
			   for="hoggeragents"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_HOGGERAGENTS'); ?></label>

		<div class="controls">
			<textarea cols="80" rows="10" name="hoggeragents" id="hoggeragents"
					  class="input-wide"><?php echo implode("\n", $config->hoggeragents); ?></textarea>
		</div>
	</div>
</fieldset>
<!-- ======================================================================= -->
<fieldset>
	<legend><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_SERVERPROT'); ?></legend>

	<h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_SERVERPROT_TOGGLES'); ?></h3>

	<div class="control-group">
		<label class="control-label" for="backendprot"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_BACKENDPROT'); ?></label>

		<div class="controls">
			<?php echo Select::booleanlist('backendprot', array('class' => 'input-small'), $config->backendprot); ?>

		</div>
	</div>
	<div class="control-group">
		<label class="control-label"
			   for="frontendprot"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_FRONTENDPROT'); ?></label>

		<div class="controls">
			<?php echo Select::booleanlist('frontendprot', array('class' => 'input-small'), $config->frontendprot); ?>

		</div>
	</div>

	<h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_SERVERPROT_FINETUNE'); ?></h3>

	<div class="control-group">
		<label class="control-label" for="bepexdirs"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_BEPEXDIRS'); ?></label>

		<div class="controls">
			<textarea cols="80" rows="10" name="bepexdirs"
					  id="bepexdirs"><?php echo $this->escape(implode("\n", $config->bepexdirs)); ?></textarea>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="bepextypes"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_BEPEXTYPES'); ?></label>

		<div class="controls">
			<textarea cols="80" rows="10" name="bepextypes"
					  id="bepextypes"><?php echo $this->escape(implode("\n", $config->bepextypes)); ?></textarea>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="fepexdirs"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_FEPEXDIRS'); ?></label>

		<div class="controls">
			<textarea cols="80" rows="10" name="fepexdirs"
					  id="fepexdirs"><?php echo $this->escape(implode("\n", $config->fepexdirs)); ?></textarea>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="fepextypes"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_FEPEXTYPES'); ?></label>

		<div class="controls">
			<textarea cols="80" rows="10" name="fepextypes"
					  id="fepextypes"><?php echo $this->escape(implode("\n", $config->fepextypes)); ?></textarea>
		</div>
	</div>

	<h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_SERVERPROT_EXCEPTIONS'); ?></h3>

	<div class="control-group">
		<label class="control-label"
			   for="exceptionfiles"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_EXCEPTIONFILES'); ?></label>

		<div class="controls">
			<textarea cols="80" rows="10" name="exceptionfiles"
					  id="exceptionfiles"><?php echo $this->escape(implode("\n", $config->exceptionfiles)); ?></textarea>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"
			   for="exceptiondirs"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_EXCEPTIONDIRS'); ?></label>

		<div class="controls">
			<textarea cols="80" rows="10" name="exceptiondirs"
					  id="exceptiondirs"><?php echo $this->escape(implode("\n", $config->exceptiondirs)); ?></textarea>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"
			   for="fullaccessdirs"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_FULLACCESSDIRS'); ?></label>

		<div class="controls">
			<textarea cols="80" rows="10" name="fullaccessdirs"
					  id="fullaccessdirs"><?php echo $this->escape(implode("\n", $config->fullaccessdirs)); ?></textarea>
		</div>
	</div>
</fieldset>

<!-- ======================================================================= -->
<fieldset>
	<legend><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_CUSTOM'); ?></legend>
	<div class="control-group">
		<label class="control-label" for="custhead"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_CUSTHEAD'); ?></label>

		<div class="controls">
			<textarea cols="80" rows="10" name="custhead" id="custhead"><?php echo $this->escape($config->custhead); ?></textarea>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="custfoot"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_CUSTFOOT'); ?></label>

		<div class="controls">
			<textarea cols="80" rows="10" name="custfoot" id="custfoot"><?php echo $this->escape($config->custfoot); ?></textarea>
		</div>
	</div>
</fieldset>
<!-- ======================================================================= -->
<fieldset>
	<legend><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_OPTUTIL'); ?></legend>

	<div class="control-group">
		<label class="control-label" for="fileorder"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_FILEORDER'); ?></label>

		<div class="controls">
			<?php echo Select::booleanlist('fileorder', array('class' => 'input-small'), $config->fileorder); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="exptime"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_EXPTIME'); ?></label>

		<div class="controls">
			<?php echo Select::booleanlist('exptime', array('class' => 'input-small'), $config->exptime); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label"
			   for="autocompress"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_AUTOCOMPRESS'); ?></label>

		<div class="controls">
			<?php echo Select::booleanlist('autocompress', array('class' => 'input-small'), $config->autocompress); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label"
			   for="forcegzip"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_FORCEGZIP'); ?></label>

		<div class="controls">
			<?php echo Select::booleanlist('forcegzip', array('class' => 'input-small'), $config->forcegzip); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="autoroot"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_AUTOROOT'); ?></label>

		<div class="controls">
			<?php echo Select::booleanlist('autoroot', array('class' => 'input-small'), $config->autoroot); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="wwwredir"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_WWWREDIR'); ?></label>

		<div class="controls">
			<?php echo Select::wwwredirs('wwwredir', null, $config->wwwredir); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="olddomain"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_OLDDOMAIN'); ?></label>

		<div class="controls">
			<input type="text" name="olddomain" id="olddomain" class="input-xlarge"
				   value="<?php echo $this->escape($config->olddomain); ?>">
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="httpsurls"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_HTTPSURLS'); ?></label>

		<div class="controls">
			<textarea cols="80" rows="10" name="httpsurls"
					  id="httpsurls"><?php echo $this->escape(implode("\n", $config->httpsurls)); ?></textarea>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="hstsheader"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_HSTSHEADER'); ?></label>

		<div class="controls">
			<?php echo Select::booleanlist('hstsheader', array('class' => 'input-small'), $config->hstsheader); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="notracetrack"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_NOTRACETRACK'); ?></label>

		<div class="controls">
			<?php echo Select::booleanlist('notracetrack', array('class' => 'input-small'), $config->notracetrack); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="cors"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_CORS'); ?></label>

		<div class="controls">
			<?php echo Select::booleanlist('cors', array('class' => 'input-small'), $config->cors); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="utf8charset"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_UTF8CHARSET'); ?></label>

		<div class="controls">
			<?php echo Select::booleanlist('utf8charset', array('class' => 'input-small'), $config->utf8charset); ?>

		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="etagtype"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_ETAGTYPE'); ?></label>

		<div class="controls">
			<?php echo Select::etagtype('etagtype', array('class' => 'input-medium'), $config->etagtype); ?>

		</div>
	</div>
</fieldset>


<!-- ======================================================================= -->

	<fieldset>
	<legend><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_SYSCONF'); ?></legend>
	<div class="control-group">
		<label class="control-label" for="httpshost"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_HTTPSHOST'); ?></label>

		<div class="controls">
			<input type="text" name="httpshost" id="httpshost" value="<?php echo $this->escape($config->httpshost); ?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="httphost"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_HTTPHOST'); ?></label>

		<div class="controls">
			<input type="text" name="httphost" id="httphost" value="<?php echo $this->escape($config->httphost); ?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="symlinks"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_SYMLINKS'); ?></label>

		<div class="controls">
			<?php echo Select::symlinks('symlinks', array('class' => 'input-medium'), $config->symlinks); ?>

		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="rewritebase"><?php echo \JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_REWRITEBASE'); ?></label>

		<div class="controls">
			<input type="text" name="rewritebase" id="rewritebase" value="<?php echo $this->escape($config->rewritebase); ?>">
		</div>
	</div>

	<div class="clearfix"></div>
</fieldset>
<!-- ======================================================================= -->
</form>