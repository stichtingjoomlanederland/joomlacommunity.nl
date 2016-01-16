<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

if ($this->item->icon && file_exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/'.$this->item->icon)) {
	$iconsrc = JRoute::_('index.php?option=com_rseventspro&task=image&id='.rseventsproHelper::sef($this->item->id,$this->item->name).'&width=188', false);
} else {
	$iconsrc = JURI::root().'components/com_rseventspro/assets/images/profile_pic.png';
} ?>
<img id="rsepro-photo" src="<?php echo $iconsrc; ?>" alt="" />