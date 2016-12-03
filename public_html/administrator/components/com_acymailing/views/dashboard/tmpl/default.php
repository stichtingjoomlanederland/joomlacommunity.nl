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

	<script type="text/javascript">
		function displayDetails(detailsDivID){

			var oldDisplay = document.getElementById(detailsDivID).style.display;

			document.getElementById('userStatisticDetails').style.display = "none";
			document.getElementById('newsletterStatisticDetails').style.display = "none";
			document.getElementById('listStatisticDetails').style.display = "none";

			if(oldDisplay == 'block'){
				document.getElementById(detailsDivID).style.display = 'none';
			}else{
				document.getElementById(detailsDivID).style.display = 'block';
			}
		}

		(function(){
			window.onload = function(){
				var circles = document.querySelectorAll('.acyprogress');
				for(var i = 0; i < 3; i++){
					var totalProgress = circles[i].querySelector('circle').getAttribute('stroke-dasharray');
					var progress = circles[i].parentNode.getAttribute('data-percent');
					circles[i].querySelector('.bar').style['stroke-dashoffset'] = totalProgress * progress / 100;
				}
			}
		})();
	</script>

	<div id="dashboard_mainview">

		<?php include(dirname(__FILE__).DS.'stats.php'); ?>

		<!-- Special content -->
		<?php
		if(!empty($this->contentToDisplay['displayType'])){
			echo '<div class="acydashboard_specialcontent">';
			if($this->contentToDisplay['displayType'] == 'url'){
				$height = !empty($this->contentToDisplay['height']) ? $this->contentToDisplay['height'] : 'auto';
				echo '<iframe frameborder="0" src="'.$this->contentToDisplay['content'].'" width="100%" height="'.$height.'" scrolling="auto"></iframe>';
			}else{
				echo $this->contentToDisplay['content'];
			}
			echo '</div>';
		}
		?>

		<!-- dashboard progress bar -->
		<div id="dashboard_progress">
			<!-- progress bar -->
			<div class="acydashboard_progressbar">
				<table width="100%">

					<tr>
						<td width="25%" class="acydashboard_plane1 <?php echo(!empty($this->progressBarSteps->listCreated) ? 'acystepdone' : ''); ?>" height="36"></td>
						<td width="25%" class="acydashboard_plane2 <?php echo(!empty($this->progressBarSteps->contactCreated) ? 'acystepdone' : ''); ?>" height="36"></td>
						<td width="25%" class="acydashboard_plane3 <?php echo(!empty($this->progressBarSteps->newsletterCreated) ? 'acystepdone' : ''); ?>" height="36"></td>
						<td width="25%" class="acydashboard_plane4 <?php echo(!empty($this->progressBarSteps->newsletterSent) ? 'acystepdone' : ''); ?>" height="36"></td>

					</tr>
					<tr class="acydashboard_progressbar_colors">
						<td width="25%" height="3" class="acydashboard_progress1"><span class="<?php echo(!empty($this->progressBarSteps->listCreated) ? 'acystepdone' : ''); ?>"></span></td>
						<td width="25%" height="3" class="acydashboard_progress2"><span class="<?php echo(!empty($this->progressBarSteps->contactCreated) ? 'acystepdone' : ''); ?>"></span></td>
						<td width="25%" height="3" class="acydashboard_progress3"><span class="<?php echo(!empty($this->progressBarSteps->newsletterCreated) ? 'acystepdone' : ''); ?>"></span></td>
						<td width="25%" height="3" class="acydashboard_progress4"><span class="<?php echo(!empty($this->progressBarSteps->newsletterSent) ? 'acystepdone' : ''); ?>"></span></td>
					</tr>
				</table>
			</div>

			<!-- progress steps -->
			<div class="acydashboard_progress_steps">
				<a href="index.php?option=com_acymailing&ctrl=list">
					<div class="acydashboard_progress_block acydashboard_step1">
						<div class="step_image"></div>
						<div class="step_info"><span class="step_title"><?php echo JText::_('MAILING_LISTS'); ?></span><?php echo JText::_('ACY_MAILING_LIST_STEP_DESC'); ?></div>
					</div>
				</a>

				<a href="index.php?option=com_acymailing&ctrl=subscriber">
					<div class="acydashboard_progress_block acydashboard_step2">
						<div class="step_image"></div>
						<div class="step_info"><span class="step_title"><?php echo JText::_('ACY_CONTACTS'); ?></span><?php echo JText::_('ACY_MAILING_CONTACT_STEP_DESC'); ?>                        </div>
					</div>
				</a>

				<a href="index.php?option=com_acymailing&ctrl=newsletter">
					<div class="acydashboard_progress_block acydashboard_step3">
						<div class="step_image"></div>
						<div class="step_info"><span class="step_title"><?php echo JText::_('NEWSLETTERS'); ?></span><?php echo JText::_('ACY_MAILING_NEWSLETTER_STEP_DESC'); ?>                        </div>
					</div>
				</a>

				<a href="index.php?option=com_acymailing&ctrl=queue">
					<div class="acydashboard_progress_block acydashboard_step4">
						<div class="step_image"></div>
						<div class="step_info"><span class="step_title"><?php echo JText::_('SEND_PROCESS'); ?></span><?php echo JText::_('ACY_MAILING_SEND_PROCESS_STEP_DESC'); ?></div>
					</div>
				</a>
			</div>

			<div id="acy_stepbystep"><?php echo JText::_('ACY_STEP_BY_STEP_DESC1').'<br />'.JText::_('ACY_STEP_BY_STEP_DESC2').' '.JText::_('ACY_STEP_BY_STEP_DESC3').'<br />'.JText::_('ACY_STEP_BY_STEP_DESC4'); ?><br/>

				<form target="_blank" action="https://www.acyba.com/index.php?option=com_acymailing&ctrl=sub" method="post">
					<input id="user_name" type="text" name="user[name]" value="" placeholder="<?php echo JText::_('NAMECAPTION'); ?>"/>
					<input id="user_email" type="text" name="user[email]" value="" placeholder="<?php echo JText::_('EMAILCAPTION'); ?>"/>
					<br/>
					<input class="acymailing_button" type="submit" value="<?php echo JText::_('SUBSCRIBE'); ?>" name="Submit"/>
					<input type="hidden" name="acyformname" value="formAcymailing1"/>
					<input type="hidden" name="ctrl" value="sub"/>
					<input type="hidden" name="task" value="optin"/>
					<input type="hidden" name="option" value="com_acymailing"/>
					<input type="hidden" name="visiblelists" value=""/>
					<input type="hidden" name="hiddenlists" value="23"/>
					<input type="hidden" name="redirect" value="https://www.acyba.com"/>
				</form>
			</div>
		</div>
	</div>
</div>
