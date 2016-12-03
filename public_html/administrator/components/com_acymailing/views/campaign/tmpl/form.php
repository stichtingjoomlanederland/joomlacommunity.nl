<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><style type="text/css">
	.campaignarea{
		float: left;
		max-width: 700px;
		width: 100%;
		display: inline-table;
	}
</style>

<div id="acy_content">
	<div id="iframedoc"></div>
	<form action="index.php?option=<?php echo ACYMAILING_COMPONENT ?>" method="post" name="adminForm" autocomplete="off" id="adminForm">
		<div style="display:block; float:left; max-width: 800px;">
			<div class="acyblockoptions campaignarea">
				<span class="acyblocktitle"><?php echo JText::_('ACY_CAMPAIGN_INFORMATIONS'); ?></span>
				<table cellspacing="1" width="100%">
					<tr>
						<td class="acykey" style="width:200px;">
							<label for="name">
								<?php echo JText::_('ACY_TITLE'); ?>
							</label>
						</td>
						<td>
							<input type="text" name="data[list][name]" id="name" class="inputbox" style="width:200px" value="<?php echo $this->escape(@$this->list->name); ?>"/>
						</td>
					</tr>
					<tr>
						<td class="acykey">
							<label for="activated">
								<?php echo JText::_('ENABLED'); ?>
							</label>
						</td>
						<td>
							<?php echo JHTML::_('acyselect.booleanlist', "data[list][published]", '', $this->list->published); ?>
						</td>
					</tr>
					<tr>
						<td class="acykey">
							<label>
								<?php echo JText::_('ACY_START_CAMPAIGN'); ?>
							</label>
						</td>
						<td>
							<?php echo JHTML::_('select.genericlist', $this->startoptions, "data[list][startrule]", 'size="1" style="width:auto;" ', 'value', 'text', (string)$this->list->startrule); ?>
						</td>
					</tr>
				</table>
			</div>

			<div class="acyblockoptions campaignarea">
				<span class="acyblocktitle"><?php echo JText::_('ACY_DESCRIPTION'); ?></span>
				<?php echo $this->editor->display(); ?>
			</div>
		</div>
		<div class="acyblockoptions campaignarea">
			<span class="acyblocktitle"><?php echo JText::_('LISTS'); ?></span>

			<span><?php echo JText::_('CAMPAIGN_START') ?></span>
			<?php
			$currentPage = 'campaign';
			include_once(ACYMAILING_BACK.'views'.DS.'newsletter'.DS.'tmpl'.DS.'filter.lists.php');
			?>
		</div>

		<div style="clear:both;"></div>

		<input type="hidden" name="cid[]" value="<?php echo @$this->list->listid; ?>"/>
		<input type="hidden" name="data[list][type]" value="campaign"/>
		<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="ctrl" value="campaign"/>
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>
