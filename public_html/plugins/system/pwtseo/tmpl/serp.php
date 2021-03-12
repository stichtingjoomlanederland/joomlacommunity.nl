<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>

<div class="pseo-serp-wrapper" v-if="!page.error_global && !page.notice_global">
	<h2 class="pseo-heading"><?php echo Text::_('PLG_SYSTEM_PWTSEO_LABELS_SERP') ?></h2>
	<div class="pseo-serp">
		<div class="pseo-serp__title js-serp-title">{{ page.pagetitle | truncate({length: 70}) }}</div>
		<div class="pseo-serp__url js-serp-ext-url">{{ page.new_url }}</div>
		<div class="pseo-serp__description js-serp-description">{{ page.metadesc | truncate({length: 300}) }}</div>
	</div>
</div>
