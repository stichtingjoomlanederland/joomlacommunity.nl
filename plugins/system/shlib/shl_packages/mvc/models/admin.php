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
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

if(version_compare(JVERSION, '3', 'ge')) {

  abstract Class ShlMvcModel_Admin extends JModelAdmin {

    /**
     * Constructor
     *
     * @param   array  $config  An array of configuration options (name, state, dbo, table_path, ignore_request).
     *
     * @since   11.1
     */
    public function __construct($config = array())
    {

      parent::__construct( $config);

      // Set the model dbo
      if (!array_key_exists('dbo', $config))
      {
        $this->_db = JFactory::getDbo();
      }

    }

  }

} else {

  jimport( 'joomla.application.component.model' );
  abstract Class ShlMvcModel_Admin extends JModelAdmin {

    /**
     * Constructor
     *
     * @param   array  $config  An array of configuration options (name, state, dbo, table_path, ignore_request).
     *
     * @since   11.1
     */
    public function __construct($config = array())
    {

      parent::__construct( $config);

      // Set the model dbo
      if (!array_key_exists('dbo', $config))
      {
        $this->_db = ShlDbHelper::getDb();
      }

    }
  }

}
