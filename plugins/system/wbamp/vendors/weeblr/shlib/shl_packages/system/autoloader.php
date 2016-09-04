<?php
/**
 * Shlib - programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2016
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.3.1.580
 * @date				2016-08-25
 */

// Security check to ensure this file is being included by a parent file.
defined('_JEXEC') or die;

/**
 *
 * Autoloader, as per naming conventions:
 *
 * - root path stored in self::$_rootPathes, one per registered prefix
 * - process all classes starting with registered list of prefixes
 * - Global naming scheme:
 *   ShlPackageSubdirOthersubdir_Filename
 *
 *		Shl is one of many prefixes. Prefixes are registered to the class
 *    using the registerPrefix() method, passing in the desired prefix
 *    together with an associated root path (which will default to
 *    2 levels up from current file, that is the /plugins dir)
 *    Package is a subdir of (root_path)
 *    Subdir, Othersubdir are nested subdirs, all lowercase
 *    Subdir, Othersubdir will all be pluralized, as in:
 *    "Helper" ==> "helpers"
 *    If last letter of Subdir name is an s, then "es" is used as in:
 *    "Class" ==> "classes"
 *    note: Package name is not pluralized
 *
 *    After the first underscore, the file name is set. If the file name is missing
 *    then the last subdir name is used instead.
 *    It'll be lowercased as well
 *
 *    Example1: ShlPackageSubdirOthersubdir_Filename will be searched in
 *
 *    self::$_rootPathes['Shl'] . 'package/subdirs/othersubdirs/filename.php'
 *
 *    Example2: YgExampleClassClient_Http will be searched for in
 *
 *    self::$_rootPathes['Yg'] . 'example/classes/clients/http.php'
 *
 *		Example3: YgExampleClassClient will be searched for in
 *
 *    self::$_rootPathes['Yg'] . 'example/classes/clients/client.php'
 *
 *    Note: only forward slashes are used throughout
 *
 */
class ShlSystem_Autoloader {

  // array of prefixes
  protected static $_prefixes = array();
  protected static $_rootPathes = array();
  protected static $_isPackage = array();

  /**
   * Register our autoloader function with PHP
   * 
   * @param string $rootPath full path to root dir of library packages
   */
  public static function initialize( $rootPath = '') {

    // get Joomla or other extensions/scripts autoloader out
    spl_autoload_unregister("__autoload");

    // add our own
    self::registerPrefix( 'Shl', $rootPath);
    $registered = spl_autoload_register(array('ShlSystem_Autoloader', 'autoload'));

    // stitch back any pre-existing autoload function at the end of the list
    if(function_exists("__autoload")) {
      spl_autoload_register("__autoload");
    }

    return $registered;
  }

  public static function registerPrefix( $prefix = 'Shl', $rootPath = '', $isPackage = true) {

    $prefix = trim( $prefix);
    // no prefix, no love
    if(empty( $prefix)) {
      return false;
    }

    $rootPath = trim( $rootPath);
    // path maybe set, or else use default
    if(!empty( $rootPath)) {
      $rootPath = realpath( $rootPath);
    } else {
      $rootPath = realpath( rtrim( dirname(__FILE__), '/') . '/../../');
    }

    // we have a path, and a prefix, we can register them
    if( $rootPath) {

      // store the prefix
      self::$_prefixes[$prefix] = strlen( $prefix);

      // finalize and store the path
      $rootPath = str_replace( DIRECTORY_SEPARATOR, '/', $rootPath);
      $rootPath = rtrim( $rootPath, '/') . '/';
      self::$_rootPathes[$prefix] = $rootPath;
      
      // store whether this is a package
      self::$_isPackage[$prefix] = $isPackage;
    }

    return true;
  }

  public static function autoload ( $class) {

    // check if not already there
    if (class_exists( $class)) {
      return true;
    }

    // search for one of our prefixes, and exit if not found
    $prefix = self::_searchPrefix( $class);
    if(empty( $prefix)) {
      return false;
    }

    // remove prefix
    $path = ltrim(substr( $class, self::$_prefixes[$prefix]));

    // separate path and file name
    $bits = explode( '_', $path);

    // do we have a filename ?
    $fileName = empty( $bits[1]) ? '' : strtolower( $bits[1]);

    // process path
    $count = preg_match_all( '#([A-Z][a-z0-9_]+)#', $bits[0], $matches);
    $path = '';
    if(!empty( $matches[0])) {
      foreach( $matches[0] as $part) {
        // pluralize, except for first part, which is package name
        // unless this is not a package of course
        $path .= self::_format( $part, !self::$_isPackage[$prefix] || !empty( $path)) . '/';
      }
    }

    // if we don't have a filename yet, let's use the last sub dir name
    if(empty( $fileName)) {
      $fileName = strtolower(array_pop( $matches[0]));
    }

    // use Joomla loader to load
    JLoader::register( $class, self::$_rootPathes[$prefix] . $path . $fileName . '.php');

    // we must call explicitely Jloader::load. We could simply return false
    // so that it's called automatically later on, but that would fail
    // if several extensions use the same system, as __autoload (hence JLoader)
    // can be fired before one of the copies of our autoloader
    // TODO: revalidate, should not be needed anymore under 1.6+
    // also, why not include the file directly?
    $loaded = JLoader::load( $class);

    return $loaded;
  }


  /**
   * Iterate over registered prefixes record and return
   * longest prefix that matches beginning of class name
   *
   * @param string $class full class name we're trying to autoload
   */
  protected static function _searchPrefix( $class) {

    $prefix = '';
    foreach(self::$_prefixes as $storedPrefix => $prefixLength) {
      $match = substr( $class, 0, $prefixLength) == $storedPrefix;
      if($match) {
        if (strlen($storedPrefix) > strlen($prefix) || empty( $prefix)) {
          $prefix = $storedPrefix;
        }
      }
    }

    return $prefix;
  }

  protected static function _format( $pathPart, $pluralize) {

    $pathPart = trim( $pathPart);
    if(empty($pathPart)) {
      return '';
    }

    $formated = strtolower( $pathPart);
    if($pluralize) {
      $formated .= strtolower(substr( $pathPart, -1)) == 's' ? 'es' : 's';
    }

    return $formated;

  }

}
