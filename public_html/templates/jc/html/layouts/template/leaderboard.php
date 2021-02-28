<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.jc
 *
 * @copyright   Copyright (C) 2021 Volunteers
 * @license     GNU General Public License version 3 or later
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var array $displayData */
extract($displayData);
?>
<div class="leaderboard-container">
	<div class="banner">
		<div id='<?php echo $id; ?>'>
			<script>
				googletag.cmd.push(function () {
					googletag.display('<?php echo $id; ?>')
				})
			</script>
		</div>
	</div>
</div>
