<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 

class JHTMLRSEventsPro
{
	public static function calendar($value, $name, $id, $format = '%Y-%m-%d', $readonly = false, $js = false, $no12 = false, $allday = 0) {
		JHTML::_('behavior.calendar'); //load the calendar behavior
		
		$time12 = rseventsproHelper::getConfig('time_format','int');
		if ($time12 && !$no12) $format = '%Y-%m-%d %I:%M:%S %P';
		
		$theid = $time12 && !$no12 ? $id.'_dummy' : $id;
		
		if ($time12 && !$no12) {
			if (!empty($value)) {
				$thevalue = $allday ? date('Y-m-d',strtotime($value)) : date('Y-m-d h:i:s a',strtotime($value));
			}
		} else {
			$thevalue = $value;
		}
		
		if ($time12 && !$no12)
		{
			if (substr_count($name,'[') >= 1)
			{
				$thename = $name;
				$thename = str_replace(array('[',']'),'',$thename);
				$thename = $thename.'_dummy';
				
			} else $thename = $name.'_dummy';
		} else $thename = $name;
		
		
		$document = JFactory::getDocument();
		$declaration = 'window.addEvent(\'domready\', function() {Calendar.setup({'."\n";
        $declaration .= "\t".'inputField	:	"'.$theid.'",'."\n";
        
		if ($id == 'start')
			$declaration .= "\t".'ifFormat	:	$(\'allday\').checked ? "%Y-%m-%d" : "'.$format.'",'."\n";
		else 
			$declaration .= "\t".'ifFormat	:	"'.$format.'",'."\n";
        
		$declaration .= "\t".'button		:	"'.$theid.'_img",'."\n";
        $declaration .= "\t".'align		:	"Tl",'."\n";
		
		if ($id == 'repeat_date')
			$declaration .= "\t".'electric		: false,'."\n";
		
		if ($time12 && !$no12) 
		{
			if ($id == 'start')
				$declaration .= "\t".'showsTime	:	$(\'allday\').checked ? false : true,'."\n";
			else
				$declaration .= "\t".'showsTime	:	true,'."\n";
			
			$declaration .= "\t".'time24	:	false,'."\n";
			
			if ($id == 'start')
				$declaration .= "\t".'onClose	:	function() { if ($(\'allday\').checked) { document.getElementById(\''.$id.'\').value = this.date.print("%Y-%m-%d"); } else { document.getElementById(\''.$id.'\').value = this.date.print("%Y-%m-%d %H:%M:%S"); } this.hide(); },'."\n";
			else
				$declaration .= "\t".'onClose	:	function() { document.getElementById(\''.$id.'\').value = this.date.print("%Y-%m-%d %H:%M:%S"); this.hide(); },'."\n";
		}
        
		$declaration .= "\t".'singleClick	:	true'."\n";
		$declaration .= '});});'."\n";
	
		$document->addScriptDeclaration($declaration);

		$readonly = $readonly || ($time12 && !$no12) ? 'readonly="readonly"' : '';
		$js = $js ? $js : '';
		
		$return = '<input type="text" name="'.$thename.'" id="'.$theid.'" value="'.htmlspecialchars($thevalue, ENT_COMPAT, 'UTF-8').'" class="rs_inp" '.$readonly.' '.$js.' />';
		$return .= '<a href="javascript:void(0)" class="rs_calendar_icon" id="rs_starting_calendar">';
		$return .= '<img src="'.JURI::root().'components/com_rseventspro/assets/images/edit/calendar-small.png" alt="calendar" id="'.$theid.'_img" />';
		$return .= '</a>';
		
		if ($time12 && !$no12) 
		{
			$return .= '<input type="hidden" name="'.$name.'" id="'.$id.'" value="'.htmlspecialchars($value, ENT_COMPAT, 'UTF-8').'" />';
		}
		
		return $return;
	}
	
	/**
	 * @param   int $value	The state value
	 * @param   int $i
	 */
	public static function featured($value = 0, $i) {
		// Array of image, task, title, action
		$states	= array(
			0	=> array((rseventsproHelper::isJ3() ? 'star-empty' : 'disabled.png'),	'events.featured',		'COM_RSEVENTSPRO_UNFEATURED',	'COM_RSEVENTSPRO_TOGGLE_TO_FEATURE'),
			1	=> array((rseventsproHelper::isJ3() ? 'star' : 'featured.png'),			'events.unfeatured',	'COM_RSEVENTSPRO_FEATURED',		'COM_RSEVENTSPRO_TOGGLE_TO_UNFEATURE'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[1]);
		$icon	= $state[0];
		$image 	= JHtml::_('image', 'admin/'.$state[0], JText::_($state[2]), NULL, true);
		
		if (rseventsproHelper::isJ3()) {
			JHtml::_('bootstrap.tooltip');
			$html	= '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" class="btn btn-micro hasTooltip' . ($value == 1 ? ' active' : '') . '" title="'.JText::_($state[3]).'"><i class="icon-'
					. $icon.'"></i></a>';
		} else {
			JHtml::_('behavior.tooltip');
			$html	= '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" class="hasTip' . ($value == 1 ? ' active' : '') . '" title="'.JText::_($state[3]).'::">'
					. $image.'</a>';
		}

		return $html;
	}
}