<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2017 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;
?>
<div class="container-fluid">
	<div class="row-fluid text-center">
		<div class="rsfp-column-option span3">
			<div class="span12 rsfp-column-color"></div>
			<p>
				<button class="btn" onclick="RSFormPro.gridModal.save([12]);" type="button"><?php echo JText::_('RSFP_GRID_ONE_COLUMN'); ?></button>
			</p>
		</div>
		
		<div class="rsfp-column-option span3">
			<div class="span6 rsfp-column-color"></div>
			<div class="span6 rsfp-column-color"></div>
			<p>
				<button class="btn" onclick="RSFormPro.gridModal.save([6,6]);" type="button"><?php echo JText::_('RSFP_GRID_TWO_COLUMNS'); ?></button>
			</p>
		</div>
		
		<div class="rsfp-column-option span3">
			<div class="span4 rsfp-column-color"></div>
			<div class="span4 rsfp-column-color"></div>
			<div class="span4 rsfp-column-color"></div>
			<p>
				<button class="btn" onclick="RSFormPro.gridModal.save([4,4,4]);" type="button"><?php echo JText::_('RSFP_GRID_THREE_COLUMNS'); ?></button>
			</p>
		</div>
		
		<div class="rsfp-column-option span3">
			<div class="span3 rsfp-column-color"></div>
			<div class="span3 rsfp-column-color"></div>
			<div class="span3 rsfp-column-color"></div>
			<div class="span3 rsfp-column-color"></div>
			<p>
				<button class="btn" onclick="RSFormPro.gridModal.save([3,3,3,3]);" type="button"><?php echo JText::_('RSFP_GRID_FOUR_COLUMNS'); ?></button>
			</p>
		</div>
	</div>
</div>
