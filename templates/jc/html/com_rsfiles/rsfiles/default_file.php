<?php
/**
 * @package RSFiles!
 * @copyright (C) 2010-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */
defined( '_JEXEC' ) or die('Restricted access');
?>

<div class="well download">
	<div class="row">
		<div class="content-7">
			<h2>
				<?php echo (isset($this->file->FileName) ? $this->file->FileName : $this->file->filename); ?>
				<br />
				<?php if ($this->config->show_date_added) : ?>
					<small>
						<?php
						$date = new JDate($this->file->DateAdded);
						echo $date->format("j F Y");
						?>
					</small>
				<?php endif; ?>
			</h2>
			<?php echo $this->file->filedescription; ?>
		</div>
		<div class="content-5">
			<div class="panel panel-downloads">
				<div class="panel-heading">
					<?php echo JText::_("COM_RSFILES_FILE_DETAILS"); ?>
				</div>
				<ul class="list-group list-group-flush">
					<?php if ($this->config->show_file_size) : ?>
						<li class="list-group-item">
							<span class="info">
								<?php echo (isset($this->file->filesize) ? $this->file->filesize : $this->file->size) ?>
							</span>
							<?php echo JText::_("COM_RSFILES_FILE_SIZE"); ?>
						</li>
					<?php endif; ?>
					<?php if ($this->config->show_hits) : ?>
						<li class="list-group-item">
							<span class="info">
								<?php echo $this->file->hits; ?>
							</span>
							<?php echo JText::_("COM_RSFILES_FILE_HITS"); ?>
						</li>
					<?php endif; ?>
					<li class="list-group-item">
						<a class="btn btn-block btn-success <?php echo $this->download->enablemodal; ?>" <?php echo $this->download->rel; ?> href="<?php echo $this->download->dlink; ?>">
							<span class="glyphicon glyphicon-download"></span> Download</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>