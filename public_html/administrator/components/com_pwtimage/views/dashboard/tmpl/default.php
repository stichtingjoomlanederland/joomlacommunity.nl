<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

?>

<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
    <div id="pwtacl" class="dashboard row-fluid">
        <div class="span8"></div>

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
                        <a class="btn btn-large btn-primary" href="https://extensions.perfectwebteam.com/pwt-image/documentation"><?php echo Text::_('COM_PWTIMAGE_DASHBOARD_ABOUT_DOCUMENTATION'); ?></a>
                        <a class="btn btn-large btn-primary" href="https://extensions.perfectwebteam.com/support"><?php echo Text::_('COM_PWTIMAGE_DASHBOARD_ABOUT_SUPPORT'); ?></a>
                    </div>

                </div>

                <div class="pwt-section pwt-section--border-top">
                    <p><strong><?php echo Text::sprintf('COM_PWTIMAGE_DASHBOARD_ABOUT_VERSION', '</strong>1.4.0'); ?></p>
                </div>
                <!-- End PWT branding -->

            </div>
        </div>
        <!-- End Sidebar -->
    </div>
</div>