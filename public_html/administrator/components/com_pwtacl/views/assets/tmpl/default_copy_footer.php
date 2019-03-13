<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\Language\Text;

// No direct access.
defined('_JEXEC') or die;
?>

<a class="btn" type="button" onclick="document.getElementById('copy-group-id').value=''" data-dismiss="modal">
	<?php echo Text::_('JCANCEL'); ?>
</a>
<button class="btn btn-success" type="submit" onclick="Joomla.submitbutton('assets.copy');">
	<?php echo Text::_('JGLOBAL_BATCH_PROCESS'); ?>
</button>