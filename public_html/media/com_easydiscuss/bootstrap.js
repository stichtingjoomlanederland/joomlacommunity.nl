<?php
/**
 * @package     EasyDiscuss
 * @copyright   Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php.
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
dispatch('Foundry/2.1').to(function($, manifest) {
	$.Component(
		'EasyDiscuss',
		{
			baseUrl: '<?php echo $url; ?>',
			environment: '<?php echo $environment; ?>',
			version: '<?php echo DiscussHelper::getLocalVersion(); ?>',
			jversion: '<?php echo DiscussHelper::getJoomlaVersion(); ?>',
			spinner: $.rootPath + 'media/com_easydiscuss/images/loading.gif',
			optimizeResources: true,
			dependencies: function(require) {
				require
					.library(
						'bootstrap',
						'markitup',
						'autogrow',
						'bookmarklet',
						'placeholder'
					)
					.script(
						'discuss'
					);
			},
			ajax: {
				data: {
					'<?php echo DiscussHelper::getToken(); ?>' : 1
				}
			},
			element: {}
		},
		function(self)
		{
			window.discussQuery = $;
			if (EasyDiscuss.environment == 'development')
			{
				try {
					console.info('EasyDiscuss component is now ready');
				} catch (e) {}
			}

			EasyDiscuss.require()
				.library('fancybox')
				.done(function($) {
					discuss.attachments.initGallery({
						type: 'image',
						helpers: {
							overlay: null
						}
					});
				});

			$("#discuss-wrapper [placeholder]").placeholder();


			<?php if( DiscussHelper::getConfig()->get( 'main_responsive' ) ){ ?>
			EasyDiscuss.require()
				.library( 'responsive' )
				.script('toolbar' )
				.done(function($) {
					$('#discuss-wrapper').responsive({at: 818, switchTo: 'w768'});
					$('#discuss-wrapper').responsive({at: 600, switchTo: 'w600'});
					$('#discuss-wrapper').responsive({at: 500, switchTo: 'w320'});

					$('.discuss-searchbar').responsive({at: 500, switchTo: 'narrow'});
			    });
			<?php } ?>

		}
	);

	$.language.add('COM_EASYDISCUSS_LOADING', "<?php echo JText::_('COM_EASYDISCUSS_LOADING'); ?>");

	<?php include('scripts_/bbcode.js'); ?>
});
/*]]>*/
