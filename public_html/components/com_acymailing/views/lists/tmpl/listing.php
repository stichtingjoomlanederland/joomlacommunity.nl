<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="acylistslisting" >
<h1 class="componentheading"><?php echo JText::_('MAILING_LISTS'); ?></h1>
<?php
	if(!empty($this->listsintrotext)) echo '<div class="acymailing_listsintrotext" >'.$this->listsintrotext.'</div>';
	$k = 0;
	$my = JFactory::getUser();

	foreach($this->rows as $i => $oneList){
		$row =& $this->rows[$i];
		$frontEndAccess = true;
		$frontEndManagement = false;

		if(acymailing_level(3)){
			if((int)$my->id == (int)$row->userid)  $frontEndManagement = true;

			if(!empty($my->id)){
				if($row->access_manage == 'all' OR acymailing_isAllowed($row->access_manage)){
					 $frontEndManagement = true;
				}
			}
			if($row->access_sub != 'all' AND ($row->access_sub == 'none' OR empty($my->id) OR !acymailing_isAllowed($row->access_sub))){
				$frontEndAccess = false;
			}
		}
		if(!$frontEndManagement AND (!$frontEndAccess OR !$row->published OR !$row->visible)) continue;
?>

	<div class="<?php echo "acymailing_list acymailing_row$k"; ?>">
			<div class="list_name"><a href="<?php echo acymailing_completeLink('archive&listid='.$row->listid.'-'.$row->alias.$this->item)?>"><?php echo $row->name; ?></a></div>
			<div class="list_description"><?php echo $row->description; ?></div>
	</div>
<?php
		$k = 1-$k;
	}

	if(!empty($this->listsfinaltext)) echo '<div class="acymailing_listsfinaltext" >'.$this->listsfinaltext.'</div>';
?>
</div>
