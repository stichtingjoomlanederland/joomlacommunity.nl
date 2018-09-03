<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\Access\Access;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

// No direct access.
defined('_JEXEC') or die;
?>

<div class="container-fluid">
    <div class="row-fluid">
        <div id="copy-choose-action" class="controls">
            <p class="alert alert-warning"><?php echo Text::sprintf('COM_PWTACL_ASSETS_COPY_DESC', Access::getGroupTitle($this->group)); ?></p>
            <div class="control-group">
                <label class="control-label" for="copy-group-id">
					<?php echo Text::_('COM_PWTACL_WIZARD_SELECT_GROUP'); ?>
                </label>

                <select name="copy-group" id="copy-group-id">
                    <option value=""><?php echo Text::_('JSELECT'); ?></option>
					<?php echo HTMLHelper::_('select.options', HTMLHelper::_('user.groups')); ?>
                </select>
            </div>
        </div>
    </div>
</div>
