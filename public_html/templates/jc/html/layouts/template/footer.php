<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.perfecttemplate
 *
 * @copyright   Copyright (C) 2019 Perfect Web Team / perfectwebteam.nl
 * @license     GNU General Public License version 3 or later
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

defined('JPATH_BASE') or die;
?>

<!-- Footer -->
<div class="footer">
	<div class="footer-navs">
		<div class="container">
			<div class="row">
				<div class="col-sm-12 col-md-12 col-lg-4">
					<jdoc:include type="modules" name="footer-1" style="xhtml"/>
				</div>
				<div class="col-sm-12 col-md-3 col-lg-2">
					<jdoc:include type="modules" name="footer-2" style="xhtml"/>
				</div>
				<div class="col-sm-12  col-md-3  col-lg-2 ">
					<jdoc:include type="modules" name="footer-3" style="xhtml"/>
				</div>
				<div class="col-sm-12  col-md-3  col-lg-2 ">
					<jdoc:include type="modules" name="footer-4" style="xhtml"/>
				</div>
				<div class="col-sm-12  col-md-3  col-lg-2 ">
					<jdoc:include type="modules" name="footer-5" style="xhtml"/>
				</div>
			</div>
		</div>
	</div>
	<div class="footer-copyright">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<p class="copyright">&copy; Copyright 2008-<?php echo date('Y'); ?></p>
					<jdoc:include type="modules" name="copyright"/>

					<p class="followusicons"><?php
						echo HTMLHelper::_('link',
							'https://www.facebook.com/joomlacommunity/',
							'<i class="fa fa-facebook-square fa-2x" aria-hidden="true"></i>',
							[
								'target' => '_blank'
							]
						);
						echo HTMLHelper::_('link',
							'https://www.linkedin.com/groups/1857791',
							'<i class="fa fa-linkedin-square fa-2x" aria-hidden="true"></i>',
							[
								'target' => '_blank'
							]
						);
						echo HTMLHelper::_('link',
							'https://twitter.com/joomlacommunity',
							'<i class="fa fa-twitter-square fa-2x" aria-hidden="true"></i>',
							[
								'target' => '_blank'
							]
						);
						echo HTMLHelper::_('link',
							'https://www.github.com/stichtingjoomlanederland',
							'<i class="fa fa-github-square fa-2x" aria-hidden="true"></i>',
							[
								'target' => '_blank'
							]
						);
						?></p>
					<p class="followus">
						Volg ons op:
					</p>
				</div>
			</div>
		</div>
	</div>
</div>
