<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var \Akeeba\Backup\Admin\View\Browser\Html $this */

$rootDirWarning = JText::_('COM_AKEEBA_CONFIG_UI_ROOTDIR', true);

$this->addJavascriptInline(<<<JS

	;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
	// due to missing trailing semicolon and/or newline in their code.
	function akeeba_browser_useThis()
	{
		var rawFolder = document.forms.adminForm.folderraw.value;
		if( rawFolder == '[SITEROOT]' )
		{
			alert('$rootDirWarning');
			rawFolder = '[SITETMP]';
		}
		window.parent.akeeba.Configuration.onBrowserCallback( rawFolder );
	}

JS
, 'text/javascript');

?>

<?php if(empty($this->folder)): ?>
	<form action="index.php" method="post" name="adminForm" id="adminForm">
		<input type="hidden" name="option" value="com_akeeba"/>
		<input type="hidden" name="view" value="Browser"/>
		<input type="hidden" name="format" value="html"/>
		<input type="hidden" name="tmpl" value="component"/>
		<input type="hidden" name="folder" id="folder" value=""/>
		<input type="hidden" name="processfolder" id="processfolder" value="0"/>
		<input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
	</form>
<?php endif; ?>

<?php if ( ! (empty($this->folder))): ?>
<div class="row-fluid">
	<div class="span12">
		<form action="index.php" method="get" name="adminForm" id="adminForm" class="form-inline">
			<input type="hidden" name="option" value="com_akeeba"/>
			<input type="hidden" name="view" value="Browser"/>
			<input type="hidden" name="tmpl" value="component"/>
			<div class="input-prepend">
				<span class="add-on" title="<?php echo \JText::_($this->writable ? 'WRITEABLE' : 'COM_AKEEBA_CPANEL_LBL_UNWRITABLE'); ?>">
					<span class="icon-<?php echo $this->writable ? 'ok' : 'ban-circle'; ?>"></span>
				</span>
				<input class="input-xlarge" type="text" name="folder" id="folder" value="<?php echo $this->escape($this->folder); ?>"/>
			</div>
			<input type="hidden" name="folderraw" id="folderraw" value="<?php echo $this->escape($this->folder_raw); ?>"/>
			<button class="btn btn-primary" onclick="document.form.adminForm.submit(); return false;">
				<span class="icon-folder-open icon-white"></span>
				<?php echo \JText::_('COM_AKEEBA_BROWSER_LBL_GO'); ?>
			</button>
			<button class="btn btn-success" onclick="akeeba_browser_useThis(); return false;">
				<span class="icon-share icon-white"></span>
				<?php echo \JText::_('COM_AKEEBA_BROWSER_LBL_USE'); ?>
			</button>
			<input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
		</form>
	</div>
</div>

<?php if(count($this->breadcrumbs)): ?>
<div class="row-fluid">
	<div class="span12">
		<ul class="breadcrumb">
			<?php $i = 0 ?>
			<?php foreach($this->breadcrumbs as $crumb): ?>
			<?php $i++; ?>
			<li class="<?php echo ($i < count($this->breadcrumbs)) ? '' : 'active'; ?>">
				<?php if($i < count($this->breadcrumbs)): ?>
				<a href="<?php echo $this->escape(JUri::base() . "index.php?option=com_akeeba&view=Browser&tmpl=component&folder=" . urlencode($crumb['folder'])); ?>">
					<?php echo $this->escape($crumb['label']); ?>

				</a>
				<span class="divider">&bull;</span>
				<?php else: ?>
				<?php echo $this->escape($crumb['label']); ?>

				<?php endif; ?>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php endif; ?>

<div class="row-fluid">
	<div class="span12">
		<?php if(count($this->subfolders)): ?>
		<table class="table table-striped">
			<tr>
				<td>
					<a class="btn btn-mini btn-inverse"
					   href="<?php echo $this->escape(JUri::base()); ?>index.php?option=com_akeeba&view=Browser&tmpl=component&folder=<?php echo $this->escape($this->parent); ?>">
						<span class="icon-arrow-up icon-white"></span>
						<?php echo \JText::_('COM_AKEEBA_BROWSER_LBL_GOPARENT'); ?>
					</a>
				</td>
			</tr>
			<?php foreach($this->subfolders as $subfolder): ?>
			<tr>
				<td>
					<a href="<?php echo $this->escape(JUri::base()); ?>index.php?option=com_akeeba&view=Browser&tmpl=component&folder=<?php echo $this->escape($this->folder . '/' . $subfolder); ?>"><?php echo $this->escape($subfolder); ?></a>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>
		<?php else: ?>
			<?php if(!$this->exists): ?>
			<div class="alert alert-error">
				<?php echo \JText::_('COM_AKEEBA_BROWSER_ERR_NOTEXISTS'); ?>
			</div>
			<?php elseif(!$this->inRoot): ?>
			<div class="alert">
				<?php echo \JText::_('COM_AKEEBA_BROWSER_ERR_NONROOT'); ?>
			</div>
			<?php elseif($this->openbasedirRestricted): ?>
			<div class="alert alert-error">
				<?php echo \JText::_('COM_AKEEBA_BROWSER_ERR_BASEDIR'); ?>
			</div>
			<?php else: ?>
			<table class="table table-striped">
				<tr>
					<td>
						<a class="btn btn-mini btn-inverse"
						   href="<?php echo $this->escape(JUri::base()); ?>index.php?option=com_akeeba&view=Browser&tmpl=component&folder=<?php echo $this->escape($this->parent); ?>">
							<span class="icon-arrow-up icon-white"></span>
							<?php echo \JText::_('COM_AKEEBA_BROWSER_LBL_GOPARENT'); ?>
						</a>
					</td>
				</tr>
			</table>
			<?php endif; ?> <?php /* secondary block */ ?>
		<?php endif; ?> <?php /* count($this->subfolders) */ ?>
	</div>
</div>
<?php endif; ?>