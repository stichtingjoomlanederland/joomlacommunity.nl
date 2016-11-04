<?php
/**
 * @build_title_build@
 *
 * @author       @build_author_build@
 * @copyright    @build_copyright_build@
 * @package      @build_package_build@
 * @license      @build_license_build@
 * @version      @build_version_build@
 *
 * @build_current_date_build@
 */

// no direct access
defined('_JEXEC') or die;

if (!$displayData['params']->get('enable_analytics') || $displayData['params']->get('analytics_type', 'ga') != 'gtm')
{
	return;
}

?>
<!-- wbAMP: Google Tag Manager -->
<amp-analytics
	config="https://www.googletagmanager.com/amp.json?id=<?php echo trim($displayData['params']->get('analytics_webproperty_id')); ?>"
	data-credentials="include"></amp-analytics>
<!-- wbAMP: Google Tag Manager -->
