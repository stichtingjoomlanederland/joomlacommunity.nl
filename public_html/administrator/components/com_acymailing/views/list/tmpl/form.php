<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="acy_content">
	<div id="iframedoc"></div>
	<form action="index.php?option=com_acymailing&amp;ctrl=list" method="post" name="adminForm" id="adminForm" autocomplete="off">
		<div class="acyblockoptions" style="display:block; float:none;">
			<span class="acyblocktitle" style="display:block; float:none;"><?php echo JText::_('ACY_LIST_INFORMATIONS'); ?></span>
			<table cellspacing="1" width="100%">
				<tr>
					<td class="acykey">
						<label for="name">
							<?php echo JText::_('LIST_NAME'); ?>
						</label>
					</td>
					<td>
						<input type="text" name="data[list][name]" id="name" class="inputbox" style="width:200px" value="<?php echo $this->escape(@$this->list->name); ?>"/>
					</td>
					<td class="acykey">
						<label for="enabled">
							<?php echo JText::_('ENABLED'); ?>
						</label>
					</td>
					<td>
						<?php echo JHTML::_('acyselect.booleanlist', "data[list][published]", '', $this->list->published); ?>
					</td>
				</tr>
				<tr>
					<td class="acykey">
						<label for="alias">
							<?php echo JText::_('JOOMEXT_ALIAS'); ?>
						</label>
					</td>
					<td>
						<input type="text" name="data[list][alias]" id="alias" class="inputbox" style="width:200px" value="<?php echo $this->escape(@$this->list->alias); ?>"/>
					</td>
					<td class="acykey">
						<label for="visible">
							<?php echo JText::_('JOOMEXT_VISIBLE'); ?>
						</label>
					</td>
					<td>
						<?php echo JHTML::_('acyselect.booleanlist', "data[list][visible]", '', $this->list->visible); ?>
					</td>
				</tr>
				<tr>
					<td class="acykey">
						<label for="datalistcategory">
							<?php echo JText::_('ACY_CATEGORY'); ?>
						</label>
					</td>
					<td>
						<?php $catType = acymailing_get('type.categoryfield');
						echo $catType->display('list', 'data[list][category]', $this->list->category); ?>
					</td>
					<td class="acykey">
						<label for="creator">
							<?php echo JText::_('CREATOR'); ?>
						</label>
					</td>
					<td>
						<input type="hidden" id="listcreator" name="data[list][userid]" value="<?php echo @$this->list->userid; ?>"/>
						<?php echo '<span id="creatorname">'.@$this->list->creatorname.'</span>';
						echo ' <a class="modal" title="'.JText::_('ACY_EDIT', true).'"  href="index.php?option=com_acymailing&amp;tmpl=component&amp;ctrl=subscriber&amp;task=choose&amp;onlyreg=1" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><img class="icon16" src="'.ACYMAILING_IMAGES.'icons/icon-16-edit.png" alt="'.JText::_('ACY_EDIT', true).'"/></a>';
						?>
					</td>
				</tr>
				<tr>
					<td class="acykey">
						<label for="datalistwelmailid">
							<?php echo JText::_('MSG_WELCOME'); ?>
						</label>
					</td>
					<td>
						<?php if(acymailing_level(1)){
							echo $this->welcomeMsg->display(@$this->list->welmailid);
						}else{
							echo acymailing_getUpgradeLink('essential');
						} ?>
					</td>
					<td class="acykey">
						<label for="colorexample">
							<?php echo JText::_('COLOUR'); ?>
						</label>
					</td>
					<td>
						<?php echo $this->colorBox->displayAll('', 'data[list][color]', @$this->list->color); ?>
					</td>
				</tr>
				<tr>
					<td class="acykey">
						<label for="datalistunsubmailid">
							<?php echo JText::_('MSG_UNSUB'); ?>
						</label>
					</td>
					<td colspan="3">
						<?php echo $this->unsubMsg->display(@$this->list->unsubmailid); ?>
					</td>
				</tr>
			</table>
		</div>

		<div class="acyblockoptions" style="float:none;display:block;">
			<span class="acyblocktitle"><?php echo JText::_('ACY_DESCRIPTION'); ?></span>
			<?php echo $this->editor->display(); ?>
		</div>
		<?php
		if(acymailing_level(1)){
			if($this->languages->multipleLang){
				include(dirname(__FILE__).DS.'languages.php');
			}
			if(acymailing_level(3)){
				include(dirname(__FILE__).DS.'acl.php');
			}
		} ?>
		<div class="clr"></div>

		<input type="hidden" name="cid[]" value="<?php echo @$this->list->listid; ?>"/>
		<input type="hidden" name="option" value="com_acymailing"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="ctrl" value="list"/>
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>
