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
	<?php include(dirname(__FILE__).DS.'test.php');
	if($this->type != 'joomlanotification'){ ?>
		<div <?php echo ($this->app->isAdmin()) ? 'class="acyblockoptions" style="width:42%;min-width:480px;"' : 'class="onelineblockoptions"'; ?> id="receiversinfo">
			<span class="acyblocktitle"><?php echo JText::_('NEWSLETTER_SENT_TO'); ?></span>

			<table class="<?php echo ($this->app->isAdmin()) ? 'acymailing_table' : 'adminlist table table-striped'; ?>" cellspacing="1" align="center">
				<tbody>
				<?php if(!empty($this->lists)){
					$k = 0;
					$listids = array();
					foreach($this->lists as $row){
						$listids[] = $row->listid;
						?>
						<tr class="<?php echo "row$k"; ?>">
							<td>
								<?php
								if(!$row->published) echo '<a href="index.php?option=com_acymailing&ctrl=list&task=edit&listid='.$row->listid.'" title="'.JText::_('LIST_PUBLISH', true).'"><img style="margin:0px;" src="'.ACYMAILING_IMAGES.'warning.png" alt="Warning" /></a> ';
								echo acymailing_tooltip($row->description, $row->name, '', $row->name);
								echo ' ( '.JText::sprintf('SELECTED_USERS', $row->nbsub).' )';
								echo '<div class="roundsubscrib rounddisp" style="background-color:'.$row->color.'"></div>';
								?>
							</td>
						</tr>
						<?php $k = 1 - $k;
					}
				}else{ ?>
					<tr>
						<td>
							<?php echo JText::_('EMAIL_AFFECT'); ?>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
			<?php
			$filterClass = acymailing_get('class.filter');
			if(!empty($this->mail->filter)){
				$resultFilters = $filterClass->displayFilters($this->mail->filter);
				if(!empty($resultFilters)){
					echo '<br />'.JText::_('RECEIVER_LISTS').'<br />'.JText::_('FILTER_ONLY_IF');
					echo '<ul><li>'.implode('</li><li>', $resultFilters).'</li></ul>';
				}
			}

			if(!empty($this->lists)){
				?>
				<div style="text-align:center;font-size:14px;padding-top:10px;margin:10px 30px;border-top: 1px solid #ccc;">
					<?php
					$nbTotalReceivers = $filterClass->countReceivers($listids, $this->mail->filter, $this->mail->mailid);
					echo JText::sprintf('SENT_TO_NUMBER', '<span style="font-weight:bold;" id="nbreceivers" >'.$nbTotalReceivers.'</span>');
					?>
				</div>
			<?php } ?>
		</div>
	<?php }
	include(dirname(__FILE__).DS.'previewcontent.php'); ?>
</div>
