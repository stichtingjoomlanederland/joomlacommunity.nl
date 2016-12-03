<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="config_plugins">

	<div class="acyblockoptions" style="width: 42%;min-width: 480px;">
		<span class="acyblocktitle"><?php echo JText::_('PLUG_TAG') ?></span>
		<table class="acymailing_table" cellpadding="1">
			<thead>
			<tr>
				<th class="title titlenum">
					<?php echo JText::_('ACY_NUM'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('ACY_NAME'); ?>
				</th>
				<th class="title titletoggle">
					<?php echo JText::_('ENABLED'); ?>
				</th>
				<th class="title titleid">
					<?php echo JText::_('ACY_ID'); ?>
				</th>
			</tr>
			</thead>
			<tbody>
			<?php
			$k = 0;

			for($i = 0, $a = count($this->plugins); $i < $a; $i++){
				$row =& $this->plugins[$i];

				$publishedid = 'published_'.$row->id;
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center" style="text-align:center">
						<?php echo $i + 1 ?>
					</td>
					<td>
						<a target="_blank" href="<?php echo !ACYMAILING_J16 ? 'index.php?option=com_plugins&amp;view=plugin&amp;client=site&amp;task=edit&amp;cid[]=' : 'index.php?option=com_plugins&amp;task=plugin.edit&amp;extension_id=';
						echo $row->id ?>"><?php echo $row->name; ?></a>
					</td>
					<td align="center" style="text-align:center">
						<span id="<?php echo $publishedid ?>" class="loading"><?php echo $this->toggleClass->toggle($publishedid, $row->published, 'plugins') ?></span>
					</td>
					<td align="center" style="text-align:center">
						<?php echo $row->id; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>
	</div>
	<div class="acyblockoptions" style="width: 42%;min-width: 480px;">
		<span class="acyblocktitle"><?php echo JText::_('PLUG_INTE') ?></span>
		<table class="acymailing_table" cellpadding="1">
			<thead>
			<tr>
				<th class="title titlenum">
					<?php echo JText::_('ACY_NUM'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('ACY_NAME'); ?>
				</th>
				<th class="title titletoggle">
					<?php echo JText::_('ENABLED'); ?>
				</th>
				<th class="title titleid">
					<?php echo JText::_('ACY_ID'); ?>
				</th>
			</tr>
			</thead>
			<tbody>
			<?php
			$k = 0;

			for($i = 0, $a = count($this->integrationplugins); $i < $a; $i++){
				$row =& $this->integrationplugins[$i];

				$publishedid = 'published_'.$row->id;
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center" style="text-align:center">
						<?php echo $i + 1 ?>
					</td>
					<td>
						<a target="_blank" href="<?php echo !ACYMAILING_J16 ? 'index.php?option=com_plugins&amp;view=plugin&amp;client=site&amp;task=edit&amp;cid[]=' : 'index.php?option=com_plugins&amp;task=plugin.edit&amp;extension_id=';
						echo $row->id ?>"><?php echo $row->name; ?></a>
					</td>
					<td align="center" style="text-align:center">
						<span id="<?php echo $publishedid ?>" class="spanloading"><?php echo $this->toggleClass->toggle($publishedid, $row->published, 'plugins') ?></span>
					</td>
					<td align="center" style="text-align:center">
						<?php echo $row->id; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>
	</div>
	<span class="acymailing_button" style="margin:15px;">
		<i class="acyicon-import"></i>
		<a style="margin-left:5px;color:#fff;text-decoration: none;" href="https://www.acyba.com/acymailing/plugins.html" target="_blank"><?php echo JText::_('MORE_PLUGINS'); ?></a>
	</span>
</div>
