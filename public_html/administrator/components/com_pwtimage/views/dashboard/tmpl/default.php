<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2020 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

$canEdit  = $this->canDo->get('core.edit');
$canAdmin = $this->canDo->get('core.admin');
?>

<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
    <div id="pwtacl" class="dashboard row-fluid">
        <div class="span8">
            <!-- Standard Sitemaps -->
            <div class="well">
                <legend>
					<?php echo Text::_('COM_PWTIMAGE_SUBMENU_PROFILES'); ?>
                </legend>

				<?php if (!empty($this->profiles)): ?>
                    <table class="table table-striped table-vcenter" id="sitemap">
                        <tbody>
						<?php foreach ($this->profiles as $i => $item) : ?>
                            <tr>
                                <td>
									<?php if ($canAdmin) : ?>
                                        <a href="<?php echo Route::_('index.php?option=com_pwtimage&task=profile.edit&id=' . (int) $item->id); ?>"
                                           title="<?php echo Text::sprintf('COM_PWTIMAGE_EDIT_PROFILE', $this->escape($item->name)); ?>">
											<?php echo $this->escape($item->name); ?></a>
									<?php else : ?>
										<?php echo $this->escape($item->name); ?>
									<?php endif; ?>
                                </td>
                            </tr>
						<?php endforeach; ?>

                        </tbody>
                    </table>
				<?php endif; ?>
            </div>
        </div>

        <!-- Start Sidebar -->
        <div class="span4">
            <div class="well well-large pwt-extensions">

                <!-- PWT branding -->
                <div class="pwt-section">
					<?php echo HTMLHelper::_('image', 'com_pwtimage/pwt-image.png', 'PWT Image', array('class' => 'pwt-extension-logo'), true); ?>
                    <p class="pwt-heading"><?php echo Text::_('COM_PWTIMAGE_DASHBOARD_ABOUT_HEADER'); ?></p>
                    <p><?php echo Text::_('COM_PWTIMAGE_DASHBOARD_ABOUT_DESC'); ?></p>
                    <p>
                        <a href="https://extensions.perfectwebteam.com/pwt-image">https://extensions.perfectwebteam.com/pwt-image</a>
                    </p>
                    <p><?php echo Text::sprintf('COM_PWTIMAGE_DASHBOARD_ABOUT_REVIEW', 'https://extensions.joomla.org/extension/pwt-image'); ?></p>
                </div>

                <div class="pwt-section">

                    <div class="btn-group btn-group-justified">
                        <a class="btn btn-large btn-primary"
                           href="https://extensions.perfectwebteam.com/pwt-image/documentation"><?php echo Text::_('COM_PWTIMAGE_DASHBOARD_ABOUT_DOCUMENTATION'); ?></a>
                        <a class="btn btn-large btn-primary"
                           href="https://extensions.perfectwebteam.com/support"><?php echo Text::_('COM_PWTIMAGE_DASHBOARD_ABOUT_SUPPORT'); ?></a>
                    </div>

                </div>

                <div class="pwt-section pwt-section--border-top">
                    <p>
                        <strong><?php echo Text::sprintf('COM_PWTIMAGE_DASHBOARD_ABOUT_VERSION', '</strong>1.6.0'); ?>
                    </p>
                </div>
                <!-- End PWT branding -->

            </div>
        </div>
        <!-- End Sidebar -->
    </div>
</div>
