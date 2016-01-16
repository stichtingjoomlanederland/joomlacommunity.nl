<?php
/**
 * @package AkeebaReleaseSystem
 * @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

$Itemid_query = empty($Itemid) ? "" : "&Itemid=$Itemid";
$download_url = AKRouter::_('index.php?option=com_ars&view=download&format=raw&id='.$item->id.$Itemid_query);
?>

<div class="panel panel-downloads">
	<div class="panel-heading panel-hover">
		<h3 class="panel-title">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion<?php echo($item->release_id)?>" href="#<?php echo $this->escape($item->alias) ?>" ><?php echo $item->title ?> <br/><small><?php echo $this->escape(strip_tags($item->description)) ?></small></a>
		</h3>
	</div>
	<ul class="list-group panel-collapse collapse"  id="<?php echo $this->escape($item->alias) ?>">
		<li class="list-group-item">
			<span class="info">
				<?php
					$versions = json_decode($item->environments); 
					if($versions):
					foreach($versions as $version):
				?>
				<?php if($version == 1):?>
				<span class="label label-joomla1"><span class="jc-joomla"></span> Joomla 1.5</span>
				<?php elseif($version == 2):?>
				<span class="label label-joomla2"><span class="jc-joomla"></span> Joomla 2.5</span>
				<?php elseif($version == 3):?>
				<span class="label label-joomla3"><span class="jc-joomla"></span> Joomla 3.0</span>
				<?php endif;?>
				<?php endforeach;?>
				<?php endif;?>
			</span>
			<?php echo JText::_('LBL_ITEMS_ENVIRONMENTS') ?>
		</li>
		<li class="list-group-item">
			<span class="info"><?php echo ArsHelperHtml::sizeFormat($item->filesize) ?></span>
			<?php echo JText::_('LBL_ITEMS_FILESIZE') ?>
		</li>
		<li class="list-group-item">
			<span class="info"><?php echo JText::sprintf( ($item->hits == 1 ? 'LBL_RELEASES_TIME' : 'LBL_RELEASES_TIMES'), $item->hits) ?></span>
			<?php echo JText::_('LBL_ITEMS_HITS') ?>
		</li>
		<li class="list-group-item">
			<a href="<?php echo $download_url ?>" rel="nofollow" class="btn btn-block btn-success"><span class="glyphicon glyphicon-download"></span> Download</a>
		</li>
	</ul>
</div>