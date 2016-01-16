<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

if( ( $system->config->get( 'show_name' ) == 2 || $system->config->get( 'show_email' ) == 2 || $system->config->get( 'show_website' ) == 2 ) || ( $system->my->guest && ( $system->config->get( 'show_name' ) == 1 || $system->config->get( 'show_email' ) == 1 || $system->config->get( 'show_website' ) == 1 ) ) ) { ?>
	<div class="kmt-form-author kmt-form-grid clearfix formAuthor">
		<div class="row-table">
			<?php
				// Name field li.kmt-form-name
				echo $this->fetch( 'comment/form/namefield.php' );

				// Email field li.kmt-form-email
				echo $this->fetch( 'comment/form/emailfield.php' );

				// Website field li.kmt-form-website
				echo $this->fetch( 'comment/form/websitefield.php' );
			?>
		</div>
	</div>
<?php } ?>
