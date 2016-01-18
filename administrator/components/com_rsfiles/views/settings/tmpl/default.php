<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.keepalive');
JHtml::_('behavior.modal');
JHtml::_('behavior.formvalidation'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rsfiles&view=settings'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal" autocomplete="off">
	<div class="row-fluid">
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
		<?php foreach ($this->layouts as $layout) {
			// add the tab title
			$this->tabs->title('COM_RSFILES_CONF_TAB_'.strtoupper($layout), $layout);
			
			// prepare the content
			$content = $this->loadTemplate($layout);
			
			// add the tab content
			$this->tabs->content($content);
		}
		
		// render tabs
		echo $this->tabs->render();
		?>
			<div>
			<?php echo JHtml::_('form.token'); ?>
			<input type="hidden" name="task" value="" />
			</div>
		</div>
	</div>
</form>
<script type="text/javascript">
<?php if (rsfilesHelper::isRsmail()) { ?>rsf_rsmail(jQuery('#jform_rsmail_list_id').val(), '<?php echo $this->config->rsmail_field_name; ?>');<?php } ?>
</script>