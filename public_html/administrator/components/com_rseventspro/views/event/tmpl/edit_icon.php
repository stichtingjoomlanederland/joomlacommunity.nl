<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

if ($this->item->icon && file_exists(JPATH_SITE.'/components/com_rseventspro/assets/images/events/'.$this->item->icon)) {
	$iconsrc = rseventsproHelper::thumb($this->item->id, 188);
} else {
	$iconsrc = rseventsproHelper::defaultImage();
}

?>
<img id="rsepro-photo" src="<?php echo $iconsrc; ?>" alt="" width="188" />