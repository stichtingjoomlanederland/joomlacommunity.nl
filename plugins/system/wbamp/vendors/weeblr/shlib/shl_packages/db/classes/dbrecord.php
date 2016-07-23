<?php
/**
 * Shlib - programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2015
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.3.1.540
 * @date				2016-07-18
 */

// Security check to ensure this file is being included by a parent file.
defined('_JEXEC') or die;

/**
 *
 * Holds data for a database instance description
 *
 * @author yannick
 *
 */
class ShlDbClass_Dbrecord {

  // (decorated) J! database object
  var $db =  null;

  // enabled flag
  var $enabled = true;

  // weight, when multiple instances are in use
  var $weight = 1;

  /**
   *
   * Construct a database instance record, for use by shLib
   * database helper
   *
   * @param JDatabase $db the original Joomla! database object
   * @param string $name unique name of the instance
   * @param integer $weight weight of the instance in the weighted random selection process
   * in case of multiple instances use
   * @param boolean $enableQueryCache enabled flag, decide to use or not query cache on this instance
   * @param array $tablesExclusionList array of tables names that will disable query caching if in a query
   * @param boolean $enableForJoomla if true, query caching will be enabled on the global Joomla DB instance
   */
  public function __construct( $db, $name, $weight, $enableQueryCache, $tablesExclusionList, $enableForJoomla) {

    // decorates db instance with our simplified access methods
    $this->db = new ShlDbClass( $db);
    $this->db->queryCacheEnabled = $enableQueryCache;
    $this->db->instanceName = $name;
    $this->db->queryCacheEnabledForJoomla = $enableForJoomla;
    $this->db->queryCacheTableExclusionList = $tablesExclusionList;
    $this->enabled = true;
    $this->weight = $weight;
  }

}
