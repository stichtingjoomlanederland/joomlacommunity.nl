<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussGdprTemplate extends JObject
{
	public $id = null;
	public $type = null;
	public $created = null;

	// Determines if the item has an extended view (standalone pag)
	public $view = false;

	// Determines if the item is related to another view (standalone page)
	public $relation = false;

	// Title of the subject
	public $title = null;

	// Is only used in the listing to preview a snippet of html codes
	public $intro = null;

	// Is only used to generate contents on the item view page
	public $content = null;

	public $source = null;

	// to override the source filename.
	// in some situation, the source might be a hash only.
	// thus we need this attribute to hold the filename.
	// the array size of sourceFilename should be indentical to $source
	public $sourceFilename = null;


	/**
	 * Generates the view file
	 *
	 * @since   4.1.0
	 * @access  public
	 */
	public function createViewFile(EasyDiscussGdrpTab $tab)
	{
		$viewFile = $this->getViewFile($tab);
		$contents = $this->getItemContent($tab);

		JFile::write($viewFile, $contents);
	}

	/**
	 * Renders the output
	 *
	 * @since   4.1.0
	 * @access  public
	 */
	public function getListingContent(EasyDiscussGdrpTab $tab)
	{
		$relation = $this->hasRelation($tab);

		ob_start();
?>
		<div class="gdpr-item">
			<?php if ($relation) { ?>
			<a href="<?php echo $relation;?>" class="gdpr-item__title"><?php echo $this->title;?></a>
			<?php } else { ?>
				<?php if ($this->hasView()) { ?>
					<a href="<?php echo $this->getLink();?>" class="gdpr-item__title"><?php echo $this->title;?></a>
				<?php } else { ?>
					<b><?php echo $this->title;?></b>
				<?php } ?>
			<?php } ?>

			<?php if ($this->hasIntro()) { ?>
			<div class="gdpr-item__intro">
				<?php echo $this->intro;?>
			</div>
			<?php } ?>
		</div>
<?php
		$contents = ob_get_contents();
		ob_end_clean();


		return $contents;
	}

	/**
	 * Determines if the item has relation
	 *
	 * @since   4.1.0
	 * @access  public
	 */
	public function hasRelation(EasyDiscussGdrpTab $tab)
	{
		$path = false;

		if ($this->relation) {
			$userTmpPath = EasyDiscussGdpr::getUserTempPath($tab->adapter->user);

			$path = $userTmpPath . '/' . $this->relation;
		}

		return $path;
	}

	/**
	 * Renders the output for the item view
	 *
	 * @since   4.1.0
	 * @access  public
	 */
	public function getItemContent($tab)
	{
		$baseUrl = '';
		$sidebar = EasyDiscussGdpr::getSidebarContents(false, $tab->key);

		$content = $this->content;

		// Process media file
		if ($this->hasSource()) {
			$userTmpPath = EasyDiscussGdpr::getUserTempPath($tab->adapter->user);

			$sources = $this->source;
			$sourceFilenames = $this->sourceFilename;

			if (!is_array($sources)) {
				$sources = ED::makeArray($sources);
			}

			if (!is_array($sourceFilenames)) {
				$sourceFilenames = ED::makeArray($sourceFilenames);
			}

			for ($i = 0; $i < count($sources); $i++) {

				$source = $sources[$i];
				$filename = isset($sourceFilenames[$i]) ? $sourceFilenames[$i] : '';

				$mediaPath = $this->processMedia($userTmpPath, $source, $filename);

				// Replace the media link
				$content = preg_replace('/\{%MEDIA%\}/', '../' . $mediaPath, $content, 1);
			}
		}

		$theme = ED::themes();
		$theme->set('baseUrl', $baseUrl);
		$theme->set('sidebar', $sidebar);
		$theme->set('contents', $content);
		$theme->set('hasBack', true);
		$theme->set('sectionTitle', $this->title);
		$theme->set('sectionDesc', false);

		$output = $theme->output('site/gdpr/template');

		return $output;
	}

	/**
	 * Retrieves the link of the item
	 *
	 * @since   4.1.0
	 * @access  public
	 */
	public function getLink()
	{
		$link = $this->id . '.html';

		return $link;
	}

	/**
	 * Retrieves the file path to the view file
	 *
	 * @since   4.1.0
	 * @access  public
	 */
	public function getViewFile($tab)
	{
		$path = $tab->path . '/' . $this->id . '.html';

		return $path;
	}

	/**
	 * Determines if the item has an intro
	 *
	 * @since   4.1.0
	 * @access  public
	 */
	public function hasIntro()
	{
		if (!$this->intro) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if the item should have an item view
	 *
	 * @since   4.1.0
	 * @access  public
	 */
	public function hasView()
	{
		return $this->view;
	}

	/**
	 * Determines if the item should process media file
	 *
	 * @since   4.1.0
	 * @access  public
	 */
	public function hasSource()
	{
		return $this->source;
	}

	/**
	 * Copy over media file into download folder
	 *
	 * @since   4.1.0
	 * @access  public
	 */
	public function processMedia($tmpPath, $source, $fileName = '')
	{
		$segments = explode(':', $source);
		$storage = $segments[0];
		$relativePath = $segments[1];

		if (!$fileName) {
			$fileName = basename($relativePath);
		}

		// folder for media would be /media/[type]/id/file
		$folderPath = 'media/' . $this->type . '/' . $this->id;
		$filePath = $folderPath . '/' . $fileName;

		ED::makeFolder($tmpPath . '/' . $folderPath);

		// Generate the destination path
		$destinationFile = $tmpPath . '/' . $filePath;

		$this->download($storage, $relativePath, $destinationFile);

		return $filePath;
	}

	/**
	 * Download/Copy over the files
	 *
	 * @since   4.1.0
	 * @access  public
	 */
	public function download($storage, $source, $destination)
	{
		if ($storage == 'joomla') {
			$sourceFile = JPATH_ROOT . $source;

			if (JFile::exists($sourceFile)) {
				return JFile::copy($sourceFile, $destination);
			}

			return false;
		}

		$storage = ED::storage($storage);
		$url = $storage->getPermalink($source);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
		$rawdata = curl_exec($ch);
		curl_close ($ch);

		JFile::write($destination, $rawdata);
	}
}
