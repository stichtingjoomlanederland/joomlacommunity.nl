<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php $config = acymailing_config(); ?>
<div id="acy_content">
	<div id="iframedoc"></div>
	<form action="<?php echo JRoute::_('index.php?option='.ACYMAILING_COMPONENT); ?>" method="post" name="adminForm" enctype="multipart/form-data" id="adminForm">
		<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>"/>
		<?php if(!empty($this->Itemid)) echo '<input type="hidden" name="Itemid" value="'.$this->Itemid.'" />';
		echo JHTML::_('form.token'); ?>
		<div style="width:100%;">
			<div id="import_mode_container">
				<div id="import_mode" class="<?php echo $this->isAdmin ? 'acyblockoptions' : 'onelineblockoptions'; ?>">
					<span class="acyblocktitle"><?php echo JText::_('IMPORT_FROM'); ?></span>
					<?php echo JHTML::_('acyselect.radiolist', $this->importvalues, 'importfrom', 'class="inputbox" size="1" onclick="updateImport(this.value);"', 'value', 'text', JRequest::getCmd('importfrom', 'textarea')); ?>
				</div>
			</div>
			<div id="import_options" class="<?php echo $this->isAdmin ? 'acyblockoptions' : 'onelineblockoptions'; ?>">
				<?php foreach($this->importdata as $div => $name){
					echo '<div id="'.$div.'"';
					if($div != JRequest::getCmd('importfrom', 'textarea')) echo ' style="display:none"';
					echo '>';
					echo '<span class="acyblocktitle">'.$name.'</span>';
					include(dirname(__FILE__).DS.$div.'.php');
					echo '</div>';
				} ?>
			</div>
			<div class="<?php echo $this->isAdmin ? 'acyblockoptions' : 'onelineblockoptions'; ?>" id="importlists">
				<span class="acyblocktitle"><?php echo JText::_('SUBSCRIPTION'); ?></span>
				<?php if(acymailing_isAllowed($this->config->get('acl_lists_manage', 'all'))){ ?>
					<table class="acymailing_table" cellpadding="1">
						<tr class="<?php echo "row1"; ?>" id="importcreatelist">
							<td colspan="2">
								<?php echo JText::_('IMPORT_SUBSCRIBE_CREATE').' : <input type="text" name="createlist" placeholder="'.JText::_('LIST_NAME').'" />'; ?>
							</td>
						</tr>
					</table>
				<?php }
				$currentPage = 'import';
				$currentValues = JRequest::getVar('importlists');
				$listid = JRequest::getInt('listid');
				include_once(ACYMAILING_BACK.'views'.DS.'list'.DS.'tmpl'.DS.'filter.lists.php');
				?>
			</div>
		</div>
	</form>
	<div style="clear: both;"></div>
</div>
