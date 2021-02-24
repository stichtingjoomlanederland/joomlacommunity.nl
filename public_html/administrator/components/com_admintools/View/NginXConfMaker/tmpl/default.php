<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var $this  Akeeba\AdminTools\Admin\View\NginXConfMaker\Html */

use Akeeba\AdminTools\Admin\Helper\Select;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') || die;

$config = $this->nginxconfig;

$nginxConfPath = rtrim(JPATH_ROOT, '/\\') . '/nginx.conf';

?>
<div class="akeeba-block--info">
	<p>
		<strong>
			<?php echo Text::_('COM_ADMINTOOLS_LBL_NGINXCONFMAKER_WILLTHISWORK'); ?>
		</strong>
	</p>
	<p>
		<?php if ($this->isSupported == 0): ?>
			<?php echo Text::_('COM_ADMINTOOLS_LBL_NGINXCONFMAKER_WILLTHISWORK_NO'); ?>
		<?php elseif ($this->isSupported == 1): ?>
			<?php echo Text::_('COM_ADMINTOOLS_LBL_NGINXCONFMAKER_WILLTHISWORK_YES'); ?>
		<?php else: ?>
			<?php echo Text::_('COM_ADMINTOOLS_LBL_NGINXCONFMAKER_WILLTHISWORK_MAYBE'); ?>
		<?php endif; ?>
	</p>
</div>

<div class="akeeba-block--warning">
	<h3><?php echo Text::_('COM_ADMINTOOLS_LBL_NGINXCONFMAKER_WARNING'); ?></h3>

	<p><?php echo Text::sprintf('COM_ADMINTOOLS_LBL_NGINXCONFMAKER_WARNTEXT', $nginxConfPath); ?></p>

	<p><?php echo Text::_('COM_ADMINTOOLS_LBL_NGINXCONFMAKER_TUNETEXT'); ?></p>
</div>

<form name="adminForm" id="adminForm" action="index.php" method="post" class="akeeba-form--horizontal">
	<!-- ======================================================================= -->
	<div class="akeeba-panel--primary">
		<header class="akeeba-block-header">
			<h3><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_BASICSEC'); ?></h3>
		</header>

		<div class="akeeba-form-group">
			<label for="nodirlists"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_NODIRLISTS'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'nodirlists', $config->nodirlists); ?>
		</div>
		<div class="akeeba-form-group">
			<label for="fileinj"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_FILEINJ'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'fileinj', $config->fileinj); ?>
		</div>
		<div class="akeeba-form-group">
			<label for="phpeaster"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_PHPEASTER'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'phpeaster', $config->phpeaster); ?>
		</div>
		<div class="akeeba-form-group">
			<label for="leftovers"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_LEFTOVERS'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'leftovers', $config->leftovers); ?>
		</div>
		<div class="akeeba-form-group">
			<label for="clickjacking"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_CLICKJACKING'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'clickjacking', $config->clickjacking); ?>
		</div>
		<div class="akeeba-form-group">
			<label for="nohoggers"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_NOHOGGERS'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'nohoggers', $config->nohoggers); ?>
		</div>
		<div class="akeeba-form-group">
			<label for="hoggeragents"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_HOGGERAGENTS'); ?></label>

			<textarea cols="80" rows="10" name="hoggeragents" id="hoggeragents"
					  class="input-wide"><?php echo $this->escape(implode("\n", $config->hoggeragents)); ?></textarea>
		</div>
		<div class="akeeba-form-group">
			<label for="blockcommon"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_BLOCKCOMMON'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'blockcommon', $config->blockcommon); ?>
		</div>
		<div class="akeeba-form-group">
			<label for="enablesef"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_ENABLESEF'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'enablesef', $config->enablesef); ?>
		</div>
	</div>
	<!-- ======================================================================= -->
	<div class="akeeba-panel--primary">
		<header class="akeeba-block-header">
			<h3><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_SERVERPROT'); ?></h3>
		</header>

		<h3><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_SERVERPROT_TOGGLES'); ?></h3>

		<div class="akeeba-form-group">
			<label for="backendprot"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_BACKENDPROT'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'backendprot', $config->backendprot); ?>
		</div>
		<div class="akeeba-form-group">
			<label
					for="frontendprot"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_FRONTENDPROT'); ?></label>
			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'frontendprot', $config->frontendprot); ?>
		</div>

		<h3><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_SERVERPROT_FINETUNE'); ?></h3>

		<div class="akeeba-form-group">
			<label for="bepexdirs"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_BEPEXDIRS'); ?></label>

			<textarea cols="80" rows="10" name="bepexdirs"
					  id="bepexdirs"><?php echo $this->escape(implode("\n", $config->bepexdirs)); ?></textarea>
		</div>
		<div class="akeeba-form-group">
			<label for="bepextypes"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_BEPEXTYPES'); ?></label>

			<textarea cols="80" rows="10" name="bepextypes"
					  id="bepextypes"><?php echo $this->escape(implode("\n", $config->bepextypes)); ?></textarea>
		</div>
		<div class="akeeba-form-group">
			<label for="fepexdirs"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_FEPEXDIRS'); ?></label>

			<textarea cols="80" rows="10" name="fepexdirs"
					  id="fepexdirs"><?php echo $this->escape(implode("\n", $config->fepexdirs)); ?></textarea>
		</div>
		<div class="akeeba-form-group">
			<label for="fepextypes"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_FEPEXTYPES'); ?></label>

			<textarea cols="80" rows="10" name="fepextypes"
					  id="fepextypes"><?php echo $this->escape(implode("\n", $config->fepextypes)); ?></textarea>
		</div>

		<h3><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_SERVERPROT_EXCEPTIONS'); ?></h3>

		<div class="akeeba-form-group">
			<label for="exceptionfiles"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_EXCEPTIONFILES'); ?></label>

			<textarea cols="80" rows="10" name="exceptionfiles"
					  id="exceptionfiles"><?php echo $this->escape(implode("\n", $config->exceptionfiles)); ?></textarea>
		</div>
		<div class="akeeba-form-group">
			<label for="exceptiondirs"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_EXCEPTIONDIRS'); ?></label>

			<textarea cols="80" rows="10" name="exceptiondirs"
					  id="exceptiondirs"><?php echo $this->escape(implode("\n", $config->exceptiondirs)); ?></textarea>
		</div>
		<div class="akeeba-form-group">
			<label for="fullaccessdirs"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_FULLACCESSDIRS'); ?></label>

			<div>
                <textarea cols="80" rows="10" name="fullaccessdirs"
						  id="fullaccessdirs"><?php echo $this->escape(implode("\n", $config->fullaccessdirs)); ?></textarea>
			</div>
		</div>
	</div>

	<!-- ======================================================================= -->
	<div class="akeeba-panel--primary">
		<header class="akeeba-block-header">
			<h3><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_KITCHENSINK'); ?></h3>
		</header>
		<div class="akeeba-form-group">
			<label for="cfipfwd"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_CFIPFWD'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'cfipfwd', $config->cfipfwd); ?>
		</div>
		<div class="akeeba-form-group">
			<label for="opttimeout"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_OPTTIMEOUT'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'opttimeout', $config->opttimeout); ?>
		</div>
		<div class="akeeba-form-group">
			<label for="optsockets"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_OPTSOCKETS'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'optsockets', $config->optsockets); ?>
		</div>
		<div class="akeeba-form-group">
			<label for="opttcpperf"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_OPTTCPPERF'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'opttcpperf', $config->opttcpperf); ?>
		</div>
		<div class="akeeba-form-group">
			<label for="optoutbuf"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_OPTOUTBUF'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'optoutbuf', $config->optoutbuf); ?>
		</div>
		<div class="akeeba-form-group">
			<label for="optfhndlcache"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_OPTFHNDLCACHE'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'optfhndlcache', $config->optfhndlcache); ?>
		</div>
		<div class="akeeba-form-group">
			<label for="encutf8"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_ENCUTF8'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'encutf8', $config->encutf8); ?>
		</div>
		<div class="akeeba-form-group">
			<label for="nginxsecurity"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_NGINXSECURITY'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'nginxsecurity', $config->nginxsecurity); ?>
		</div>
		<div class="akeeba-form-group">
			<label for="maxclientbody"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_MAXCLIENTBODY'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'maxclientbody', $config->maxclientbody); ?>
		</div>

	</div>
	<!-- ======================================================================= -->
	<div class="akeeba-panel--primary">
		<header class="akeeba-block-header">
			<h3><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_OPTUTIL'); ?></h3>
		</header>

		<div class="akeeba-form-group">
			<label for="fileorder"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_FILEORDER'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'fileorder', $config->fileorder); ?>
		</div>
		<div class="akeeba-form-group">
			<label for="exptime"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_EXPTIME'); ?></label>

			<?= Select::exptime('exptime', null, $config->exptime); ?>
		</div>
		<div class="akeeba-form-group">
			<label for="autocompress"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_AUTOCOMPRESS'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'autocompress', $config->autocompress); ?>
		</div>
		<div class="akeeba-form-group">
			<label for="wwwredir"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_WWWREDIR'); ?></label>

			<?php
			$value   = $config->wwwredir;
			$attribs = null;

			if (!$this->enableRedirects)
			{
				$value   = 0;
				$attribs = ['disabled' => 'disabled'];
			}

			echo Select::wwwredirs('wwwredir', $attribs, $value);
			?>
		</div>
		<div class="akeeba-form-group">
			<label for="olddomain"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_OLDDOMAIN'); ?></label>

			<input type="text" name="olddomain" id="olddomain" value="<?php echo $this->escape($config->olddomain); ?>">
		</div>
		<div class="akeeba-form-group">
			<label for="hstsheader"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_HSTSHEADER'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'hstsheader', $config->hstsheader); ?>
		</div>
		<div class="akeeba-form-group">
			<label for="notracetrack"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_NOTRACETRACK'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'notracetrack', $config->notracetrack); ?>
		</div>

		<div class="akeeba-form-group">
			<label for="cors"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_CORS'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.genericlist', Select::getCorsOptions(), 'cors', ['list.select' => $config->cors]); ?>
		</div>

		<div class="akeeba-form-group">
			<label for="reducemimetyperisks"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_REDUCEMIMETYPERISKS'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'reducemimetyperisks', $config->reducemimetyperisks); ?>
		</div>

		<div class="akeeba-form-group">
			<label for="reflectedxss"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_REFLECTEDXSS'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'reflectedxss', $config->reflectedxss); ?>
		</div>

		<div class="akeeba-form-group">
			<label for="svgneutralise"><?php echo JText::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_SVGNEUTRALISE'); ?></label>

			<?php echo JHtml::_('FEFHelper.select.booleanswitch', 'svgneutralise', $config->svgneutralise); ?>
		</div>

		<div class="akeeba-form-group">
			<label for="notransform"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_NOTRANSFORM'); ?></label>

			<?php echo HTMLHelper::_('FEFHelper.select.booleanswitch', 'notransform', $config->notransform); ?>
		</div>

		<div class="akeeba-form-group">
			<label for="etagtype"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_ETAGTYPE'); ?></label>

			<?php echo Select::etagtypeNginX('etagtype', ['class' => 'input-medium'], $config->etagtype); ?>
		</div>

		<div class="akeeba-form-group">
			<label for="referrerpolicy"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_REFERERPOLICY'); ?></label>

			<?php echo Select::referrerpolicy('referrerpolicy', [], $config->referrerpolicy); ?>
		</div>
	</div>
	<!-- ======================================================================= -->
	<div class="akeeba-panel--primary">
		<header class="akeeba-block-header">
			<h3><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_SYSCONF'); ?></h3>
		</header>

		<div class="akeeba-form-group">
			<label for="httpshost"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_HTTPSHOST'); ?></label>

			<input type="text" name="httpshost" id="httpshost" value="<?php echo $this->escape($config->httpshost); ?>">
		</div>
		<div class="akeeba-form-group">
			<label for="httphost"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_HTTPHOST'); ?></label>

			<input type="text" name="httphost" id="httphost" value="<?php echo $this->escape($config->httphost); ?>">
		</div>
		<div class="akeeba-form-group">
			<label for="symlinks"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_SYMLINKS'); ?></label>

			<?php echo Select::symlinks('symlinks', $config->symlinks); ?>
		</div>
		<div class="akeeba-form-group">
			<label for="rewritebase"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_REWRITEBASE'); ?></label>

			<input type="text" name="rewritebase" id="rewritebase"
				   value="<?php echo $this->escape($config->rewritebase); ?>">
		</div>
		<div class="akeeba-form-group">
			<label for="fastcgi_pass_block"><?php echo Text::_('COM_ADMINTOOLS_LBL_HTACCESSMAKER_FASTCGIPASSBLOCK'); ?></label>

			<textarea name="fastcgi_pass_block" id="fastcgi_pass_block" cols="80"
					  rows="5"><?php echo $this->escape($config->fastcgi_pass_block); ?></textarea>
		</div>
	</div>
	<!-- ======================================================================= -->
	<input type="hidden" name="option" value="com_admintools" />
	<input type="hidden" name="view" value="NginXConfMaker" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1" />
</form>
