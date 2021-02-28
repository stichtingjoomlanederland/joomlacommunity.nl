<?php
/*
 * @package     perfecttemplate
 * @copyright   Copyright (c) Perfect Web Team / perfectwebteam.nl
 * @license     GNU General Public License version 3 or later
 */

// No direct access.
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

// Load Perfect Template Helper
require_once JPATH_THEMES . '/' . $this->template . '/helpers/helper.php';

// Helpers
$favicolorMS = '#95a5a6';
$favicolorTheme = '#95a5a6';
$favicolorSVG = '#95a5a6';
PWTTemplateHelper::setMetadata($favicolorMS, $favicolorTheme);
PWTTemplateHelper::setFavicon($favicolorSVG);
//PWTTemplateHelper::unloadCss(['com_finder', 'foundry', 'com_rseventspro', 'com_rscomments', 'com_easydiscuss']);
PWTTemplateHelper::unloadCss(['com_finder', 'foundry', 'com_rseventspro', 'com_rscomments']);
PWTTemplateHelper::unloadJs();
PWTTemplateHelper::loadCss();
PWTTemplateHelper::loadJs(true, false);
PWTTemplateHelper::localstorageFont();
PWTTemplateHelper::setMetadataTwitter('joomlacommunity', 'joomlacommunity');
?>
<!DOCTYPE html>
<html class="html no-js" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<?php echo LayoutHelper::render('template.head'); ?>
</head>

<body class="<?php echo PWTTemplateHelper::setBodyClass(); ?>">

<?php echo LayoutHelper::render('template.leaderboard', ['id' => 'div-gpt-ad-1483558516537-0']); ?>

<?php echo LayoutHelper::render('template.header'); ?>

<?php if (PWTTemplateHelper::isHome() == true) : ?>
	<div class="jc-welcome">
		<div class="overlay">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="joomla-intro">
							<jdoc:include type="modules" name="welcome-intro"/>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-lg-3">
						<jdoc:include type="modules" name="welcome-a"/>
					</div>
					<div class="col-md-6 col-lg-3">
						<jdoc:include type="modules" name="welcome-b"/>
					</div>
					<div class="col-md-6 col-lg-3">
						<jdoc:include type="modules" name="welcome-c"/>
					</div>
					<div class="col-md-6 col-lg-3">
						<jdoc:include type="modules" name="welcome-d"/>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>

<!-- Body -->
<div class="content">
	<div class="container">
		<div class="row">
			<jdoc:include type="modules" name="top-a" style="panel"/>
		</div>
		<div class="row">
			<jdoc:include type="modules" name="breadcrumbs"/>

			<?php if (PWTTemplateHelper::isHome() == true) : ?>
				<div class="col-md-7 laatste-nieuws">
					<jdoc:include type="modules" name="home-left" style=""/>
				</div>
				<div class="col-md-5">
					<div class="promo">
						<jdoc:include type="modules" name="home-promo"/>
					</div>
					<jdoc:include type="modules" name="home-right" style="panel"/><?php
					echo HTMLHelper::_('link',
						Route::_('index.php?Itemid=244'),
						'Bekijk de volledige agenda',
						[

							'class' => 'btn btn-agenda btn-block'
						]
					);
					?></div>
			<?php else: ?>
				<div class="content-<?php echo($this->countModules('rechts') ? 8 : 12); ?>">
					<?php if (count(Factory::getApplication()->getMessageQueue())) : ?>
						<jdoc:include type="message"/>
					<?php endif; ?>
					<jdoc:include type="modules" name="above-component"/>
					<jdoc:include type="component"/>
					<jdoc:include type="modules" name="below-component"/>
				</div>
				<?php if ($this->countModules('rechts')) : ?>
					<div class="content-4">
						<jdoc:include type="modules" name="rechts" style="panel"/>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php echo LayoutHelper::render('template.leaderboard', ['id' => 'div-gpt-ad-1487456152006-0']); ?>

<?php echo LayoutHelper::render('template.footer'); ?>

</body>
</html>
