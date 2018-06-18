<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

// No direct access.
defined('_JEXEC') or die;

$odd    = false;
$assets = $displayData['assets'];
$group  = $displayData['group'];
?>

<tbody>
<?php foreach ($assets as $asset) : ?>
	<?php $oddclass = false; ?>
    <tr<?php if ($odd = !$odd): ?> class="odd"<?php $oddclass = true; ?><?php endif; ?>>
        <td class="title">
            <div class="icons">
				<?php echo str_repeat('<span class="gi">|&mdash;</span>', $asset->level); ?>
                <span class="type icon-<?php echo $asset->icon; ?>"></span>
            </div>
			<?php echo $asset->title; ?>
        </td>
		<?php foreach ($asset->actions->core as $actionname => $action): ?>
			<?php echo LayoutHelper::render('pwtacl.action', array('group' => $group, 'asset' => $asset, 'action' => $action)); ?>
		<?php endforeach; ?>
        <td class="center border-left padding-small">
			<?php if ($asset->additional): ?>
                <button type="button" class="btn btn-small" data-toggle="additional" data-target="#<?php echo 'pwtacl' . $asset->id; ?>">
                    <span class="icon-arrow-right large-icon"></span>
                </button>
			<?php endif; ?>
        </td>
    </tr>
	<?php if ($asset->additional): ?>
        <tr id="<?php echo 'pwtacl' . $asset->id; ?>" class="additional<?php if ($oddclass): ?> odd<?php endif; ?>">
            <td></td>
            <td class="border-left no-action" colspan="4"></td>
            <td class="border-left padding-none" colspan="3">
                <table class="table table-additional">
					<?php foreach ($asset->actions->additional as $actionname => $action): ?>
                        <tr>
                            <td width="75%" class="border-right"><?php echo Text::_($action->title); ?></td>
							<?php echo LayoutHelper::render('pwtacl.action', array('group' => $group, 'asset' => $asset, 'action' => $action)); ?>
                        </tr>
					<?php endforeach; ?>
                </table>
            </td>
        </tr>
	<?php endif; ?>
<?php endforeach; ?>
</tbody>
