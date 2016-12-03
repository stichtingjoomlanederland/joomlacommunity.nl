<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="page-header">
	<h1><?php echo JText::sprintf('COM_RSFILES_DETAILS_FOR',$this->file->fname); ?></h1>
</div>

<div class="rsfiles-layout">
<div class="row-fluid">
	<div class="span12">
		<?php echo $this->loadTemplate('navbar'); ?>
		
		<table class="table table-striped table-hover table-bordered">
			<?php if ($this->file->thumb) { ?>
			<tr>
				<td colspan="2" class="center" align="center"><img src="<?php echo $this->file->thumb; ?>" alt="" class="img-polaroid" /></td>
			</tr>
			<?php } ?>
			<tr>
				<td><b><?php echo JText::_('COM_RSFILES_FILE_NAME'); ?></b></td>
				<td><?php echo $this->file->fname; ?></td>
			</tr>
			<tr>
				<td><b><?php echo JText::_('COM_RSFILES_FILE_DESCRIPTION'); ?></b></td>
				<td><?php echo $this->file->filedescription; ?></td>
			</tr>
			<tr>
				<td><b><?php echo JText::_('COM_RSFILES_FILE_LICENSE'); ?></b></td>
				<td>
					<?php if (!empty($this->file->filelicense)) { ?>
					<a class="rs_modal" rel="{handler: 'iframe'}" href="<?php echo $this->file->filelicense; ?>"><?php echo $this->file->LicenseName; ?></a>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td><b><?php echo JText::_('COM_RSFILES_THE_FILE_NAME'); ?></b></td>
				<td><?php echo $this->file->filename; ?></td>
			</tr>
			<tr>
				<td><b><?php echo JText::_('COM_RSFILES_FILE_VERSION'); ?></b></td>
				<td><?php echo $this->file->fileversion; ?></td>
			</tr>
			<tr>
				<td><b><?php echo JText::_('COM_RSFILES_FILE_SIZE'); ?></b></td>
				<td><?php echo $this->file->filesize; ?></td>
			</tr>
			<tr>
				<td><b><?php echo JText::_('COM_RSFILES_FILE_TYPE'); ?></b></td>
				<td><?php echo $this->file->filetype; ?></td>
			</tr>
			<tr>
				<td><b><?php echo JText::_('COM_RSFILES_FILE_OWNER'); ?></b></td>
				<td><?php echo $this->file->owner; ?></td>
			</tr>
			<tr>
				<td><b><?php echo JText::_('COM_RSFILES_FILE_DATE_ADDED'); ?></b></td>
				<td><?php echo $this->file->dateadded; ?></td>
			</tr>
			<tr>
				<td><b><?php echo JText::_('COM_RSFILES_FILE_HITS'); ?></b></td>
				<td><?php echo $this->file->hits; ?></td>
			</tr>
			<tr>
				<td><b><?php echo JText::_('COM_RSFILES_FILE_LAST_MODIFIED'); ?></b></td>
				<td><?php echo $this->file->lastmodified; ?></td>
			</tr>
			<tr>
				<td><b><?php echo JText::_('COM_RSFILES_FILE_CHECKSUM'); ?></b></td>
				<td><?php echo $this->file->checksum; ?></td>
			</tr>
		</table>
	</div>
</div>
</div>