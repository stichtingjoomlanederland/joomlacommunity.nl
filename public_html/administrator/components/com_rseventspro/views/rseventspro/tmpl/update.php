<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );?>
<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&layout=update'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
			<table class="table table-striped adminlist">
				<tbody>
					<tr class="row0">
						<td>
							<iframe src="//www.rsjoomla.com/index.php?option=com_rsmembership&amp;task=checkupdate&amp;sess=<?php echo rseventsproHelper::genKeyCode();?>&amp;revision=<?php echo $this->version;?>&amp;version=<?php echo urlencode($this->jversion); ?>&amp;tmpl=component" style="border:0px solid;width:100%;height:25px;" scrolling="no" frameborder="no"></iframe>
						</td>
					</tr>
					<tr class="row1">
						<td>
							<iframe src="http://www.rsjoomla.com/latest.html?tmpl=component" style="border:0px solid;width:100%;height:450px;" scrolling="no" frameborder="no"></iframe>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="task" value="" />
</form>