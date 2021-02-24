<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator'); ?>

<script type="text/javascript">
	window.addEventListener('DOMContentLoaded', function() {
		<?php if (!$this->social['js']) { ?>jQuery('#jform_user_display option[value=2]').prop('disabled',true);<?php } ?>
		<?php if (!$this->social['cb']) { ?>jQuery('#jform_user_display option[value=3]').prop('disabled',true);<?php } ?>
		<?php if (!$this->social['js']) { ?>jQuery('#jform_user_profile option[value=1]').prop('disabled',true);<?php } ?>
		<?php if (!$this->social['cb']) { ?>jQuery('#jform_user_profile option[value=2]').prop('disabled',true);<?php } ?>
		<?php if (!$this->social['easysocial']) { ?>jQuery('#jform_user_profile option[value=3]').prop('disabled',true);<?php } ?>
		<?php if (!$this->social['js']) { ?>jQuery('#jform_event_owner option[value=2]').prop('disabled',true);<?php } ?>
		<?php if (!$this->social['cb']) { ?>jQuery('#jform_event_owner option[value=3]').prop('disabled',true);<?php } ?>
		<?php if (!$this->social['js']) { ?>jQuery('#jform_event_owner_profile option[value=1]').prop('disabled',true);<?php } ?>
		<?php if (!$this->social['cb']) { ?>jQuery('#jform_event_owner_profile option[value=2]').prop('disabled',true);<?php } ?>
		<?php if (!$this->social['easysocial']) { ?>jQuery('#jform_event_owner_profile option[value=3]').prop('disabled',true);<?php } ?>
		<?php if (!$this->social['cb']) { ?>jQuery('#jform_user_avatar option[value=comprofiler]').prop('disabled',true);<?php } ?>
		<?php if (!$this->social['js']) { ?>jQuery('#jform_user_avatar option[value=community]').prop('disabled',true);<?php } ?>
		<?php if (!$this->social['k2']) { ?>jQuery('#jform_user_avatar option[value=k2]').prop('disabled',true);<?php } ?>
		<?php if (!$this->social['kunena']) { ?>jQuery('#jform_user_avatar option[value=kunena]').prop('disabled',true);<?php } ?>
		<?php if (!$this->social['fireboard']) { ?>jQuery('#jform_user_avatar option[value=fireboard]').prop('disabled',true);<?php } ?>
		<?php if (!$this->social['easydiscuss']) { ?>jQuery('#jform_user_avatar option[value=easydiscuss]').prop('disabled',true);<?php } ?>
		<?php if (!$this->social['easydiscuss']) { ?>jQuery('#jform_user_profile option[value=4]').prop('disabled',true);<?php } ?>
		<?php if (!$this->social['easysocial']) { ?>jQuery('#jform_user_avatar option[value=easysocial]').prop('disabled',true);<?php } ?>
		<?php if (!$this->social['rscomments']) { ?>jQuery('#jform_event_comment option[value=2]').prop('disabled',true);<?php } ?>
		<?php if (!$this->social['jcomments']) { ?>jQuery('#jform_event_comment option[value=3]').prop('disabled',true);<?php } ?>
		<?php if (!$this->social['jomcomment']) { ?>jQuery('#jform_event_comment option[value=4]').prop('disabled',true);<?php } ?>
		
		jQuery('#jform_user_display').trigger('liszt:updated');
		jQuery('#jform_user_profile').trigger('liszt:updated');
		jQuery('#jform_event_owner').trigger('liszt:updated');
		jQuery('#jform_event_owner_profile').trigger('liszt:updated');
		jQuery('#jform_user_avatar').trigger('liszt:updated');
		jQuery('#jform_event_comment').trigger('liszt:updated');
	});
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=settings'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal" autocomplete="off" enctype="multipart/form-data">
	<?php echo RSEventsproAdapterGrid::sidebar(); ?>
		<?php 
			foreach ($this->layouts as $layout) {
				$this->tabs->addTitle('COM_RSEVENTSPRO_CONF_TAB_'.strtoupper($layout), $layout);
				$this->tabs->addContent($this->loadTemplate($layout));
			}
		
			echo $this->tabs->render();
		?>
	</div>
	
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
</form>

<?php echo JHtml::_('bootstrap.renderModal', 'rseproFacebookLog', array('title' => JText::_('COM_RSEVENTSPRO_CONF_SYNC_LOG_BTN'), 'url' => 'index.php?option=com_rseventspro&view=settings&layout=log&from=facebook&tmpl=component', 'bodyHeight' => 70)); ?>
<?php echo JHtml::_('bootstrap.renderModal', 'rseproGoogleLog', array('title' => JText::_('COM_RSEVENTSPRO_CONF_SYNC_LOG_BTN'), 'url' => 'index.php?option=com_rseventspro&view=settings&layout=log&from=google&tmpl=component', 'bodyHeight' => 70)); ?>