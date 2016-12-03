<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

$folders = array_filter($this->items, function ($i) { return ($i->type == 'folder'); });
$files   = array_filter($this->items, function ($i) { return ($i->type == 'file'); });
?>

<?php if ($this->params->get('show_page_heading') != 0) : ?>
<div class="page-header">
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
</div>
<?php endif; ?>

<?php echo $this->loadTemplate('navbar'); ?>

<?php if (!empty($folders) || !empty($files)) : ?>
	<div class="row">
		<?php if (!empty($folders)) : ?>
			<?php foreach ($folders as $i => $folder) : ?>
				<?php
				// If the paramter "folder" is not public, we have to append this to our filepath
				if ($this->params->get("folder") != "Public")
				{
					$folder->fullpath = $this->params->get("folder") . DIRECTORY_SEPARATOR . $folder->fullpath;
				}
				?>
				<div class="content-4">
					<div class="panel panel-downloads panel-<?php echo $folder->name; ?>">
						<div class="panel-heading">
							<?php echo (!empty($folder->filename) ? $folder->filename : $folder->name); ?>
						</div>
						<?php echo rsfilesHelper::display(str_replace("%2F", "/", $folder->fullpath), $this->params) ?>
					</div>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php if (!empty($files) && !empty($folders)) : ?>
			<div class="row">
				<div class="content-4">
					<div class="panel panel-downloads panel-files">
						<div class="panel-heading">
							Bestanden
						</div>
						<div class="list-group list-group-flush">
							<?php foreach ($files as $i => $file) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&layout=download&path='.rsfilesHelper::encode($file->fullpath).$file->itemid); ?>" class="list-group-item">
									<i class="rsicon-file"></i> <?php echo (!empty($file->filename) ? $file->filename : $file->name); ?>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>

<?php if (!empty($files) && empty($folders)) : ?>
	<?php foreach ($files as $i => $file) :
		$this->file = $file;
		$this->download = rsfilesHelper::downloadlink($file, $file->fullpath);

		echo $this->loadTemplate('file');
	endforeach; ?>
<?php endif; ?>
