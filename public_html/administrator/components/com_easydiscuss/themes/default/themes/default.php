<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" data-ed-form>
	<div class="panel-table">
		<table class="app-table app-table-middle table table-striped" data-ed-table>
			<thead>
				<tr>
					<td width="1%" class="center">
                    &nbsp;
               		</td>	
					<td style="text-align:left;">
						<?php echo JText::_('COM_EASYDISCUSS_THEMES_TITLE'); ?>
					</td>
					<td width="5%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_THEMES_DEFAULT'); ?>
					</td>
					<td width="5%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_THEMES_VERSION'); ?>
					</td>
					<td width="10%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_THEMES_UPDATED'); ?>
					</td>
					<td width="15%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_THEMES_AUTHOR'); ?>
					</td>
					
				</tr>
			</thead>
			<tbody>
				
				<?php $i = 0; ?>
				<?php foreach ($themes as $theme) { ?>
				<tr>
					<td class="center">
					    <input type="radio" name="cid[]" value="<?php echo $theme->element;?>" onclick="Joomla.isChecked(this.checked);" data-ed-table-checkbox />
					</td>
					
					<td>
	                    <h4>
	                        <?php echo JText::_($this->escape($theme->name));?>
	                    </h4>
	                </td>

					<td class="center">
						<?php echo $this->html('table.featured', 'themes', $theme, 'featured', 'makeDefault', $theme->element != $this->config->get('layout_site_theme')); ?>
					</td>
					<td class="center">
						<?php echo $theme->version; ?>
					</td>

					<td class="center">
						<?php echo $theme->updated; ?>
					</td>
					<td class="center">
						<?php echo $theme->author; ?>
					</td>	
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>

	<?php echo $this->html('form.hidden', 'themes', 'themes'); ?>
	
</form>