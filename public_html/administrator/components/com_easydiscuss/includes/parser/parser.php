<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussParser extends EasyDiscuss
{
	public $hasGist = null;

	/**
	 * BBcode parsing
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function bbcode($text, $debug = false, $isAmp = false)
	{
		$text = trim($text);

		// We need to escape the content to avoid xss attacks
		$text = ED::string()->escape($text);

		// Replace [code] blocks
		if ($this->config->get('layout_bbcode_code')) {
			$text = $this->replaceCodes($text);
		}

		// Replace [gist] blocks
		if ($this->config->get('integrations_github')) {
			$text = $this->replaceGist($text, $isAmp);
		}

		$text = $this->replaceQuotedPost($text, $debug);

		// BBCode to find...
		$bbcodeSearch = array(
						 '/\[color=(.*?)\](.*?)\[\/color\]/ims',
						 '/\[b\](.*?)\[\/b\]/ims',
						 '/\[i\](.*?)\[\/i\]/ims',
						 '/\[u\](.*?)\[\/u\]/ims',
						 '/\[img\]((http|https):\/\/([a-zA-Z0-9\%\._\s\*_\/\+\-\=\?\&]+)\.(jpg|JPG|jpeg|JPEG|png|PNG|gif|GIF)?.*?)\[\/img]/ims',
						 '/\[quote\]([^\[\/quote\]].*?)\[\/quote\]/ims',
						 '/\[quote\](.*?)\[\/quote\]/ims',
						 '/\[left\](.*?)\[\/left\]/ims',
						 '/\[center\](.*?)\[\/center\]/ims',
						 '/\[right\](.*?)\[\/right\]/ims',
						 '/\[ul\](.*?)\[\/ul\]/ims',
						 '/\[ol\](.*?)\[\/ol\]/ims',
						 '/\[li\](.*?)\[\/li\]/ims',
						 '/\[email\](.*?)\[\/email\]/ims',
						 '/\[tr\](.*?)\[\/tr\]/ims',
						 '/\[td\](.*?)\[\/td\]/ims'
		);

		// And replace them by...
		$bbcodeReplace = array(
						 '<strong>\2</strong>',
						 '<strong>\1</strong>',
						 '<em>\1</em>',
						 '<u>\1</u>',
						 '<img src="\1" alt="\1" />',
						 '<blockquote>\1</blockquote>',
						 '<blockquote>\1</blockquote>',
						 '<p style="text-align: left;">\1</p>',
						 '<p style="text-align: center;">\1</p>',
						 '<p style="text-align: right;">\1</p>',
						 '<ul>\1</ul>',
						 '<ol>\1</ol>',
						 '<li>\1</li>',
						 '<a href="mailto:\1">\1</a>',
						 '<tr>\1</tr>',
						 '<td>\1</td>'
		);

		// @rule: Replace URL links.
		// We need to strip out bbcode's data first.
		$tmp = preg_replace($bbcodeSearch, '', $text);

		// Replace video codes if needed
		if ($this->config->get('layout_bbcode_video')) {
			$tmp = ED::videos()->strip($tmp);

			// @rule: Replace video links
			$text = ED::videos()->replace($text, $isAmp);
		}

		// we treat the quote abit special here for the nested tag.
		$parserUtil = new EasyDiscussParserUtilities('quote');
		$text = $parserUtil->parseTagsRecursive($text);

		// special treatment to UL and LI. Need to do this step 1st before send for replacing the rest bbcodes. @sam
		$text = EasyDiscussParserUtilities::parseListItems($text);
		$text = EasyDiscussParserUtilities::parseTables($text, $isAmp);

		// Replace bbcodes
		$text = preg_replace($bbcodeSearch, $bbcodeReplace, $text);

		// Replace any unused img tag to url tag for fail regex match
		$text = $this->replaceImgToUrl($text);

		// Urls have special treatments
		$text = $this->replaceBBCodeURL($text);

		// Replace the [giphy] with the its display
		$text = $this->replaceGiphyCode($text, $isAmp);

		// Replace twitter links with twitter embed objects
		if ($this->config->get('bbcode_tweet')) {
			$text = $this->replaceTweets($text, $isAmp);
		}

		// Replace gsit links with embed objects
		if ($this->config->get('bbcode_gist')) {
			$text = $this->replaceGistLinks($text, $isAmp);	
		}

		// Replace URLs ! important, we only do this url replacement after the bbcode url processed. @sam at 07 Jan 2013
		$text = ED::string()->replaceUrl($tmp, $text);

		// Replace mentions
		if ($this->config->get('main_mentions')) {
			$text = $this->replaceMentions($text);
		}

		// Replace smileys before anything else
		$text = $this->replaceSmileys($text);

		// Replace article tags (For kunena migrated items)
		$text = $this->replaceArticles($text);

		if ($isAmp) {
			$text = ED::image()->processAMP($text);
		}

		return $text;
	}

	/**
	 * Replace the [giphy] with its embed
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function replaceGiphyCode($content, $isAmp = false)
	{
		$giphy = ED::giphy();

		if (!$giphy->isEnabled()) {
			return $content;
		}

		preg_match_all('/\[giphy\](.*?)\[\/giphy\]/ims', $content, $matches);

		if (empty($matches) || !isset($matches[0]) || empty($matches[0])) {
			return $content;
		}

		$codes = $matches[0];
		$urls = $matches[1];
		$i = 0;

		foreach ($urls as $url) {
			if (!$giphy->isValidUrl($url)) {
				continue;
			}

			$item = $giphy->getEmbedItem($url);

			$content = EDJString::str_ireplace($codes[$i], $item, $content);
			$i++;
		}

		return $content;
	}

	/**
	 * Inject max-width styling for content image in email section
	 *
	 * @since	4.0.19
	 * @access	public
	 */
	public function normaliseImageStyle($content)
	{
		// we need to remove anything style attribute then reformat to show width 100%
		$content = preg_replace('/(<img[^>]+)style=".*?"/i', '$1', $content);
		$pattern = '/<img[^>]+>/ims';

		if (preg_match_all($pattern, $content, $matches)) {

			foreach ($matches as $match) {
				// Replace all occurences of width/height
				$clean = preg_replace('/(width|height)=["\'\d%\s]+/ims', "", $match);
				// Replace with result within html
				$content = str_replace($match, $clean, $content);
			}
		}

		// we need to inject max-width 100% for image tag
		$content = str_ireplace('src="', 'max-width="100%" style="max-width:100%" src="', $content);

		return $content;
	}

	/**
	 * Inject width attribute into bbcode image tag in email content.
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function normaliseBBCode($content)
	{
		$content = EDJString::str_ireplace('class="bb-smiley"', 'class="bb-smiley" width="20"', $content);
		return $content;
	}

	/**
	 * Normalize contents that can be used in the e-mail
	 *
	 * @since	5.0.3
	 * @access	public
	 */
	public function normaliseForEmail($content)
	{
		$pattern = "#<iframe[^>]+>.*?</iframe>#is";
		$content = preg_replace($pattern, "", $content);

		return $content;
	}

	/**
	 * Replaces attachments embed codes with proper values
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function replaceAttachmentsEmbed($text, $post, $isRemove = false)
	{
		if (!$this->config->get('attachment_questions') || !$post->id) {
			return $text;
		}

		// We cannot decode the htmlentities here or else, xss will occur!
		preg_match_all('/\[attachment\](.*?)\[\/attachment\]/ims', $text, $matches);

		if (empty($matches) || !isset($matches[0]) || empty($matches[0])) {
			return $text;
		}

		$codes = $matches[0];
		$files = $matches[1];
		$i = 0;

		foreach ($files as $title) {

			$code = $codes[$i];
			$i++;

			if ($isRemove) {
				$text = EDJString::str_ireplace($code, '', $text);
				continue;
			}

			$table = ED::table('Attachments');
			$table->load(array('uid' => $post->id, 'title' => $title));

			if ($table->id) {
				$attachment = ED::attachment($table);
				$contents = $attachment->getEmbedCodes();

				$text = EDJString::str_ireplace($code, $contents, $text);
			}
		}

		return $text;
	}

	/**
	 * Replaces quoted posts
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function replaceQuotedPost($text, $debug = false)
	{
		$pattern = '/\[quotePost id=&quot;(.*?)&quot;?\](.*?)\[\/quotePost\]/ms';

		$count = 0;

		do {		
			$text = preg_replace_callback($pattern, array('EasyDiscussParser', 'processQuotedPost'), $text, -1, $count);
		} while($count > 0);
		

		// $pattern = '/\[quote( id=&quot;(.*?)&quot;)?\](.*?)\[\/quote\]/ms';
		// $text = preg_replace_callback($pattern, array('EasyDiscussParser', 'processQuotedPost'), $text);

		// $pattern = '/\[quote( id="(.*?)")?\](.*?)\[\/quote\]/ms';
		// $text = preg_replace_callback($pattern, array('EasyDiscussParser', 'processQuotedPost'), $text);

		return $text;
	}

	/**
	 * Replace known smileys
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function replaceSmileys($text)
	{
		// We need to check if there is smileys override
		$overridePath = '/'.$this->app->getTemplate() . '/html/com_easydiscuss/smileys';

		$smileys = array (':D' => '/emoticon-happy.png',
						  ':)' => '/emoticon-smile.png',
						  ':o' => '/emoticon-surprised.png',
						  ':p' => '/emoticon-tongue.png',
						  ':(' => '/emoticon-unhappy.png',
						  ';)' => '/emoticon-wink.png'
		);

		$in = array();
		$out = array();

		foreach ($smileys as $smiley => $file) {

			$filePath = $overridePath . $file;

			// Check for subfolder
			$subfolder = JURI::root(true);

			$path = '/media/com_easydiscuss/images/markitup' . $file;

			if (!empty($subfolder)) {
				$path = $subfolder . $path;
			}

			// If the override file exist, we use that path.
			if (JFile::exists(DISCUSS_JOOMLA_SITE_TEMPLATES . $filePath)) {
				$path = DISCUSS_JOOMLA_SITE_TEMPLATES_URI . $filePath;
			}

			$str = str_replace(array(':', ';', ')', '('), array('\:', '\;', '\)', '\('), $smiley);
			$in[] = '/(?<![a-zA-Z0-9])(' . $str . ')(?![a-zA-Z0-9])/';
			$out[] = '<img alt="'.$smiley.'" class="bb-smiley" src="'.$path.'" />';
			
		}

		$text = preg_replace($in, $out, $text);
		return $text;
	}

	/**
	 * Replace any [img] tag to [url] tag
	 *
	 * @since	4.0.16
	 * @access	public
	 */
	public function replaceImgToUrl($text)
	{
		$pattern = '/\[img\](.*?)\[\/img]/ims';
		$replace = '[url="\1"]\1[/url]';

		$text = preg_replace($pattern, $replace, $text);

		return $text;
	}

	/**
	 * Replaces twitter url links with twitter embeds
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function replaceTweets($text, $isAmp = false)
	{
		// Pattern to skip the url that are within the specific html tag. eg: <img src="www.url.com">.
		$skipPattern = '<img[^>]*>(*SKIP)(*FAIL)|<script[^>]*>(*SKIP)(*FAIL)|<iframe[^>]*>(*SKIP)(*FAIL)|<a.*?<\/a>(*SKIP)(*FAIL)';

		// $pattern = '@' . $skipPattern . '|(?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’])@i';
		$pattern = '@' . $skipPattern . '|(?:https?:\/\/twitter.com)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’])@i';

		preg_match_all($pattern, $text, $matches);

		if (!empty($matches) && isset($matches[0]) && !empty($matches[0])) {
			$links = $matches[0];

			foreach ($links as $link) {
				if ($isAmp) {
					$html = $this->getTweetAMPHtml($link);

					$text = EDJString::str_ireplace($link, $html, $text);
					continue;
				}

				$url = 'https://api.twitter.com/1/statuses/oembed.json?url=' . $link;

				// Load up the connector first.
				$connector = ED::connector();
				$connector->addUrl($url);
				$connector->execute();

				// Get the result and parse them.
				$contents = $connector->getResult($url);

				// We are retrieving json data
				$oembed = json_decode($contents);

				if ($oembed && isset($oembed->html)) {
					// Remove newlines to avoid ED replacing newlines with <br/>
					$oembed->html = str_ireplace("\n", "", $oembed->html);

					$text = EDJString::str_ireplace($link, $oembed->html, $text);
				}
			}
		}

		return $text;
	}

	/**
	 * Replaces gist url links with twitter embeds
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function replaceGistLinks($text, $isAmp = false)
	{
		// Pattern to skip the url that are within the specific html tag. eg: <img src="www.url.com">.
		$skipPattern = '<img[^>]*>(*SKIP)(*FAIL)|<script[^>]*>(*SKIP)(*FAIL)|<iframe[^>]*>(*SKIP)(*FAIL)|<a.*?<\/a>(*SKIP)(*FAIL)';

		// $pattern = '@' . $skipPattern . '|(?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’])@i';
		$pattern = '@' . $skipPattern . '|(?:https?:\/\/gist.github.com)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’])@i';

		preg_match_all($pattern, $text, $matches);

		if (!empty($matches) && isset($matches[0]) && !empty($matches[0])) {
			$links = $matches[0];
			$regex = '#https://gist.github.com/(?:\#!/)?(\w+)/([A-Za-z0-9]+)#is';

			$this->hasGist = true;

			foreach ($links as $link) {
				if ($isAmp) {
					$html = $this->getGistAMPHtml($link, $regex);

					$text = EDJString::str_ireplace($link, $html, $text);
					continue;
				}

				$text = EDJString::str_ireplace($link, '<script src="' . $link . '.js"></script>', $text);
			}
		}

		return $text;
	}

	/**
	 * Determine if the content has gist after parsed
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function hasGist()
	{
		return $this->hasGist;
	}

	/**
	 * Replaces url tags in bbcode
	 *
	 * @since	4.0.15
	 * @access	public
	 */
	public function replaceBBCodeURL($text)
	{
		// We cannot decode the htmlentities here or else, xss will occur!
		preg_match_all('/\[url\=(.*?)\](.*?)\[\/url\]/ims', $text, $matches);

		if (!empty($matches) && isset($matches[0]) && !empty($matches[0])) {

			// Get the list of url tags
			$urlTags = $matches[0];
			$urls = $matches[1];
			$titles = $matches[2];

			$text = $this->processBBCodeURL($text, $urlTags, $urls, $titles);
		}

		// Further check if [url] tag still exists, eg [url]url.com[/url]
		preg_match_all('/\[url\](.*?)\[\/url\]/ims', $text, $matches);

		if (!empty($matches) && isset($matches[0]) && !empty($matches[0])) {

			// Get the list of url tags
			$urlTags = $matches[0];
			$urls = $matches[1];
			$titles = $matches[1];

			$text = $this->processBBCodeURL($text, $urlTags, $urls, $titles);
		}

		return $text;
	}

	/**
	 * Further processing for replaceBBCodeURL method
	 *
	 * @since	4.0.16
	 * @access	private
	 */
	public function processBBCodeURL($text, $urlTags, $urls, $titles)
	{
		$total = count($urlTags);

		for ($i = 0; $i < $total; $i++) {
			$url = $urls[$i];

			// Ensure that the url doesn't contain " or ' or &quot;
			$url = str_ireplace(array('"', "'", '&quot;'), '', $url);

			if (stristr($url, 'http://') === false && stristr($url, 'https://') === false && stristr($url, 'ftp://') === false) {
				$url = 'http://' . $url;
			}

			$linkAttr = ED::getLinkAttributes();

			$text = str_ireplace($urlTags[$i], '<a href="' . $url . '"' . $linkAttr .'>' . $titles[$i] . '</a>', $text);
		}

		return $text;
	}

	public static function removeBr($s)
	{
		// $string = str_replace("<br />", "", $s[0]);
		// $string = str_replace("<br>", "", $s[0]);

		$string = strip_tags($s[0], '<pre></pre>');
		return $string;
	}

	public function removeNewline($s) {
		return str_replace("\r\n", "", $s[0]);
	}

	/**
	 * Replace code blocks with prism.js compatible codes
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function replaceCodes($text, $debug = false)
	{
		// [code type=&quot*&quot]*[/code]
		$codesPattern = '/\[code( type=&quot;(.*?)&quot;)?\](.*?)\[\/code\]/ms';
		$text = preg_replace_callback($codesPattern, array('EasyDiscussParser', 'processCodeBlocks'), $text);

		// Replace [code type="*"]*[/code]
		$codesPattern = '/\[code( type="(.*?)")?\](.*?)\[\/code\]/ms';
		$text = preg_replace_callback($codesPattern, array('EasyDiscussParser', 'processCodeBlocks'), $text);

		return $text;
	}

	/**
	 * Replace gist blocks with correct gist url
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function replaceGist($text, $isAmp = false)
	{
		$codesPattern1 = '/\[gist( type=&quot;(.*?)&quot;)?\](.*?)\[\/gist\]/ms';
		$codesPattern2 = '/\[gist( type="(.*?)")?\](.*?)\[\/gist\]/ms';

		if ($isAmp) {
			$text = preg_replace_callback($codesPattern1, ['EasyDiscussParser', 'processGistBlocksForAMP'], $text);
			$text = preg_replace_callback($codesPattern2, ['EasyDiscussParser', 'processGistBlocksForAMP'], $text);

			return $text;
		}

		$text = preg_replace_callback($codesPattern1, array('EasyDiscussParser', 'processGistBlocks'), $text);
		$text = preg_replace_callback($codesPattern2, array('EasyDiscussParser', 'processGistBlocks'), $text);

		return $text;
	}

	/**
	 * Process gist blocks for AMP
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function processGistBlocksForAMP($matches)
	{
		$html = '';
		$url = '';
		$tmp = $matches[1];
		$tmp = str_replace('&quot;', '"', $tmp);
		$regex = '#https://gist.github.com/([A-Za-z0-9]+)#is';

		$pattern = '/url="(.*?)"/ms';
		preg_match($pattern, $tmp, $match);

		if ($match && isset($match[1])) {
			// Remove '.js' from the url
			if (strpos($match[1], '.js') !== false) {
				$url = str_replace('.js', '', $match[1]);
			}

			$html = $this->getGistAMPHtml($url, $regex);
		}

		return $html;
	}

	/**
	 * Replace article tags
	 *
	 * @since	4.1.18
	 * @access	public
	 */
	public function replaceArticles($text)
	{
		$pattern = '/\[article(=)?(.*?)\](.*?)\[\/article\]/ims';

		preg_match($pattern, $text, $matches);

		if (empty($matches) || !isset($matches[0]) || empty($matches[0]) || empty($matches[3])) {
			return $text;
		}

		$articleId = (int) $matches[3];
		$type = isset($matches[2]) && $matches[2] ? $matches[2] : 'intro';

		if (!$articleId) {
			return $text;
		}

		$article = JTable::getInstance('Content');
		$article->load($articleId);

		$article->params = new JRegistry($article->attribs);
		$article->metadata = new JRegistry($article->metadata);
		$article->text = $article->introtext;

		if ($type == 'full') {
			$article->text = $article->introtext . ' ' . $article->fulltext;
		}

		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('content');
		$dispatcher->trigger('onContentPrepare', array ('com_content.article', &$article, &$article->params, 0));

		// Get the permalink
		$alias = $article->alias ? ($article->id . ':' . $article->alias) : $article->id;
		$link = EDContentHelperRoute::getArticleRoute($alias, $article->catid, $article->language);
		$permalink = EDR::siteLink($link, true, null, false);

		JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);

		$theme = ED::themes();
		$theme->set('text', $article->text);
		$theme->set('permalink', $permalink);
		$articleText = $theme->output('site/parser/article');

		// Remove all newlines from the article text to avoid the parser in ED to replace to br
		$articleText = EDJString::str_ireplace("\r\n", "", $articleText);
		
		$contents = EDJString::str_ireplace($matches[0], $articleText, $text);


		return $contents;
	}

	/**
	 * Process gist blocks
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function processGistBlocks($blocks)
	{
		$this->hasGist = true;

		// check if we have the gist url or not.
		// if yes, we will just reuse the existing gist url.
		$tmp = $blocks[1];

		$tmp = str_replace('&quot;', '"', $tmp);

		$pattern = '/url="(.*?)"/ms';
		preg_match($pattern, $tmp, $matches);

		if ($matches && isset($matches[1])) {
			$url = $matches[1];

			return '<script src="' . $url . '" data-ed-scripts-gist></script>';
		}

		// The codes are on the 3rd index.
		$code = $blocks[3];

		// Determine the language type
		$language = isset($blocks[2]) && !empty($blocks[2]) ? $blocks[2] : 'html';

		// Because the text / contents are already escaped, we need to revert back to the original html codes only for the codes.
		$code = html_entity_decode($code);

		// Send to gist to create the gist now.
		$github = ED::github();
		$url = $github->createGist($code, $language);

		// Check if the gist was successfully created
		if (!$url) {
			return '';
		}

		return '<script src="' . $url . '.js" data-ed-scripts-gist></script>';
	}

	/**
	 * Replace mentions
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function replaceMentions($text, $debug = false)
	{
		$users = ED::string()->detectNames($text);

		if (!$users) {
			return $text;
		}

		foreach ($users as $user) {
			$link = $user->getLink();
			$name = htmlentities($user->getName());

			$search = array('@' . $name . '#');

			$popbox = "";
			if (!$this->config->get('integration_easysocial_popbox')) {
				$popbox .= ' data-ed-popbox="ajax://site/views/popbox/user"';
				$popbox .= ' data-ed-popbox-position="top-left"';
				$popbox .= ' data-ed-popbox-toggle="hover"';
				$popbox .= ' data-ed-popbox-offset="4"';
				$popbox .= ' data-ed-popbox-type="ed-avatar"';
				$popbox .= ' data-ed-popbox-component="o-popbox--user"';
				$popbox .= ' data-ed-popbox-cache="1"';
				$popbox .= ' data-args-id="' . $user->id . '"';
			}

			$replace = '<a href="' . $link . '"' . $popbox . '>' . $name . '</a>';

			if ($user->isBlocked()) {
				$replace = $user->getName();
			}

			$text = EDJString::str_ireplace($search, $replace, $text);
		}

		return $text;
	}

	/**
	 * Replace [tag] tag to make compatible with kunena content.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function processHideTag($content)
	{
		if ($this->my->guest) {
			// remove content in [hide] tags
			$content = preg_replace( '/\[hide\](.*?)\[\/hide\]/ms' , '' , $content);
		} else {
			$content = preg_replace( '/\[hide\](.*?)\[\/hide\]/ms' , '<div class="o-alert o-alert--info-o"><i>\1</i></div>' , $content);
		}

		return $content;
	}

	public function processSizeTag($content)
	{
		// BBCode to find...
		$sizeSearch = array(
			'/\[size=1](.*?)\[\/size\]/ims',
			'/\[size=2](.*?)\[\/size\]/ims',
			'/\[size=3](.*?)\[\/size\]/ims',
			'/\[size=4](.*?)\[\/size\]/ims',
			'/\[size=5](.*?)\[\/size\]/ims',
			'/\[size=6](.*?)\[\/size\]/ims',
			'/\[size=(.*?)](.*?)\[\/size\]/ims'
			);

		// And replace them by...
		$sizeReplace = array(
			'<span style="font-size:0.6em">\1</span>',
			'<span style="font-size:0.8em">\1</span>',
			'<span style="font-size:1.0em">\1</span>',
			'<span style="font-size:1.2em">\1</span>',
			'<span style="font-size:1.4em">\1</span>',
			'<span style="font-size:1.8em">\1</span>',
			'<span style="font-size:\1">\2</span>'
		);

		$content = preg_replace($sizeSearch, $sizeReplace , $content);
		return $content;
	}


	/**
	 * Replace [spoiler] tag to make compatible with kunena content.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function processSpoilerTag($content)
	{
		// We cannot decode the htmlentities here or else, xss will occur!
		preg_match_all('/\[spoiler\](.*?)\[\/spoiler\]/ims', $content, $matches);

		if (empty($matches) || !isset($matches[0]) || empty($matches[0])) {
			// nothing found.
			return $content;
		}

		$codes = $matches[0];
		$contents = $matches[1];

		$i = 0;

		foreach ($codes as $code) {

			$text = $contents[$i];

			$theme = ED::themes();
			$theme->set('content', $text);
			$spoiler = $theme->output('site/parser/spoiler');

			$content = EDJString::str_ireplace($code, $spoiler, $content);

			$i++;
		}

		return $content;
	}

	/**
	 * Replace [code] blocks with prism.js compatibility
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function processCodeBlocks($blocks)
	{
		$code = $blocks[3];

		// Remove break tags
		$code = str_ireplace("<br />", "", $code);
		$code = str_replace("[", "&#91;", $code);
		$code = str_replace("]", "&#93;", $code);

		// Determine the language type
		$language = isset($blocks[2]) && !empty($blocks[2]) ? $blocks[2] : 'markup';

		// Fix legacy code blocks
		if ($language == 'xml' || $language == 'html') {
			$language = 'markup';
		}

		// Because the text / contents are already escaped, we need to revert back to the original html codes only
		// for the codes.
		$code = html_entity_decode($code);

		// Fix html codes not displaying correctly
		$code = htmlspecialchars($code, ENT_NOQUOTES);

		return '<pre class="line-numbers"><code class="language-' . $language . '">'.$code.'</code></pre>';
	}

	/**
	 * Processes quoted post blocks
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function processQuotedPost($blocks)
	{
		// Get the post id
		$id = (int) $blocks[1];

		if (!$id) {
			return $blocks[0];
		}
		
		// The quoted contents are on the 3rd index.
		$contents = $blocks[2];
		// $contents = html_entity_decode($contents);

		// If we cannot determine the post id or the post has been deleted, just ignore this
		$user = false;
		$permalink = false;
		$postDate = null;

		if ($id) {
			$post = ED::post($id);
			
			if ($post->id) {
				$user = $post->getAuthor();
				$permalink = $post->getPermalink();
				$postDate = $post->getDateObject()->format(JText::_('DATE_FORMAT_LC1'));
			}
		}

		$theme = ED::themes();
		$theme->set('contents', $contents);
		$theme->set('user', $user);
		$theme->set('permalink', $permalink);
		$theme->set('postDate', $postDate);

		$contents = $theme->output('site/parser/quotes');

		// Remove newlines to avoid newline to br replacing these newlines
		$contents = EDJString::str_ireplace("\n", "", $contents);

		return $contents;
	}

	public function removeCodes( $content )
	{
		$codesPattern	= '/\[code( type="(.*?)")?\](.*?)\[\/code\]/ms';

		return preg_replace( $codesPattern , '' , $content );
	}

	public function filter($text)
	{
		$text	= htmlspecialchars($text , ENT_NOQUOTES );
		$text	= trim($text);

		// @rule: Replace [code]*[/code]
		$text = preg_replace_callback('/\[code( type="(.*?)")?\](.*?)\[\/code\]/ms', array( 'EasyDiscussParser' , 'replaceCodes' ) , $text );

		// BBCode to find...
		$bbcodeSearch = array( 	 '/\[b\](.*?)\[\/b\]/ims',
						 '/\[i\](.*?)\[\/i\]/ims',
						 '/\[u\](.*?)\[\/u\]/ims',
						 '/\[img\](.*?)\[\/img\]/ims',
						 '/\[email\](.*?)\[\/email\]/ims',
						 '/\[url\="?(.*?)"?\](.*?)\[\/url\]/ims',
						 '/\[size\="?(.*?)"?\](.*?)\[\/size\]/ims',
						 '/\[color\="?(.*?)"?\](.*?)\[\/color\]/ims',
						 '/\[quote](.*?)\[\/quote\]/ims',
						 '/\[list\=(.*?)\](.(\[\*\])+.*?)\[\/list\]/ims',
						 '/\[list\](.(\[\*\])+.*?)\[\/list\]/ims',
						 '/\[\*\]\s?(.*?)\n/ims'
		);

		// @rule: Replace URL links.
		// We need to strip out bbcode's data first.
		$text = preg_replace($bbcodeSearch, '', $text);
		$text = ED::string()->replaceUrl($text, $text);


		// Smileys to find...
		$in = array( 	 ':)',
						 ':D',
						 ':o',
						 ':p',
						 ':(',
						 ';)'
		);
		// And replace them by...
		$out = array(	 '<img alt=":)" src="'.EMOTICONS_DIR.'emoticon-smile.png" />',
						 '<img alt=":D" src="'.EMOTICONS_DIR.'emoticon-happy.png" />',
						 '<img alt=":o" src="'.EMOTICONS_DIR.'emoticon-surprised.png" />',
						 '<img alt=":p" src="'.EMOTICONS_DIR.'emoticon-tongue.png" />',
						 '<img alt=":(" src="'.EMOTICONS_DIR.'emoticon-unhappy.png" />',
						 '<img alt=";)" src="'.EMOTICONS_DIR.'emoticon-wink.png" />'
		);
		$text = str_replace($in, $out, $text);

		// now we need to decode the the special html chars back to original chars.
		$text = html_entity_decode( $text );

		return $text;
	}

	/**
	 * Converts those image link do not have contain with domain name
	 *
	 * @since	4.0.12
	 * @access	public
	 */
	public function convert2validImgLink($content)
	{
		$base =	rtrim(JURI::root());

		// TODO: check for the subfolder as well

		$pattern = '/<img.*?src=["|\'](.*?)["|\'].*?\>/';
		preg_match_all($pattern, $content, $matches);

		if (isset($matches[1]) && count($matches[1]) > 0) {

			// To prevent duplicate matched value
			$uniqueMatched = array_unique($matches[1]);

			foreach ($uniqueMatched as $imgPath) {
				if ($imgPath[0] == '/') {
					$base =	rtrim(JURI::root(), '/');
				}

				// Check whether this is valid img link with the http protocol
				// Replace the domain name into image path
				if (EDJString::strpos($imgPath , 'http://') === false && EDJString::strpos($imgPath, 'https://') === false) {
					$content = str_replace($imgPath, $base . $imgPath, $content);
				}
			}
		}

		return $content;
	}

	/**
	 * Converts html codes to bbcode
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function html2bbcode($text)
	{
		if ((stripos($text, '<p') === false) && (stripos($text, '<div') === false) &&  (stripos($text, '<br') === false)) {
			return $text;
		}

		$bbcodeSearch = array(
			'/<strong>(.*?)<\/strong>/ims',
			'/<b>(.*?)<\/b>/ims',
			'/<big>(.*?)<\/big>/ims',
			'/<em>(.*?)<\/em>/ims',
			'/<i>(.*?)<\/i>/ims',
			'/<u>(.*?)<\/u>/ims',
			'/<img.*?src=["|\'](.*?)["|\'].*?\>/ims',
			'/<[pP]>/ims',
			'/<[pP] style=["|\']text-align: (center|left|right).*?["|\'].*?>(.*?)<\/[pP]>/ims', // Check for supported text align
			'/<[pP] style=["|\'].*?["|\'].*?>/ims', // Remove all other styling as bbcode does not support it
			'/<\/[pP]>/ims',
			'/<blockquote>(.*?)<\/blockquote>/ims',
			'/<ol.*?\>(.*?)<\/ol>/ims',
			'/<ul.*?\>(.*?)<\/ul>/ims',
			'/<li.*?\>(.*?)<\/li>/ims',
			'/<tr.*?\>(.*?)<\/tr>/ims',
			'/<td.*?\>(.*?)<\/td>/ims',
			'/<a.*?href=["|\']mailto:(.*?)["|\'].*?\>.*?<\/a>/ims',
			'/<a.*?href=["|\'](.*?)["|\'].*?\>(.*?)<\/a>/ims',
			'/<pre.*?\>(.*?)<\/pre>/ims'
		);

		$bbcodeReplace = array(
			'[b]\1[/b]',
			'[b]\1[/b]',
			'[b]\1[/b]',
			'[i]\1[/i]',
			'[i]\1[/i]',
			'[u]\1[/u]',
			'[img]\1[/img]',
			'',
			'[\1]\2[/\1]',
			'',
			'<br />',
			'[quote]\1[/quote]',
			'[list=1]\1[/list]',
			'[list]\1[/list]',
			'[*] \1',
			'[tr]\1[/tr]',
			'[td] \1',
			'[email]\1[/email]',
			'[url=\1]\2[/url]',
			'[code type="xml"]\1[/code]'
		);

		// Smileys to find...
		$out = array( 	 ':)',
						 ':D',
						 ':o',
						 ':p',
						 ':(',
						 ';)'
		);
		// And replace them by...
		$in = array(	 '<img alt=":)" src="'.EMOTICONS_DIR.'emoticon-smile.png" />',
						 '<img alt=":D" src="'.EMOTICONS_DIR.'emoticon-happy.png" />',
						 '<img alt=":o" src="'.EMOTICONS_DIR.'emoticon-surprised.png" />',
						 '<img alt=":p" src="'.EMOTICONS_DIR.'emoticon-tongue.png" />',
						 '<img alt=":(" src="'.EMOTICONS_DIR.'emoticon-unhappy.png" />',
						 '<img alt=";)" src="'.EMOTICONS_DIR.'emoticon-wink.png" />'
		);

		//@samhere
		//$text = str_replace($in, $out, $text);

		$oldText = $text;

		// Replace bbcodes
		$text = strip_tags($text, '<br><strong><em><u><img><a><p><blockquote><ol><ul><li><b><big><i><pre>');
		$text = preg_replace($bbcodeSearch , $bbcodeReplace, $text);
		$text = str_ireplace('<br />', "\r\n", $text);
		$text = str_ireplace('<br>', "\r\n", $text);

		return $text;
	}


	public function smiley2bbcode( $content )
	{

		$pattern		= '/<img.*?src=["|\'](.*?)["|\'].*?\>/';
		preg_match_all( $pattern , $content , $matches );

		if( isset( $matches[0] ) &&	count( $matches[0] ) > 0 )
		{
			for( $i = 0; $i < count( $matches[0] ); $i++ )
			{
				$imgTag = $matches[0][$i];
				$imgSrc = $matches[1][$i];

				if( strpos($imgSrc, 'emoticon-smile.png') !== false )
				{
					$content	= str_replace( $imgTag , ':)' , $content);
					continue;
				}

				if( strpos($imgSrc, 'emoticon-happy.png') !== false )
				{
					$content	= str_replace( $imgTag , ':D' , $content);
					continue;
				}

				if( strpos($imgSrc, 'emoticon-surprised.png') !== false )
				{
					$content	= str_replace( $imgTag , ':o' , $content);
					continue;
				}

				if( strpos($imgSrc, 'emoticon-tongue.png') !== false )
				{
					$content	= str_replace( $imgTag , ':p' , $content);
					continue;
				}

				if( strpos($imgSrc, 'emoticon-unhappy.png') !== false )
				{
					$content	= str_replace( $imgTag , ':(' , $content);
					continue;
				}

				if( strpos($imgSrc, 'emoticon-wink.png') !== false )
				{
					$content	= str_replace( $imgTag , ';)' , $content);
					continue;
				}

			}
		}

		return $content;
	}



	public function removeBrTag( $content )
	{
		$content	= nl2br($content);

		//Remove BR in pre tag
		$content = preg_replace_callback('/<pre.*?\>(.*?)<\/pre>/ims', array( 'EasyDiscussParser' , 'removeBr' ) , $content );

		return $content;
	}

	public function quoteBbcode( $text )
	{
		// BBCode to find...
		$bbcodeSearch = array( 	 '/\[b\](.*?)\[\/b\]/ims',
						 '/\[i\](.*?)\[\/i\]/ims',
						 '/\[u\](.*?)\[\/u\]/ims',
						 '/\[img\](.*?)\[\/img\]/ims',
						 '/\[email\](.*?)\[\/email\]/ims',
						 '/\[size\="?(.*?)"?\](.*?)\[\/size\]/ims',
						 '/\[color\="?(.*?)"?\](.*?)\[\/color\]/ims',
						 '/\[list\=(.*?)\](.*?)\[\/list\]/ims',
						 '/\[list\](.*?)\[\/list\]/ims'
		);

		// And replace them by...
		$addQuote = array('[quote][b]\1[/b][/quote]',
						 '[quote][i]\1[/i][/quote]',
						 '[quote][u]\1[/u][/quote]',
						 '[quote][img]\1[/img][/quote]',
						 '[quote][email]\1[/email][/quote]',
						 '[quote][size="\1"]\2[/size][/quote]',
						 '[quote][color="\1"]\2[/color][/quote]',
						 '[quote][list="\1"]\2[/list][/quote]',
						 '[quote][list]\1[/list][/quote]'
					);

		$quoteSearch = array(	'/\[quote](.*?)\[\/quote\]/ims',

		);

		$quoteReplace = array(
						 '<blockquote>\1</blockquote>',
		);

		// Replace bbcodes
		$text = preg_replace( $bbcodeSearch , $addQuote, $text);
		$text = preg_replace( $quoteSearch, $quoteReplace, $text );

		return $text;
	}

	/**
	 * Retrieve AMP html for tweet
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public function getTweetAMPHtml($url)
	{
		// retrieve the Tweet ID
		$regex = '#https?://twitter\.com/(?:\#!/)?(\w+)/status(es)?/(\d+)#is';

		$html = '';

		if (preg_match($regex, $url, $match)) {

			$html = '<amp-twitter width="486" height="657" layout="responsive" data-tweetid="' . $match[3] . '" ></amp-twitter>';

			return $html;
		}

		return $html;
	}

	/**
	 * Retrieve AMP html for gist
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public function getGistAMPHtml($url, $regex)
	{
		$html = '';

		if (!$url) {
			return $html;
		}

		if (preg_match($regex, $url, $match)) {
			$id = $match[1];

			if (isset($match[2]) && $match[2]) {
				$id = $match[2];
			}

			$html = '<amp-gist data-gistid="' . $id . '" layout="fixed-height" height="225"></amp-gist>';

			return $html;
		}

		return $html;
	}
}

class EasyDiscussParserUtilities
{
	var $bbcode = '';

	public function __construct( $bbcode )
	{
		$this->bbcode = $bbcode;
	}

	public function parseTagsRecursive( $inputs )
	{
		$inputs = preg_replace('#\[quote\]#', '<blockquote>', $inputs );
		$inputs = preg_replace('#\[quote=(.+?)\]#', '<blockquote>', $inputs );
		$inputs = preg_replace('#\[quote=(.+?);(.+?)\]#', '<blockquote>', $inputs );
		$inputs = preg_replace('#\[quote=(.+?)\d+\]#', '<blockquote>', $inputs);
		$inputs = preg_replace('#\[/quote\]#', '</blockquote>', $inputs );

		return $inputs;
	 }

	/**
	 * Parse list items
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function parseListItems($content)
	{
		// BBCode to find... e.g.
		// [list]
		// [*]hello world
		// [*]marihome
		// [/list]
		$bbcodeListItemsSearch = '#\[list.*?\](.*?)\[\/list\]#ims';

		// BBCode to find... e.g.
		// [*]hello world
		$bbcodeLISearch = array(
			 '/\[\*\]\s?(.*?)\n/ims',
			 '/\[\*\]\s?(.*?)/ims'
		);

		// And replace them by...
		$bbcodeLIReplace = array(
			 '<li>\1</li>',
			 '<li>\1</li>'
		);

		// And replace them by...
		$bbcodeLIReplaceString = array(
			 '\1',
			 '\1'
		);

		// BBCode to find...
		$bbcodeListPattern = array(
			 '/\[list\=(.*?)\]/ims',
			 '/\[list\]/ims',
			 '/\[\/list\]/ims'
		);

		$bbcodeULSearch = array(
			 '/\[list\=(.*?)\](.*?)\[\/list\]/ims',
			 '/\[list\](.*?)\[\/list\]/ims',
		);

		// And replace them by...
		$bbcodeULReplace = array(
			 '<ol start="\1">\2</ol>',
			 '<ul>\1</ul>'
		);

		// And replace them by...
		$bbcodeULReplaceString = array('\2', '\1');

		preg_match_all($bbcodeListItemsSearch, $content, $matches);

		if (!$matches || !$matches[0]) {
			return $content;
		}

		$lists = array();

		// Fix any unclosed list tags
		foreach ($matches[0] as &$contents) {

			$original = $contents;

			// The match of lists within this block should always be the first and last. Anything within the "list" should be considered as unclosed.
			$contents = preg_replace($bbcodeListPattern, '', $contents);
			$contents = '[list]' . $contents . '[/list]';

			$item = new stdClass();
			$item->original = $original;
			$item->contents = $contents;

			$lists[] = $item;
		}

		foreach ($lists as $list) {

			// Check if this list contains any list items "[*]"
			if (EDJString::strpos($list->contents, '[*]') !== false) {

				$text = preg_replace($bbcodeULSearch, $bbcodeULReplace, $list->original);

				$text = preg_replace($bbcodeLISearch, $bbcodeLIReplace, $text);
				// dump($text);
			} else {
				$text = preg_replace($bbcodeULSearch, $bbcodeULReplaceString, $list->original);
			}

			// Update the content
			$content = EDJString::str_ireplace($list->original, $text, $content);
		}

		return $content;
	}

	public static function parseTables($content, $isAmp = false)
	{
		// BBCode to find... e.g.
		// [table][tr]
		// [td] Hola1
		// [td] Hola2
		// [td] Hola3
		// [tr][/table]
		$bbcodeTablesSearch = '#\[table.*?\](.*?)\[\/table\]#ims';

		// BBCode to find. e.g.
		// [td]hello world
		$bbcodeTDSearch = array(
			'/\[td\]\s?(.*?)\[\/td\]\r\n/ims'
			// '/\[td\]\s?(.*?)\n/ims'
			 // '/\[td\]\s?(.*?)/ims'
		);

		// And replace them by...
		$bbcodeTDReplace = array(
			 '<td>\1</td>'
		);

		// And replace them by...
		$bbcodeTDReplaceString = array(
			 '\1',
			 '\1'
		);

		// BBCode to find...
		$bbcodeTablePattern = array(
			 '/\[table\=(.*?)\]/ims',
			 '/\[table\]/ims',
			 '/\[\/table\]/ims'
		);

		$bbcodeTableSearch = array(
			 '/\[table\](.*?)\[\/table\]/ims',
		);

		$tableTag = '<table>\1</table>';

		if ($isAmp) {
			$tableTag = '<table class="table-bordered">\1</table>';
		}

		// And replace them by...
		$bbcodeTableReplace = array(
			 $tableTag
		);

		// And replace them by...
		$bbcodeTRReplaceString = array('\2', '\1');

		// BBCode to find...
		$bbcodeTRSearch = array('/\[tr\]\s?\r\n(.*?)\[\/tr\]/ims');

		// And replace them by...
		$bbcodeTRReplace = array('<tr>\1</tr>');

		preg_match_all($bbcodeTablesSearch, $content, $matches);

		if (!$matches || !$matches[0]) {
			return $content;
		}

		$tables = array();

		// Fix any unclosed list tags
		foreach ($matches[0] as &$contents) {

			$original = $contents;

			// The match of lists within this block should always be the first and last. Anything within the "list" should be considered as unclosed.
			$contents = preg_replace($bbcodeTablePattern, '', $contents);
			
			$contents = '[table]' . $contents . '[/table]';

			$item = new stdClass();
			$item->original = $original;
			$item->contents = $contents;

			$tables[] = $item;
		}

		foreach ($tables as $table) {

			// Check if this table contains any table items "[td]"
			if (EDJString::strpos($table->contents, '[td]') !== false) {
				$text = preg_replace($bbcodeTableSearch, $bbcodeTableReplace, $table->original);
				$text = preg_replace($bbcodeTDSearch, $bbcodeTDReplace, $text);
				$text = preg_replace($bbcodeTRSearch, $bbcodeTRReplace, $text);

				// $text = trim(preg_replace('/\s\s+/', ' ', $text));
			
			} else {
				$text = preg_replace($bbcodeTRSearch, $bbcodeTRReplaceString, $table->original);
			}

			// Update the content
			$content = EDJString::str_ireplace($table->original, $text, $content);
		}

		return $content;
	}
}
