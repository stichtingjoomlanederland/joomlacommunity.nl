<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="unsubpage">
	<?php echo $this->intro; ?>
	<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm">
		<?php if($this->config->get('unsub_dispoptions', 1)){ ?>
			<div class="unsuboptions">
				<?php if(!empty($this->mailid)){ ?>
					<div id="unsublist_div" class="unsubdiv">
						<label for="unsublist"><input type="checkbox" value="1" name="unsublist" id="unsublist" disabled="disabled" checked="checked"/> <?php echo str_replace(array_keys($this->replace), $this->replace, JText::_('UNSUB_CURRENT')); ?></label>
					</div>
				<?php } ?>
				<div id="unsuball_div" class="unsubdiv">
					<label for="unsuball"><input type="checkbox" value="1" name="unsuball" id="unsuball" <?php if(empty($this->mailid)) echo 'checked="checked"'; ?> /> <?php echo str_replace(array_keys($this->replace), $this->replace, JText::_('UNSUB_ALL')); ?></label>

					<div id="unsubfull_div" class="unsubdiv">
						<label for="refuse"><input type="checkbox" value="1" name="refuse" id="refuse"/> <?php echo str_replace(array_keys($this->replace), $this->replace, JText::_('UNSUB_FULL')); ?></label>
					</div>
				</div>
				<?php
				if(!empty($this->otherSubscriptions) && $this->config->get('unsub_dispothersubs', 0)){
					?>
					<div id="unsub_list_div" class="unsubdiv">
						<?php
						echo JText::_('ACY_OTHERSUBSCRIPTIONS');
						$i = 0;
						foreach($this->otherSubscriptions as $oneSubscription){
							echo '<div><label for="unsubotherlists'.$i.'"><input type="checkbox" value="1" name="unsubotherlists[]" id="unsubotherlists'.$i.'" class="unsubotherlistscheckbox"/> '.$oneSubscription->name.'</label>';
							echo '<input type="hidden" value="'.$oneSubscription->listid.'" name="unsubotherlistsid[]" id="unsubotherlistsid'.$i.'"/></div>';
							$i++;
						}
						?>
					</div>
				<?php } ?>
			</div>
		<?php }else{
			echo '<input type="hidden" value="1" name="unsuball" />';
		}
		if($this->config->get('unsub_survey', 1)){ ?>
			<div class="unsubsurvey">
				<div class="unsubsurveytext"><?php echo str_replace(array_keys($this->replace), $this->replace, JText::_('UNSUB_SURVEY')); ?></div>
				<?php $reasons = unserialize($this->config->get('unsub_reasons'));
				foreach($reasons as $i => $oneReason){
					if(preg_match('#^[A-Z_]*$#', $oneReason)){
						$trans = JText::_($oneReason);
					}else{
						$trans = $oneReason;
					}
					echo '<div>';
					echo '<label for="reason'.$i.'"><input type="checkbox" value="'.$oneReason.'" name="survey[]" id="reason'.$i.'" /> '.$trans.'</label>';
					echo '</div>';
				} ?>
				<div id="otherreasons">
					<label for="other"><?php echo JText::_('UNSUB_SURVEY_OTHER'); ?></label><br/>
					<textarea name="survey[]" id="other" style="width:300px;height:70px"></textarea>
				</div>
			</div>
		<?php } ?>
		<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>"/>
		<input type="hidden" name="task" value="saveunsub"/>
		<input type="hidden" name="ctrl" value="user"/>
		<input type="hidden" name="subid" value="<?php echo $this->subscriber->subid; ?>"/>
		<input type="hidden" name="key" value="<?php echo $this->subscriber->key; ?>"/>
		<input type="hidden" name="mailid" value="<?php echo $this->mailid; ?>"/>
		<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid'); ?>"/>
		<?php if(JRequest::getCmd('tmpl') == 'component'){ ?><input type="hidden" name="tmpl" value="component"/><?php } ?>
		<div id="unsubbutton_div" class="unsubdiv">
			<input class="button btn btn-primary" type="submit" value="<?php echo JText::_('UNSUBSCRIBE', true) ?>"/>
		</div>
	</form>
</div>
