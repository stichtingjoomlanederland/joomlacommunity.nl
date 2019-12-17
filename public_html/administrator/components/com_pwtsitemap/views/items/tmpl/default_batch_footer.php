<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/** @var PwtSitemapViewItems $this */
?>

<a class="btn" type="button" onclick="document.getElementById('batch-addtohtmlsitemap').value='';document.getElementById('batch-addtoxmlsitemap').value='';document.getElementById('batch-changemenuitemrobots').value='';" data-dismiss="modal">
	<?php echo Text::_('JCANCEL'); ?>
</a>
<button class="btn btn-success" type="submit" onclick="Joomla.submitbutton('item.batch');">
	<?php echo Text::_('JGLOBAL_BATCH_PROCESS'); ?>
</button>
