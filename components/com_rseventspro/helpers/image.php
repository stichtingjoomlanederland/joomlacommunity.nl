<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RSEventsProImage
{
	/**
	 * Array to hold the object instances
	 *
	 * @var    array
	 */
	public static $instances = array();
	
	/**
	 * Event ID
	 *
	 * @var    int
	 */
	protected $id;
	
	/**
	 * Image width
	 *
	 * @var    int
	 */
	protected $width;
	
	/**
	 * Image height
	 *
	 * @var    int
	 */
	protected $height = null;
	
	/**
	 * Default image
	 *
	 * @var    string
	 */
	protected $default;
	
	/**
	 * Class constructor
	 *
	 * @param   int  	$id  	Event ID
	 * @param   int  	$width  Image width
	 * @param   mixed  	$height Image height
	 *
	 */
	public function __construct($id, $width, $height = null) {
		$this->id		= (int) $id;
		$this->width	= (int) $width;
		$this->height	= !is_null($height) ? (int) $height : null;
		
		$this->setDefault();
	}
	
	/**
	 * Returns a reference to a RSEventsProImage object
	 *
	 * @param   int  	$id  	Event ID
	 * @param   int  	$width  Image width
	 * @param   mixed  	$height Image height
	 *
	 * @return  RSEventsProImage   RSEventsProImage object
	 *
	 */
	public static function getInstance($id, $width, $height = null) {
		$hash = md5($id.$width);
		
		if (!isset(self::$instances[$hash])) {
			$classname = 'RSEventsProImage';
			self::$instances[$hash] = new $classname($id, $width, $height);
		}
		
		return self::$instances[$hash];
	}
	
	/**
	 * Return the image path
	 *
	 * @return  string  The image path
	 *
	 */
	public function output() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$local	= JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs';
		$abs	= JUri::root().'components/com_rseventspro/assets/images/events/thumbs';
		
		$query->select($db->qn('icon'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.$db->q($this->id));
		$db->setQuery($query);
		$image = $db->loadResult();
		
		// No image was found or the required width is 0
		if (!$image || $this->width == 0) {
			return $this->default;
		}
		
		// Get file extension
		$extension	= $this->getExt($image);
		// Strip extension
		$name		= $this->stripExt($image);
		
		// Check if the requested width for the image exists on the server
		if (file_exists($local.'/'.$this->width.'/'.md5($this->width.$name).'.'.$extension)) {
			return $abs.'/'.$this->width.'/'.md5($this->width.$name).'.'.$extension;
		}
		
		// The file does not exist on the server. In this case, try to create the image
		return $this->createImage($name, $extension);
	}
	
	/**
	 * If the image thumb does not exist, then try to create it
	 *
	 * @param   string  $name  		The file name
	 * @param   string  $extension  The file extension
	 *
	 * @return  string  The thumb image
	 *
	 */
	protected function createImage($name, $extension) {
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		
		$original		= JPATH_SITE.'/components/com_rseventspro/assets/images/events/'.$name.'.'.$extension;
		$thumb_location	= JPATH_SITE.'/components/com_rseventspro/assets/images/events/thumbs/'.$this->width;
		$image			= $thumb_location.'/'.md5($this->width.$name).'.'.$extension;
		$absolute		= JUri::root().'components/com_rseventspro/assets/images/events/thumbs/'.$this->width.'/'.md5($this->width.$name).'.'.$extension;
		
		// Check for the original image
		if (!file_exists($original)) {
			return $this->default;
		}
		
		if (!class_exists('phpThumb')) {
			require_once JPATH_SITE.'/components/com_rseventspro/helpers/phpthumb/phpthumb.class.php';
		}
		
		// Create a new instance of the phpThumb class
		$thumb									= new phpThumb();
		$thumb->src 							= $original;
		$thumb->w								= $this->width;
		$thumb->q								= 90;
		$thumb->iar								= 1;
		$thumb->config_output_format			= $extension;
		$thumb->config_error_die_on_error		= false;
		$thumb->config_cache_disable_warning	= true;
		$thumb->config_allow_src_above_docroot	= true;
		
		// Set the height if this is available
		if ($this->height > 0) {
			$thumb->h = (int) $this->height;
		}
		
		// Get image properties, so we can crop the image to its specific values
		if ($properties = $this->getProperties()) {
			$registry = new JRegistry;
			$registry->loadString($properties);
			$properties = $registry->toArray();
			
			$thumb->sx = round($properties['left']);
			$thumb->sy = round($properties['top']);
			$thumb->sw = round($properties['width']);
			$thumb->sh = round($properties['height']);
			$thumb->zc = 0;
		}
		
		// Generate the thumbnail
		try {
			if ($thumb->GenerateThumbnail()) {
				
				if (!is_dir($thumb_location)) {
					if (JFolder::create($thumb_location)) {
						$buffer = '<html><body bgcolor="#FFFFFF"></body></html>';
						JFile::write($thumb_location.'/index.html', $buffer);
					}
				}
				
				if ($thumb->RenderToFile($image)) {
					return $absolute;
				}
			}
		} catch (Exception $e) {}
		
		// If something went wrong, then show the default image
		return $this->default;
	}
	
	/**
	 * Gets the extension of a file name
	 *
	 * @param   string  $file  The file name
	 *
	 * @return  string  The file extension
	 *
	 */
	protected function getExt($file) {
		$file = basename($file);
		
		if (strrpos($file, '.') !== false) {
			$file = explode('.',$file);
			return end($file);
		}
		
		return false;
	}
	
	/**
	 * Strips the last extension off of a file name
	 *
	 * @param   string  $file  The file name
	 *
	 * @return  string  The file name without the extension
	 *
	 */
	protected function stripExt($file) {
		return preg_replace('#\.[^.]*$#', '', $file);
	}
	
	/**
	 * Get the image properties, for croping
	 *
	 * @return  string  The image properties
	 *
	 */
	protected function getProperties() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select($db->qn('properties'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.$db->q($this->id));
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	
	/**
	 * Set default image
	 *
	 * @return  void
	 *
	 */
	protected function setDefault() {
		if ($default = rseventsproHelper::getConfig('default_image')) {
			$this->default = JUri::root().'components/com_rseventspro/assets/images/default/'.$default;
		} else {
			$this->default = JUri::root().'components/com_rseventspro/assets/images/blank.png';
		}
	}
}