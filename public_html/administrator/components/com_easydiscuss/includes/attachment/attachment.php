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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class EasyDiscussAttachment extends EasyDiscuss
{
	// This is the DiscussConversation table
	public $table = null;

	public $message = null;

	public function __construct($item)
	{
		parent::__construct();

		// Always have a default table available.
		$this->table = ED::table('Attachments');

		// For object that is being passed in
		if (is_object($item) && !($item instanceof DiscussAttachments)) {
			$this->table->bind($item);
		}

		// If the object is DiscussConversation, just map the variable back.
		if ($item instanceof DiscussAttachments) {
			$this->table = $item;
		}

		// If this is an integer
		if (is_int($item) || is_string($item)) {
			$this->table->load($item);
		}

		// Legacy mapping for attachment type
		if (isset($item->type)) {
			$this->attachmentType = $this->getType();
		}

		$this->acl = ED::acl();
	}

	/**
	 * Magic method to get properties which don't exist on this object but on the table
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function __get($key)
	{
		if (isset($this->table->$key)) {
			return $this->table->$key;
		}

		if (isset($this->$key)) {
			return $this->$key;
		}

		return $this->table->$key;
	}

	/**
	 * Determines if this attachment can be deleted
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function canDelete()
	{
		// Guest user shouldn't have permisson to delete
		if (!$this->my->id) {
			return false;
		}

		// For super admin and moderators, we should allow this to happen
		if (ED::isSiteAdmin()) {
			return true;
		}

		if ($this->acl->allowed('delete_attachment')) {
			return true;
		}

		// Get the post
		$post = ED::post($this->table->uid);

		if (ED::isModerator($post->category_id)) {
			return true;
		}

		if ($post->user_id == $this->my->id) {
			return true;
		}

		return false;
	}

	/**
	 * Creates a thumbnail
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function createThumbnail($pathToImage)
	{
		// Generate a temporary file
		$temp = dirname($pathToImage) . '/' . basename($pathToImage) . '_thumb';

		$image = ED::simpleimage();
		$image->load($pathToImage);
		$image->resizeToFill(160, 120);
		$image->save($temp, $image->image_type);

		return $temp;
	}

	/**
	 * Allows caller to set properties to the table without directly accessing it
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function set($key, $value)
	{
		$this->table->$key = $value;
	}

	/**
	 * Deletes an attachment
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function delete()
	{
		// Delete it from the db first
		$state = $this->table->delete();

		if (!$state) {
			$this->setError($this->table->getError());
			return false;
		}

		// Get the path to the file
		$path = $this->getAbsolutePath();

		// Now we need to delete the file
		$storage = ED::storage($this->table->storage);
		$storage->delete($path);

		// If this is an image, we need to delete the thumbnail as well
		if ($this->isImage()) {
			$path = $this->getAbsolutePath(true);

			$storage->delete($path);
		}

		return $state;
	}

	/**
	 * Saves an attachment
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function save()
	{
		return $this->table->store();
	}

	/**
	 * Renders the html output used in the content of a post
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getEmbedCodes()
	{
		// Get attachment type
		$type = $this->getType();

		$theme = ED::themes();
		$theme->set('type', $type);
		$theme->set('attachment', $this);

		return $theme->output('site/attachments/embed');
	}

	/**
	 * Renders the html output of an attachment for display purposes
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function html($external = false)
	{
		// Get attachment type
		$type = $this->getType();

		$theme = ED::themes();
		$theme->set('type', $type);
		$theme->set('attachment', $this);
		$theme->set('external', $external);

		return $theme->output('site/attachments/item');
	}

	/**
	 * Determines if this is an image
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function isImage()
	{
		$type = $this->getType();

		if ($type == 'image') {
			return true;
		}

		return false;
	}

	/**
	 * Get the source of the file
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getThumbnail($external = false)
	{
		$type = $this->getType();

		if ($external) {
			$url = EDR::getRoutedURL('index.php?option=com_easydiscuss&controller=attachment&task=thumbnail&tmpl=component&id=' . $this->table->id, false, true);
		} else {
			$url = JRoute::_('index.php?option=com_easydiscuss&controller=attachment&task=thumbnail&tmpl=component&id=' . $this->table->id);
		}

		// If the item is stored remotely, we need to set the source to the amazon site
		if ($this->table->storage == 'amazon') {

			// Get the storage relative path
			$relativePath = $this->getStoragePath(true) . '/' . $this->table->path . '_thumb';

			$storage = ED::storage('amazon');
			$url = $storage->getPermalink($relativePath);
		}

		return $url;
	}

	/**
	 * Gets the absolute path to a file
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getAbsolutePath($thumbnail = false)
	{
		$storage = $this->getStoragePath();
		$file = $storage . '/' . $this->table->path;

		if ($thumbnail) {
			$file .= '_thumb';
		}

		return $file;
	}

	/**
	 * Gets the download link
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getDownloadLink()
	{
		$task = 'download';

		if ($this->table->storage == 'amazon') {
			$task = 'downloadAmazon';
		}

		$url = 'index.php?option=com_easydiscuss&controller=attachment&task=' . $task . '&tmpl=component&id=' . $this->table->id;
		$link = rtrim(JURI::root(), '/') . '/' . $url;

		return $link;
	}

	/**
	 * Gets the storage path
	 *
	 * @since	4.0.16
	 * @access	public
	 */
	public function getStoragePath($relative = false)
	{
		// Prior to 4.0.16, we allow users to only change the relative path name
		// This is why, we need to fallback to the original path if there is no value on storage_path
		$path = ltrim($this->config->get('storage_path', '/media/com_easydiscuss/' . trim($this->config->get('attachment_path', '/'))), '/');

		if ($relative) {
			return '/' . $path;
		}

		$folder = rtrim(JPATH_ROOT, '/') . '/' . $path;

		if (!JFolder::exists($folder)) {
			JFolder::create($folder);
		}

		return $folder;
	}

	/**
	 * Determines if the file exceeded the limit
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getUploadLimit()
	{
		$size = (double) $this->config->get('attachment_maxsize') * 1024 * 1024;

		return $size;
	}

	/**
	 * Retrieves the image type
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function getType()
	{
		$type = explode("/", $this->table->mime);

		return $type[0];
	}

	/**
	 * Given a file, get the extension
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getExtension($file)
	{
		$extension = JFile::getExt($file['name']);
		$extension = strtolower($extension);

		return $extension;
	}

	/**
	 * Optimize the attachment
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function optimize($providedPath = null)
	{
		if (!$this->isImage()) {
			return;
		}

		$path = $this->getAbsolutePath();

		// If provided path is given, this is probably optimize during upload
		if ($providedPath) {
			$path = $providedPath;
		}

		$optimizer = ED::imageoptimizer();

		if (!$optimizer->enabled()) {
			return;
		}
		
		$exists = JFile::exists($path);
			
		$table = ED::table('Optimizer');
		$table->attachment_id = $this->id;
		$table->created = JFactory::getDate()->toSql();

		if (!$exists) {
			$table->status = ED_OPTIMIZER_FAILED;
			$table->log = JText::_('File does not exist on the site');	
			$table->store();
			return false;
		}

		$state = $optimizer->optimize($path);

		if ($state !== true) {
			$table->status = ED_OPTIMIZER_FAILED;
			$table->log = $state;
		}

		if ($state === true) {
			$table->status = ED_OPTIMIZER_SUCCESS;
		}

		// Optimize the thumbnail as well
		if (!$providedPath) {
			$thumbnailPath = $this->getAbsolutePath(true);
			$optimizer->optimize($thumbnailPath);
		}

		$table->store();
	}

	/**
	 * Allows caller to upload a file
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function upload(EasyDiscussPost $post, $file)
	{
		// Get the allowed files
		$allowed = explode(',', $this->config->get('main_attachment_extension'));

		// Get the extension
		$extension = $this->getExtension($file);

		if (!$extension || !in_array($extension, $allowed)) {
			$this->setError(JText::sprintf('COM_EASYDISCUSS_FILE_ATTACHMENTS_INVALID_EXTENSION', $file['name'], $this->config->get('main_attachment_extension')));
			return false;
		}

		// Get the file size
		$size = $file['size'];
		$maxSize = $this->getUploadLimit();

		if ($maxSize && $size > $maxSize) {
			$this->setError(JText::sprintf('COM_EASYDISCUSS_FILE_ATTACHMENTS_MAX_SIZE_EXCLUDED', $file['name'], $maxSize));
			return false;
		}


		// @TODO: Ensure that the file doesn't contain any hacks.

		// Generate a unique id
		$hash = ED::getHash($file['name'] . ED::date()->toSql() . uniqid());

		// This determines which storage type to use
		$storageType = $this->config->get('storage_attachments');

		$this->table->type = '';
		$this->table->path = $hash;
		$this->table->title = $file['name'];
		$this->table->uid = $post->id;
		$this->table->created = ED::date()->toSql();
		$this->table->published = 1;
		$this->table->storage = $storageType;
		$this->table->mime = $file['type'];
		$this->table->size = $size;
		$this->table->store();

		// Get the storage path
		$storagePath = $this->getStoragePath() . '/' . $this->table->path;

		// Copy the temporary file to Joomla's temporary folder first
		$temporaryPath = JPATH_ROOT . '/tmp/' . $hash;
		JFile::copy($file['tmp_name'], $temporaryPath);

		// Determine if the file is an image
		$isImage = ED::image()->isImage($temporaryPath);

		// Get the storage
		$storage = ED::storage($this->table->storage);

		// Optimize the image before we upload to the storage
		$this->optimize($temporaryPath);

		// If this is an image, create a thumb
		if ($isImage) {

			// Create a thumbnail and upload it as well
			$thumbnailPath = $this->createThumbnail($temporaryPath);
			$thumbnailFilename = basename($thumbnailPath);
			$thumbnailStoragePath = dirname($storagePath) . '/' . $thumbnailFilename;

			// Upload the thumbnail
			$storage->upload($thumbnailFilename, $thumbnailPath, $thumbnailStoragePath, true);
		}

		// Now we upload the file
		$storage->upload($hash, $temporaryPath, $storagePath, true);

		return true;
	}

	/**
	 * Duplicate the attachment
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function duplicate($newPostId)
	{
		$table = ED::table('Attachments');

		// Bind the current attachment data for a new record
		$table->bind($this->table);

		// Reset the id
		$table->id = null;

		// Update to the duplicated post id
		$table->uid = $newPostId;

		// Get the original hash first before update it
		$oriHash = $table->path;

		// Get the new hash for the duplicated file
		$hash = ED::getHash($table->title . ED::date()->toSql() . uniqid());

		// Update the path
		$table->path = $hash;

		// Store now to duplicate the attachment
		$table->store();

		// The storage path of the original file
		$oriPath = $this->getStoragePath() . '/' . $oriHash;

		// The new storage path for the duplicated file
		$newPath = $this->getStoragePath() . '/' . $table->path;

		// Get the storage
		$storage = ED::storage($table->storage);

		// Now duplicate the file
		$storage->upload(basename($newPath), $oriPath, $newPath);

		$isImage = ED::image()->isImage($oriPath);

		// Duplicate its thumbnail as well if this is an image
		if ($isImage) {
			$oriPathThumb = $oriPath . '_thumb';
			$newPathThumb = $newPath . '_thumb';

			$storage->upload(basename($newPathThumb), $oriPathThumb, $newPathThumb);
		}

		return $table;
	}
}
