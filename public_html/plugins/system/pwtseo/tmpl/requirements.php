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

<div class="pseo-result-wrapper">
	<div class="error score-0" v-if="page.error_global">
		<div class="pseo-score__content">
			{{ page.error_global }} - <?php echo Text::_('PLG_SYSTEM_PWTSEO_LABELS_REQUESTED_URL') ?> {{ page.url }}
		</div>
	</div>
	<div class="notice" v-else-if="page.notice_global">
		<div class="pseo-score__content">
			{{ page.notice_global }}
		</div>
	</div>
	<div class="pseo-score" v-else>
		<h2 class="pseo-heading" title="<?php echo Text::_('PLG_SYSTEM_PWTSEO_LABELS_SEO_SCORE_DESC') ?>">
			<?php echo Text::_('PLG_SYSTEM_PWTSEO_LABELS_SEO_SCORE_LABEL') ?>:
		</h2>
		<?php if ($this->form->getName() === 'com_pwtseo.custom')
		:
			?>
			<test-keyword-in-title
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					@score-change="calculateTotalScore"></test-keyword-in-title>
			<test-page-title
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					@score-change="calculateTotalScore"></test-page-title>
			<test-page-title-length
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					@score-change="calculateTotalScore"></test-page-title-length>
			<test-keyword-in-meta
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					@score-change="calculateTotalScore"></test-keyword-in-meta>
			<test-keyword-in-url
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					@score-change="calculateTotalScore"></test-keyword-in-url>
			<test-result-body-has-images
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					@score-change="calculateTotalScore"></test-result-body-has-images>
			<test-result-body-keyword-density
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					@score-change="calculateTotalScore"></test-result-body-keyword-density>
			<test-keyword-not-used
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					@score-change="calculateTotalScore"></test-keyword-not-used>
		<?php else

		:
			?>
			<test-keyword-in-title
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					@score-change="calculateTotalScore"></test-keyword-in-title>
			<test-page-title
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					@score-change="calculateTotalScore"></test-page-title>
			<test-page-title-length
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					@score-change="calculateTotalScore"></test-page-title-length>
			<test-keyword-in-meta
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					@score-change="calculateTotalScore"></test-keyword-in-meta>
			<test-keyword-in-url
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					@score-change="calculateTotalScore"></test-keyword-in-url>
			<test-keyword-not-used
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					@score-change="calculateTotalScore"></test-keyword-not-used>
			<test-body-has-images
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					@score-change="calculateTotalScore"></test-body-has-images>
			<test-body-images-have-alt
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					@score-change="calculateTotalScore"></test-body-images-have-alt>
			<test-body-has-heading
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					@score-change="calculateTotalScore"></test-body-has-heading>
			<test-body-has-paragraph-first
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					@score-change="calculateTotalScore"></test-body-has-paragraph-first>
			<test-body-keyword-density
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					@score-change="calculateTotalScore"></test-body-keyword-density>
			<test-body-length
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					@score-change="calculateTotalScore"></test-body-length>
			<test-robots-reachable
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					@score-change="calculateTotalScore"></test-robots-reachable>
		<?php endif; ?>

	</div>
</div>

<?php if ($this->form->getName() !== 'com_pwtseo.custom')
:
	?>
	<div class="pseo-result-wrapper">
		<div class="score-0" v-if="page.error">
			<div class="pseo-score__content">
				{{ page.error }} - <?php echo Text::_('PLG_SYSTEM_PWTSEO_LABELS_REQUESTED_URL') ?> {{ page.url }}
			</div>
		</div>
		<div class="" v-else-if="page.notice_global">

		</div>
		<div class="pseo-score" v-else>
			<h2 class="pseo-heading" title="<?php echo Text::_('PLG_SYSTEM_PWTSEO_LABELS_RESULTING_PAGE_DESC') ?>">
				<?php echo Text::_('PLG_SYSTEM_PWTSEO_LABELS_RESULTING_PAGE_LABEL') ?>:
			</h2>
			<test-result-body-has-images
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					:applyscore="false"
					@score-change="calculateTotalScore"></test-result-body-has-images>
			<test-result-body-keyword-density
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					:applyscore="false"
					@score-change="calculateTotalScore"></test-result-body-keyword-density>
			<test-result-article-title-unique
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					:applyscore="false"
					@score-change="calculateTotalScore"></test-result-article-title-unique>
			<test-result-page-metadesc-unique
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					:applyscore="false"
					@score-change="calculateTotalScore"></test-result-page-metadesc-unique>
			<test-result-loading-times
					:local-config="localConfig"
					:plugin-config="pluginConfig"
					:page="page"
					:applyscore="false"
					@score-change="calculateTotalScore"></test-result-loading-times>
		</div>
	</div>
<?php endif; ?>

<div class="pseo-result-wrapper" v-if="!page.error_global && !page.notice_global">
	<h2 class="pseo-heading" title="<?php echo Text::_('PLG_SYSTEM_PWTSEO_LABELS_GENERAL_PAGE_DESC') ?>">
		<?php echo Text::_('PLG_SYSTEM_PWTSEO_LABELS_GENERAL_PAGE_LABEL') ?>:
	</h2>

	<test-result-most-common-words
			:local-config="localConfig"
			:plugin-config="pluginConfig"
			:page="page"
			:applyscore="false"
			@score-change="calculateTotalScore"></test-result-most-common-words>
	<test-results-current-google-rank
			:local-config="localConfig"
			:plugin-config="pluginConfig"
			:page="page"
			:applyscore="false"
			v-if="pluginConfig.has_rank_check"
			@score-change="calculateTotalScore"></test-results-current-google-rank>
</div>
