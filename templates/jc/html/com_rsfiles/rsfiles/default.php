<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<?php if ($this->params->get('show_page_heading') != 0) { ?>
<div class="page-header">
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
</div>
<?php } ?>
<div class="row-fluid">
  
		<?php if ($this->config->show_folder_desc == 1 && !empty($this->fdescription)) { ?>
  <?php echo $this->fdescription; ?>
		<?php } ?>
			<div>
					<?php if (!empty($this->items)) { ?>
               		<?php foreach ($this->items as $i => $item) { ?>
					<?php $canDownload = rsfilesHelper::permissions('CanDownload',$item->fullpath); ?>
					<div class="row">
                      	<div class="content-4">
						<div class="panel panel-downloads">
                        	<div class="panel-heading">
								<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&folder='.rsfilesHelper::encode($item->fullpath).$this->itemid); ?>" class="<?php echo $thumbnail->class; ?>" title="<?php echo $thumbnail->image; ?>">
									<?php echo (!empty($item->filename) ? $item->filename : $item->name); ?>
								</a>
                          	</div> 

						</div>
                          </div>
					</div>
					<?php } ?>
					<?php } ?>
			</div>
		
		<?php if (($this->config->show_pagination_position == 1 || $this->config->show_pagination_position == 2) && $this->pagination->{rsfilesHelper::isJ3() ? 'pagesTotal' : 'pages.total'} > 1) { ?>
		<div class="pagination">
			<p class="counter pull-right">
				<?php echo $this->pagination->getPagesCounter(); ?>
			</p>
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
		<?php } ?>
</div>