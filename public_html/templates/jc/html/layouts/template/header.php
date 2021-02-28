<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.perfecttemplate
 *
 * @copyright   Copyright (C) 2019 Perfect Web Team / perfectwebteam.nl
 * @license     GNU General Public License version 3 or later
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Uri\Uri;
?>

<div class="header<?php echo PWTTemplateHelper::isHome() ? ' homepage' : ''; ?>">
	<nav class="navbar navbar-default navbar-main" role="navigation" data-spy="affix" data-offset-top="110">
		<div class="container">

			<?php //Brand and toggle get grouped for better mobile display ?>
			<div class="navbar-header navbar-logo">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#jc-navbar-collapse-1" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="logo" href=".">
					<span class="navbar-logo-name"><?php echo PWTTemplateHelper::getSitename(); ?>
                        <span class="navbar-logo-subline"><?php echo Text::_('TPL_JC_SUBLINE') ?></span>
					</span>
				</a>
			</div>

			<div class="navbar-main-menu navbar-left">
				<jdoc:include type="modules" name="mainmenu"/>
			</div>

			<?php // Collect the nav links, forms, and other content for toggling ?>
			<div class="collapse navbar-collapse" id="jc-navbar-collapse-1">
				<div class="navbar-mobile">
					<jdoc:include type="modules" name="mainmenu-mobile"/>
					<jdoc:include type="modules" name="search"/>
				</div>
				<jdoc:include type="modules" name="usermenu"/>
			</div>

		</div>
	</nav>

	<div class="subnav" data-spy="affix" data-offset-top="110">
		<div class="container">
			<nav class="navbar navbar-sub" role="navigation">
				<jdoc:include type="modules" name="submenu"/>
				<jdoc:include type="modules" name="search"/>
			</nav>
		</div>
	</div>
</div>
