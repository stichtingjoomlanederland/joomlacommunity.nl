<?php
/**
 * @package     Komento
 * @copyright   Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

$newConfig = clone $config->toObject();
$newKonfig = clone $konfig->toObject();

// remove sensitive information
unset( $newConfig->antispam_recaptcha_private_key );
unset( $newConfig->antispam_recaptcha_public_key );
unset( $newConfig->antispam_akismet_key );
unset( $newConfig->layout_phpbb_path );
unset( $newConfig->layout_phpbb_url );
unset( $newKonfig->layout_phpbb_path );
unset( $newKonfig->layout_phpbb_url );
?>

dispatch("Foundry/2.1").to(function($, manifest) {

	$.Component(
		'Komento',
		{
			baseUrl: '<?php echo $url; ?>',
			environment: '<?php echo $environment; ?>',
			version: '<?php echo KomentoVersionHelper::getLocalVersion(); ?>',
			jversion: '<?php echo Komento::joomlaVersion(); ?>',
			spinner: '<?php echo JURI::root(); ?>media/com_komento/images/loader.gif',
			view: '<?php echo JRequest::getVar( "view", "" ); ?>',
			config: <?php echo json_encode($newConfig); ?>,
			konfig: <?php echo json_encode($newKonfig); ?>,
			acl: <?php echo isset($acl) ? json_encode($acl) : 0; ?>,
			guest: <?php echo isset($guest) ? $guest : 0; ?>,
			optimizeResources: true,
			resourceCollectionInterval: 1000,
			<?php if( isset( $resourcePath ) ) {
				echo "resourcePath: '" . $resourcePath . "',";
			} ?>
			ajax: {
				data: {
					"<?php echo Komento::_( 'getToken' ); ?>" : 1
				}
			},
			element: {}
		},
		function(self)
		{
			if(Komento.environment == "development") {
				try {
					console.info('Komento component is now ready');
				} catch(err) {}
			}

			if(Komento.options.config.enable_responsive == 1) {
				Komento.require().library('responsive').done(function($){
					$('#section-kmt').responsive({at: 818, switchTo: 'w768'});
					$('#section-kmt').responsive({at: 600, switchTo: 'w600'});
					$('#section-kmt').responsive({at: 400, switchTo: 'w320'});
				});
			}
		}
	);
});
