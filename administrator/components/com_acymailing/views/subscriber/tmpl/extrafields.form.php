<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div class="<?php echo $this->isAdmin ? 'acyblockoptions respuserinfo' : 'onelineblockoptions'; ?>">
	<span class="acyblocktitle"><?php echo JText::_('EXTRA_INFORMATION'); ?></span>

	<div width="100%" id="acyuserinfo">
		<?php
		$tmpCatId = array();
		$tmpCatTag = array();
		foreach($this->extraFields as $fieldName => $oneExtraField){
			if($oneExtraField->type == 'category'){
				if(empty($oneExtraField->fieldcat)){
					while(!empty($tmpCatId)){
						echo '</'.str_replace('fldset', 'fieldset', end($tmpCatTag)).'>';
						array_pop($tmpCatId);
						array_pop($tmpCatTag);
					}
				}
				$tmpCatId[] = $oneExtraField->fieldid;
				$tmpCatTag[] = $oneExtraField->options['fieldcattag'];
				echo '<'.str_replace('fldset', 'fieldset', end($tmpCatTag)).' class="fieldCategory '.$oneExtraField->options['fieldcatclass'].'" id="tr'.$oneExtraField->namekey.'">';
				if(in_array(end($tmpCatTag), array('fieldset', 'fldset'))) echo '<legend>'.$oneExtraField->fieldname.'</legend>';
			}else{
				if(in_array($oneExtraField->fieldcat, $tmpCatId) || empty($oneExtraField->fieldcat)){
					while(!empty($tmpCatId) && $oneExtraField->fieldcat != end($tmpCatId)){
						echo '</'.str_replace('fldset', 'fieldset', end($tmpCatTag)).'>';
						array_pop($tmpCatId);
						array_pop($tmpCatTag);
					}
				}
				echo '<div id="tr'.$fieldName.'" class="acy_onefield"><div class="acykey">'.$this->fieldsClass->getFieldName($oneExtraField).'</div>';
				echo '<div class="inputVal">'.$this->fieldsClass->display($oneExtraField, @$this->subscriber->$fieldName, 'data[subscriber]['.$fieldName.']').'</div></div>';
			}
		}
		$lastVal = end($tmpCatId);
		while(!empty($lastVal)){
			echo '</'.str_replace('fldset', 'fieldset', end($tmpCatTag)).'>';
			array_pop($tmpCatId);
			array_pop($tmpCatTag);
			$lastVal = end($tmpCatId);
		}
		?>
	</div>
</div>
