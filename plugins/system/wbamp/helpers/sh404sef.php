<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      wbAmp
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      1.4.2.551
 * @date        2016-07-19
 */

defined('_JEXEC') or die();

class WbampHelper_Sh404sef
{

	/**
	 * Collects meta data from sh404SEF, if installed and enabled
	 * and format them into an array
	 *
	 * @param $data
	 * @param $document
	 * @param $manager
	 */
	public static function processMetaData(& $data, $document, $manager)
	{
		if (defined('SH404SEF_IS_RUNNING')
			&& method_exists('Sh404sefHelperMetadata', 'getCustomMetaDataFromDb')
			&& method_exists('Sh404sefHelperOgp', 'buildOpenGraphTags')
			&& method_exists('Sh404sefHelperTcards', 'buildTwitterCardsTags')
			&& Sh404sefFactory::getConfig()->Enabled
			&& Sh404sefFactory::getConfig()->shMetaManagementActivated
		)
		{
			$data['sh404sef_custom_data'] = Sh404sefHelperMetadata::getCustomMetaDataFromDb();
			if (!empty($data['sh404sef_custom_data']->canonical))
			{
				$manager->setCanonicalUrl(ShlSystem_Route::absolutify($data['sh404sef_custom_data']->canonical));
			}
			Sh404sefFactory::getPageInfo()->pageCanonicalUrl = $manager->getCanonicalUrl();
			if (!empty($data['sh404sef_custom_data']->metatitle))
			{
				$document->setTitle($data['sh404sef_custom_data']->metatitle);
				$data['metadata']['title'] = $data['sh404sef_custom_data']->metatitle;
			}
			if (!empty($data['sh404sef_custom_data']->metadesc))
			{
				$document->setTitle($data['sh404sef_custom_data']->metadesc);
				$data['metadata']['description'] = $data['sh404sef_custom_data']->metadesc;
			}
			$data['metadata']['robots'] = JString::trim($data['sh404sef_custom_data']->metarobots);

			$ogp = Sh404sefHelperOgp::buildOpenGraphTags();
			$ogp = empty($ogp) || empty($ogp['openGraphData']) ? '' : $ogp['openGraphData'];
			$data['metadata']['ogp'] = $ogp;

			$data['metadata']['tcards'] = Sh404sefHelperTcards::buildTwitterCardsTags();

			// only use sh404SEF id if none set in wbAMP
			if (empty($data['metadata']['publisher_id']))
			{
				$data['metadata']['publisher_id'] = Sh404sefFactory::getConfig()->googlePublisherUrl;
				$data['metadata']['publisher_id'] = JString::trim($data['metadata']['publisher_id'], '/');
			}
		}
	}
}
