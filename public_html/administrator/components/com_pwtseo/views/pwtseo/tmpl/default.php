<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;
?>

<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
    <div class="row-fluid">
        <div class="span8">
            <div class="well">
				<?php if (Factory::getUser()->authorise('core.edit', 'com_plugins')): ?>
                    <p>
                        <a class="btn btn-primary btn-large"
                          href="<?php echo Route::_('index.php?option=com_plugins&task=plugin.edit&extension_id=' . PWTSEOHelper::getPlugin() . '&return=' . base64_encode('index.php?option=com_pwtseo')) ?>">
							<?php echo Text::_('COM_PWTSEO_ABOUT_PLUGIN_SETTINGS'); ?>
                        </a>
                    </p>
				<?php endif; ?>

                <p>
                    <strong>
						<?php echo Text::_('COM_PWTSEO_SEF_ENABLED_LABEL'); ?>
                    </strong>
					<?php echo Factory::getConfig()->get('sef', 0) === '1' ? Text::_('JYES') : Text::_('JNO') ?>
                </p>

				<?php if ($this->bHasSitemap !== null) : ?>
                    <p>
                        <strong>
							<?php echo Text::_('COM_PWTSEO_HAS_SITEMAP_LABEL'); ?>
                        </strong>
						<?php echo $this->bHasSitemap === true ? Text::_('JYES') : Text::_('JNO') ?>
                    </p>
				<?php endif; ?>

				<?php if ($this->aSitemaps !== null): ?>
                    <div class="">
                        <p>
                            <strong>
								<?php echo Text::_('COM_PWTSEO_GOOGLE_SITEMAPS_LABEL'); ?>
                            </strong>
                        </p>
						<?php if (!$this->aSitemaps): ?>
                            <span class="error">
                            <?php echo Text::_('JNO'); ?>
                        </span>
						<?php endif; ?>
                    </div>

					<?php if (is_array($this->aSitemaps) && count($this->aSitemaps) > 0): ?>
                        <div class="">
							<?php foreach ($this->aSitemaps as $oSitemap): ?>
                                <div class="pwt-flag-object">
                                    <div class="pwt-flag-object__aside">
										<?php echo $oSitemap->path; ?>
                                    </div>
                                    <div class="pwt-flag-object__body">
                                        <div>
											<?php echo Text::sprintf('COM_PWTSEO_SITEMAP_WARNINGS_LABEL', $oSitemap->warnings); ?>
                                        </div>
                                        <div>
											<?php echo Text::sprintf('COM_PWTSEO_SITEMAP_ERRORS_LABEL', $oSitemap->errors); ?>
                                        </div>
                                    </div>
                                </div>
							<?php endforeach; ?>
                        </div>
					<?php endif; ?>
				<?php endif; ?>
            </div>
            <?php if (is_array($this->aDomain) && isset($this->aDomain['google'])): ?>
                <div class="well">
                    <h3><?php echo Text::_('COM_PWTSEO_GENERAL_DOMAIN_INFORMATION') ?></h3>
                    <table>
                        <tr>
                            <td><?php echo Text::_('COM_PWTSEO_GENERAL_DOMAIN_GOOGLE_PAGES') ?>:&nbsp;</td>
                            <td><?php echo $this->aDomain['google']['pages'] ?></td>
                        </tr>
                        <tr>
                            <td><?php echo Text::_('COM_PWTSEO_GENERAL_DOMAIN_GOOGLE_BACKLINK') ?>:&nbsp;</td>
                            <td><?php echo $this->aDomain['google']['backlinks'] ?></td>
                        </tr>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Start Sidebar -->
        <div class="span4">
            <div class="well well-large pwt-extensions">

                <!-- PWT branding -->
                <div class="pwt-section">
					<?php echo HTMLHelper::_('image', 'com_pwtseo/pwt-seo.png', 'PWT SEO', array('class' => 'pwt-extension-logo'), true); ?>
                    <p class="pwt-heading"><?php echo Text::_('COM_PWTSEO_DASHBOARD_ABOUT_HEADER'); ?></p>
                    <p><?php echo Text::_('COM_PWTSEO_DASHBOARD_ABOUT_DESC'); ?></p>
                    <p>
                        <a href="https://extensions.perfectwebteam.com/pwt-seo">https://extensions.perfectwebteam.com/pwt-seo</a>
                    </p>
                    <p><?php echo Text::sprintf('COM_PWTSEO_DASHBOARD_ABOUT_REVIEW', 'https://extensions.joomla.org/extension/pwt-seo'); ?></p>
                </div>

                <div class="pwt-section">

                    <div class="btn-group btn-group-justified">
                        <a class="btn btn-large btn-primary" href="https://extensions.perfectwebteam.com/pwt-seo/documentation"><?php echo Text::_('COM_PWTSEO_DASHBOARD_ABOUT_DOCUMENTATION'); ?></a>
                        <a class="btn btn-large btn-primary" href="https://extensions.perfectwebteam.com/support"><?php echo Text::_('COM_PWTSEO_DASHBOARD_ABOUT_SUPPORT'); ?></a>
                    </div>

                </div>

                <div class="pwt-section pwt-section--border-top">
                    <p><strong><?php echo Text::sprintf('COM_PWTSEO_DASHBOARD_ABOUT_VERSION', '</strong>1.5.2'); ?>
                    </p>
                </div>
                <!-- End PWT branding -->

            </div>
        </div>
        <!-- End Sidebar -->
    </div>
</div>
