<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
    <div class="row-fluid">
        <div class="span8">
        </div>

        <!-- Start Sidebar -->
        <div class="span4">
            <div class="well well-large pwt-extensions">

                <!-- PWT branding -->
                <div class="pwt-section">
				    <?php echo HTMLHelper::_('image', 'com_pwtsitemap/pwt-sitemap.png', 'PWT Sitemap', array('class' => 'pwt-extension-logo'), true); ?>
                    <p class="pwt-heading"><?php echo Text::_('COM_PWTSITEMAP_DASHBOARD_ABOUT_HEADER'); ?></p>
                    <p><?php echo Text::_('COM_PWTSITEMAP_DASHBOARD_ABOUT_DESC'); ?></p>
                    <p>
                        <a href="https://extensions.perfectwebteam.com/pwt-sitemap">https://extensions.perfectwebteam.com/pwt-sitemap</a>
                    </p>
                    <p><?php echo Text::sprintf('COM_PWTSITEMAP_DASHBOARD_ABOUT_REVIEW', 'https://extensions.joomla.org/extension/pwt-sitemap'); ?></p>
                </div>

                <div class="pwt-section">

                    <div class="btn-group btn-group-justified">
                        <a class="btn btn-large btn-primary" href="https://extensions.perfectwebteam.com/pwt-sitemap/documentation"><?php echo Text::_('COM_PWTSITEMAP_DASHBOARD_ABOUT_DOCUMENTATION'); ?></a>
                        <a class="btn btn-large btn-primary" href="https://extensions.perfectwebteam.com/support"><?php echo Text::_('COM_PWTSITEMAP_DASHBOARD_ABOUT_SUPPORT'); ?></a>
                    </div>

                </div>

                <div class="pwt-section pwt-section--border-top">
                    <p><strong><?php echo Text::sprintf('COM_PWTSITEMAP_DASHBOARD_ABOUT_VERSION', '</strong>1.2.1'); ?></p>
                </div>
                <!-- End PWT branding -->

            </div>
        </div>
        <!-- End Sidebar -->
    </div>
</div>
