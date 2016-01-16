<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<?php if (!empty($this->legend)) { ?>
	<h3><?php echo JText::_('COM_RSEVENTSPRO_CALENDAR_LEGEND'); ?></h3>
	
	<table width="100%" class="rs_table" id="rsepro-legend">
	<?php $i = 0; ?>
	<?php foreach($this->legend as $category) { ?>
	<?php $i++; ?>
	<?php if ($i % 3 == 1) { ?><tr><?php } ?>
		<td width="33%">
			<div class="rsepro_legend_block">
				<span class="rsepro_legend_color" style="background:<?php echo $category->color; ?>;border:2px solid <?php echo $category->color; ?>"></span> 
				<a class="rsepro_legend_text<?php echo $this->selected == $category->id ? ' rsepro_legend_selected' : ''; ?>" href="javascript:void(0);" onclick="rs_calendar_add_filter('<?php echo !empty($category->id) ? $this->escape($category->title) : ''; ?>','<?php echo (int) $this->params->get('search',1); ?>');"><?php echo $category->title; ?></a>
			</div>
		</td>
	<?php if ($i % 3 == 0) { ?></tr><?php } ?>
	<?php } ?>
	</table>
<?php } ?>