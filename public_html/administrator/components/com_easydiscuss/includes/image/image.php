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

class EasyDiscussImage extends EasyDiscuss
{
	/**
	 * Checks if a given file path is a valid image
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function isImage($filePath)
	{
		$images = array('image/jpeg', 'image/png', 'image/jpg', 'image/gif');

		$info = getimagesize($filePath);

		// For whatever reason that we cannot get the image info from getimagesize, we just treat this as non image
		if ($info === false) {
			return false;
		}

		if (in_array($info['mime'], $images)) {
			return true;
		}

		return false;
	}

	/**
	 * Checks for the file extension name
	 *
	 * @since	4.1.11
	 * @access	public
	 */
	public static function getFileExtention($fileName)
	{
		if (empty($fileName)) {
			return false;
		}

		$data = explode('.', $fileName);

		return $data[count($data) - 1];
	}

	/**
	 * Checks if the file is an image
	 * @param string The filename
	 * @return file type
	 */
	public static function getTypeIcon( $fileName )
	{
		// Get file extension
		return strtolower(substr($fileName, strrpos($fileName, '.') + 1));
	}

	/**
	 * Determines if an extension is an image type
	 *
	 * @since	4.1.11
	 * @access	public
	 */
	public static function isImageExtension($fileName)
	{
		static $imageTypes = 'gif|jpg|jpeg|png';

		return preg_match("/$imageTypes/i", $fileName);
	}

	/**
	 * Checks if the file can be uploaded
	 *
	 * @since	4.1.11
	 * @access	public
	 */
	public static function canUpload($file, &$error)
	{
		//$params = JComponentHelper::getParams( 'com_media' );
		$config = ED::config();
		$maxSize = $config->get( 'main_upload_maxsize' );

		// Convert MB to B
		$maxSize = $maxSize * 1024 * 1024;

		if (empty($file['name'])) {
			$error = JText::_('COM_EASYDISCUSS_EMPTY_FILENAME');
			return false;
		}

		jimport('joomla.filesystem.file');

		$targetFile = JFile::makesafe($file['name']);

		if ($file['name'] !== $targetFile) {
			$error = JText::_('COM_EASYDISCUSS_INVALID_FILENAME');
			return false;
		}

		// Checks if the file contains any funky html tags
		$containsXSS = self::containsXSS($file['tmp_name']);

		if ($containsXSS) {
			$error = JText::_('COM_EASYDISCUSS_INVALID_IMG');
			return false;
		}

		// Validate the file whether is an image type
		$isImageType = self::isImageExtension($file['name']);

		if (!$isImageType) {
			$error = JText::_('COM_EASYDISCUSS_INVALID_IMG');
			return false;
		}

		// Validate the file whether is valid image
		$isImage = ED::image()->isImage($file['tmp_name']);

		if (!$isImage) {
			$error = JText::_('COM_EASYDISCUSS_INVALID_IMG');
			return false;
		}

		$maxWidth	= 160;
		$maxHeight	= 160;

		// maxsize should get from eblog config
		//$maxSize	= 2000000; //2MB
		//$maxSize	= 200000; //200KB
		//$maxSize = (int) $params->get( 'main_upload_maxsize', 0 );

		if ($maxSize > 0 && (int) $file['size'] > $maxSize) {
			$error = JText::_('COM_EASYDISCUSS_FILE_TOO_LARGE');
			return false;
		}

		$user = JFactory::getUser();
		$imginfo = null;

		if (($imginfo = getimagesize($file['tmp_name'])) === FALSE) {
			$error = JText::_('COM_EASYDISCUSS_IMAGE_CORRUPT');
			return false;
		}

		return true;
	}

	/**
	 * Checks if the file contains any funky html tags
	 *
	 * @since	4.1.11
	 * @access	public
	 */
	public static function containsXSS($path)
	{
		// Sanitize the content of the files
		$contents = file_get_contents($path, false, null, 0, 256);
		$tags = array('abbr','acronym','address','applet','area','audioscope','base','basefont','bdo','bgsound','big','blackface','blink','blockquote','body','bq','br','button','caption','center','cite','code','col','colgroup','comment','custom','dd','del','dfn','dir','div','dl','dt','em','embed','fieldset','fn','font','form','frame','frameset','h1','h2','h3','h4','h5','h6','head','hr','html','iframe','ilayer','img','input','ins','isindex','keygen','kbd','label','layer','legend','li','limittext','link','listing','map','marquee','menu','meta','multicol','nobr','noembed','noframes','noscript','nosmartquotes','object','ol','optgroup','option','param','plaintext','pre','rt','ruby','s','samp','script','select','server','shadow','sidebar','small','spacer','span','strike','strong','style','sub','sup','table','tbody','td','textarea','tfoot','th','thead','title','tr','tt','ul','var','wbr','xml','xmp','!DOCTYPE', '!--');

		// If we can't read the file, just skip this altogether
		if (!$contents) {
			return false;
		}

		foreach ($tags as $tag) {
			// If this tag is matched anywhere in the contents, we can safely assume that this file is dangerous
			if (stristr($contents, '<' . $tag . ' ') || stristr($contents, '<' . $tag . '>') || stristr($contents, '<?php') || stristr($contents, '?\>')) {
				return true;
			}
		}

		return false;
	}

	public static function parseSize($size)
	{
		if ($size < 1024) {
			return $size . ' bytes';
		}
		else
		{
			if ($size >= 1024 && $size < 1024 * 1024) {
				return sprintf('%01.2f', $size / 1024.0) . ' Kb';
			} else {
				return sprintf('%01.2f', $size / (1024.0 * 1024)) . ' Mb';
			}
		}
	}

	public static function imageResize($width, $height, $target)
	{
		//takes the larger size of the width and height and applies the
		//formula accordingly...this is so this script will work
		//dynamically with any size image
		if ($width > $height) {
			$percentage = ($target / $width);
		} else {
			$percentage = ($target / $height);
		}

		//gets the new value and applies the percentage, then rounds the value
		$width = round($width * $percentage);
		$height = round($height * $percentage);

		return array($width, $height);
	}

	public static function countFiles( $dir )
	{
		$total_file = 0;
		$total_dir = 0;

		if (is_dir($dir)) {
			$d = dir($dir);

			while (false !== ($entry = $d->read())) {
				if (substr($entry, 0, 1) != '.' && is_file($dir . '/' . $entry) && strpos($entry, '.html') === false && strpos($entry, '.php') === false) {
					$total_file++;
				}
				if (substr($entry, 0, 1) != '.' && is_dir($dir . '/' . $entry)) {
					$total_dir++;
				}
			}

			$d->close();
		}

		return array ( $total_file, $total_dir );
	}

	public static function getAvatarDimension($avatar)
	{
		//resize the avatar image
		$avatar	= JPath::clean( JPATH_ROOT . '/' . $avatar );
		$info	= getimagesize($avatar);
		if(! $info === false)
		{
			$thumb	= DiscussImageHelper::imageResize($info[0], $info[1], 60);
		}
		else
		{
			$config = ED::config();
			$size = $config->get('layout_avatarthumbwidth', 60);
			// the image ratio always 1:1
			$thumb  = array($size, $size);
		}

		return $thumb;
	}

	public static function getAvatarRelativePath($type = 'profile')
	{
		$config = ED::config();
		$avatar_config_path = '';

		switch($type)
		{
			case 'category':
				$avatar_config_path = $config->get('main_categoryavatarpath');
				break;
			case 'profile':
			default:
				$avatar_config_path = $config->get('main_avatarpath');
				break;
		}

		$avatar_config_path = rtrim($avatar_config_path, '/');
		//$avatar_config_path = str_replace('/', DIRECTORY_SEPARATOR, $avatar_config_path);

		return $avatar_config_path;
	}

	public static function rel2abs($rel, $base)
	{
		/* return if already absolute URL */
		if (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;


		/* queries and anchors */
		if ($rel[0]=='#' || $rel[0]=='?') return $base.$rel;

		/* parse base URL and convert to local variables:
			$scheme, $host, $path */
		extract(parse_url($base));

		if( isset($path) )
		{
			/* remove non-directory element from path */
			$path = preg_replace('#/[^/]*$#', '', $path);

			/* destroy path if relative url points to root */
			if ($rel[0] == '/') $path = '';
		}
		else
		{
			$path = '';
		}

		/* dirty absolute URL */
		$abs = "$host$path/$rel";
		/* replace '//' or '/./' or '/foo/../' with '/' */
		$re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
		for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}

		/* absolute URL is ready! */
		return $scheme.'://'.$abs;
	}

	/**
	 * Process the <img> to <amp-img>
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function processAMP($content)
	{
		$pattern = '#<img[^>]*>#i';
		preg_match_all($pattern, $content, $matches);

		if (!$matches) {
			return [];
		}

		$smileys = ['emoticon-happy.png',
					'emoticon-smile.png',
					'emoticon-surprised.png',
					'emoticon-tongue.png',
					'emoticon-unhappy.png',
					'emoticon-wink.png'];

		foreach ($matches[0] as $image) {

			preg_match('/src="([^"]+)"/', $image, $src);

			$url = $src[1];
			$responsive = 'layout="responsive"';
			$isSmiley = false;

			foreach ($smileys as $smiley) {
				if (strrpos($url, $smiley) !== false) {
					$isSmiley = true;

					break;
				}
			}

			// Do not set responsive for the smiley img
			if ($isSmiley) {
				$responsive = '';
			}

			$subDomain = JURI::root(true);

			if (stristr($url, 'https:') === false && stristr($url, 'http:') === false) {

				if (stristr($url, '//') === false) {
					$url = ltrim($url, '/');

					// If this is a subdomain
					if ($subDomain) {
						$subDomain = ltrim($subDomain, '/') . '/';

						// Remove the subdomain from the url
						if (stristr($url, $subDomain) !== false) {
							$url = str_replace($subDomain, '', $url);
						}
					}

					$url = rtrim(JURI::root(), '/') . '/' . ltrim($url);
				} else {
					$uri = JURI::getInstance();

					$scheme = $uri->toString(array('scheme'));

					$scheme = str_replace('://', ':', $scheme);

					$url = $scheme . $url;
				}
			}

			// we need to supress the warning here in case allow_url_fopen disabled on the site. #865
			$imageData = @getimagesize($url);

			// Set the default width and height if is false
			if (!$imageData && !$isSmiley) {
				$imageData = [];
				$imageData[0] = 300;
				$imageData[1] = 225;
			}

			// Set the width and height for the smiley
			if ($isSmiley) {
				$imageData = [];
				$imageData[0] = 20;
				$imageData[1] = 20;
			}

			$coverInfo = 'width="' . $imageData[0] . '" height="' . $imageData[1] . '"';

			$ampImage = '<amp-img src="' . $url . '" ' . $coverInfo . $responsive . '></amp-img>';

			ob_start();
			echo '<!-- START -->';
			echo $ampImage;
			echo '<!-- END -->';
			$output = ob_get_contents();
			ob_end_clean();

			//For legacy gallery, it always be wrap in <p>. We need to take it out.
			// $output = str_replace('<!-- START -->', '<p>', $output);
			// $output = str_replace('<!-- END -->', '<p>', $output);

			$content = str_ireplace($image, $output, $content);
		}

		return $content;
	}
}
