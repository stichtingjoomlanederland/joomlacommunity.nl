<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\Layout\LayoutHelper;

// No direct access.
defined('_JEXEC') or die;
?>

    <div class="js-stools-field-filter js-stools-group hidden-phone hidden-tablet">

		<?php if ($displayData['view']->type == 'group'): ?>
			<?php echo $displayData['view']->filterForm->getField('group')->input; ?>
		<?php endif; ?>

		<?php if ($displayData['view']->type == 'user'): ?>
			<?php echo $displayData['view']->filterForm->getField('user')->input; ?>
		<?php endif; ?>
    </div>

<?php
// Display the main joomla layout
echo LayoutHelper::render('joomla.searchtools.default.bar', $displayData, null, array('component' => 'none'));
