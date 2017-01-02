<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var $this \Akeeba\AdminTools\Admin\View\ScanAlerts\Form */

defined('_JEXEC') or die;

$scan_id   = $this->getModel()->getState('scan_id', '');

/** @var \Akeeba\AdminTools\Admin\Model\Scans $scanModel */
$scanModel = $this->getContainer()->factory->model('Scans')->tmpInstance();
$scanModel->find($scan_id);

?>
<div class="span6 pull-right">
	<a href="index.php?option=com_admintools&view=Scans&task=edit&id=<?php echo $scan_id?>" class="btn btn-success pull-right">
		<i class="icon-pencil"></i><?php echo JText::_('COM_ADMINTOOLS_SCANALERTS_EDIT_COMMENT')?>
	</a>
	<span id="showComment" class="btn btn-primary pull-right" style="margin-right: 10px;"><i class="icon-comments icon-white"></i><?php echo JText::_('COM_ADMINTOOLS_SCANALERTS_SHOWCOMMENT')?></span>
	<div style="clear: both;"></div>
	<div id="comment" style="display:none">
		<p class="well well-small" style="margin:5px 0 0">
			<?php echo nl2br($scanModel->comment);?>
		</p>
	</div>
</div>
<?php

echo $this->getRenderedForm();